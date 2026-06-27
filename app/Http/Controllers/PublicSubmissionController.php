<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Story;
use Illuminate\Http\Request;

class PublicSubmissionController extends Controller
{
    /**
     * Show the public story submission form.
     */
    public function create(Publication $publication)
    {
        return view('public.submit', compact('publication'));
    }

    /**
     * Accept a public story/photo submission. It lands as a pending story
     * for an editor to review (Phase 6 moderation queue).
     */
    public function store(Request $request, Publication $publication)
    {
        // Honeypot: silently accept (but drop) bot submissions.
        if (filled($request->input('company_website'))) {
            return view('public.submitted', compact('publication'));
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'submitter_name' => 'nullable|string|max:255',
            'submitter_email' => 'nullable|email|max:255',
            'consent' => 'accepted',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:4096',
        ]);

        $story = $publication->stories()->create([
            'issue_id' => null,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'layout' => 'standard',
            'source' => Story::SOURCE_PUBLIC,
            'status' => Story::STATUS_PENDING,
            'submitter_name' => $validated['submitter_name'] ?? null,
            'submitter_email' => $validated['submitter_email'] ?? null,
            'order' => 0,
        ]);

        if ($request->hasFile('images')) {
            $disk = Publication::mediaDisk();
            $order = 0;
            foreach ($request->file('images') as $file) {
                $order++;
                $story->images()->create([
                    'publication_id' => $publication->id,
                    'path' => $file->store("publications/{$publication->id}/submissions/{$story->id}", $disk),
                    'order' => $order,
                ]);
            }
        }

        return view('public.submitted', compact('publication'));
    }
}
