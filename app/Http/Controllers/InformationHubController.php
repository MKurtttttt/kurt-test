<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InformationHubLinks;

class InformationHubController extends Controller
{
    /**
     * Display the public Information Hub dashboard (no authentication required).
     */
    public function publicIndex()
    {
        $allLinks = InformationHubLinks::orderBy('id')->get();

        $categories = $allLinks->pluck('category')->unique()->filter(function($v){return !empty($v);})->sort()->values()->toArray();
        $linksByCategory = [];
        foreach ($categories as $cat) {
            $linksByCategory[$cat] = $allLinks->where('category', $cat)->groupBy('sub_category');
        }

        // Manually append Policies category for public home index view
        $categories[] = 'Policies';

        $policies = \App\Models\Policy::with('category')
            ->where('status', 'Active')
            ->whereHas('category')
            ->orderBy('title', 'asc')
            ->get()
            ->groupBy(function($policy) {
                return $policy->category->name;
            })
            ->sortKeys(SORT_NATURAL | SORT_FLAG_CASE);

        $policyCategories = \App\Models\PolicyCategory::orderBy('name', 'asc')->get();

        return view('home.information-hub-home', [
            'categories' => $categories,
            'linksByCategory' => $linksByCategory,
            'policies' => $policies,
            'policyCategories' => $policyCategories,
        ]);
    }

    /**
     * Display the Information Hub dashboard grouped by categories and departments.
     */
    public function index()
    {
        $allLinks = InformationHubLinks::orderBy('id')->get();

        // Get all unique categories
        $category = $allLinks->pluck('category')->unique()->filter()->values();

        // Group links by category, then by department
        $linksByCategory = [];
        foreach ($category as $cat) {
            $linksByCategory[$cat] = $allLinks->where('category', $cat)->groupBy('sub_category');
        }

        $policies = \App\Models\Policy::with('category')
            ->where('status', 'Active')
            ->whereHas('category')
            ->orderBy('title', 'asc')
            ->get()
            ->groupBy(function($policy) {
                return $policy->category->name;
            })
            ->sortKeys(SORT_NATURAL | SORT_FLAG_CASE);

        $policyCategories = \App\Models\PolicyCategory::orderBy('name', 'asc')->get();

        return view('information-hub.information-hub-dashboard', [
            'category' => $category,
            'linksByCategory' => $linksByCategory,
            'policies' => $policies,
            'policyCategories' => $policyCategories,
        ]);
    }

    /**
     * Show the form for creating a new Information Hub link.
     */
    public function create()
    {
        $categories = collect(InformationHubLinks::pluck('category')->unique()->filter(function($v){return !empty($v);})->values()->toArray())->sort()->values()->toArray();

        // Group departments by category
        $subCatByCategory = [];
        foreach ($categories as $cat) {
            $subCatByCategory[$cat] = collect(
                InformationHubLinks::where('category', $cat)
                    ->pluck('sub_category')->unique()->filter(function($v){return !empty($v);})->values()->toArray()
            )->sort()->values()->toArray();
        }

        return view('information-hub.information-hub-add', [
            'categories' => $categories,
            'subCatByCategory' => $subCatByCategory,
        ]);
    }

    /**
     * Store a newly created Information Hub in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'      => 'nullable|string|max:255',
            'sub_category'  => 'nullable|string|max:255',
            'type'          => 'nullable|string|max:255',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title'         => 'required|string|max:255',
            'url'           => 'required|url|max:1000',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->uploadAndSyncImage($request->file('image'));
        }

        InformationHubLinks::create($validated);

        return redirect()->route('information-hub.dashboard')->with('success', 'information Hub link added successfully!');
    }

    /**
     * Show the form to select which Information Hub link to edit.
     */
    public function editList()
    {
        $links = InformationHubLinks::orderBy('title')->get();
        return view('information-hub.information-hub-edit-list', compact('links'));
    }

    /**
     * Show the edit form for a specific Information Hub link.
     */
    public function edit($id)
    {
        $link = InformationHubLinks::findOrFail($id);

        // Get all unique categories
        $categories = collect(InformationHubLinks::pluck('category')->unique()->filter(function($v){return !empty($v);})->values()->toArray())->sort()->values()->toArray();

        // Group sub-categories by category
        $subCatByCategory = [];
        foreach ($categories as $cat) {
            $subCatByCategory[$cat] = collect(
                InformationHubLinks::where('category', $cat)
                    ->pluck('sub_category')->unique()->filter(function($v){return !empty($v);})->values()->toArray()
            )->sort()->values()->toArray();
        }

        return view('information-hub.information-hub-edit', [
            'link' => $link,
            'categories' => $categories,
            'subCatByCategory' => $subCatByCategory,
        ]);
    }

    /**
     * Update the specified information Hub link.
     */
    public function update(Request $request, $id)
    {
        $link = InformationHubLinks::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'url'           => 'required|url|max:1000',
            'category'      => 'nullable|string|max:255',
            'sub_category'  => 'nullable|string|max:255',
            'type'          => 'nullable|string|max:255',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image'  => 'nullable',
        ]);

        // Handle image removal
        if ($request->has('remove_image') && $link->image_path) {
            $this->deleteImageFiles($link->image_path);
            $validated['image_path'] = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($link->image_path) {
                $this->deleteImageFiles($link->image_path);
            }
            $validated['image_path'] = $this->uploadAndSyncImage($request->file('image'));
        }

        // Remove image and remove_image from validated array if not uploading
        unset($validated['image']);
        unset($validated['remove_image']);

        $link->update($validated);

        return redirect()->route('information-hub.edit-list')
                ->with('msg', 'Information Hub link updated successfully.');
    }

    /**
     * Remove the specified Information Hub link from storage.
     */
    public function destroy($id)
    {
        $link = InformationHubLinks::findOrFail($id);
        
        // Delete the image file if it exists
        if ($link->image_path) {
            $this->deleteImageFiles($link->image_path);
        }
        
        // Delete the database record
        $link->delete();

        return redirect()->route('information-hub.edit-list')
            ->with('msg', 'Information Hub link deleted successfully.');
    }

    /**
     * Show a dropdown/form to select a link to edit.
     */
    public function selectForm()
    {
        $links = InformationHubLinks::orderBy('label')->get();
        return view('information-hub.select-link', compact('links'));
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

        return redirect()->route('information-hub.edit', ['id' => $id]);
    }

    /**
     * Sync uploaded image to both public_html and public paths.
     */
    protected function uploadAndSyncImage($image)
    {
        $imageName = time() . '_' . $image->getClientOriginalName();
        $destPath = base_path('public_html/images/information_hub');
        
        if (!is_dir($destPath)) {
            mkdir($destPath, 0755, true);
        }
        $image->move($destPath, $imageName);
        
        // Sync to public/ directory for local development environment
        $localDestPath = base_path('public/images/information_hub');
        if (is_dir(base_path('public'))) {
            if (!is_dir($localDestPath)) {
                mkdir($localDestPath, 0755, true);
            }
            copy($destPath . '/' . $imageName, $localDestPath . '/' . $imageName);
        }
        
        return 'images/information_hub/' . $imageName;
    }

    /**
     * Delete image files from both public_html and public paths.
     */
    protected function deleteImageFiles($path)
    {
        if (!$path) {
            return;
        }
        
        $paths = [
            base_path('public_html/' . $path),
            base_path('public/' . $path)
        ];

        foreach ($paths as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}
