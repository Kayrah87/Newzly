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
    public function index(Request $request, Publication $publication)
    {
        $this->authorize('view', $publication);

        $sortable = ['title', 'status', 'issue_number', 'release_date', 'created_at'];

        $sort = in_array($request->query('sort'), $sortable, true) ? $request->query('sort') : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
        $search = trim((string) $request->query('search', ''));

        $issues = $publication->issues()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('coverage_label', 'like', "%{$search}%")
                        ->orWhere('issue_number', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('publications.issues.index', compact('publication', 'issues', 'sort', 'direction', 'search'));
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

    /**
     * Schedule this issue to send automatically at a future time. The
     * issues:send-scheduled command dispatches it once that time arrives.
     */
    public function schedule(Request $request, Publication $publication, Issue $issue)
    {
        $this->authorize('update', $publication);

        if ($issue->isSent()) {
            return redirect()->route('publications.issues.show', [$publication, $issue])
                ->with('error', 'This issue has already been sent.');
        }

        $validated = $request->validate([
            'published_at' => 'required|date|after:now',
        ]);

        $issue->update([
            'status' => 'scheduled',
            'published_at' => $validated['published_at'],
        ]);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Issue scheduled to send on '.$issue->published_at->format('M j, Y g:i A').'.');
    }

    /**
     * Cancel a pending schedule and return the issue to draft.
     */
    public function unschedule(Publication $publication, Issue $issue)
    {
        $this->authorize('update', $publication);

        if ($issue->isSent()) {
            return redirect()->route('publications.issues.show', [$publication, $issue])
                ->with('error', 'This issue has already been sent.');
        }

        $issue->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Schedule cancelled — the issue is back to draft.');
    }
}
