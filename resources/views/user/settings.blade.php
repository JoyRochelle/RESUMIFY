@extends('layouts.user.app')

@section('title', 'Resumify - Settings')

@section('content')
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Page Header --}}
        <x-user.page-header title="Account Settings">
            <div class="flex items-center space-x-6 hidden md:flex">
                <button class="text-primary/60 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                </button>
                <div class="flex items-center space-x-3 group cursor-pointer">
                    <div class="text-right">
                        <p class="text-xs font-bold text-primary">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-primary/60">{{ auth()->user()->isPremium() ? 'Premium Member' : 'Basic Member' }}</p>
                    </div>
                    <img alt="User Profile" class="w-8 h-8 rounded-full border border-primary/10 object-cover"
                        src="{{ auth()->user()->avatar_url }}" />
                </div>
            </div>
        </x-user.page-header>

        <main class="flex-1 bg-primary/5 overflow-y-auto custom-scrollbar p-4 lg:p-10 pb-24 md:pb-10">

            {{-- Flash Messages --}}
            @if (session('status') === 'avatar-updated')
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl text-sm font-body">✅ Avatar updated
                    successfully.</div>
            @endif
            @if (session('status') === 'avatar-deleted')
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl text-sm font-body">✅ Avatar removed.</div>
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
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl text-sm font-body">✅ Profile updated
                    successfully.</div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl text-sm font-body">✅ Password updated
                    successfully.</div>
            @endif

            <div class="max-w-4xl mx-auto space-y-8">

                {{-- Page Title --}}
                <header class="mb-4">
                    <h2 class="font-headline text-3xl text-primary font-bold tracking-tight">Settings</h2>
                    <p class="text-primary/60 mt-2 font-body text-sm">Manage your editorial presence and workspace security.
                    </p>
                </header>

                {{-- User Profile Section --}}
                <section class="bg-tertiary rounded-2xl p-8 border border-primary/10 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="font-headline text-2xl text-primary">User Profile</h3>
                        <div
                            class="flex items-center text-secondary font-bold text-xs uppercase tracking-widest bg-secondary/5 px-3 py-1 rounded-full">
                            <span class="material-symbols-outlined text-sm mr-1 icon-filled">verified</span>
                            Verified Author
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-10">

                        {{-- Avatar Upload Form --}}
                        <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="flex-shrink-0 flex flex-col items-center">
                                <div class="relative group">
                                    <img alt="User Avatar"
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
                                    Format: JPG, PNG (Max 2MB)</p>
                            </div>
                        </form>

                        {{-- Name Update Form --}}
                        <div class="flex-1 space-y-6">
                            <form action="{{ route('user-profile-information.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-user.settings-input label="Full Name" name="name"
                                        value="{{ auth()->user()->name }}" />

                                    {{-- Email: read-only, not part of the form --}}
                                    <div class="space-y-1">
                                        <label class="text-xs font-bold text-primary/60 uppercase tracking-widest">Email
                                            Address</label>
                                        <div class="flex items-center gap-2 border-b border-primary/20 py-2">
                                            <span
                                                class="font-body text-primary/40 text-lg">{{ auth()->user()->email }}</span>
                                            <span
                                                class="text-[10px] text-primary/30 uppercase tracking-wider font-bold ml-auto">locked</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4 flex justify-end">
                                    <x-user.button type="submit" variant="primary">Save Changes</x-user.button>
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
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="font-headline text-3xl">Subscription & Billing</h3>
                                    {{-- role badge --}}
                                    @if (auth()->user()->isPremium())
                                        <span
                                            class="bg-secondary text-tertiary text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">
                                            Premium Member
                                        </span>
                                    @else
                                        <span
                                            class="bg-secondary text-tertiary text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">
                                            Basic Member
                                        </span>
                                    @endif
                                </div>
                                {{-- <p class="text-tertiary/80 font-body">Your subscription will automatically renew on October
                                    12, 2026.</p> --}}
                            </div>
                            <a href="{{ route('user.upgrade-quota') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-label font-bold bg-secondary/10 text-secondary hover:bg-secondary/20 transition-all duration-300">
                                <span class="material-symbols-outlined text-sm icon-filled">bolt</span>
                                Upgrade Quota
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
                                <p class="mt-4 text-xs italic opacity-60">Optimized by Resumify Editorial Engine.</p>
                            </div>
                            <div class="flex flex-col justify-end items-start md:items-end">
                                <a class="text-sm font-bold text-secondary border-b border-secondary/30 hover:border-secondary transition-all flex items-center gap-2"
                                    href="#">View Transaction History <span
                                        class="material-symbols-outlined text-sm">arrow_forward</span></a>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Security & Password --}}
                <section class="bg-tertiary rounded-2xl p-8 border border-primary/10 shadow-sm">
                    <h3 class="font-headline text-2xl text-primary mb-8">Security & Password</h3>
                    <form action="{{ route('user-password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="max-w-2xl space-y-8">
                            <x-user.settings-input label="Current Password" name="current_password" type="password"
                                placeholder="••••••••" :showToggle="true" />
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <x-user.settings-input label="New Password" name="password" type="password" />
                                <x-user.settings-input label="Confirm New Password" name="password_confirmation"
                                    type="password" />
                            </div>
                            <div class="bg-primary/5 p-4 rounded-xl flex items-start gap-3">
                                <span class="material-symbols-outlined text-secondary text-lg">info</span>
                                <p class="text-xs text-primary/80 font-body leading-relaxed">Use at least 8 characters with
                                    a combination of numbers and symbols.</p>
                            </div>
                            <div class="flex justify-start">
                                <x-user.button type="submit" variant="outline">Update Password</x-user.button>
                            </div>
                        </div>
                    </form>
                </section>

                {{-- Danger Zone --}}
                <section class="bg-red-50 border border-red-200 rounded-2xl p-8 shadow-sm">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <h3 class="font-headline text-2xl text-red-600 mb-1">Danger Zone</h3>
                            <p class="text-sm text-red-800/70">This action cannot be undone. All your data will be
                                permanently deleted from our servers.</p>
                        </div>
                        <form action="{{ route('profile.destroy') }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <div class="flex flex-col gap-3">
                                <input type="password" name="password" required placeholder="Confirm your password"
                                    class="border-b border-red-300 bg-transparent outline-none text-red-800 placeholder-red-400 py-2 text-sm w-full" />
                                <x-user.button type="submit" variant="danger" icon="delete_forever"
                                    iconClass="text-lg">Delete Account</x-user.button>
                            </div>
                        </form>
                    </div>
                </section>

                <div class="pb-10"></div>
            </div>
        </main>
    </div>
@endsection
