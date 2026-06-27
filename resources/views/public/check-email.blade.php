<x-public-layout :publication="$publication" title="Check your email">
    <h2 class="text-lg font-semibold mb-2">Almost there!</h2>
    <p class="text-gray-600">
        We've sent a confirmation link to your email address. Please click the link to
        confirm your subscription to {{ $publication->name }}.
    </p>
    <p class="text-gray-500 text-sm mt-4">
        Didn't get it? Check your spam folder, or try subscribing again.
    </p>
</x-public-layout>
