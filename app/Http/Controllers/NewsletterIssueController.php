<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Models\NewsletterIssue;
use Illuminate\Http\Request;

class NewsletterIssueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Newsletter $newsletter)
    {
        $this->authorize('view', $newsletter);
        
        $issues = $newsletter->issues()->latest()->paginate(10);
        
        return view('newsletters.issues.index', compact('newsletter', 'issues'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Newsletter $newsletter)
    {
        $this->authorize('update', $newsletter);
        
        return view('newsletters.issues.create', compact('newsletter'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Newsletter $newsletter)
    {
        $this->authorize('update', $newsletter);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,scheduled,sent',
            'published_at' => 'nullable|date',
        ]);

        $issue = $newsletter->issues()->create($validated);

        return redirect()->route('newsletters.issues.show', [$newsletter, $issue])
            ->with('success', 'Issue created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Newsletter $newsletter, NewsletterIssue $issue)
    {
        $this->authorize('view', $newsletter);
        
        $issue->load('articles.author');
        
        return view('newsletters.issues.show', compact('newsletter', 'issue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Newsletter $newsletter, NewsletterIssue $issue)
    {
        $this->authorize('update', $newsletter);
        
        $issue->load('articles');
        
        return view('newsletters.issues.edit', compact('newsletter', 'issue'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Newsletter $newsletter, NewsletterIssue $issue)
    {
        $this->authorize('update', $newsletter);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,scheduled,sent',
            'published_at' => 'nullable|date',
        ]);

        $issue->update($validated);

        return redirect()->route('newsletters.issues.show', [$newsletter, $issue])
            ->with('success', 'Issue updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsletter $newsletter, NewsletterIssue $issue)
    {
        $this->authorize('update', $newsletter);

        $issue->delete();

        return redirect()->route('newsletters.issues.index', $newsletter)
            ->with('success', 'Issue deleted successfully!');
    }
}
