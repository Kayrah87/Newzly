<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Editors - {{ $publication->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Current Editors</h3>
                    
                    @if($editors->count() > 0)
                        <div class="space-y-3 mb-6">
                            @foreach($editors as $editor)
                                <div class="flex justify-between items-center border-b pb-3">
                                    <div>
                                        <p class="font-medium">{{ $editor->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $editor->email }}</p>
                                    </div>
                                    <span class="text-sm text-gray-500">Editor</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mb-6">No editors assigned yet.</p>
                    @endif

                    <div class="border-t pt-6">
                        <h4 class="font-semibold mb-3">Invite Editor</h4>
                        <p class="text-sm text-gray-600 mb-4">
                            To add an editor, they must first be registered in the system. 
                            You can invite users by their email address.
                        </p>
                        <div class="bg-blue-50 border border-blue-200 rounded p-4">
                            <p class="text-sm text-blue-800">
                                <strong>Note:</strong> Editor management functionality is coming soon. 
                                Editors will be able to create and edit issues and stories, 
                                but won't be able to delete the publication or manage other editors.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('publications.show', $publication) }}" class="text-indigo-600 hover:text-indigo-800">
                            ← Back to Publication
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
