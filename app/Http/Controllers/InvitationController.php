<?php

namespace App\Http\Controllers;

use App\Mail\TeamInvitation;
use App\Models\Invitation;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
    /**
     * Send an invitation to join a publication's team.
     */
    public function store(Request $request, Publication $publication)
    {
        $this->authorize('manageEditors', $publication);

        $validated = $request->validate([
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('invitations')->where(fn ($q) => $q->where('publication_id', $publication->id)),
            ],
            'role' => ['required', Rule::in(Invitation::ROLES)],
        ]);

        // Don't invite someone who's already a member.
        $alreadyMember = $publication->members()
            ->where('users.email', $validated['email'])
            ->exists();

        if ($alreadyMember) {
            return back()->withErrors(['email' => 'That person is already on the team.']);
        }

        $invitation = $publication->invitations()->create([
            'email' => $validated['email'],
            'role' => $validated['role'],
            'invited_by' => $request->user()->id,
        ]);

        Mail::to($invitation->email)->send(new TeamInvitation($invitation));

        return redirect()->route('publications.editors', $publication)
            ->with('success', 'Invitation sent to '.$invitation->email.'.');
    }

    /**
     * Revoke a pending invitation.
     */
    public function destroy(Publication $publication, Invitation $invitation)
    {
        $this->authorize('manageEditors', $publication);

        $invitation->delete();

        return redirect()->route('publications.editors', $publication)
            ->with('success', 'Invitation revoked.');
    }

    /**
     * Show an invitation to the recipient (public landing for the email link).
     */
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->first();

        if (! $invitation || ! $invitation->isPending()) {
            return view('invitations.invalid');
        }

        return view('invitations.show', [
            'invitation' => $invitation,
            'publication' => $invitation->publication,
        ]);
    }

    /**
     * Accept an invitation (must be logged in as the invited email).
     */
    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->first();

        if (! $invitation || ! $invitation->isPending()) {
            return view('invitations.invalid');
        }

        if ($request->user()->email !== $invitation->email) {
            return redirect()->route('invitations.show', $token)
                ->with('error', 'This invitation is for '.$invitation->email.'. Please sign in with that email to accept.');
        }

        $invitation->publication->members()->syncWithoutDetaching([
            $request->user()->id => ['role' => $invitation->role],
        ]);

        $invitation->forceFill(['accepted_at' => now()])->save();

        return redirect()->route('publications.show', $invitation->publication)
            ->with('success', 'You have joined '.$invitation->publication->name.'.');
    }
}
