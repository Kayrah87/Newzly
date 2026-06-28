<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <span class="np-kicker">{{ $publication->name }}</span>
                <h2 class="font-display text-3xl font-black text-ink leading-tight">
                    Subscribers
                </h2>
            </div>
            <a href="{{ route('publications.show', $publication) }}" class="text-press-600 hover:text-press-700 font-semibold">← Back to publication</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            @if(session('success'))
                <div class="mb-4 border-l-4 border-press-600 bg-press-50 text-ink px-4 py-3 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Counts --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="np-card border-t-4 border-t-ink p-4">
                    <div class="np-kicker">Total</div>
                    <div class="mt-1 font-display text-3xl font-black text-ink">{{ $counts['total'] }}</div>
                </div>
                <div class="np-card border-t-4 border-t-press-600 p-4">
                    <div class="np-kicker">Confirmed</div>
                    <div class="mt-1 font-display text-3xl font-black text-press-600">{{ $counts['confirmed'] }}</div>
                </div>
                <div class="np-card border-t-4 border-t-ink p-4">
                    <div class="np-kicker">Pending</div>
                    <div class="mt-1 font-display text-3xl font-black text-ink">{{ $counts['pending'] }}</div>
                </div>
                <div class="np-card border-t-4 border-t-ink p-4">
                    <div class="np-kicker">Unsubscribed</div>
                    <div class="mt-1 font-display text-3xl font-black text-ink-soft">{{ $counts['unsubscribed'] }}</div>
                </div>
            </div>

            {{-- Shareable public subscribe link --}}
            <div class="np-card mb-6">
                <div class="p-6">
                    <h3 class="font-display text-lg font-bold mb-2">Public subscribe link</h3>
                    <p class="text-sm text-ink-soft mb-2">Share this link so people can opt in (double opt-in confirmation is sent automatically).</p>
                    <input type="text" readonly value="{{ route('public.subscribe', ['publication' => $publication->slug]) }}"
                        onclick="this.select()"
                        class="np-input text-sm">
                </div>
            </div>

            {{-- Manual add --}}
            <div class="np-card mb-6">
                <div class="p-6">
                    <h3 class="font-display text-lg font-bold mb-4">Add a subscriber</h3>
                    <form method="POST" action="{{ route('publications.subscribers.store', $publication) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="name" :value="__('Name (optional)')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>
                        <label class="flex items-start gap-2 text-sm text-ink-soft">
                            <input type="checkbox" name="consent" value="1" class="mt-1 rounded border-ink/30 text-press-600 focus:ring-press-500" required>
                            <span>I confirm this person has given consent to receive this publication. This attestation is recorded for GDPR compliance.</span>
                        </label>
                        <x-input-error :messages="$errors->get('consent')" class="mt-2" />
                        <x-primary-button>{{ __('Add subscriber') }}</x-primary-button>
                    </form>
                </div>
            </div>

            {{-- List --}}
            <div class="np-card">
                <div class="p-6">
                    @if($subscribers->count() > 0)
                        <table class="min-w-full">
                            <thead>
                                <tr class="text-left text-xs font-bold text-ink-soft uppercase tracking-wider border-b-2 border-ink">
                                    <th class="py-2">Email</th>
                                    <th class="py-2">Name</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Consent</th>
                                    <th class="py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ink/10">
                                @foreach($subscribers as $subscriber)
                                    <tr>
                                        <td class="py-3 text-ink">{{ $subscriber->email }}</td>
                                        <td class="py-3 text-ink">{{ $subscriber->name ?? '—' }}</td>
                                        <td class="py-3">
                                            @php
                                                $badgeClass = $subscriber->status === 'confirmed' ? 'np-badge-press' : 'np-badge-ink';
                                            @endphp
                                            <span class="{{ $badgeClass }}">{{ ucfirst($subscriber->status) }}</span>
                                        </td>
                                        <td class="py-3 text-sm text-ink-soft">
                                            {{ $subscriber->consent_at?->format('M d, Y') ?? '—' }}
                                            @if($subscriber->consent_source)
                                                <span class="text-ink-soft/70">({{ $subscriber->consent_source }})</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right space-x-3">
                                            @if($subscriber->status !== 'unsubscribed')
                                                <form method="POST" action="{{ route('publications.subscribers.unsubscribe', [$publication, $subscriber]) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-ink-soft hover:text-ink font-semibold text-sm">Unsubscribe</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('publications.subscribers.destroy', [$publication, $subscriber]) }}" class="inline" onsubmit="return confirm('Permanently delete this subscriber and their audit trail?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-press-600 hover:text-press-700 font-semibold text-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $subscribers->links() }}</div>
                    @else
                        <div class="border border-dashed border-ink/15 px-4 py-8 text-center text-ink-soft">No subscribers yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
