<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Publication;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $validated = $this->validateStory($request);

        $story = $issue->stories()->create([
            'publication_id' => $publication->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'layout' => $validated['layout'],
            'source' => Story::SOURCE_ADMIN,
            'status' => Story::STATUS_APPROVED,
            'author_id' => Auth::id(),
            'order' => $validated['order'] ?? 0,
        ]);

        $this->storeImages($request, $story, $publication);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Story added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publication $publication, Issue $issue, Story $story)
    {
        $this->authorize('update', $publication);

        $story->load('images');

        return view('publications.issues.stories.edit', compact('publication', 'issue', 'story'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publication $publication, Issue $issue, Story $story)
    {
        $this->authorize('update', $publication);

        $validated = $this->validateStory($request, withStatus: true);

        $story->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'layout' => $validated['layout'],
            'status' => $validated['status'] ?? $story->status,
            'order' => $validated['order'] ?? 0,
        ]);

        // Remove any images the editor unchecked.
        foreach ($request->input('remove_images', []) as $imageId) {
            $image = $story->images()->whereKey($imageId)->first();
            if ($image) {
                Storage::disk(Publication::mediaDisk())->delete($image->path);
                $image->delete();
            }
        }

        $this->storeImages($request, $story, $publication);

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Story updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publication $publication, Issue $issue, Story $story)
    {
        $this->authorize('update', $publication);

        foreach ($story->images as $image) {
            Storage::disk(Publication::mediaDisk())->delete($image->path);
        }

        $story->delete();

        return redirect()->route('publications.issues.show', [$publication, $issue])
            ->with('success', 'Story deleted successfully!');
    }

    protected function validateStory(Request $request, bool $withStatus = false): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'layout' => 'required|in:'.implode(',', Story::LAYOUTS),
            'order' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'image|max:4096',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
        ];

        if ($withStatus) {
            $rules['status'] = 'required|in:'.implode(',', [
                Story::STATUS_PENDING, Story::STATUS_APPROVED, Story::STATUS_REJECTED,
            ]);
        }

        return $request->validate($rules);
    }

    protected function storeImages(Request $request, Story $story, Publication $publication): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $disk = Publication::mediaDisk();
        $order = (int) $story->images()->max('order');

        foreach ($request->file('images') as $file) {
            $order++;
            $story->images()->create([
                'publication_id' => $publication->id,
                'path' => $file->store("publications/{$publication->id}/stories/{$story->id}", $disk),
                'order' => $order,
            ]);
        }
    }
}
