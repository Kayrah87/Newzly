<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriberController extends Controller
{
    /**
     * List the publication's mailing list.
     */
    public function index(Publication $publication)
    {
        $this->authorize('manageSubscribers', $publication);

        $subscribers = $publication->subscribers()->latest()->paginate(25);

        $counts = [
            'total' => $publication->subscribers()->count(),
            'confirmed' => $publication->subscribers()->where('status', Subscriber::STATUS_CONFIRMED)->count(),
            'pending' => $publication->subscribers()->where('status', Subscriber::STATUS_PENDING)->count(),
            'unsubscribed' => $publication->subscribers()->where('status', Subscriber::STATUS_UNSUBSCRIBED)->count(),
        ];

        return view('publications.subscribers.index', compact('publication', 'subscribers', 'counts'));
    }

    /**
     * Manually add a subscriber. The owner must attest that consent was given;
     * we record that attestation in the audit trail.
     */
    public function store(Request $request, Publication $publication)
    {
        $this->authorize('manageSubscribers', $publication);

        $validated = $request->validate([
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('subscribers')->where(fn ($q) => $q->where('publication_id', $publication->id)),
            ],
            'name' => 'nullable|string|max:255',
            'consent' => 'accepted',
        ]);

        $subscriber = $publication->subscribers()->create([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? null,
            'status' => Subscriber::STATUS_CONFIRMED,
            'consent_at' => now(),
            'consent_ip' => $request->ip(),
            'consent_source' => 'manual',
            'confirmed_at' => now(),
        ]);

        $subscriber->recordEvent('subscribed', [
            'source' => 'manual',
            'added_by' => $request->user()->id,
        ], $request->ip(), $request->userAgent());

        return redirect()->route('publications.subscribers.index', $publication)
            ->with('success', 'Subscriber added.');
    }

    /**
     * Mark a subscriber as unsubscribed (admin action).
     */
    public function unsubscribe(Request $request, Publication $publication, Subscriber $subscriber)
    {
        $this->authorize('manageSubscribers', $publication);

        $subscriber->unsubscribe($request->ip(), 'admin', $request->userAgent());

        return redirect()->route('publications.subscribers.index', $publication)
            ->with('success', 'Subscriber unsubscribed.');
    }

    /**
     * Permanently delete a subscriber and their audit trail (GDPR erasure).
     */
    public function destroy(Publication $publication, Subscriber $subscriber)
    {
        $this->authorize('manageSubscribers', $publication);

        $subscriber->delete();

        return redirect()->route('publications.subscribers.index', $publication)
            ->with('success', 'Subscriber permanently deleted.');
    }
}
