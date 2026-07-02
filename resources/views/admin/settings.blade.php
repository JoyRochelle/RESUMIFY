@extends('layouts.admin.app')

@section('title', 'System Settings - Admin Dashboard')

@section('content')
<div class="admin-shell relative">

    <!-- Header Section -->
    <div>
        <h1 class="text-3xl font-headline font-bold text-primary mb-2">System Management & Admin Settings</h1>
        <p class="text-sm font-label text-primary/60">Configure core application behaviors, monitor usage, and manage security protocols.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Left Column -->
        <div class="space-y-8">
            
            <!-- App Configuration -->
            <div class="admin-card-pad p-8">
                <div class="flex items-center space-x-3 mb-6">
                    <span class="material-symbols-outlined text-primary text-[24px]">tune</span>
                    <h2 class="text-xl font-headline text-primary">App Configuration</h2>
                </div>
                
                <div class="space-y-6">
                    <x-user.form-input label="Site Name" name="site_name" value="Resumify Production" />
                    <x-user.form-input label="Global AI Quota Limit (Monthly)" name="global_ai_quota_limit" value="50000" type="number" />
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <p class="text-sm font-label text-primary">Maintenance Mode</p>
                            <p class="text-[11px] font-label text-primary/60">Disable public access temporarily.</p>
                        </div>
                        <!-- Toggle Switch -->
                        <div class="w-10 h-5 bg-surface-container-low rounded-full relative cursor-pointer border border-primary/10">
                            <div class="w-4 h-4 bg-tertiary rounded-full absolute top-[1px] left-[1px] shadow-sm"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="admin-card-pad p-8">
                <div class="flex items-center space-x-3 mb-4">
                    <span class="material-symbols-outlined text-primary text-[24px]">lock</span>
                    <h2 class="text-xl font-headline text-primary">Security Settings</h2>
                </div>
                <p class="text-[11px] font-label text-primary/60 mb-6">Update your administrative credentials. Ensure a strong password is used.</p>
                
                <div class="space-y-6 mb-8">
                    <x-user.form-input label="Current" name="current_password" type="password" value="password" autocomplete="current-password" />
                    <x-user.form-input label="New" name="new_password" type="password" value="password" autocomplete="new-password" />
                </div>

                <div class="flex justify-end">
                    <x-ui.loading-button type="button" variant="outline" loading-text="Updating..." icon="lock">Update Password</x-ui.loading-button>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            
            <!-- User Overview -->
            <div class="admin-card-pad p-8">
                <div class="flex items-center space-x-3 mb-6">
                    <span class="material-symbols-outlined text-[#006c49] text-2xl" style="font-variation-settings: 'FILL' 1;">signal_cellular_alt</span>
                    <h2 class="text-xl font-headline text-primary">User Overview</h2>
                </div>

                <div class="bg-surface-container-low rounded-lg p-6 px-7 mb-4">
                    <h3 class="text-sm font-label text-primary/60 uppercase mb-2">TOTAL USERS</h3>
                    <div class="flex justify-between items-end">
                        <div class="text-5xl font-headline text-primary leading-none">12,480</div>
                        <span class="material-symbols-outlined text-6xl text-primary/30 leading-none">group</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-surface-container-low rounded-lg p-6 px-7">
                        <h3 class="text-sm font-label text-primary/60 mb-2">Basic Tier</h3>
                        <div class="text-3xl font-headline text-primary leading-none">8,102</div>
                    </div>
                    <div class="relative overflow-hidden rounded-lg border border-secondary/20 bg-secondary/10 p-6 px-7">
                        <h3 class="text-sm font-label text-secondary mb-2 flex items-center relative z-10">
                            Premium <span class="material-symbols-outlined text-base ml-1" style="font-variation-settings: 'FILL' 1;">star</span>
                        </h3>
                        <div class="text-3xl font-headline text-primary leading-none relative z-10">4,378</div>
                    </div>
                </div>
            </div>

            <!-- Advanced Tools -->
            <div class="admin-card-pad p-8">
                <div class="flex items-center space-x-3 mb-4">
                    <span class="material-symbols-outlined text-[#dc2626] text-[20px]">build</span>
                    <h2 class="text-xl font-headline text-primary">Advanced Tools</h2>
                </div>
                <p class="text-[11px] font-label text-primary/60 mb-6">System-level operations and data extraction.</p>
                
                <div class="space-y-4">
                    <x-ui.loading-button type="button" variant="outline" loading-text="Clearing..." icon="cleaning_services" class="w-full">
                        Clear System Cache
                    </x-ui.loading-button>
                    <x-ui.loading-button type="button" loading-text="Preparing..." icon="download" class="w-full">
                        Download System Logs
                    </x-ui.loading-button>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
