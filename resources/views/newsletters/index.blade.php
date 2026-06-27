<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Newsletters') }}
            </h2>
            <a href="{{ route('newsletters.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                Create Newsletter
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Owned Newsletters -->
            @if($ownedNewsletters->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Newsletters You Own</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($ownedNewsletters as $newsletter)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <h4 class="font-semibold text-lg mb-2">{{ $newsletter->name }}</h4>
                                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($newsletter->description, 100) }}</p>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('newsletters.show', $newsletter) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">View</a>
                                        <a href="{{ route('newsletters.edit', $newsletter) }}" class="text-gray-600 hover:text-gray-800 text-sm">Edit</a>
                                        <a href="{{ route('newsletters.issues.index', $newsletter) }}" class="text-green-600 hover:text-green-800 text-sm">Issues</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- All Newsletters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">All My Newsletters</h3>
                    @if($newsletters->count() > 0)
                        <div class="space-y-4">
                            @foreach($newsletters as $newsletter)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-lg">{{ $newsletter->name }}</h4>
                                            <p class="text-gray-600 text-sm">{{ $newsletter->description }}</p>
                                            <p class="text-xs text-gray-500 mt-2">
                                                Owner: {{ $newsletter->owner->name }} | 
                                                Role: {{ $newsletter->users()->where('user_id', auth()->id())->first()?->pivot->role ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('newsletters.show', $newsletter) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">View</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $newsletters->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">You don't have any newsletters yet. Create one to get started!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
