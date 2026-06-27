<?php

namespace App\Http\Controllers;

use App\Jobs\SendIssue;
use App\Models\Issue;
use App\Models\Publication;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Publication $publication)
    {
        $this->authorize('view', $publication);

        $issues = $publication->issues()->latest()->paginate(10);

        return view('publications.issues.index', compact('publication', 'issues'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Publication $publication)
    {
        $this->authorize('update', $publication);

        return view('publications.issues.create', compact('publication'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Publication $publication)
    {
        $this->authorize('update', $publication);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,scheduled,sent',
            'published_at' => 'nullable|date',
        ]);

        $issue = $publication->issues()->create($validated);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Issue created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Publication $publication, Issue $issue)
    {
        $this->authorize('view', $publication);

        $issue->load('stories.author');

        return view('publications.issues.show', compact('publication', 'issue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publication $publication, Issue $issue)
    {
        $this->authorize('update', $publication);

        $issue->load('stories');

        return view('publications.issues.edit', compact('publication', 'issue'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publication $publication, Issue $issue)
    {
        $this->authorize('update', $publication);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,scheduled,sent',
            'published_at' => 'nullable|date',
        ]);

        $issue->update($validated);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Issue updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publication $publication, Issue $issue)
    {
        $this->authorize('update', $publication);

        $issue->delete();

        return redirect()->route('publications.issues.index', $publication)
            ->with('success', 'Issue deleted successfully!');
    }

    /**
     * Send this issue to the publication's confirmed subscribers.
     */
    public function send(Publication $publication, Issue $issue)
    {
        $this->authorize('update', $publication);

        if ($issue->isSent()) {
            return redirect()->route('publications.issues.show', [$publication, $issue])
                ->with('error', 'This issue has already been sent.');
        }

        SendIssue::dispatch($issue);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Issue queued for sending to confirmed subscribers.');
    }
}
