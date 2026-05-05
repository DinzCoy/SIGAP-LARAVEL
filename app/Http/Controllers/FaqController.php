<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    //Bagian untuk pengguna umum.
    public function publicIndex()
    {
        $categories = FaqCategory::with(['faqs' => function ($query) {
            $query->where('is_published', true);
        }])->get();

        return view('faq.index', compact('categories'));
    }

    public function publicShow(string $id)
    {
        $faq = Faq::findOrFail($id);
        
        if (!$faq->is_published) {
            abort(404);
        }

        $faq->increment('views');

        return view('faq.show', compact('faq'));
    }

    public function publicFeedback(Request $request, string $id)
    {
        $faq = Faq::findOrFail($id);
        
        if (!$faq->is_published) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $request->validate([
            'type' => 'required|in:helpful,unhelpful'
        ]);

        if ($request->type === 'helpful') {
            $faq->increment('helpful_count');
        } else {
            $faq->increment('unhelpful_count');
        }

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted'
        ]);
    }

    //Bagian khusus administrator.
    public function index()
    {
        $faqs = Faq::with('category')->latest()->get();
        return view('admin.faq.index', compact('faqs'));
    }

    public function create()
    {
        $categories = FaqCategory::all();
        return view('admin.faq.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        Faq::create([
            'faq_category_id' => $request->faq_category_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.faq.articles.index')->with('success', 'Artikel FAQ berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $faq = Faq::findOrFail($id);
        $categories = FaqCategory::all();
        return view('admin.faq.edit', compact('faq', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq->update([
            'faq_category_id' => $request->faq_category_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.faq.articles.index')->with('success', 'Artikel FAQ berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();
        return redirect()->route('admin.faq.articles.index')->with('success', 'Artikel FAQ berhasil dihapus.');
    }
}
