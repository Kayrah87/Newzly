<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">My Newsletters</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ Auth::user()->ownedNewsletters()->count() }}</p>
                        <a href="{{ route('newsletters.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all →</a>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Collaborating On</h3>
                        <p class="text-3xl font-bold text-green-600">{{ Auth::user()->newsletters()->count() }}</p>
                        <a href="{{ route('newsletters.index') }}" class="text-sm text-green-600 hover:text-green-800">View all →</a>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Articles Written</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ Auth::user()->articles()->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('newsletters.create') }}" class="border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white px-6 py-4 rounded-lg text-center font-semibold transition">
                            Create New Newsletter
                        </a>
                        <a href="{{ route('newsletters.index') }}" class="border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-6 py-4 rounded-lg text-center font-semibold transition">
                            Browse All Newsletters
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
