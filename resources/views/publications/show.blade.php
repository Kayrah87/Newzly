<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $publication->name }}
            </h2>
            <div class="space-x-2">
                @can('update', $publication)
                    <a href="{{ route('publications.edit', $publication) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                @endcan
                <a href="{{ route('publications.issues.index', $publication) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                    View Issues
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Publication Info -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex items-start gap-4 mb-4">
                                @if($publication->logoUrl())
                                    <img src="{{ $publication->logoUrl() }}" alt="{{ $publication->name }} logo" class="h-16 w-16 object-contain rounded border">
                                @endif
                                <h3 class="text-lg font-semibold pt-1">Publication Information</h3>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600 font-medium">Description:</span>
                                    <p class="text-gray-800">{{ $publication->description ?? 'No description provided' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">Owner:</span>
                                    <p class="text-gray-800">{{ $publication->owner->name }}</p>
                                </div>
                                @if($publication->website_url)
                                    <div>
                                        <span class="text-gray-600 font-medium">Website:</span>
                                        <a href="{{ $publication->website_url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-800 break-all">{{ $publication->website_url }}</a>
                                    </div>
                                @endif
                                @if(!empty($publication->social_links))
                                    <div>
                                        <span class="text-gray-600 font-medium">Social:</span>
                                        <span class="space-x-3">
                                            @foreach($publication->social_links as $platform => $url)
                                                <a href="{{ $url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-800">{{ \App\Models\Publication::SOCIAL_PLATFORMS[$platform] ?? ucfirst($platform) }}</a>
                                            @endforeach
                                        </span>
                                    </div>
                                @endif
                                @if($publication->from_email)
                                    <div>
                                        <span class="text-gray-600 font-medium">Sends as:</span>
                                        <p class="text-gray-800">{{ $publication->from_name ?: $publication->name }} &lt;{{ $publication->from_email }}&gt;</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-gray-600 font-medium">Created:</span>
                                    <p class="text-gray-800">{{ $publication->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Issues -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Recent Issues</h3>
                                @can('update', $publication)
                                    <a href="{{ route('publications.issues.create', $publication) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Create Issue</a>
                                @endcan
                            </div>
                            @if($publication->issues->count() > 0)
                                <div class="space-y-3">
                                    @foreach($publication->issues->take(5) as $issue)
                                        <div class="border-l-4 border-indigo-500 pl-4 py-2">
                                            <a href="{{ route('publications.issues.show', [$publication, $issue]) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                                {{ $issue->title }}
                                            </a>
                                            <p class="text-sm text-gray-500">{{ $issue->status }} • {{ $issue->created_at->format('M d, Y') }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No issues created yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Quick Stats</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Issues:</span>
                                    <span class="font-semibold">{{ $publication->issues->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Editors:</span>
                                    <span class="font-semibold">{{ $publication->editors()->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subscribers:</span>
                                    <span class="font-semibold">{{ $publication->subscribers()->where('status', 'confirmed')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @can('manageSubscribers', $publication)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Mailing List</h3>
                                <a href="{{ route('publications.subscribers.index', $publication) }}" class="block text-indigo-600 hover:text-indigo-800">
                                    Manage Subscribers
                                </a>
                            </div>
                        </div>
                    @endcan

                    @can('manageEditors', $publication)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Manage Team</h3>
                                <div class="space-y-2">
                                    <a href="{{ route('publications.editors', $publication) }}" class="block text-indigo-600 hover:text-indigo-800">
                                        Manage Editors
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan

                    @can('delete', $publication)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4 text-red-600">Danger Zone</h3>
                                <form method="POST" action="{{ route('publications.destroy', $publication) }}" onsubmit="return confirm('Are you sure you want to delete this publication? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">
                                        Delete Publication
                                    </x-danger-button>
                                </form>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
