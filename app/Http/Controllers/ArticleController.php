<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Models\NewsletterIssue;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Newsletter $newsletter, NewsletterIssue $issue)
    {
        $this->authorize('update', $newsletter);
        
        return view('newsletters.issues.articles.create', compact('newsletter', 'issue'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Newsletter $newsletter, NewsletterIssue $issue)
    {
        $this->authorize('update', $newsletter);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $article = $issue->articles()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'author_id' => Auth::id(),
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()->route('newsletters.issues.show', [$newsletter, $issue])
            ->with('success', 'Article added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Newsletter $newsletter, NewsletterIssue $issue, Article $article)
    {
        $this->authorize('update', $newsletter);
        
        return view('newsletters.issues.articles.edit', compact('newsletter', 'issue', 'article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Newsletter $newsletter, NewsletterIssue $issue, Article $article)
    {
        $this->authorize('update', $newsletter);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $article->update($validated);

        return redirect()->route('newsletters.issues.show', [$newsletter, $issue])
            ->with('success', 'Article updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsletter $newsletter, NewsletterIssue $issue, Article $article)
    {
        $this->authorize('update', $newsletter);

        $article->delete();

        return redirect()->route('newsletters.issues.show', [$newsletter, $issue])
            ->with('success', 'Article deleted successfully!');
    }
}
