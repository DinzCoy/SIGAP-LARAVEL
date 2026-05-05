<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FaqCategoryController extends Controller
{
    public function index()
    {
        $categories = FaqCategory::latest()->get();
        return view('admin.faq.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:faq_categories,name',
        ]);

        FaqCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Kategori FAQ berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $category = FaqCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:faq_categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Kategori FAQ berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $category = FaqCategory::findOrFail($id);
        
        if ($category->faqs()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori ini tidak dapat dihapus karena masih memiliki artikel FAQ.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Kategori FAQ berhasil dihapus.');
    }
}
