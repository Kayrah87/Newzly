<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="np-kicker">Editorial Desk</span>
            <h2 class="font-display text-3xl font-black text-ink leading-tight">
                {{ __('Edit Publication') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="np-card">
                <div class="p-6">
                    <form method="POST" action="{{ route('publications.update', $publication) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        {{-- Basics --}}
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Publication Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $publication->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label :value="__('Public URL slug')" />
                            <p class="mt-1 text-sm text-ink-soft">{{ url('/p/'.$publication->slug) }}</p>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-ink/25 bg-white text-ink focus:border-press-500 focus:ring-press-500 shadow-sm">{{ old('description', $publication->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="website_url" :value="__('Website')" />
                            <x-text-input id="website_url" class="block mt-1 w-full" type="url" name="website_url" :value="old('website_url', $publication->website_url)" placeholder="https://example.com" />
                            <x-input-error :messages="$errors->get('website_url')" class="mt-2" />
                        </div>

                        {{-- Logo --}}
                        <div class="np-rule my-6 pt-6">
                            <h3 class="font-display text-lg font-bold mb-4">{{ __('Branding') }}</h3>

                            <div class="mb-4">
                                <x-input-label for="logo" :value="__('Logo')" />
                                @if ($publication->logoUrl())
                                    <div class="flex items-center gap-4 mt-2 mb-3">
                                        <img src="{{ $publication->logoUrl() }}" alt="Logo" class="h-16 w-16 object-contain border border-ink/15">
                                        <label class="inline-flex items-center text-sm text-ink-soft">
                                            <input type="checkbox" name="remove_logo" value="1" class="rounded border-ink/30 text-press-600 focus:ring-press-500 mr-2">
                                            {{ __('Remove current logo') }}
                                        </label>
                                    </div>
                                @endif
                                <input id="logo" type="file" name="logo" accept="image/*" class="block mt-1 w-full text-sm text-ink-soft">
                                <p class="mt-1 text-xs text-ink-soft">{{ __('PNG, JPG, GIF, SVG up to 2MB.') }}</p>
                                <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                            </div>

                            {{-- Social links --}}
                            <div class="mb-2">
                                <x-input-label :value="__('Social links')" />
                            </div>
                            @foreach (\App\Models\Publication::SOCIAL_PLATFORMS as $key => $label)
                                <div class="mb-3">
                                    <label for="social_{{ $key }}" class="block text-sm text-ink-soft">{{ $label }}</label>
                                    <x-text-input id="social_{{ $key }}" class="block mt-1 w-full" type="url" name="social_links[{{ $key }}]" :value="old('social_links.'.$key, $publication->social_links[$key] ?? '')" placeholder="https://..." />
                                    <x-input-error :messages="$errors->get('social_links.'.$key)" class="mt-2" />
                                </div>
                            @endforeach
                        </div>

                        {{-- Sending identity --}}
                        <div class="np-rule my-6 pt-6">
                            <h3 class="font-display text-lg font-bold mb-4">{{ __('Email sending identity') }}</h3>
                            <p class="text-sm text-ink-soft mb-4">{{ __('Used as the From/Reply-To when issues are sent. SMTP delivery is configured in a later step.') }}</p>

                            <div class="mb-4">
                                <x-input-label for="from_name" :value="__('From name')" />
                                <x-text-input id="from_name" class="block mt-1 w-full" type="text" name="from_name" :value="old('from_name', $publication->from_name)" placeholder="{{ $publication->name }}" />
                                <x-input-error :messages="$errors->get('from_name')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="from_email" :value="__('From email')" />
                                <x-text-input id="from_email" class="block mt-1 w-full" type="email" name="from_email" :value="old('from_email', $publication->from_email)" placeholder="hello@example.com" />
                                <x-input-error :messages="$errors->get('from_email')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="reply_to_email" :value="__('Reply-to email')" />
                                <x-text-input id="reply_to_email" class="block mt-1 w-full" type="email" name="reply_to_email" :value="old('reply_to_email', $publication->reply_to_email)" placeholder="replies@example.com" />
                                <x-input-error :messages="$errors->get('reply_to_email')" class="mt-2" />
                            </div>
                        </div>

                        {{-- SMTP delivery --}}
                        <div class="np-rule my-6 pt-6">
                            <h3 class="font-display text-lg font-bold mb-1">{{ __('Email delivery (SMTP)') }}</h3>
                            <p class="text-sm text-ink-soft mb-4">
                                {{ __('Optional. When set, issues are sent through your own SMTP server. Leave blank to use the platform default.') }}
                                @if($publication->hasSmtpConfigured())
                                    <span class="np-badge-press">SMTP is configured</span>
                                @endif
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="smtp_host" :value="__('Host')" />
                                    <x-text-input id="smtp_host" class="block mt-1 w-full" type="text" name="smtp_host" :value="old('smtp_host', $publication->smtp_host)" placeholder="smtp.example.com" />
                                    <x-input-error :messages="$errors->get('smtp_host')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="smtp_port" :value="__('Port')" />
                                    <x-text-input id="smtp_port" class="block mt-1 w-full" type="number" name="smtp_port" :value="old('smtp_port', $publication->smtp_port)" placeholder="587" />
                                    <x-input-error :messages="$errors->get('smtp_port')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="smtp_username" :value="__('Username')" />
                                    <x-text-input id="smtp_username" class="block mt-1 w-full" type="text" name="smtp_username" :value="old('smtp_username', $publication->smtp_username)" autocomplete="off" />
                                    <x-input-error :messages="$errors->get('smtp_username')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="smtp_password" :value="__('Password')" />
                                    <x-text-input id="smtp_password" class="block mt-1 w-full" type="password" name="smtp_password" autocomplete="new-password" placeholder="{{ $publication->smtp_password ? '•••••••• (unchanged)' : '' }}" />
                                    <x-input-error :messages="$errors->get('smtp_password')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="smtp_encryption" :value="__('Encryption')" />
                                    <select id="smtp_encryption" name="smtp_encryption" class="block mt-1 w-full border-ink/25 bg-white text-ink focus:border-press-500 focus:ring-press-500 shadow-sm">
                                        <option value="">None</option>
                                        <option value="tls" @selected(old('smtp_encryption', $publication->smtp_encryption) === 'tls')>TLS</option>
                                        <option value="ssl" @selected(old('smtp_encryption', $publication->smtp_encryption) === 'ssl')>SSL</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('smtp_encryption')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('publications.show', $publication) }}" class="np-btn-outline mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Publication') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
