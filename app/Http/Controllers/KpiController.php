<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kpi;
use App\Exports\KpiExport;
use Maatwebsite\Excel\Facades\Excel;

class KpiController extends Controller
{
    /**
     * Display the KPI library dashboard with optional search.
     */
    public function dashboard(Request $request)
    {
        $kpis = Kpi::query()->get();

        return view('kpis.kpi-dashboard', compact('kpis'));
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
            'category' => 'required|string|in:departmental,institutional,personnel',
            'description' => 'nullable|string',
            'measure_type' => 'required|string',
            'lead_lag' => 'nullable|string',
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
    
    public function export(?Kpi $kpi = null)
    {
        if ($kpi) {
            return Excel::download(new KpiExport($kpi), "KPI_{$kpi->measure_name}.xlsx");
        }
        return Excel::download(new KpiExport(), 'KPI_List.xlsx');
    }

    public function advancedSearch(Request $request)
    {
        $source = $request->get('source', 'default');

        if ($source === 'advanced-search') {
            
            $kpis = Kpi::query()
                // Basic filters
                ->when($request->filled('category'), function ($query) use ($request) {
                    $query->where('category', $request->category);
                })
                ->when($request->filled('code'), function ($query) use ($request) {
                    $query->where('measure_code', 'LIKE', '%' . $request->code . '%');
                })
                ->when($request->filled('measure_owner'), function ($query) use ($request) {
                    $query->where('measure_owner', 'LIKE', '%' . $request->measure_owner . '%');
                })
                ->when($request->filled('perspective'), function ($query) use ($request) {
                    $query->where('perspective', 'LIKE', '%' . $request->perspective . '%');
                })
                ->when($request->filled('theme'), function ($query) use ($request) {
                    $query->where('strategic_theme', 'LIKE', '%' . $request->theme . '%');
                })
                ->when($request->filled('objective'), function ($query) use ($request) {
                    $query->where('objective', 'LIKE', '%' . $request->objective . '%');
                })
                ->when($request->filled('objective_owner'), function ($query) use ($request) {
                    $query->where('objective_owner', 'LIKE', '%' . $request->objective_owner . '%');
                })
                ->when($request->filled('measure_type'), function ($query) use ($request) {
                    $query->where('measure_type', 'LIKE', '%' . $request->measure_type . '%');
                })
                ->when($request->filled('collection_frequency'), function ($query) use ($request) {
                    $query->where('collection_frequency', 'LIKE', '%' . $request->collection_frequency . '%');
                })
                ->when($request->filled('reporting_frequency'), function ($query) use ($request) {
                    $query->where('reporting_frequency', 'LIKE', '%' . $request->reporting_frequency . '%');
                })
                ->when($request->filled('verified_by'), function ($query) use ($request) {
                    $query->where('verified_by', 'LIKE', '%' . $request->verified_by . '%');
                })
                ->when($request->filled('validated_by'), function ($query) use ($request) {
                    $query->where('validated_by', 'LIKE', '%' . $request->validated_by . '%');
                })
                
                // Segmentations filters
                ->when($request->filled('segmentation'), function ($query) use ($request) {
                    $query->whereHas('segmentations', function ($q) use ($request) {
                        $q->where('segmentation', 'LIKE', '%' . $request->segmentation . '%');
                    });
                })
                ->when($request->filled('seg_code'), function ($query) use ($request) {
                    $query->whereHas('segmentations', function ($q) use ($request) {
                        $q->where('code', 'LIKE', '%' . $request->seg_code . '%');
                    });
                })
                ->when($request->filled('seg_owner'), function ($query) use ($request) {
                    $query->whereHas('segmentations', function ($q) use ($request) {
                        $q->where('owner', 'LIKE', '%' . $request->seg_owner . '%');
                    });
                })
                ->when($request->filled('target_level'), function ($query) use ($request) {
                    $query->whereHas('segmentations', function ($q) use ($request) {
                        $q->where('target_level', 'LIKE', '%' . $request->target_level . '%');
                    });
                })
                
                // Accreditations filters
                ->when($request->filled('accrediting_body_id'), function ($query) use ($request) {
                    $query->whereHas('accreditations', function ($q) use ($request) {
                        $q->where('accrediting_body_id', 'LIKE', '%' . $request->accrediting_body_id . '%');
                    });
                })
                ->when($request->filled('accrediting_body_name'), function ($query) use ($request) {
                    $query->whereHas('accreditations', function ($q) use ($request) {
                        $q->where('accrediting_body_name', 'LIKE', '%' . $request->accrediting_body_name . '%');
                    });
                })
                ->when($request->filled('program_unit'), function ($query) use ($request) {
                    $query->whereHas('accreditations', function ($q) use ($request) {
                        $q->where('program_unit', 'LIKE', '%' . $request->program_unit . '%');
                    });
                })
                
                // Dimensions filters
                ->when($request->filled('dimensions'), function ($query) use ($request) {
                    $query->whereHas('dimensions', function ($q) use ($request) {
                        $q->where('dimensions', 'LIKE', '%' . $request->dimensions . '%');
                    });
                })
                ->when($request->filled('dim_descriptions'), function ($query) use ($request) {
                    $query->whereHas('dimensions', function ($q) use ($request) {
                        $q->where('description', 'LIKE', '%' . $request->dim_descriptions . '%');
                    });
                })
                ->get();

            return view('kpis.kpi-dashboard', compact('kpis'));
        }

        return view('kpis.kpi-advanced-search');
    }
}
