<?php

namespace App\Http\Controllers;

use App\Models\IsoMasterDocument;
use App\Imports\DocumentsImport;
use App\Exports\DocumentsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class IsoManagementController extends Controller
{
    public function index()
    {
        // Check if user is authorized
        $userRole = auth()->user()->role;
        $allowedRoles = ['IDC Admin', 'SuperAdmin'];

        if(!in_array($userRole, $allowedRoles)){
            return redirect()->back()->with('error', 'Unauthorized Action');
        }
        // Get total count of all registered documents
        $totalDocuments = IsoMasterDocument::count();
        $originalDocuments = IsoMasterDocument::where('is_original', true)->count();
        $revisedDocuments = IsoMasterDocument::where('current_revision', '>', 0)->count();
        $activeDocuments = IsoMasterDocument::where('status', 'active')->count();
        $deletedDocuments = IsoMasterDocument::where('status', 'deleted')->count();
        $supersededDocuments = IsoMasterDocument::where('status', 'superseded')->count();
        $byClassification = IsoMasterDocument::selectRaw('source_type, COUNT(*) as count')
            ->groupBy('source_type')
            ->get();
        // Top 10 departments
        $byDepartment = IsoMasterDocument::selectRaw('originating_section, COUNT(*) as count')
            ->groupBy('originating_section')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        return view('iso.management.index', compact(
            'totalDocuments',
            'originalDocuments',
            'revisedDocuments',
            'activeDocuments',
            'deletedDocuments',
            'supersededDocuments',
            'byClassification',
            'byDepartment'
        ));
    }

    public function getDocuments(Request $request)
    {
        $query = IsoMasterDocument::query();
        if($request->has('source_type') && !empty($request->source_type)){
            $query->whereIn('source_type', $request->source_type);
        }
        if($request->has('originating_section') && !empty($request->originating_section)){
            $query->whereIn('originating_section', $request->originating_section);
        }
        if($request->has('status') && !empty($request->status)){
            $query->whereIn('status', $request->status);
        }
        if($request->has('revision_filter')){
            if($request->revision_filter === 'original_only'){
                $query->where('is_original',true);
            } elseif($request->revision_filter === 'has_revisions'){
                $query->where('current_revision', '>', 0);
            }
        }
        $documents = $query->orderBy('registered_at','desc')->get();
        return response()->json($documents);
    }
    // ===============================
    // Import and Export Documents Function
    // ===============================
    public function import(Request $request){
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx, xls,csv|max:10240', //10MB max
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try{
            $import = new DocumentsImport();
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            
            return response()->json([
                'message' => "Successfully imported {$importedCount} documents!",
                'success' => true
            ], 200);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e){
            $failures = $e->failures();
            $errors = [];

            foreach($failures as $failure){
                $errors[] = "Row {$failure->row()}: " .implode(', ', $failure->errors());
            }
            return response()->json([
                'message' => 'Import failed: ',
                'errors' => ['file' => $errors]
            ], 422);
        } catch (\Exception $e){
            $decoded = json_decode($e->getMessage(), true);

            if(json_last_error() === JSON_ERROR_NONE && is_array($decoded)){
                return response()->json([
                    'message' => 'Import completed with errors',
                    'errors' => ['file' => $decoded]
                ], 422);
            }

            return response()->json([
                'message' => 'Import failed: ' . $e->getMessage(),
                'errors' => ['file' => [$e->getMessage()]]
            ], 422);
        }
    }

    // Export Filtered Documents to Excel
    public function export(Request $request){
        $filters = [
            'source_type' => $request->input('source_type', []),
            'originating_section' => $request->input('originating_section', []),
            'revision_status' => $request->input('revision_status'),
            'status' => $request->input('status', []),
        ];

        $filename = 'iso_documents_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new DocumentsExport($filters), $filename);
    }

    // Download blank template
    public function downloadTemplate(){
        $template = [
            [
                'document_code',
                'document_title',
                'source_type',
                'specific_type',
                'originating_section',
                'current_revision',
                'registered_at',
                'status',
                'superseded_at',
                'deleted_at'
            ],
            [
                'AAC-BED-001',
                'Sample Document Title',
                'Policies',
                '',
                'AAC-BED',
                '0',
                '2024-01-15',
                'Active',
                '',
                ''
            ],
            [
                'AAC-BED-001',
                'Sample Document Title',
                'Policies',
                '',
                'AAC-BED',
                '1',
                '2024-06-20',
                'Active',
                '',
                ''
            ]
        ];

        return Excel::download(
            new class($template) implements \Maatwebsite\Excel\Concerns\FromArray {
                protected $data;

                public function __construct($data){
                    $this->data = $data;
                }
                public function array(): array{
                    return $this->data;
                }
            },
            'iso_documents_template.xlsx'
        );
    }
}
