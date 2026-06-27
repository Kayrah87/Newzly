<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionConfirmation;
use App\Models\Publication;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PublicSubscriptionController extends Controller
{
    /**
     * Show the public subscribe form.
     */
    public function create(Publication $publication)
    {
        return view('public.subscribe', compact('publication'));
    }

    /**
     * Handle a subscription request (double opt-in).
     *
     * Always renders the same "check your email" page regardless of branch,
     * so the form can't be used to enumerate who is already subscribed.
     */
    public function store(Request $request, Publication $publication)
    {
        // Honeypot: a real user never fills this hidden field.
        if (filled($request->input('company_website'))) {
            return view('public.check-email', compact('publication'));
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'consent' => 'accepted',
        ]);

        $subscriber = $publication->subscribers()->firstWhere('email', $validated['email']);

        if (! $subscriber) {
            $subscriber = $publication->subscribers()->create([
                'email' => $validated['email'],
                'name' => $validated['name'] ?? null,
                'status' => Subscriber::STATUS_PENDING,
                'consent_source' => 'public_form',
                'consent_ip' => $request->ip(),
            ]);
            $subscriber->recordEvent('subscribed', ['source' => 'public_form'], $request->ip(), $request->userAgent());
            $this->sendConfirmation($subscriber);
        } elseif ($subscriber->status === Subscriber::STATUS_PENDING) {
            // Resend the confirmation for an unconfirmed signup.
            $subscriber->confirmation_token ??= Str::random(40);
            $subscriber->save();
            $subscriber->recordEvent('confirmation_resent', [], $request->ip(), $request->userAgent());
            $this->sendConfirmation($subscriber);
        } elseif ($subscriber->status === Subscriber::STATUS_UNSUBSCRIBED) {
            // Allow re-subscribing: back to pending with a fresh token.
            $subscriber->forceFill([
                'status' => Subscriber::STATUS_PENDING,
                'confirmation_token' => Str::random(40),
                'unsubscribed_at' => null,
                'consent_source' => 'public_form',
                'consent_ip' => $request->ip(),
            ])->save();
            $subscriber->recordEvent('resubscribed', ['source' => 'public_form'], $request->ip(), $request->userAgent());
            $this->sendConfirmation($subscriber);
        }
        // Already confirmed: do nothing (and don't reveal it).

        return view('public.check-email', compact('publication'));
    }

    /**
     * Confirm a subscription via the emailed token (completes double opt-in).
     */
    public function confirm(Publication $publication, string $token)
    {
        $subscriber = $publication->subscribers()
            ->where('confirmation_token', $token)
            ->where('status', Subscriber::STATUS_PENDING)
            ->first();

        if ($subscriber) {
            $subscriber->confirm(request()->ip(), request()->userAgent());
        }

        return view('public.confirmed', [
            'publication' => $publication,
            'confirmed' => (bool) $subscriber,
        ]);
    }

    /**
     * Landing page for the unsubscribe link in emails.
     */
    public function unsubscribeForm(Publication $publication, string $token)
    {
        $subscriber = $this->findByUnsubscribeToken($publication, $token);

        if ($subscriber->status === Subscriber::STATUS_UNSUBSCRIBED) {
            return view('public.unsubscribed', compact('publication'));
        }

        return view('public.unsubscribe', compact('publication', 'subscriber', 'token'));
    }

    /**
     * Perform the unsubscribe.
     */
    public function unsubscribe(Request $request, Publication $publication, string $token)
    {
        $subscriber = $this->findByUnsubscribeToken($publication, $token);

        if ($subscriber->status !== Subscriber::STATUS_UNSUBSCRIBED) {
            $subscriber->unsubscribe($request->ip(), 'self', $request->userAgent());
        }

        return view('public.unsubscribed', compact('publication'));
    }

    protected function findByUnsubscribeToken(Publication $publication, string $token): Subscriber
    {
        return $publication->subscribers()
            ->where('unsubscribe_token', $token)
            ->firstOrFail();
    }

    protected function sendConfirmation(Subscriber $subscriber): void
    {
        Mail::to($subscriber->email)->send(new SubscriptionConfirmation($subscriber));
    }
}
