@extends('layouts.admin.app')

@section('title', 'System Settings - Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 relative pb-20">

    <!-- Header Section -->
    <div>
        <h1 class="text-3xl font-headline font-bold text-primary mb-2">System Management & Admin Settings</h1>
        <p class="text-sm font-label text-primary/60">Configure core application behaviors, monitor usage, and manage security protocols.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Left Column -->
        <div class="space-y-8">
            
            <!-- App Configuration -->
            <div class="bg-white rounded-3xl p-8 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
                <div class="flex items-center space-x-3 mb-6">
                    <span class="material-symbols-outlined text-primary text-[24px]">tune</span>
                    <h2 class="text-xl font-headline text-primary">App Configuration</h2>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-[11px] font-label text-primary/60 mb-1">Site Name</label>
                        <input type="text" value="Resumify Production" class="w-full border-b border-primary/20 bg-transparent py-2 text-sm font-label text-primary focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-label text-primary/60 mb-1">Global AI Quota Limit (Monthly)</label>
                        <input type="text" value="50000" class="w-full border-b border-primary/20 bg-transparent py-2 text-sm font-label text-primary focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <p class="text-sm font-label text-primary">Maintenance Mode</p>
                            <p class="text-[11px] font-label text-primary/60">Disable public access temporarily.</p>
                        </div>
                        <!-- Toggle Switch -->
                        <div class="w-10 h-5 bg-surface-container-low rounded-full relative cursor-pointer border border-primary/10">
                            <div class="w-4 h-4 bg-white rounded-full absolute top-[1px] left-[1px] shadow-sm"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white rounded-3xl p-8 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
                <div class="flex items-center space-x-3 mb-4">
                    <span class="material-symbols-outlined text-primary text-[24px]">lock</span>
                    <h2 class="text-xl font-headline text-primary">Security Settings</h2>
                </div>
                <p class="text-[11px] font-label text-primary/60 mb-6">Update your administrative credentials. Ensure a strong password is used.</p>
                
                <div class="space-y-6 mb-8">
                    <div>
                        <label class="block text-[11px] font-label text-primary/60 mb-1">Current</label>
                        <input type="password" value="password" class="w-full border-b border-primary/20 bg-transparent py-2 text-sm font-label text-primary focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-label text-primary/60 mb-1">New</label>
                        <input type="password" value="password" class="w-full border-b border-primary/20 bg-transparent py-2 text-sm font-label text-primary focus:outline-none focus:border-primary transition-colors">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button class="bg-surface border border-primary/10 text-primary px-6 py-2.5 rounded-xl text-[11px] font-label font-bold hover:bg-primary/5 transition">
                        Update Password
                    </button>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            
            <!-- User Overview -->
            <div class="bg-white rounded-3xl p-8 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
                <div class="flex items-center space-x-3 mb-6">
                    <span class="material-symbols-outlined text-[#006c49] text-2xl" style="font-variation-settings: 'FILL' 1;">signal_cellular_alt</span>
                    <h2 class="text-xl font-headline text-primary">User Overview</h2>
                </div>

                <div class="bg-surface-container-low rounded-2xl p-6 px-7 mb-4">
                    <h3 class="text-sm font-label text-primary/60 uppercase mb-2">TOTAL USERS</h3>
                    <div class="flex justify-between items-end">
                        <div class="text-5xl font-headline text-primary leading-none">12,480</div>
                        <span class="material-symbols-outlined text-6xl text-primary/30 leading-none">group</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-surface-container-low rounded-2xl p-6 px-7">
                        <h3 class="text-sm font-label text-primary/60 mb-2">Basic Tier</h3>
                        <div class="text-3xl font-headline text-primary leading-none">8,102</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 px-7 border border-[#bbf7d0] relative overflow-hidden">
                        <div class="absolute -right-8 -top-8 w-40 h-40 bg-green-200 opacity-50 rounded-full blur-2xl"></div>
                        <h3 class="text-sm font-label text-[#166534] mb-2 flex items-center relative z-10">
                            Premium <span class="material-symbols-outlined text-base ml-1" style="font-variation-settings: 'FILL' 1;">star</span>
                        </h3>
                        <div class="text-3xl font-headline text-primary leading-none relative z-10">4,378</div>
                    </div>
                </div>
            </div>

            <!-- Advanced Tools -->
            <div class="bg-white rounded-3xl p-8 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
                <div class="flex items-center space-x-3 mb-4">
                    <span class="material-symbols-outlined text-[#dc2626] text-[20px]">build</span>
                    <h2 class="text-xl font-headline text-primary">Advanced Tools</h2>
                </div>
                <p class="text-[11px] font-label text-primary/60 mb-6">System-level operations and data extraction.</p>
                
                <div class="space-y-4">
                    <button class="w-full bg-white border border-primary/20 text-primary py-3 rounded-xl text-sm font-label hover:bg-surface transition flex items-center justify-center space-x-2">
                        <span class="material-symbols-outlined text-[18px]">cleaning_services</span>
                        <span>Clear System Cache</span>
                    </button>
                    <button class="w-full bg-[#3e322b] text-white py-3 rounded-xl text-sm font-label hover:bg-opacity-90 transition flex items-center justify-center space-x-2 shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Download System Logs</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Floating Action Button -->
    <button class="fixed bottom-10 right-10 w-14 h-14 bg-[#10b981] rounded-full shadow-lg flex items-center justify-center text-white hover:scale-105 transition-transform z-50">
        <span class="material-symbols-outlined text-3xl">auto_awesome</span>
    </button>

</div>
@endsection
