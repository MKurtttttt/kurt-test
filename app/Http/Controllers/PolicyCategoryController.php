<?php

namespace App\Http\Controllers;

use App\Models\PolicyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PolicyCategoryController extends Controller
{
    /**
     * Authorize management access (IDC Admin or SuperAdmin).
     */
    protected function authorizeManagement()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['IDC Admin', 'SuperAdmin'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $this->authorizeManagement();
        $categories = PolicyCategory::withCount('policies')
            ->orderBy('name', 'asc')
            ->paginate(15);

        return view('iso.management.policies.categories', compact('categories'));
    }

    /**
     * Normalize a category name for fuzzy duplicate comparison.
     */
    protected function normalizeCategoryName($name)
    {
        $str = strtolower(trim($name));
        // Remove special characters, keep only alphanumerics and spaces
        $str = preg_replace('/[^a-z0-9\s]/', '', $str);
        // Collapse multiple spaces
        $str = preg_replace('/\s+/', ' ', $str);
        
        // Split into words, filter out "policy" / "policies"
        $words = explode(' ', $str);
        $filtered = [];
        foreach ($words as $w) {
            if ($w !== 'policy' && $w !== 'policies') {
                // Remove trailing 's' for simple singularization (e.g. Academics -> Academic)
                if (strlen($w) > 3 && substr($w, -1) === 's') {
                    $w = substr($w, 0, -1);
                }
                $filtered[] = $w;
            }
        }
        
        // Fallback to original words if everything was filtered out
        if (empty($filtered)) {
            $filtered = $words;
        }
        
        return implode(' ', $filtered);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeManagement();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $name = preg_replace('/\s+/', ' ', trim($request->name));

        // Exact case-insensitive check
        $exists = PolicyCategory::whereRaw('LOWER(name) = ?', [strtolower($name)])->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['name' => 'A category with a similar name already exists (case-insensitive duplicate check).']);
        }

        // Fuzzy similarity check
        $normalizedNew = $this->normalizeCategoryName($name);
        $existingCategories = PolicyCategory::all();
        foreach ($existingCategories as $existing) {
            if ($this->normalizeCategoryName($existing->name) === $normalizedNew) {
                return back()->withInput()->withErrors([
                    'name' => "A similar category already exists: '{$existing->name}'. To avoid confusion, please use the existing category or select a different name."
                ]);
            }
        }

        PolicyCategory::create([
            'name' => $name,
        ]);

        return redirect()->route('iso.management.policy-categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeManagement();
        $category = PolicyCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $name = preg_replace('/\s+/', ' ', trim($request->name));

        // Exact case-insensitive check excluding self
        $exists = PolicyCategory::whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['name' => 'A category with a similar name already exists (case-insensitive duplicate check).']);
        }

        // Fuzzy similarity check excluding self
        $normalizedNew = $this->normalizeCategoryName($name);
        $existingCategories = PolicyCategory::where('id', '!=', $id)->get();
        foreach ($existingCategories as $existing) {
            if ($this->normalizeCategoryName($existing->name) === $normalizedNew) {
                return back()->withInput()->withErrors([
                    'name' => "A similar category already exists: '{$existing->name}'. To avoid confusion, please use the existing category or select a different name."
                ]);
            }
        }

        $category->update([
            'name' => $name,
        ]);

        return redirect()->route('iso.management.policy-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($id)
    {
        $this->authorizeManagement();
        $category = PolicyCategory::findOrFail($id);

        // Check if there are policies assigned to this category
        if ($category->policies()->count() > 0) {
            return redirect()->route('iso.management.policy-categories.index')
                ->with('error', 'Cannot delete category because it has policies assigned to it.');
        }

        $category->delete();

        return redirect()->route('iso.management.policy-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
