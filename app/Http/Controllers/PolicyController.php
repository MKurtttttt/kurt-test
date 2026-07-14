<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;
use Illuminate\Support\Facades\Auth;

class PolicyController extends Controller
{
    private function authorizeManagement()
    {
        $allowedRoles = ['IDC Admin', 'SuperAdmin'];
        if (!in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'Unauthorized Action');
        }
    }

    /**
     * Display a listing of policies (admin dashboard view).
     */
    public function index(Request $request)
    {
        $this->authorizeManagement();

        $dbQuery = Policy::query();
        $searchQuery = $request->input('query');
        $category = $request->input('category');
        $revisionFilter = $request->input('revision_filter');
        $policyYear = $request->input('policy_year');

        if (!empty($searchQuery)) {
            $dbQuery->where(function ($q) use ($searchQuery) {
                $q->where('title', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('document_code', 'LIKE', "%{$searchQuery}%");
            });
        }

        if (!empty($category)) {
            $dbQuery->where('category', $category);
        }

        if ($revisionFilter === 'original') {
            $dbQuery->where(function($q) {
                $q->where('revision_count', 0)
                  ->orWhereNull('revision_count');
            });
        } elseif ($revisionFilter === 'revised') {
            $dbQuery->where('revision_count', '>', 0);
        }

        if (!empty($policyYear)) {
            $dbQuery->where('policy_date', $policyYear);
        }

        $policies = $dbQuery->orderBy('title', 'asc')->paginate(20);
        
        $categories = Policy::pluck('category')->unique()->filter()->sort(SORT_FLAG_CASE | SORT_NATURAL)->values()->toArray();
        $policyYears = Policy::whereNotNull('policy_date')->pluck('policy_date')->unique()->filter()->sort()->values()->toArray();

        return view('iso.management.policies.index', compact(
            'policies', 
            'searchQuery', 
            'category', 
            'categories', 
            'revisionFilter', 
            'policyYear', 
            'policyYears'
        ))->with('query', $searchQuery);
    }

    /**
     * Show the form for creating a new policy.
     */
    public function create()
    {
        $this->authorizeManagement();
        $categories = Policy::pluck('category')->unique()->filter()->sort(SORT_FLAG_CASE | SORT_NATURAL)->values()->toArray();
        return view('iso.management.policies.create', compact('categories'));
    }

    /**
     * Store a newly created policy in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeManagement();

        $request->validate([
            'title'            => 'required|string|max:255',
            'url'              => 'required|url|max:1000',
            'category'         => 'nullable|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'document_code'    => 'nullable|string|max:100',
            'revision_count'   => 'nullable|integer|min:0',
            'effectivity_date' => 'nullable|date',
            'policy_date'      => 'nullable|string|max:100',
        ]);

        Policy::create([
            'title'            => trim($request->title),
            'url'              => trim($request->url),
            'category'         => trim($request->category),
            'description'      => trim($request->description),
            'document_code'    => $request->document_code ? trim($request->document_code) : null,
            'revision_count'   => $request->filled('revision_count') ? intval($request->revision_count) : 0,
            'effectivity_date' => $request->effectivity_date ?: null,
            'policy_date'      => $request->policy_date ? trim($request->policy_date) : null,
        ]);

        return redirect()->route('iso.management.policies.index')
            ->with('success', 'Policy created successfully.');
    }

    /**
     * Show the form for editing the specified policy.
     */
    public function edit($id)
    {
        $this->authorizeManagement();
        $policy = Policy::findOrFail($id);
        $categories = Policy::pluck('category')->unique()->filter()->sort(SORT_FLAG_CASE | SORT_NATURAL)->values()->toArray();
        return view('iso.management.policies.edit', compact('policy', 'categories'));
    }

    /**
     * Update the specified policy in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeManagement();
        $policy = Policy::findOrFail($id);

        $request->validate([
            'title'            => 'required|string|max:255',
            'url'              => 'required|url|max:1000',
            'category'         => 'nullable|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'document_code'    => 'nullable|string|max:100',
            'revision_count'   => 'nullable|integer|min:0',
            'effectivity_date' => 'nullable|date',
            'policy_date'      => 'nullable|string|max:100',
        ]);

        $policy->update([
            'title'            => trim($request->title),
            'url'              => trim($request->url),
            'category'         => trim($request->category),
            'description'      => trim($request->description),
            'document_code'    => $request->document_code ? trim($request->document_code) : null,
            'revision_count'   => $request->filled('revision_count') ? intval($request->revision_count) : 0,
            'effectivity_date' => $request->effectivity_date ?: null,
            'policy_date'      => $request->policy_date ? trim($request->policy_date) : null,
        ]);

        return redirect()->route('iso.management.policies.index')
            ->with('success', 'Policy updated successfully.');
    }

    /**
     * Remove the specified policy from storage.
     */
    public function destroy($id)
    {
        $this->authorizeManagement();
        $policy = Policy::findOrFail($id);
        $policy->delete();

        return redirect()->route('iso.management.policies.index')
            ->with('success', 'Policy deleted successfully.');
    }
}
