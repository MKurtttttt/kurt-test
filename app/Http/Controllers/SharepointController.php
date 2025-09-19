<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SharepointLinks;

class SharepointController extends Controller
{
    /**
     * Display the public SharePoint dashboard (no authentication required).
     */
    public function publicIndex()
    {
        $allLinks = SharepointLinks::orderBy('id')->get();

        $categories = $allLinks->pluck('category')->unique()->filter(function($v){return !empty($v);})->sort()->values()->toArray();
        $linksByCategory = [];
        foreach ($categories as $cat) {
            $linksByCategory[$cat] = $allLinks->where('category', $cat)->groupBy('department');
        }

        return view('home.sharepoint-home', [
            'categories' => $categories,
            'linksByCategory' => $linksByCategory,
        ]);
    }

    /**
     * Display the SharePoint dashboard grouped by categories and departments.
     */
    public function index()
    {
        $allLinks = SharepointLinks::orderBy('id')->get();

        // Get all unique categories
        $category = $allLinks->pluck('category')->unique()->filter()->values();

        // Group links by category, then by department
        $linksByCategory = [];
        foreach ($category as $cat) {
            $linksByCategory[$cat] = $allLinks->where('category', $cat)->groupBy('department');
        }

        return view('sharepoint-sites.sharepoint-sites-dashboard', [
            'category' => $category,
            'linksByCategory' => $linksByCategory,
        ]);
    }

    /**
     * Show the form for creating a new SharePoint link.
     */
    public function create()
    {
    $categories = collect(SharepointLinks::pluck('category')->unique()->filter(function($v){return !empty($v);})->values()->toArray())->sort()->values()->toArray();

        // Group departments by category
        $departmentsByCategory = [];
        foreach ($categories as $cat) {
            $departmentsByCategory[$cat] = collect(
                SharepointLinks::where('category', $cat)
                    ->pluck('department')->unique()->filter(function($v){return !empty($v);})->values()->toArray()
            )->sort()->values()->toArray();
        }

        // Group offices by department
        $officesByDepartment = [];
        $allDepartments = collect(SharepointLinks::pluck('department')->unique()->filter(function($v){return !empty($v);})->values()->toArray())->sort()->values()->toArray();
        foreach ($allDepartments as $dept) {
            $officesByDepartment[$dept] = collect(
                SharepointLinks::where('department', $dept)
                    ->pluck('office')->unique()->filter(function($v){return !empty($v);})->values()->toArray()
            )->sort()->values()->toArray();
        }

        return view('sharepoint-sites.sharepoint-sites-add', [
            'categories' => $categories,
            'departmentsByCategory' => $departmentsByCategory,
            'officesByDepartment' => $officesByDepartment,
        ]);
    }

    /**
     * Store a newly created SharePoint link in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'      => 'required|string|max:255',
            'url'        => 'required|url|max:255',
            'description'=> 'nullable|string|max:1000',
            'category'   => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'office'     => 'nullable|string|max:100',
        ]);

        $validated['created_by'] = Auth::id();

    SharepointLinks::create($validated);

    return redirect()->route('sharepoint-sites.dashboard')->with('success', 'SharePoint link added successfully!');
    }

    /**
     * Show the form to select which SharePoint link to edit.
     */
    public function editList()
    {
        $links = SharepointLinks::orderBy('label')->get();
        return view('sharepoint-sites.sharepoint-sites-edit-list', compact('links'));
    }

    /**
     * Show the edit form for a specific SharePoint link.
     */
    public function edit($id)
    {
        $link = SharepointLinks::findOrFail($id);

        // Get all unique categories
        $categories = collect(SharepointLinks::pluck('category')->unique()->filter(function($v){return !empty($v);})->values()->toArray())->sort()->values()->toArray();

        // Group departments by category
        $departmentsByCategory = [];
        foreach ($categories as $cat) {
            $departmentsByCategory[$cat] = collect(
                SharepointLinks::where('category', $cat)
                    ->pluck('department')->unique()->filter(function($v){return !empty($v);})->values()->toArray()
            )->sort()->values()->toArray();
        }

        // Group offices by department
        $allDepartments = collect(SharepointLinks::pluck('department')->unique()->filter(function($v){return !empty($v);})->values()->toArray())->sort()->values()->toArray();
        $officesByDepartment = [];
        foreach ($allDepartments as $dept) {
            $officesByDepartment[$dept] = collect(
                SharepointLinks::where('department', $dept)
                    ->pluck('office')->unique()->filter(function($v){return !empty($v);})->values()->toArray()
            )->sort()->values()->toArray();
        }

        return view('sharepoint-sites.sharepoint-sites-edit', [
            'link' => $link,
            'categories' => $categories,
            'departmentsByCategory' => $departmentsByCategory,
            'officesByDepartment' => $officesByDepartment,
        ]);
    }

    /**
     * Update the specified SharePoint link.
     */
    public function update(Request $request, $id)
    {
        $link = SharepointLinks::findOrFail($id);

        $validated = $request->validate([
            'label'      => 'required|string|max:255',
            'url'        => 'required|url|max:255',
            'description'=> 'nullable|string|max:1000',
            'category'   => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'office'     => 'nullable|string|max:100',
        ]);

        $link->update($validated);

    return redirect()->route('sharepoint-sites.edit-list')
             ->with('msg', 'SharePoint link updated successfully.');
    }

    /**
     * Remove the specified SharePoint link from storage.
     */
    public function destroy($id)
    {
        $link = SharepointLinks::findOrFail($id);
        $link->delete();

    return redirect()->route('sharepoint-sites.edit-list')
             ->with('msg', 'SharePoint link deleted successfully.');
    }

    /**
     * Show a dropdown/form to select a link to edit.
     */
    public function selectForm()
    {
        $links = SharepointLinks::orderBy('label')->get();
        return view('sharepoint-sites.select-link', compact('links'));
    }

    /**
     * Process the selected link and redirect to its edit page.
     */
    public function select(Request $request)
    {
        $validated = $request->validate([
            'link_id' => 'required',
        ]);

        $id = decrypt($validated['link_id']);

        return redirect()->route('sharepoint-sites.edit', ['id' => $id]);
    }
}
