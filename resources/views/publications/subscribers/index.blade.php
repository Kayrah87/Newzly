<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $publication->name }} — Subscribers
            </h2>
            <a href="{{ route('publications.show', $publication) }}" class="text-gray-600 hover:text-gray-900">← Back to publication</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Counts --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-sm text-gray-500">Total</div>
                    <div class="text-2xl font-bold">{{ $counts['total'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-sm text-gray-500">Confirmed</div>
                    <div class="text-2xl font-bold text-green-600">{{ $counts['confirmed'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-sm text-gray-500">Pending</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $counts['pending'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-sm text-gray-500">Unsubscribed</div>
                    <div class="text-2xl font-bold text-gray-400">{{ $counts['unsubscribed'] }}</div>
                </div>
            </div>

            {{-- Shareable public subscribe link --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-2">Public subscribe link</h3>
                    <p class="text-sm text-gray-500 mb-2">Share this link so people can opt in (double opt-in confirmation is sent automatically).</p>
                    <input type="text" readonly value="{{ route('public.subscribe', ['publication' => $publication->slug]) }}"
                        onclick="this.select()"
                        class="block w-full bg-gray-50 border-gray-300 rounded-md text-sm">
                </div>
            </div>

            {{-- Manual add --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Add a subscriber</h3>
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
                        <label class="flex items-start gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="consent" value="1" class="mt-1 rounded border-gray-300 text-indigo-600" required>
                            <span>I confirm this person has given consent to receive this publication. This attestation is recorded for GDPR compliance.</span>
                        </label>
                        <x-input-error :messages="$errors->get('consent')" class="mt-2" />
                        <x-primary-button>{{ __('Add subscriber') }}</x-primary-button>
                    </form>
                </div>
            </div>

            {{-- List --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($subscribers->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                                    <th class="py-2">Email</th>
                                    <th class="py-2">Name</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Consent</th>
                                    <th class="py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($subscribers as $subscriber)
                                    <tr>
                                        <td class="py-3">{{ $subscriber->email }}</td>
                                        <td class="py-3">{{ $subscriber->name ?? '—' }}</td>
                                        <td class="py-3">
                                            @php
                                                $badge = match($subscriber->status) {
                                                    'confirmed' => 'bg-green-100 text-green-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    default => 'bg-gray-100 text-gray-600',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $badge }}">{{ ucfirst($subscriber->status) }}</span>
                                        </td>
                                        <td class="py-3 text-sm text-gray-500">
                                            {{ $subscriber->consent_at?->format('M d, Y') ?? '—' }}
                                            @if($subscriber->consent_source)
                                                <span class="text-gray-400">({{ $subscriber->consent_source }})</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right space-x-3">
                                            @if($subscriber->status !== 'unsubscribed')
                                                <form method="POST" action="{{ route('publications.subscribers.unsubscribe', [$publication, $subscriber]) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-sm">Unsubscribe</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('publications.subscribers.destroy', [$publication, $subscriber]) }}" class="inline" onsubmit="return confirm('Permanently delete this subscriber and their audit trail?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $subscribers->links() }}</div>
                    @else
                        <p class="text-gray-500">No subscribers yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
