<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $issue->title }}
            </h2>
            <div class="space-x-2">
                @can('update', $publication)
                    <a href="{{ route('publications.issues.edit', [$publication, $issue]) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                    <a href="{{ route('publications.issues.stories.create', [$publication, $issue]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                        Add Story
                    </a>
                @endcan
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
                <!-- Main Content -->
                <div class="md:col-span-2">
                    <!-- Issue Content -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Issue Content</h3>
                            @if($issue->content)
                                <div class="prose max-w-none">
                                    {!! $issue->content !!}
                                </div>
                            @else
                                <p class="text-gray-500">No content added yet.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Stories -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Stories</h3>
                            @if($issue->stories->count() > 0)
                                <div class="space-y-6">
                                    @foreach($issue->stories as $story)
                                        <div class="border-l-4 border-indigo-500 pl-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <h4 class="font-semibold text-lg">{{ $story->title }}</h4>
                                                @can('update', $publication)
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('publications.issues.stories.edit', [$publication, $issue, $story]) }}" class="text-gray-600 hover:text-gray-800 text-sm">Edit</a>
                                                        <form method="POST" action="{{ route('publications.issues.stories.destroy', [$publication, $issue, $story]) }}" class="inline" onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                                        </form>
                                                    </div>
                                                @endcan
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">By {{ $story->author->name ?? 'Unknown' }} • Order: {{ $story->order }}</p>
                                            <div class="prose max-w-none">
                                                {!! Str::limit($story->content, 300) !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No stories added yet. Add your first story to get started!</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Issue Details</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600 font-medium">Status:</span>
                                    <p class="capitalize px-2 py-1 rounded text-xs inline-block
                                        @if($issue->status === 'draft') bg-gray-200 text-gray-700
                                        @elseif($issue->status === 'scheduled') bg-blue-200 text-blue-700
                                        @else bg-green-200 text-green-700
                                        @endif">
                                        {{ $issue->status }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">Created:</span>
                                    <p class="text-gray-800">{{ $issue->created_at->format('M d, Y') }}</p>
                                </div>
                                @if($issue->published_at)
                                    <div>
                                        <span class="text-gray-600 font-medium">Published:</span>
                                        <p class="text-gray-800">{{ $issue->published_at->format('M d, Y H:i') }}</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-gray-600 font-medium">Stories:</span>
                                    <p class="text-gray-800">{{ $issue->stories->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="{{ route('publications.issues.index', $publication) }}" class="block text-indigo-600 hover:text-indigo-800">
                                    Back to Issues
                                </a>
                                <a href="{{ route('publications.show', $publication) }}" class="block text-indigo-600 hover:text-indigo-800">
                                    View Publication
                                </a>
                            </div>
                        </div>
                    </div>

                    @can('update', $publication)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4 text-red-600">Danger Zone</h3>
                                <form method="POST" action="{{ route('publications.issues.destroy', [$publication, $issue]) }}" onsubmit="return confirm('Are you sure you want to delete this issue? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">
                                        Delete Issue
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
