<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kpi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KpiController extends Controller
{
    /**
     * Display the KPI library dashboard with optional search.
     */
    public function dashboard(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id'); // Default to id

        $kpis = Kpi::query()
            ->when($search, function ($query, $search) {
                $query->where('measure_name', 'like', "%{$search}%")
                      ->orWhere('measure_code', 'like', "%{$search}%")
                      ->orWhere('perspective', 'like', "%{$search}%")
                      ->orWhere('objective', 'like', "%{$search}%");
            })
            ->when($sortBy === 'code', function ($query) {
                $query->orderBy('measure_code', 'asc');
            })
            ->when($sortBy === 'name', function ($query) {
                $query->orderBy('measure_name', 'asc');
            })
            ->when($sortBy === 'id', function ($query) {
                $query->orderBy('id', 'asc');
            })
            ->get();

        return view('kpis.kpi-dashboard', compact('kpis', 'sortBy', 'search'));
    }

    /**
     * Show details for a single KPI.
     */
    public function showKpiView(Kpi $kpi)
    {
        $kpi->load(['segmentations', 'accreditations', 'dimensions']);
        return view('kpis.kpi-view', compact('kpi'));
    }

    public function create()
    {
        return view('kpis.kpi-add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'measure_code' => 'required|string|unique:kpis',
            'measure_owner' => 'required|string',
            'measure_name' => 'required|string',
            'description' => 'nullable|string',
            'measure_type' => 'required|string',
            'lead_lag' => 'required|string',
            'formula' => 'nullable|string',
            'unit_type' => 'nullable|string|max:255',
            'polarity' => 'nullable|string|max:255',
            'data_provider' => 'nullable|string|max:255',
            'data_source' => 'nullable|string|max:255',
            'collection_frequency' => 'nullable|string|max:255',
            'reporting_frequency' => 'nullable|string|max:255',
            'verified_by' => 'nullable|string|max:255',
            'validated_by' => 'nullable|string|max:255',
            'baseline' => 'nullable|string|max:255',
            'target' => 'nullable|string|max:255',
            'threshold_low' => 'nullable|string|max:255',
            'threshold_high' => 'nullable|string|max:255',
            'target_rationale' => 'nullable|string|max:255',
            'perspective' => 'nullable|string|max:255',
            'strategic_theme' => 'nullable|string|max:255',
            'objective' => 'nullable|string|max:255',
            'objective_owner' => 'nullable|string|max:255',
            'strategic_initiatives' => 'nullable|string',
            'intended_results' => 'nullable|string',
            'comparator' => 'nullable|string',
            'item_author' => 'nullable|string|max:255',
            'date' => 'nullable|date',
        ]);

        KPI::create($validated); // Make sure the fillable properties are set in your KPI model

        return redirect()->route('kpis.dashboard')->with('success', 'KPI added successfully.');
    }

    public function edit(Kpi $kpi)
    {
        return view('kpis.kpi-edit', compact('kpi'));
    }

    public function update(Request $request, Kpi $kpi)
    {
        $request->validate([
            'measure_code' => 'required|string|max:255|unique:kpis,measure_code,' . $kpi->id,
            'measure_name' => 'required|string|max:255',
            'measure_owner' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'measure_type' => 'nullable|string|max:255',
            'lead_lag' => 'nullable|string|max:255',
            'formula' => 'nullable|string',
            'unit_type' => 'nullable|string|max:255',
            'polarity' => 'nullable|string|max:255',
            'data_provider' => 'nullable|string|max:255',
            'data_source' => 'nullable|string|max:255',
            'collection_frequency' => 'nullable|string|max:255',
            'reporting_frequency' => 'nullable|string|max:255',
            'verified_by' => 'nullable|string|max:255',
            'validated_by' => 'nullable|string|max:255',
            'baseline' => 'nullable|string|max:255',
            'target' => 'nullable|string|max:255',
            'threshold_low' => 'nullable|string|max:255',
            'threshold_high' => 'nullable|string|max:255',
            'target_rationale' => 'nullable|string',
            'perspective' => 'nullable|string|max:255',
            'strategic_theme' => 'nullable|string|max:255',
            'objective' => 'nullable|string|max:255',
            'objective_owner' => 'nullable|string|max:255',
            'strategic_initiatives' => 'nullable|string',
            'intended_results' => 'nullable|string',
            'comparator' => 'nullable|string',
            'item_author' => 'nullable|string|max:255',
            'date' => 'nullable|string|max:255',
        ]);

        $kpi->update($request->only([
            'measure_code',
            'measure_name',
            'measure_owner',
            'description',
            'measure_type',
            'lead_lag',
            'formula',
            'unit_type',
            'polarity',
            'data_provider',
            'data_source',
            'collection_frequency',
            'reporting_frequency',
            'verified_by',
            'validated_by',
            'baseline',
            'target',
            'threshold_low',
            'threshold_high',
            'target_rationale',
            'perspective',
            'strategic_theme',
            'objective',
            'objective_owner',
            'strategic_initiatives',
            'intended_results',
            'comparator',
            'item_author',
            'date',
        ]));

        return redirect()->route('kpis.dashboard')->with('success', 'KPI updated successfully!');
    }

    public function destroy(Kpi $kpi)
    {
        $kpi->delete();
        return redirect()->route('kpis.dashboard')->with('success', 'KPI deleted successfully!');
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id'); // Default to id

        $kpis = Kpi::when($search, function ($query, $search) {
            return $query->where('measure_name', 'like', "%{$search}%")
                         ->orWhere('measure_code', 'like', "%{$search}%")
                         ->orWhere('perspective', 'like', "%{$search}%")
                         ->orWhere('objective', 'like', "%{$search}%")
                         ->orWhere('strategic_theme', 'like', "%{$search}%");
        })
        ->when($sortBy === 'perspective', function ($query) {
            $query->orderBy('perspective', 'asc');
        })
        ->when($sortBy === 'objective', function ($query) {
            $query->orderBy('objective', 'asc');
        })
        ->when($sortBy === 'theme', function ($query) {
            $query->orderBy('strategic_theme', 'asc');
        })
        ->when($sortBy === 'code', function ($query) {
            $query->orderBy('measure_code', 'asc');
        })
        ->when($sortBy === 'name', function ($query) {
            $query->orderBy('measure_name', 'asc');
        })
        ->when($sortBy === 'id', function ($query) {
            $query->orderBy('id', 'asc');
        })
        ->get();

        return view('kpis.kpi-search', compact('kpis', 'search'));
    }

    /**
     * Export KPI data to Excel using PhpSpreadsheet
     */
    public function export(Kpi $kpi)
    {
        try {
            // Load related data
            $kpi->load(['segmentations', 'accreditations', 'dimensions']);
            
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('HAU KPI System')
                ->setTitle('KPI: ' . $kpi->measure_name)
                ->setSubject('KPI Export')
                ->setDescription('KPI details export from Holy Angel University KPI System');
            
            // Set sheet title
            $sheet->setTitle('KPI Details');
            
            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(25);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(25);
            $sheet->getColumnDimension('D')->setWidth(40);
            
            // Add title and header
            $sheet->setCellValue('A1', 'HOLY ANGEL UNIVERSITY');
            $sheet->setCellValue('A2', 'BALANCED SCORECARD Performance Measures Information Card');
            $sheet->mergeCells('A1:D1');
            $sheet->mergeCells('A2:D2');
            
            // Style the title
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF99']]
            ];
            $sheet->getStyle('A1:D2')->applyFromArray($titleStyle);
            
            // Start adding KPI details from row 4
            $currentRow = 4;
            
            // Main KPI details
            $this->addExcelRow($sheet, $currentRow++, 'Perspective', $kpi->perspective, 'Measure Code', $kpi->measure_code);
            $this->addExcelRow($sheet, $currentRow++, 'Measure Owner', $kpi->measure_owner, 'Measure Name', $kpi->measure_name, true);
            
            if ($kpi->strategic_theme) {
                $this->addExcelRow($sheet, $currentRow, 'Strategic Theme', $kpi->strategic_theme);
                $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
                $currentRow++;
            }
            
            $this->addExcelRow($sheet, $currentRow, 'Description', $kpi->description);
            $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
            $currentRow++;
            
            $this->addExcelRow($sheet, $currentRow++, 'Objective', $kpi->objective, 'Measure Type', $kpi->measure_type);
            $this->addExcelRow($sheet, $currentRow++, 'Objective Owner', $kpi->objective_owner, 'Lead/Lag', $kpi->lead_lag);
            
            $this->addExcelRow($sheet, $currentRow, 'Formula', $kpi->formula);
            $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
            $currentRow++;
            
            $this->addExcelRow($sheet, $currentRow++, 'Unit Type', $kpi->unit_type, 'Polarity', $kpi->polarity);
            $this->addExcelRow($sheet, $currentRow++, 'Data Provider', $kpi->data_provider, 'Data Source', $kpi->data_source);
            $this->addExcelRow($sheet, $currentRow++, 'Collection Frequency', $kpi->collection_frequency, 'Reporting Frequency', $kpi->reporting_frequency);
            $this->addExcelRow($sheet, $currentRow++, 'Verified by', $kpi->verified_by, 'Validated by', $kpi->validated_by);
            $this->addExcelRow($sheet, $currentRow++, 'Baseline', $kpi->baseline, 'Target', $kpi->target);
            
            // Thresholds
            $thresholds = '';
            if ($kpi->threshold_low) {
                $thresholds .= 'Low: ' . $kpi->threshold_low . ' ';
            }
            if ($kpi->threshold_high) {
                $thresholds .= 'High: ' . $kpi->threshold_high;
            }
            $this->addExcelRow($sheet, $currentRow, 'Thresholds', $thresholds);
            $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
            $currentRow++;
            
            $this->addExcelRow($sheet, $currentRow, 'Intended Results', $kpi->intended_results);
            $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
            $currentRow++;
            
            $this->addExcelRow($sheet, $currentRow, 'Strategic Initiatives', $kpi->strategic_initiatives);
            $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
            $currentRow++;
            
            $this->addExcelRow($sheet, $currentRow, 'Target Rationale', $kpi->target_rationale);
            $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
            $currentRow++;
            
            $this->addExcelRow($sheet, $currentRow, 'Comparator', $kpi->comparator);
            $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
            $currentRow++;
            
            $this->addExcelRow($sheet, $currentRow++, 'Item Author', $kpi->item_author, 'Date', $kpi->date);
            
            // Add spacing
            $currentRow += 2;
            
            // Add segmentations if they exist
            if ($kpi->segmentations && $kpi->segmentations->count() > 0) {
                $currentRow = $this->addSegmentationsToExcel($sheet, $kpi->segmentations, $currentRow);
            }
            
            // Add accreditations if they exist
            if ($kpi->accreditations && $kpi->accreditations->count() > 0) {
                $currentRow = $this->addAccreditationsToExcel($sheet, $kpi->accreditations, $currentRow);
            }
            
            // Add dimensions if they exist
            if ($kpi->dimensions && $kpi->dimensions->count() > 0) {
                $currentRow = $this->addDimensionsToExcel($sheet, $kpi->dimensions, $currentRow);
            }
            
            // Apply borders to all used cells
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];
            $sheet->getStyle("A1:D{$currentRow}")->applyFromArray($borderStyle);
            
            // Create writer and download file
            $writer = new Xlsx($spreadsheet);
            $filename = 'KPI_' . $kpi->measure_code . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            // Clean any output that might have been sent
            if (ob_get_length()) {
                ob_end_clean();
            }
            
            // Create a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'kpi_export');
            $writer->save($tempFile);
            
            // Verify the file was created and has content
            if (!file_exists($tempFile) || filesize($tempFile) === 0) {
                throw new \Exception('Failed to create Excel file');
            }
            
            // Return the file as a download response
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            // Log the error and return a user-friendly message
            \Log::error('KPI Export Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to export KPI',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Helper method to add a row to the Excel sheet
     */
    private function addExcelRow($sheet, $row, $label1, $value1, $label2 = null, $value2 = null, $bold = false)
    {
        $sheet->setCellValue("A{$row}", $label1);
        $sheet->setCellValue("B{$row}", $value1);
        
        if ($label2) {
            $sheet->setCellValue("C{$row}", $label2);
            $sheet->setCellValue("D{$row}", $value2);
        }
        
        // Style label cells
        $labelStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']]
        ];
        $sheet->getStyle("A{$row}")->applyFromArray($labelStyle);
        
        if ($label2) {
            $sheet->getStyle("C{$row}")->applyFromArray($labelStyle);
        }
        
        if ($bold) {
            $sheet->getStyle("B{$row}")->getFont()->setBold(true);
            if ($value2) {
                $sheet->getStyle("D{$row}")->getFont()->setBold(true);
            }
        }
    }
    
    /**
     * Add segmentations to Excel sheet
     */
    private function addSegmentationsToExcel($sheet, $segmentations, $startRow)
    {
        $currentRow = $startRow;
        
        // Check if target_level or goal exists
        $hasTargetLevel = $segmentations->contains(fn($seg) => !is_null($seg->target_level));
        $hasGoal = $segmentations->contains(fn($seg) => !is_null($seg->goal));
        
        // Add section title
        $sheet->setCellValue("A{$currentRow}", 'SEGMENTATIONS');
        $sheet->mergeCells("A{$currentRow}:D{$currentRow}");
        $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
        $currentRow++;
        
        // Add headers
        $sheet->setCellValue("A{$currentRow}", 'Segmentation');
        $sheet->setCellValue("B{$currentRow}", 'Code');
        $sheet->setCellValue("C{$currentRow}", 'Owner');
        
        // Add fourth column only if target_level or goal exists
        if ($hasTargetLevel || $hasGoal) {
            if ($hasTargetLevel && $hasGoal) {
                $sheet->setCellValue("D{$currentRow}", 'Target Level / Goal');
            } elseif ($hasTargetLevel) {
                $sheet->setCellValue("D{$currentRow}", 'Target Level');
            } else {
                $sheet->setCellValue("D{$currentRow}", 'Goal');
            }
        }
        
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']]
        ];
        
        if ($hasTargetLevel || $hasGoal) {
            $sheet->getStyle("A{$currentRow}:D{$currentRow}")->applyFromArray($headerStyle);
        } else {
            $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray($headerStyle);
        }
        $currentRow++;
        
        // Add data
        foreach ($segmentations as $seg) {
            $sheet->setCellValue("A{$currentRow}", $seg->segmentation);
            $sheet->setCellValue("B{$currentRow}", $seg->code);
            $sheet->setCellValue("C{$currentRow}", $seg->owner);
            
            // Only add fourth column if target_level or goal exists
            if ($hasTargetLevel || $hasGoal) {
                $targetGoal = '';
                if (!empty($seg->target_level)) {
                    $targetGoal .= $seg->target_level;
                }
                if (!empty($seg->goal)) {
                    $targetGoal .= (!empty($targetGoal) ? ' | ' : '') . $seg->goal;
                }
                $sheet->setCellValue("D{$currentRow}", $targetGoal ?: '-');
            }
            
            $currentRow++;
        }
        
        return $currentRow + 2; // Add spacing
    }
    
    /**
     * Add accreditations to Excel sheet
     */
    private function addAccreditationsToExcel($sheet, $accreditations, $startRow)
    {
        $currentRow = $startRow;
        
        // Add section title
        $sheet->setCellValue("A{$currentRow}", 'ACCREDITATIONS');
        $sheet->mergeCells("A{$currentRow}:D{$currentRow}");
        $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
        $currentRow++;
        
        // Add headers
        $sheet->setCellValue("A{$currentRow}", 'Accrediting Body ID');
        $sheet->setCellValue("B{$currentRow}", 'Accrediting Body Name');
        $sheet->setCellValue("C{$currentRow}", 'Program Unit');
        $sheet->mergeCells("C{$currentRow}:D{$currentRow}");
        
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']]
        ];
        $sheet->getStyle("A{$currentRow}:D{$currentRow}")->applyFromArray($headerStyle);
        $currentRow++;
        
        // Add data
        foreach ($accreditations as $acc) {
            $sheet->setCellValue("A{$currentRow}", $acc->accrediting_body_id);
            $sheet->setCellValue("B{$currentRow}", $acc->accrediting_body_name);
            $sheet->setCellValue("C{$currentRow}", $acc->program_unit);
            $sheet->mergeCells("C{$currentRow}:D{$currentRow}");
            $currentRow++;
        }
        
        return $currentRow + 2; // Add spacing
    }
    
    /**
     * Add dimensions to Excel sheet
     */
    private function addDimensionsToExcel($sheet, $dimensions, $startRow)
    {
        $currentRow = $startRow;
        
        // Add section title
        $sheet->setCellValue("A{$currentRow}", 'DIMENSIONS');
        $sheet->mergeCells("A{$currentRow}:D{$currentRow}");
        $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
        $currentRow++;
        
        // Add headers
        $sheet->setCellValue("A{$currentRow}", 'Dimensions');
        $sheet->setCellValue("B{$currentRow}", 'Description');
        $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
        
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']]
        ];
        $sheet->getStyle("A{$currentRow}:D{$currentRow}")->applyFromArray($headerStyle);
        $currentRow++;
        
        // Add data
        foreach ($dimensions as $dim) {
            $sheet->setCellValue("A{$currentRow}", $dim->dimensions);
            $sheet->setCellValue("B{$currentRow}", $dim->description);
            $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
            $currentRow++;
        }
        
        return $currentRow;
    }

}
