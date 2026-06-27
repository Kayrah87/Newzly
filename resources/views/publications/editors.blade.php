<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $publication->name }} — Team
            </h2>
            <a href="{{ route('publications.show', $publication) }}" class="text-gray-600 hover:text-gray-900">← Back to publication</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            {{-- Invite --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-1">Invite a team member</h3>
                    <p class="text-sm text-gray-500 mb-4">They'll get an email to join. Editors manage issues &amp; stories; contributors write stories; fact checkers review submissions.</p>
                    <form method="POST" action="{{ route('publications.invitations.store', $publication) }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div class="flex-1 min-w-[200px]">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="block mt-1 border-gray-300 rounded-md shadow-sm" required>
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
                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Pending invitations</h3>
                        <div class="space-y-3">
                            @foreach($invitations as $invitation)
                                <div class="flex justify-between items-center border-b pb-3">
                                    <div>
                                        <p class="font-medium">{{ $invitation->email }}</p>
                                        <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $invitation->role)) }} · expires {{ $invitation->expires_at?->format('M d, Y') }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('publications.invitations.destroy', [$publication, $invitation]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Revoke</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Members --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Team members</h3>
                    @error('member')<p class="text-sm text-red-600 mb-3">{{ $message }}</p>@enderror
                    <div class="space-y-3">
                        @foreach($members as $member)
                            <div class="flex justify-between items-center border-b pb-3">
                                <div>
                                    <p class="font-medium">{{ $member->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $member->email }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $member->pivot->role)) }}</span>
                                    @if($member->id !== $publication->owner_id)
                                        <form method="POST" action="{{ route('publications.members.destroy', [$publication, $member]) }}" onsubmit="return confirm('Remove this member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
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
