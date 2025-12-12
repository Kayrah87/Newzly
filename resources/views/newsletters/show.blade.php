<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $newsletter->name }}
            </h2>
            <div class="space-x-2">
                @can('update', $newsletter)
                    <a href="{{ route('newsletters.edit', $newsletter) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                @endcan
                <a href="{{ route('newsletters.issues.index', $newsletter) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
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
                <!-- Newsletter Info -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Newsletter Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600 font-medium">Description:</span>
                                    <p class="text-gray-800">{{ $newsletter->description ?? 'No description provided' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">Owner:</span>
                                    <p class="text-gray-800">{{ $newsletter->owner->name }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">Created:</span>
                                    <p class="text-gray-800">{{ $newsletter->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Issues -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Recent Issues</h3>
                                @can('update', $newsletter)
                                    <a href="{{ route('newsletters.issues.create', $newsletter) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Create Issue</a>
                                @endcan
                            </div>
                            @if($newsletter->issues->count() > 0)
                                <div class="space-y-3">
                                    @foreach($newsletter->issues->take(5) as $issue)
                                        <div class="border-l-4 border-indigo-500 pl-4 py-2">
                                            <a href="{{ route('newsletters.issues.show', [$newsletter, $issue]) }}" class="font-medium text-gray-900 hover:text-indigo-600">
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
                                    <span class="font-semibold">{{ $newsletter->issues->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Editors:</span>
                                    <span class="font-semibold">{{ $newsletter->editors()->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Recipients:</span>
                                    <span class="font-semibold">{{ $newsletter->recipients()->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @can('manageEditors', $newsletter)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Manage Team</h3>
                                <div class="space-y-2">
                                    <a href="{{ route('newsletters.editors', $newsletter) }}" class="block text-indigo-600 hover:text-indigo-800">
                                        Manage Editors
                                    </a>
                                    <a href="{{ route('newsletters.recipients', $newsletter) }}" class="block text-indigo-600 hover:text-indigo-800">
                                        Manage Recipients
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan

                    @can('delete', $newsletter)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4 text-red-600">Danger Zone</h3>
                                <form method="POST" action="{{ route('newsletters.destroy', $newsletter) }}" onsubmit="return confirm('Are you sure you want to delete this newsletter? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">
                                        Delete Newsletter
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
