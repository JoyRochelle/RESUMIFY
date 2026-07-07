@extends('layouts.user.app')

@section('title', __('messages.settings.page_title') . ' - Resumify')

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="flex-1 flex flex-col min-w-0">
        {{-- Page Header --}}
        <x-user.page-header title="{{ __('messages.settings.page_title') }}" backUrl="{{ route('dashboard') }}">
            <div class="flex items-center space-x-6 hidden md:flex">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="relative text-primary/60 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        @if($user->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                                {{ $user->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div x-show="open" style="display: none;"
                         class="absolute right-0 mt-2 w-80 bg-surface border border-primary/10 rounded-xl shadow-lg z-50 overflow-hidden">
                        <div class="p-4 border-b border-primary/10 flex justify-between items-center bg-surface-container-low">
                            <h3 class="font-bold text-primary text-sm">{{ __('messages.settings.notifications.heading') }}</h3>
                            @if($user->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.readAll') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-secondary hover:underline font-medium">{{ __('messages.settings.notifications.mark_all_read') }}</button>
                                </form>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto custom-scrollbar">
                            @forelse($user->notifications as $notification)
                                <a href="{{ route('notifications.read', $notification->id) }}" class="block p-4 border-b border-primary/5 hover:bg-surface-container-low transition-colors {{ $notification->read_at ? 'opacity-75' : 'bg-secondary/5' }}">
                                    <p class="text-sm text-primary">{{ $notification->data['message'] ?? __('messages.settings.notifications.default_message') }}</p>
                                    <span class="text-[10px] text-primary/40 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                                </a>
                            @empty
                                <div class="p-4 text-center text-primary/60 text-sm">{{ __('messages.settings.notifications.empty') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                {{-- <div class="flex items-center space-x-3 group cursor-pointer">
                    <div class="text-right">
                        <p class="text-xs font-bold text-primary">{{ $user->name }}</p>
                        <x-user.plan-badge :user="$user" size="xs" />
                    </div>
                    <div class="relative">
                        <img alt="{{ $user->name }} profile avatar" class="w-8 h-8 rounded-full border border-primary/10 object-cover {{ $user->isPremium() ? 'ring-2 ring-[#A16207]/40 ring-offset-2 ring-offset-surface' : '' }}"
                            src="{{ $user->avatar_url }}" />
                        @if($user->isPremium())
                            <span class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-[#A16207] text-white" aria-label="Premium member">
                                <span class="material-symbols-outlined text-[10px] icon-filled" aria-hidden="true">workspace_premium</span>
                            </span>
                        @endif
                    </div>
                </div> --}}
            </div>
        </x-user.page-header>

        <main class="flex-1 bg-primary/5 overflow-y-auto custom-scrollbar p-4 lg:p-10 pb-24 md:pb-10">

            {{-- Flash Messages --}}
            @if (session('status') === 'avatar-updated')
                <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-100 p-4 text-sm font-body text-green-700"><span class="material-symbols-outlined text-[18px] icon-filled" aria-hidden="true">check_circle</span> {{ __('messages.settings.flash.avatar_updated') }}</div>
            @endif
            @if (session('status') === 'avatar-deleted')
                <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-100 p-4 text-sm font-body text-green-700"><span class="material-symbols-outlined text-[18px] icon-filled" aria-hidden="true">check_circle</span> {{ __('messages.settings.flash.avatar_deleted') }}</div>
            @endif
            @if ($errors->updateProfileInformation->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl text-sm font-body">
                    <ul>
                        @foreach ($errors->updateProfileInformation->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($errors->updatePassword->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl text-sm font-body">
                    <ul>
                        @foreach ($errors->updatePassword->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('status') === 'profile-information-updated')
                <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-100 p-4 text-sm font-body text-green-700"><span class="material-symbols-outlined text-[18px] icon-filled" aria-hidden="true">check_circle</span> {{ __('messages.settings.flash.profile_updated') }}</div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-100 p-4 text-sm font-body text-green-700"><span class="material-symbols-outlined text-[18px] icon-filled" aria-hidden="true">check_circle</span> {{ __('messages.settings.flash.password_updated') }}</div>
            @endif

            <div class="max-w-4xl mx-auto space-y-8">

                {{-- Page Title --}}
                <header class="mb-4">
                    <h2 class="font-headline text-3xl text-primary font-bold tracking-tight">{{ __('messages.settings.heading') }}</h2>
                    <p class="text-primary/60 mt-2 font-body text-sm">{{ __('messages.settings.subtitle') }}
                    </p>
                </header>

                {{-- User Profile Section --}}
                <section class="bg-tertiary rounded-2xl p-8 border border-primary/10 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="font-headline text-2xl text-primary">{{ __('messages.settings.profile_section.title') }}</h3>
                        <div
                            class="flex items-center text-secondary font-bold text-xs uppercase tracking-widest bg-secondary/5 px-3 py-1 rounded-full">
                            <span class="material-symbols-outlined text-sm mr-1 icon-filled">verified</span>
                            {{ __('messages.settings.profile_section.verified_badge') }}
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-10">

                        {{-- Avatar Upload Form --}}
                        <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="flex-shrink-0 flex flex-col items-center">
                                <div class="relative group">
                                    <img alt="{{ __('messages.settings.profile_section.avatar_alt') }}"
                                        class="w-32 h-32 rounded-full object-cover border-4 border-surface"
                                        src="{{ auth()->user()->avatar_url }}" />
                                    <label for="avatar"
                                        class="absolute bottom-0 right-0 bg-primary text-tertiary w-9 h-9 rounded-full shadow-lg hover:scale-105 transition-transform flex items-center justify-center cursor-pointer">
                                        <span class="material-symbols-outlined text-sm">photo_camera</span>
                                        <input type="file" id="avatar" name="avatar" class="hidden"
                                            accept="image/png,image/jpg,image/jpeg,image/webp"
                                            onchange="this.form.submit()">
                                    </label>
                                </div>
                                <p
                                    class="text-[10px] text-primary/60 mt-4 font-bold uppercase tracking-tighter text-center">
                                    {{ __('messages.settings.profile_section.avatar_format_hint') }}</p>
                            </div>
                        </form>

                        {{-- Name Update Form --}}
                        <div class="flex-1 space-y-6">
                            <form action="{{ route('user-profile-information.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-user.settings-input label="{{ __('messages.settings.profile_section.full_name') }}" name="name"
                                        value="{{ auth()->user()->name }}" />

                                    {{-- Email: read-only, not part of the form --}}
                                    <div class="space-y-1">
                                        <label class="text-xs font-bold text-primary/60 uppercase tracking-widest">{{ __('messages.settings.profile_section.email_address') }}
                                            </label>
                                        <div class="flex items-center gap-2 border-b border-primary/20 py-2">
                                            <span
                                                class="font-body text-primary/40 text-lg">{{ auth()->user()->email }}</span>
                                            <span
                                                class="text-[10px] text-primary/30 uppercase tracking-wider font-bold ml-auto">{{ __('messages.settings.profile_section.locked') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4 flex justify-end">
                                    <x-ui.button type="submit" variant="primary">{{ __('messages.settings.profile_section.save_changes') }}</x-ui.button>
                                </div>
                            </form>
                        </div>

                    </div>
                </section>

                {{-- Subscription & Billing --}}
                <section class="bg-primary text-tertiary rounded-2xl overflow-hidden shadow-lg relative">
                    <div class="absolute right-0 top-0 h-full w-1/4 opacity-[0.04] pointer-events-none">
                        <div class="w-full h-full bg-gradient-to-l from-secondary to-transparent"></div>
                    </div>
                    <div class="p-8 relative z-10">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div>
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="font-headline text-3xl">{{ __('messages.settings.billing_section.title') }}</h3>
                                    {{-- role badge --}}
                                    <x-user.plan-badge :user="$user" surface="dark" />
                                </div>
                            </div>
                                <a href="{{ route('user.upgrade-quota') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-label font-bold bg-secondary text-tertiary hover:brightness-110 shadow-md transition-all duration-300">
                                <span class="material-symbols-outlined text-sm icon-filled">bolt</span>
                                {{ __('messages.settings.billing_section.upgrade_quota') }}
                            </a>
                        </div>
                        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-12">
                            <div>
                                <div class="flex justify-between items-end mb-4">
                                    {{-- Quota display --}}
                                    <span class="text-2xl font-headline italic">
                                        {{ auth()->user()->getQuotaRemaining() }}
                                        <span class="text-sm not-italic opacity-60">/{{ auth()->user()->getQuotaLimit() }}
                                        </span>
                                    </span>
                                </div>
                                <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                                    {{-- Progress bar: shows remaining quota (full = 100% left) --}}
                                    <div class="h-full bg-secondary rounded-full"
                                        style="width: {{ 100 - auth()->user()->getQuotaPercentage() }}%"></div>
                                </div>
                                <p class="mt-4 text-xs italic opacity-60">{{ __('messages.settings.billing_section.optimized_by') }}</p>
                            </div>
                            <div class="flex flex-col justify-end items-start md:items-end">
                                <a class="text-sm font-bold text-tertiary hover:text-secondary border-b border-tertiary/30 hover:border-secondary transition-all flex items-center gap-2 cursor-pointer"
                                    onclick="alert('{{ __('messages.settings.billing_section.transaction_history_wip') }}')">
                                    {{ __('messages.settings.billing_section.view_transaction_history') }} <span
                                        class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Security & Password --}}
                <section class="bg-tertiary rounded-2xl p-8 border border-primary/10 shadow-sm">
                    <div class="mb-8">
                        <h3 class="font-headline text-2xl text-primary mb-2">{{ __('messages.settings.security_section.title') }}</h3>
                        <p class="text-sm text-primary/60 font-body">{{ __('messages.settings.security_section.subtitle') }}</p>
                    </div>
                    <form action="{{ route('user-password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="w-full space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-user.settings-input label="{{ __('messages.settings.security_section.current_password') }}" name="current_password" type="password"
                                        placeholder="••••••••" :showToggle="true" />
                                </div>
                                <x-user.settings-input label="{{ __('messages.settings.security_section.new_password') }}" name="password" type="password" />
                                <x-user.settings-input label="{{ __('messages.settings.security_section.confirm_new_password') }}" name="password_confirmation"
                                    type="password" />
                            </div>
                            <div class="bg-primary/5 p-4 rounded-xl flex items-start gap-3 mt-2">
                                <span class="material-symbols-outlined text-secondary text-xl">info</span>
                                <p class="text-sm text-primary/80 font-body leading-relaxed">{{ __('messages.settings.security_section.password_hint') }}</p>
                            </div>
                            <div class="flex justify-end pt-4 border-t border-primary/10 mt-6">
                                <x-ui.button type="submit" variant="primary" icon="save" iconClass="text-sm">{{ __('messages.settings.security_section.update_password') }}</x-ui.button>
                            </div>
                        </div>
                    </form>
                </section>

                {{-- Danger Zone --}}
                <section class="bg-red-50 border border-red-200 rounded-2xl p-8 shadow-sm">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <h3 class="font-headline text-2xl text-red-600 mb-1">{{ __('messages.settings.danger_zone.title') }}</h3>
                            <p class="text-sm text-red-800/70">{{ __('messages.settings.danger_zone.warning') }}</p>
                        </div>
                        <form action="{{ route('profile.destroy') }}" method="POST"
                            onsubmit="return confirm('{{ __('messages.settings.danger_zone.confirm_dialog') }}')">
                            @csrf
                            @method('DELETE')
                            <div class="flex flex-col gap-3">
                                <input type="password" name="password" required placeholder="{{ __('messages.settings.danger_zone.confirm_password_placeholder') }}"
                                    class="border-b border-red-300 bg-transparent outline-none text-red-800 placeholder-red-400 py-2 text-sm w-full" />
                                <x-ui.button type="submit" variant="danger" icon="delete_forever"
                                    iconClass="text-lg">{{ __('messages.settings.danger_zone.delete_account') }}</x-ui.button>
                            </div>
                        </form>
                    </div>
                </section>

                <div class="pb-10"></div>
            </div>
        </main>
    </div>
@endsection
