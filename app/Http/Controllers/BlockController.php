<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Issue;
use App\Models\Publication;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    /**
     * Show the form for creating a new block of the given type.
     */
    public function create(Request $request, Publication $publication, Issue $issue)
    {
        $this->authorize('manageStories', $publication);

        $type = $request->query('type', Block::TYPE_EVENTS);
        abort_unless(in_array($type, Block::TYPES, true), 404);

        return view('publications.issues.blocks.create', compact('publication', 'issue', 'type'));
    }

    /**
     * Store a newly created block (placed at the end of the content stream).
     */
    public function store(Request $request, Publication $publication, Issue $issue)
    {
        $this->authorize('manageStories', $publication);

        // Default to the events block when no type is supplied.
        $request->merge(['type' => $request->input('type', Block::TYPE_EVENTS)]);
        $type = $request->validate([
            'type' => 'required|in:'.implode(',', Block::TYPES),
        ])['type'];

        $validated = $this->validateBlock($request);

        $block = $issue->blocks()->create([
            'publication_id' => $publication->id,
            'type' => $type,
            'title' => $validated['title'] ?? null,
            'title_style' => $validated['title_style'],
            'intro' => $validated['intro'] ?? null,
            'order' => $this->nextOrder($issue),
        ]);

        $this->syncEvents($block, $request);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Events block added.');
    }

    /**
     * Show the form for editing the block.
     */
    public function edit(Publication $publication, Issue $issue, Block $block)
    {
        $this->authorize('manageStories', $publication);

        $block->load('events');

        return view('publications.issues.blocks.edit', compact('publication', 'issue', 'block'));
    }

    /**
     * Update the block and its events.
     */
    public function update(Request $request, Publication $publication, Issue $issue, Block $block)
    {
        $this->authorize('manageStories', $publication);

        $validated = $this->validateBlock($request);

        $block->update([
            'title' => $validated['title'] ?? null,
            'title_style' => $validated['title_style'],
            'intro' => $validated['intro'] ?? null,
        ]);

        $this->syncEvents($block, $request);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Events block updated.');
    }

    /**
     * Remove the block (its events cascade).
     */
    public function destroy(Publication $publication, Issue $issue, Block $block)
    {
        $this->authorize('manageStories', $publication);

        $block->delete();

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Events block removed.');
    }

    /**
     * @return array{title: ?string, title_style: string, intro: ?string}
     */
    protected function validateBlock(Request $request): array
    {
        return $request->validate([
            'title' => 'nullable|string|max:255',
            'title_style' => 'required|in:'.implode(',', array_keys(Block::TITLE_STYLES)),
            'intro' => 'nullable|string|max:2000',
            'events' => 'nullable|array',
            'events.*.name' => 'nullable|string|max:255',
            'events.*.date' => 'nullable|date',
            'events.*.location' => 'nullable|string|max:255',
            'events.*.description' => 'nullable|string|max:1000',
        ]);
    }

    /**
     * Replace the block's events from the submitted rows, dropping blank ones
     * (a row with no name) and numbering the rest in submitted order.
     */
    protected function syncEvents(Block $block, Request $request): void
    {
        $block->events()->delete();

        $order = 0;
        foreach ($request->input('events', []) as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $order++;
            $block->events()->create([
                'name' => $name,
                'date' => filled($row['date'] ?? null) ? $row['date'] : null,
                'location' => filled($row['location'] ?? null) ? $row['location'] : null,
                'description' => filled($row['description'] ?? null) ? $row['description'] : null,
                'order' => $order,
            ]);
        }
    }

    /**
     * The next free position at the end of the issue's content stream
     * (stories and blocks share one `order` space).
     */
    protected function nextOrder(Issue $issue): int
    {
        return max((int) $issue->stories()->max('order'), (int) $issue->blocks()->max('order')) + 1;
    }
}
