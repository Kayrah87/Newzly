<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Publication;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Publication $publication, Issue $issue)
    {
        $this->authorize('update', $publication);

        return view('publications.issues.stories.create', compact('publication', 'issue'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Publication $publication, Issue $issue)
    {
        $this->authorize('update', $publication);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $issue->stories()->create([
            'publication_id' => $publication->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'author_id' => Auth::id(),
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Story added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publication $publication, Issue $issue, Story $story)
    {
        $this->authorize('update', $publication);

        return view('publications.issues.stories.edit', compact('publication', 'issue', 'story'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publication $publication, Issue $issue, Story $story)
    {
        $this->authorize('update', $publication);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $story->update($validated);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Story updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publication $publication, Issue $issue, Story $story)
    {
        $this->authorize('update', $publication);

        $story->delete();

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Story deleted successfully!');
    }
}
