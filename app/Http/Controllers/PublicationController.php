<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $publication->update($validated);

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
     * Show the form for managing team editors.
     */
    public function editors(Publication $publication)
    {
        $this->authorize('manageEditors', $publication);

        $editors = $publication->editors()->get();

        return view('publications.editors', compact('publication', 'editors'));
    }
}
