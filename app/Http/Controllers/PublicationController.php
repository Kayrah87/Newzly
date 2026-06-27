<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publications = Auth::user()->publications()
            ->latest()
            ->paginate(10);

        $ownedPublications = Auth::user()->ownedPublications()
            ->latest()
            ->get();

        return view('publications.index', compact('publications', 'ownedPublications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Publication::class);

        return view('publications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Publication::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $publication = Publication::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id' => Auth::id(),
        ]);

        // Record the owner as a team member.
        $publication->members()->attach(Auth::id(), ['role' => 'owner']);

        return redirect()->route('publications.show', $publication)
            ->with('success', 'Publication created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Publication $publication)
    {
        $this->authorize('view', $publication);

        $publication->load(['issues', 'owner', 'members']);

        return view('publications.show', compact('publication'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publication $publication)
    {
        $this->authorize('update', $publication);

        return view('publications.edit', compact('publication'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publication $publication)
    {
        $this->authorize('update', $publication);

        $allowedPlatforms = array_keys(Publication::SOCIAL_PLATFORMS);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url|max:255',
            'from_name' => 'nullable|string|max:255',
            'from_email' => 'nullable|email|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'social_links' => 'nullable|array',
            'social_links.*' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'remove_logo' => 'nullable|boolean',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl',
        ]);

        // Keep only known platforms with a non-empty URL.
        $social = collect($validated['social_links'] ?? [])
            ->only($allowedPlatforms)
            ->filter()
            ->all();

        $publication->fill([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'website_url' => $validated['website_url'] ?? null,
            'from_name' => $validated['from_name'] ?? null,
            'from_email' => $validated['from_email'] ?? null,
            'reply_to_email' => $validated['reply_to_email'] ?? null,
            'social_links' => $social ?: null,
            'smtp_host' => $validated['smtp_host'] ?? null,
            'smtp_port' => $validated['smtp_port'] ?? null,
            'smtp_username' => $validated['smtp_username'] ?? null,
            'smtp_encryption' => $validated['smtp_encryption'] ?? null,
        ]);

        // Only overwrite the stored SMTP password when a new one is entered.
        if (filled($validated['smtp_password'] ?? null)) {
            $publication->smtp_password = $validated['smtp_password'];
        }

        $disk = Publication::mediaDisk();

        if ($request->boolean('remove_logo') && $publication->logo_path) {
            Storage::disk($disk)->delete($publication->logo_path);
            $publication->logo_path = null;
        }

        if ($request->hasFile('logo')) {
            if ($publication->logo_path) {
                Storage::disk($disk)->delete($publication->logo_path);
            }
            $publication->logo_path = $request->file('logo')->store(
                "publications/{$publication->id}/logo",
                $disk
            );
        }

        $publication->save();

        return redirect()->route('publications.show', $publication)
            ->with('success', 'Publication updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publication $publication)
    {
        $this->authorize('delete', $publication);

        $publication->delete();

        return redirect()->route('publications.index')
            ->with('success', 'Publication deleted successfully!');
    }

    /**
     * Manage the publication's team (members + pending invitations).
     */
    public function editors(Publication $publication)
    {
        $this->authorize('manageEditors', $publication);

        $members = $publication->members()->orderBy('name')->get();
        $invitations = $publication->invitations()->pending()->latest()->get();

        return view('publications.editors', compact('publication', 'members', 'invitations'));
    }

    /**
     * Remove a member from the publication's team.
     */
    public function removeMember(Publication $publication, User $user)
    {
        $this->authorize('manageEditors', $publication);

        if ($publication->owner_id === $user->id) {
            return back()->withErrors(['member' => 'You cannot remove the owner.']);
        }

        $publication->members()->detach($user->id);

        return redirect()->route('publications.editors', $publication)
            ->with('success', 'Team member removed.');
    }
}
