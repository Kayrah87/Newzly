<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Story;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * List pending public submissions for moderation.
     */
    public function index(Publication $publication)
    {
        $this->authorize('moderateSubmissions', $publication);

        $submissions = $publication->stories()
            ->where('source', Story::SOURCE_PUBLIC)
            ->where('status', Story::STATUS_PENDING)
            ->with('images')
            ->latest()
            ->paginate(20);

        $issues = $publication->issues()->latest()->get();

        return view('publications.submissions.index', compact('publication', 'submissions', 'issues'));
    }

    /**
     * Approve a submission and assign it to an issue.
     */
    public function approve(Request $request, Publication $publication, Story $story)
    {
        $this->authorize('moderateSubmissions', $publication);

        $validated = $request->validate([
            'issue_id' => 'required|integer|exists:issues,id',
        ]);

        // Ensure the chosen issue belongs to this publication (tenant safety).
        $issue = $publication->issues()->findOrFail($validated['issue_id']);

        $story->update([
            'issue_id' => $issue->id,
            'status' => Story::STATUS_APPROVED,
        ]);

        return redirect()->route('publications.submissions.index', $publication)
            ->with('success', 'Submission approved and added to the issue.');
    }

    /**
     * Reject a submission.
     */
    public function reject(Publication $publication, Story $story)
    {
        $this->authorize('moderateSubmissions', $publication);

        $story->update(['status' => Story::STATUS_REJECTED]);

        return redirect()->route('publications.submissions.index', $publication)
            ->with('success', 'Submission rejected.');
    }
}
