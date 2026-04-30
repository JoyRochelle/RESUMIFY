@extends('layouts.user.app')

@section('title', 'Resumify - Settings')
@section('body_class', 'overflow-hidden h-screen')

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
                        <p class="text-xs font-bold text-primary">Julian Casablancas</p>
                        <p class="text-[10px] text-primary/60">Editorial Lead</p>
                    </div>
                    <img alt="User Profile" class="w-8 h-8 rounded-full border border-primary/10 object-cover" src="{{ asset('images/nion.jpg') }}"/>
                </div>
            </div>
        </x-user.page-header>

        <main class="flex-1 bg-primary/5 overflow-y-auto custom-scrollbar p-4 lg:p-10 pb-24 md:pb-10">
            <div class="max-w-4xl mx-auto space-y-8">
                
                {{-- Page Title --}}
                <header class="mb-4">
                    <h2 class="font-headline text-3xl text-primary font-bold tracking-tight">Settings</h2>
                    <p class="text-primary/60 mt-2 font-body text-sm">Manage your editorial presence and workspace security.</p>
                </header>

                {{-- User Profile Section --}}
                <section class="bg-tertiary rounded-2xl p-8 border border-primary/10 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="font-headline text-2xl text-primary">User Profile</h3>
                        <div class="flex items-center text-secondary font-bold text-xs uppercase tracking-widest bg-secondary/5 px-3 py-1 rounded-full">
                            <span class="material-symbols-outlined text-sm mr-1 icon-filled">verified</span>
                            Verified Author
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-10">
                        <div class="flex-shrink-0 flex flex-col items-center">
                            <div class="relative group">
                                <img alt="User Avatar" class="w-32 h-32 rounded-full object-cover border-4 border-surface" src="{{ asset('images/nion.jpg') }}"/>
                                <button class="absolute bottom-0 right-0 bg-primary text-tertiary w-9 h-9 rounded-full shadow-lg hover:scale-105 transition-transform flex items-center justify-center">
                                    <span class="material-symbols-outlined text-sm">photo_camera</span>
                                </button>
                            </div>
                            <p class="text-[10px] text-primary/60 mt-4 font-bold uppercase tracking-tighter text-center">Format: JPG, PNG (Max 2MB)</p>
                        </div>
                        <div class="flex-1 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-user.settings-input label="Full Name" value="Julian Casablancas" />
                                <x-user.settings-input label="Email Address" type="email" value="julian@resumify.art" />
                            </div>
                            <div class="pt-4 flex justify-end">
                                <x-user.button variant="primary">Save Changes</x-user.button>
                            </div>
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
                                    <span class="bg-secondary text-tertiary text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Premium Member</span>
                                </div>
                                <p class="text-tertiary/80 font-body">Your subscription will automatically renew on October 12, 2026.</p>
                            </div>
                            <a href="{{ route('user.upgrade-quota') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-label font-bold bg-secondary/10 text-secondary hover:bg-secondary/20 transition-all duration-300">
                                <span class="material-symbols-outlined text-sm icon-filled">bolt</span>
                                Upgrade Quota
                            </a>
                        </div>
                        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-12">
                            <div>
                                <div class="flex justify-between items-end mb-4">
                                    <span class="text-sm font-bold opacity-80">AI Quota Remaining</span>
                                    <span class="text-2xl font-headline italic">45<span class="text-sm not-italic opacity-60">/50</span></span>
                                </div>
                                <div class="h-1.5 w-full bg-white/10 rounded-full flex gap-1 p-0.5">
                                    <div class="h-full bg-secondary rounded-full w-[90%]"></div>
                                </div>
                                <p class="mt-4 text-xs italic opacity-60">Optimized by Resumify Editorial Engine.</p>
                            </div>
                            <div class="flex flex-col justify-end items-start md:items-end">
                                <a class="text-sm font-bold text-secondary border-b border-secondary/30 hover:border-secondary transition-all flex items-center gap-2" href="#">View Transaction History <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Security & Password --}}
                <section class="bg-tertiary rounded-2xl p-8 border border-primary/10 shadow-sm">
                    <h3 class="font-headline text-2xl text-primary mb-8">Security & Password</h3>
                    <div class="max-w-2xl space-y-8">
                        <x-user.settings-input label="Current Password" type="password" placeholder="••••••••" :showToggle="true" />
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <x-user.settings-input label="New Password" type="password" />
                            <x-user.settings-input label="Confirm New Password" type="password" />
                        </div>
                        <div class="bg-primary/5 p-4 rounded-xl flex items-start gap-3">
                            <span class="material-symbols-outlined text-secondary text-lg">info</span>
                            <p class="text-xs text-primary/80 font-body leading-relaxed">Use at least 8 characters with a combination of numbers and symbols.</p>
                        </div>
                        <div class="flex justify-start">
                            <x-user.button variant="outline">Update Password</x-user.button>
                        </div>
                    </div>
                </section>

                {{-- Danger Zone --}}
                <section class="bg-red-50 border border-red-200 rounded-2xl p-8 shadow-sm">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <h3 class="font-headline text-2xl text-red-600 mb-1">Danger Zone</h3>
                            <p class="text-sm text-red-800/70">This action cannot be undone. All your data will be permanently deleted from our servers.</p>
                        </div>
                        <x-user.button variant="danger" icon="delete_forever" iconClass="text-lg">Delete Account</x-user.button>
                    </div>
                </section>
                
                <div class="pb-10"></div>
            </div>
        </main>
    </div>
@endsection
