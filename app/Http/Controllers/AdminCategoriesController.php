<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoriesController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $query->orderBy('order', 'asc')->orderBy('name', 'asc');

        $categories = $query->paginate(15)->withQueryString();
        
        return view('pages.admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7'],
            'description' => ['nullable', 'string'],
            'extensions' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (!empty($data['extensions'])) {
            $data['extensions'] = array_map('trim', explode(',', $data['extensions']));
        }

        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category created successfully');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug,' . $category->id],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7'],
            'description' => ['nullable', 'string'],
            'extensions' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (!empty($data['extensions'])) {
            $data['extensions'] = array_map('trim', explode(',', $data['extensions']));
        } else {
            $data['extensions'] = [];
        }

        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('status', 'Category deleted successfully');
    }
}
