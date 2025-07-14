<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SharepointLinks;

class SharepointController extends Controller
{
    /**
     * Display the SharePoint dashboard grouped by categories and departments.
     */
    public function index()
    {
        $allLinks = SharepointLinks::orderBy('id')->get();

        return view('sharepoint-sites.sharepoint-sites-dashboard', [
            'isoLinks'      => $allLinks->where('category', 'ISO')->groupBy('department'),
            'planningLinks' => $allLinks->where('category', 'Planning and Review')->groupBy('department'),
            'qaLinks'       => $allLinks->where('category', 'Quality Assurance')->groupBy('department'),
        ]);
    }

    /**
     * Show the form for creating a new SharePoint link.
     */
    public function create()
    {
        return view('sharepoint-sites.sharepoint-sites-add');
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
            'category'   => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'office'     => 'nullable|string|max:100',
        ]);

        $validated['created_by'] = Auth::id();

        if (SharepointLinks::create($validated)) {
            return redirect()->route('sharepoint-sites.dashboard')
                             ->with('msg', 'New SharePoint link added.');
        }

        return back()->withErrors(['error' => 'Failed to add link.']);
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
        return view('sharepoint-sites.sharepoint-sites-edit', compact('link'));
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
            'category'   => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'office'     => 'nullable|string|max:100',
        ]);

        $link->update($validated);

        return redirect()->route('sharepoint-sites.dashboard')
                         ->with('msg', 'SharePoint link updated successfully.');
    }

    /**
     * Remove the specified SharePoint link from storage.
     */
    public function destroy($id)
    {
        $link = SharepointLinks::findOrFail($id);
        $link->delete();

        return redirect()->route('sharepoint-sites.dashboard')
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
