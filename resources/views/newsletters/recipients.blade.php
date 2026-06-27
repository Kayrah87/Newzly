<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Recipients - {{ $newsletter->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Current Recipients</h3>
                    
                    @if($recipients->count() > 0)
                        <div class="space-y-3 mb-6">
                            @foreach($recipients as $recipient)
                                <div class="flex justify-between items-center border-b pb-3">
                                    <div>
                                        <p class="font-medium">{{ $recipient->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $recipient->email }}</p>
                                    </div>
                                    <span class="text-sm text-gray-500">Recipient</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mb-6">No recipients subscribed yet.</p>
                    @endif

                    <div class="border-t pt-6">
                        <h4 class="font-semibold mb-3">Add Recipients</h4>
                        <p class="text-sm text-gray-600 mb-4">
                            Recipients are users who will receive your newsletter issues when they are published.
                            You can add registered users or invite new subscribers.
                        </p>
                        <div class="bg-blue-50 border border-blue-200 rounded p-4">
                            <p class="text-sm text-blue-800">
                                <strong>Note:</strong> Recipient management functionality is coming soon. 
                                You'll be able to add recipients via email, manage subscriptions, 
                                and import/export recipient lists.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('newsletters.show', $newsletter) }}" class="text-indigo-600 hover:text-indigo-800">
                            ← Back to Newsletter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
