<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $publication->name }} - Issues
            </h2>
            @can('update', $publication)
                <a href="{{ route('publications.issues.create', $publication) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                    Create Issue
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($issues->count() > 0)
                        <div class="space-y-4">
                            @foreach($issues as $issue)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold mb-2">{{ $issue->title }}</h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                <span class="capitalize px-2 py-1 rounded text-xs
                                                    @if($issue->status === 'draft') bg-gray-200 text-gray-700
                                                    @elseif($issue->status === 'scheduled') bg-blue-200 text-blue-700
                                                    @else bg-green-200 text-green-700
                                                    @endif">
                                                    {{ $issue->status }}
                                                </span>
                                                <span>{{ $issue->created_at->format('M d, Y') }}</span>
                                                @if($issue->published_at)
                                                    <span>Published: {{ $issue->published_at->format('M d, Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('publications.issues.show', [$publication, $issue]) }}" class="text-indigo-600 hover:text-indigo-800">View</a>
                                            @can('update', $publication)
                                                <a href="{{ route('publications.issues.edit', [$publication, $issue]) }}" class="text-gray-600 hover:text-gray-800">Edit</a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $issues->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No issues created yet. Create your first issue to get started!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
