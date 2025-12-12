<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $newsletters = Auth::user()->newsletters()
            ->latest()
            ->paginate(10);
        
        $ownedNewsletters = Auth::user()->ownedNewsletters()
            ->latest()
            ->get();

        return view('newsletters.index', compact('newsletters', 'ownedNewsletters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Newsletter::class);
        return view('newsletters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Newsletter::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $newsletter = Newsletter::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'owner_id' => Auth::id(),
        ]);

        // Add owner to newsletter_users table
        $newsletter->users()->attach(Auth::id(), ['role' => 'owner']);

        return redirect()->route('newsletters.show', $newsletter)
            ->with('success', 'Newsletter created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Newsletter $newsletter)
    {
        $this->authorize('view', $newsletter);
        
        $newsletter->load(['issues', 'owner', 'users']);
        
        return view('newsletters.show', compact('newsletter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Newsletter $newsletter)
    {
        $this->authorize('update', $newsletter);
        return view('newsletters.edit', compact('newsletter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Newsletter $newsletter)
    {
        $this->authorize('update', $newsletter);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $newsletter->update($validated);

        return redirect()->route('newsletters.show', $newsletter)
            ->with('success', 'Newsletter updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsletter $newsletter)
    {
        $this->authorize('delete', $newsletter);

        $newsletter->delete();

        return redirect()->route('newsletters.index')
            ->with('success', 'Newsletter deleted successfully!');
    }

    /**
     * Show the form for managing editors
     */
    public function editors(Newsletter $newsletter)
    {
        $this->authorize('manageEditors', $newsletter);
        
        $editors = $newsletter->editors()->get();
        
        return view('newsletters.editors', compact('newsletter', 'editors'));
    }

    /**
     * Show the form for managing recipients
     */
    public function recipients(Newsletter $newsletter)
    {
        $this->authorize('manageRecipients', $newsletter);
        
        $recipients = $newsletter->recipients()->get();
        
        return view('newsletters.recipients', compact('newsletter', 'recipients'));
    }
}
