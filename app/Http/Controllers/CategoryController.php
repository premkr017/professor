<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public const TYPES = [
        'income'  => 'Income',
        'expense' => 'Expense',
    ];

    public const STATUSES = [
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ];

    public function index()
    {
        $categories = Category::where('user_id', auth()->id())
            ->with('childrenRecursive')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        // Build a hierarchical tree: only top-level categories as roots.
        $tree = $categories->whereNull('parent_id')->values();

        return view('admin.categories.index', compact('categories', 'tree'));
    }

    public function create()
    {
        $parents = Category::getParentOptions();

        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['user_id'] = auth()->id();
        $data['color'] = $data['color'] ?? '#6b7280';
        $data['status'] = $data['status'] ?? 'active';
        $data['is_system'] = false;

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $this->authorizeOwnership($category);

        $parents = Category::getParentOptions($category->id);

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeOwnership($category);

        $data = $this->validateData($request, $category);

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $this->authorizeOwnership($category);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

    private function authorizeOwnership(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
    }

    private function validateData(Request $request, ?Category $category = null): array
    {
        $selfId = $category?->id;

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'type'      => ['required', 'in:income,expense'],
            'icon'      => ['nullable', 'string', 'max:50'],
            'color'     => ['nullable', 'string', 'max:7'],
            'status'    => ['required', 'in:active,inactive'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        // Prevent a category from being its own parent (or a descendant's parent).
        if (! empty($data['parent_id'])) {
            abort_if((int) $data['parent_id'] === $selfId, 422, 'A category cannot be its own parent.');

            $parent = Category::findOrFail($data['parent_id']);
            abort_if($parent->user_id !== auth()->id(), 403);

            if ($selfId !== null) {
                // Ensure the chosen parent is not a descendant of this category.
                $descendantIds = $this->descendantIds($category);
                abort_if(in_array((int) $data['parent_id'], $descendantIds, true), 422, 'A category cannot be nested under its own sub-category.');
            }
        }

        return $data;
    }

    private function descendantIds(Category $category): array
    {
        $ids = [];

        foreach ($category->children as $child) {
            $ids[] = (int) $child->id;
            $ids = array_merge($ids, $this->descendantIds($child));
        }

        return $ids;
    }
}
