<?php

namespace App\Http\Controllers;

use App\Jobs\SendIssue;
use App\Models\Issue;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'issue_number' => 'nullable|integer|min:0',
            'coverage_label' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
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

        $issue->load('stories.author', 'stories.images', 'blocks.events');
        $items = $issue->orderedContent();

        return view('publications.issues.show', compact('publication', 'issue', 'items'));
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
            'issue_number' => 'nullable|integer|min:0',
            'coverage_label' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
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
     * Render a browser preview of the issue exactly as it will be emailed:
     * the publication's header/content/footer order and palette applied to the
     * issue's stories (all stories, ordered, so drafts are previewable too).
     */
    public function preview(Publication $publication, Issue $issue)
    {
        $this->authorize('view', $publication);

        return view('emails.issue', [
            'issue' => $issue,
            'publication' => $publication,
            'items' => $issue->orderedContent(),
            'structure' => $publication->structureOrder(),
            'palette' => $publication->paletteColors(),
            'unsubscribeUrl' => '#',
        ]);
    }

    /**
     * Persist a new drag-and-drop order for the issue's content stream.
     * Accepts { order: ["story:ID", "block:ID", …] } so stories and blocks can
     * be interleaved. Tokens that don't belong to this issue are ignored.
     */
    public function reorder(Request $request, Publication $publication, Issue $issue)
    {
        $this->authorize('manageStories', $publication);

        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => ['string', 'regex:/^(story|block):\d+$/'],
        ]);

        $storyIds = $issue->stories()->pluck('id')->all();
        $blockIds = $issue->blocks()->pluck('id')->all();

        DB::transaction(function () use ($validated, $issue, $storyIds, $blockIds) {
            $position = 0;
            foreach ($validated['order'] as $token) {
                [$kind, $id] = explode(':', $token);
                $id = (int) $id;

                if ($kind === 'story' && in_array($id, $storyIds, true)) {
                    $position++;
                    $issue->stories()->whereKey($id)->update(['order' => $position]);
                } elseif ($kind === 'block' && in_array($id, $blockIds, true)) {
                    $position++;
                    $issue->blocks()->whereKey($id)->update(['order' => $position]);
                }
            }
        });

        return response()->json(['status' => 'ok']);
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
