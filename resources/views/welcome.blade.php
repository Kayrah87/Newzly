<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Newzly — Newsletter Management Software</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|playfair-display:700,800,900&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-newsprint text-ink">
        <div class="min-h-screen">
            <!-- Masthead Nav -->
            <nav class="bg-white border-b-2 border-ink">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <a href="/" class="font-display text-2xl font-black tracking-tight text-ink">
                            Newzly<span class="text-press-600">.</span>
                        </a>
                        <div class="flex items-center gap-5 text-sm font-semibold uppercase tracking-wide">
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-ink-soft hover:text-press-600">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-ink-soft hover:text-press-600">Log in</a>
                                <a href="{{ route('register') }}" class="np-btn-primary">Get Started</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero / Front Page -->
            <header class="bg-white border-b-2 border-ink">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between py-2 text-[0.7rem] font-bold uppercase tracking-[0.2em] text-ink-soft border-b border-ink/10">
                        <span>The Publisher's Edition</span>
                        <span class="hidden sm:inline">{{ now()->format('l, F j, Y') }}</span>
                        <span>Vol. I — No. 1</span>
                    </div>
                    <div class="py-16 text-center">
                        <span class="np-kicker">Newsletter Management, Reinvented</span>
                        <h1 class="mt-4 font-display text-5xl sm:text-7xl font-black leading-[0.95] tracking-tight">
                            All the News<br>That's Fit to <span class="text-press-600">Send</span>
                        </h1>
                        <p class="mt-6 text-lg text-ink-soft max-w-2xl mx-auto">
                            Create, manage, and deliver beautiful newsletters with the polish of the
                            front page and the speed of a modern newsroom.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                            @guest
                                <a href="{{ route('register') }}" class="np-btn-primary px-8 py-3">Start Free</a>
                                <a href="#features" class="np-btn-outline px-8 py-3">Read More</a>
                            @else
                                <a href="{{ route('publications.index') }}" class="np-btn-primary px-8 py-3">Go to Publications</a>
                            @endguest
                        </div>
                    </div>
                </div>
            </header>

            <!-- Features -->
            <section id="features" class="py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <span class="np-kicker">The Full Press Kit</span>
                        <h2 class="mt-2 font-display text-4xl font-black">Everything You Need</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-ink/15 border border-ink/15">
                        @php
                            $features = [
                                ['WYSIWYG Editor', 'Compose beautiful content with a fast, self-hosted rich-text editor.'],
                                ['Team Collaboration', 'Invite editors and contributors with role-based permissions.'],
                                ['Automated Sending', 'Schedule and deliver issues to your subscribers on time.'],
                                ['Issue Management', 'Organize stories into issues with editorial layouts.'],
                                ['Subscriber Lists', 'GDPR-friendly double opt-in mailing lists and consent logs.'],
                                ['Public Submissions', 'Collect stories and photos from your readership.'],
                            ];
                        @endphp
                        @foreach($features as [$title, $copy])
                            <div class="bg-white p-8">
                                <div class="np-kicker">No. {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                                <h3 class="mt-2 font-display text-xl font-bold">{{ $title }}</h3>
                                <p class="mt-2 text-sm text-ink-soft leading-relaxed">{{ $copy }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- How It Works -->
            <section class="py-20 bg-white border-y-2 border-ink">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <span class="np-kicker">The Editorial Process</span>
                        <h2 class="mt-2 font-display text-4xl font-black">How It Works</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        @foreach([
                            ['Create', 'Set up your publication and brand it.'],
                            ['Invite', 'Bring your editorial team aboard.'],
                            ['Compose', 'Write and design your issues.'],
                            ['Send & Grow', 'Deliver to subscribers and grow.'],
                        ] as [$step, $copy])
                            <div class="text-center">
                                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center border-2 border-ink bg-press-600 font-display text-2xl font-black text-white">{{ $loop->iteration }}</div>
                                <h3 class="font-bold uppercase tracking-wide text-sm">{{ $step }}</h3>
                                <p class="mt-1 text-sm text-ink-soft">{{ $copy }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="bg-ink text-white py-20">
                <div class="max-w-4xl mx-auto text-center px-4">
                    <span class="np-kicker text-press-400">Extra! Extra!</span>
                    <h2 class="mt-2 font-display text-4xl font-black">Ready to Go to Press?</h2>
                    <p class="mt-4 text-lg text-white/70">Join the teams running their newsletters the Newzly way.</p>
                    <div class="mt-8">
                        @guest
                            <a href="{{ route('register') }}" class="np-btn-primary px-8 py-3">Sign Up — It's Free</a>
                        @else
                            <a href="{{ route('publications.create') }}" class="np-btn-primary px-8 py-3">Create Your First Publication</a>
                        @endguest
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="bg-ink text-white/50 py-8 border-t border-white/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
                    <p class="font-display text-lg font-black text-white">Newzly<span class="text-press-500">.</span></p>
                    <p class="mt-1">&copy; {{ date('Y') }} Newzly — Newsletter Management Software. Built with Laravel.</p>
                </div>
            </footer>
        </div>
    </body>
</html>
