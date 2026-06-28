<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <span class="np-kicker">{{ $publication->name }}</span>
                <h2 class="font-display text-3xl font-black text-ink leading-tight">
                    {{ __('The Team') }}
                </h2>
            </div>
            <a href="{{ route('publications.show', $publication) }}" class="np-btn-ghost">← Back to publication</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 px-4">
            @if(session('success'))
                <div class="mb-4 border-l-4 border-press-600 bg-press-50 text-ink px-4 py-3 font-semibold">{{ session('success') }}</div>
            @endif

            {{-- Invite --}}
            <div class="np-card mb-6">
                <div class="p-6">
                    <h3 class="font-display text-lg font-bold mb-1">Invite a team member</h3>
                    <p class="text-sm text-ink-soft mb-4">They'll get an email to join. Editors manage issues &amp; stories; contributors write stories; fact checkers review submissions.</p>
                    <form method="POST" action="{{ route('publications.invitations.store', $publication) }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div class="flex-1 min-w-[200px]">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="block mt-1 border-ink/25 bg-white text-ink focus:border-press-500 focus:ring-press-500 shadow-sm" required>
                                <option value="editor">Editor</option>
                                <option value="contributor">Contributor</option>
                                <option value="fact_checker">Fact checker</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                        <x-primary-button>Send invite</x-primary-button>
                    </form>
                </div>
            </div>

            {{-- Pending invitations --}}
            @if($invitations->isNotEmpty())
                <div class="np-card mb-6">
                    <div class="p-6">
                        <h3 class="font-display text-lg font-bold mb-4">Pending invitations</h3>
                        <div class="space-y-3">
                            @foreach($invitations as $invitation)
                                <div class="flex justify-between items-center border-b border-ink/10 pb-3">
                                    <div>
                                        <p class="font-semibold text-ink">{{ $invitation->email }}</p>
                                        <p class="text-sm text-ink-soft">
                                            <span class="np-badge-ink">{{ ucfirst(str_replace('_', ' ', $invitation->role)) }}</span>
                                            <span class="ml-1">expires {{ $invitation->expires_at?->format('M d, Y') }}</span>
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('publications.invitations.destroy', [$publication, $invitation]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-press-600 hover:text-press-700 font-semibold text-sm">Revoke</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Members --}}
            <div class="np-card">
                <div class="p-6">
                    <h3 class="font-display text-lg font-bold mb-4">Team members</h3>
                    @error('member')<p class="text-sm text-press-600 font-semibold mb-3">{{ $message }}</p>@enderror
                    <div class="space-y-3">
                        @foreach($members as $member)
                            <div class="flex justify-between items-center border-b border-ink/10 pb-3">
                                <div>
                                    <p class="font-semibold text-ink">{{ $member->name }}</p>
                                    <p class="text-sm text-ink-soft">{{ $member->email }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    @if($member->id === $publication->owner_id)
                                        <span class="np-badge-press">{{ ucfirst(str_replace('_', ' ', $member->pivot->role)) }}</span>
                                    @else
                                        <span class="np-badge-ink">{{ ucfirst(str_replace('_', ' ', $member->pivot->role)) }}</span>
                                        <form method="POST" action="{{ route('publications.members.destroy', [$publication, $member]) }}" onsubmit="return confirm('Remove this member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-press-600 hover:text-press-700 font-semibold text-sm">Remove</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
