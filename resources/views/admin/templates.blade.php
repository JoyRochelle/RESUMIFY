@extends('layouts.admin.app')

@section('title', 'Template Catalog - Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 relative pb-20">

    <!-- Header Section -->
    <div>
        <h1 class="text-3xl font-headline font-bold text-primary mb-2">Template Catalog & Management</h1>
        <p class="text-sm font-label text-primary/60">Curate and optimize the visual narrative of professional identities.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Templates -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">TOTAL TEMPLATES</h3>
            <div class="flex items-end mb-4">
                <span class="text-3xl font-headline text-primary mr-3">12</span>
                <span class="text-sm font-label text-primary/40 mb-1">Designs</span>
            </div>
            <div class="flex space-x-1 h-1.5 w-full">
                <div class="h-full w-8 bg-primary rounded-full"></div>
                <div class="h-full w-8 bg-primary rounded-full"></div>
                <div class="h-full w-8 bg-surface-container-low rounded-full"></div>
                <div class="h-full w-8 bg-surface-container-low rounded-full"></div>
            </div>
        </div>

        <!-- Most Popular -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">MOST POPULAR</h3>
            <div class="text-2xl font-headline italic text-primary mb-4">Minimalist Noir</div>
            <div class="flex items-center text-[11px] font-label text-secondary font-bold">
                <span class="material-symbols-outlined text-[16px] mr-1">trending_up</span>
                <span>+14% this month</span>
            </div>
        </div>

        <!-- Server Status -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">SERVER STATUS</h3>
            <div class="flex items-center text-sm font-label text-secondary font-bold mb-4">
                <div class="w-2 h-2 rounded-full bg-secondary mr-2"></div>
                Synced with S3 Storage
            </div>
            <p class="text-[10px] font-label text-primary/40">Last health check: 2 mins ago</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4 flex-1 max-w-2xl">
            <!-- Search Input -->
            <div class="flex-1 bg-white rounded-xl border border-primary/10 flex items-center px-4 h-12 shadow-sm focus-within:border-primary/30 transition-colors">
                <input type="text" placeholder="Search templates..." class="bg-transparent border-none focus:outline-none text-sm font-label w-full text-primary placeholder:text-primary/40">
            </div>

            <!-- Category Filter -->
            <div class="w-48 bg-white rounded-xl border border-primary/10 flex items-center justify-between px-4 h-12 shadow-sm cursor-pointer">
                <span class="text-sm font-label text-primary">Professional</span>
                <span class="material-symbols-outlined text-primary/60">expand_more</span>
            </div>
        </div>

        <!-- Add Button -->
        <button class="bg-[#3e322b] text-white px-6 h-12 rounded-xl flex items-center space-x-2 text-sm font-label hover:bg-opacity-90 transition shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span>Add New Template</span>
        </button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Template 1 -->
        <div class="bg-white rounded-[2rem] p-4 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 flex flex-col group hover:shadow-md transition">
            <!-- Image Area (Gradient Placeholder) -->
            <div class="w-full h-[320px] rounded-[1.5rem] bg-gradient-to-br from-[#1a2322] to-[#121615] flex items-center justify-center overflow-hidden mb-6 relative">
                <!-- Abstract glowing circle -->
                <div class="w-40 h-40 rounded-full border border-[#40e0d0]/30 shadow-[0_0_50px_rgba(64,224,208,0.2)]"></div>
            </div>
            
            <div class="px-2">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-headline font-bold text-primary">The Executive V2</h3>
                    <span class="bg-[#6cf8bb]/20 text-[#006c49] px-2 py-0.5 rounded-md text-[9px] font-label font-bold flex items-center"><span class="material-symbols-outlined text-[12px] mr-1">check_circle</span> Published</span>
                </div>
                <p class="text-[11px] font-label text-primary/60 mb-4">Category: Professional</p>
                
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex items-center text-[10px] font-label text-primary/60">
                        <span class="material-symbols-outlined text-[14px] mr-1">download</span>
                        8.2k Downloads
                    </div>
                    <div class="flex items-center text-[10px] font-label text-primary/60">
                        <span class="material-symbols-outlined text-[14px] mr-1">auto_awesome</span>
                        AI Enabled
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button class="flex-1 bg-surface-container-low hover:bg-primary/10 text-primary font-label text-xs font-bold py-3 rounded-xl transition">
                        Edit Code
                    </button>
                    <button class="w-11 h-11 flex items-center justify-center bg-[#fef2f2] text-[#dc2626] rounded-xl hover:bg-[#fee2e2] transition">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Template 2 -->
        <div class="bg-white rounded-[2rem] p-4 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 flex flex-col group hover:shadow-md transition">
            <!-- Image Area (Gradient Placeholder) -->
            <div class="w-full h-[320px] rounded-[1.5rem] bg-gradient-to-b from-[#1c222b] to-[#121418] flex items-center justify-center overflow-hidden mb-6 relative">
            </div>
            
            <div class="px-2">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-headline font-bold text-primary">Minimalist Noir</h3>
                    <span class="bg-[#6cf8bb]/20 text-[#006c49] px-2 py-0.5 rounded-md text-[9px] font-label font-bold flex items-center"><span class="material-symbols-outlined text-[12px] mr-1">check_circle</span> Published</span>
                </div>
                <p class="text-[11px] font-label text-primary/60 mb-4">Category: Creative</p>
                
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex items-center text-[10px] font-label text-primary/60">
                        <span class="material-symbols-outlined text-[14px] mr-1">download</span>
                        15.4k Downloads
                    </div>
                    <div class="flex items-center text-[10px] font-label text-primary/60">
                        <span class="material-symbols-outlined text-[14px] mr-1">auto_awesome</span>
                        AI Enabled
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button class="flex-1 bg-surface-container-low hover:bg-primary/10 text-primary font-label text-xs font-bold py-3 rounded-xl transition">
                        Edit Code
                    </button>
                    <button class="w-11 h-11 flex items-center justify-center bg-[#fef2f2] text-[#dc2626] rounded-xl hover:bg-[#fee2e2] transition">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Template 3 -->
        <div class="bg-white rounded-[2rem] p-4 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 flex flex-col group hover:shadow-md transition">
            <!-- Image Area (Gradient Placeholder) -->
            <div class="w-full h-[320px] rounded-[1.5rem] bg-[#0c0c0c] flex items-center justify-center overflow-hidden mb-6 relative">
                <!-- Abstract large shapes -->
                <div class="absolute w-64 h-64 bg-white/5 rounded-full -top-10 -right-10 blur-xl"></div>
                <div class="absolute w-40 h-40 bg-white/5 rounded-full bottom-0 left-0 blur-xl"></div>
            </div>
            
            <div class="px-2">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-headline font-bold text-primary">The Scholar Alpha</h3>
                    <span class="border border-primary/20 text-primary/60 px-2 py-0.5 rounded-md text-[9px] font-label font-bold flex items-center">Draft</span>
                </div>
                <p class="text-[11px] font-label text-primary/60 mb-4">Category: Academic</p>
                
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex items-center text-[10px] font-label text-primary/60">
                        <span class="material-symbols-outlined text-[14px] mr-1">download</span>
                        0 Downloads
                    </div>
                    <div class="flex items-center text-[10px] font-label text-primary/60">
                        <span class="material-symbols-outlined text-[14px] mr-1">build</span>
                        Beta
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button class="flex-1 bg-surface-container-low hover:bg-primary/10 text-primary font-label text-xs font-bold py-3 rounded-xl transition">
                        Edit Code
                    </button>
                    <button class="w-11 h-11 flex items-center justify-center bg-[#fef2f2] text-[#dc2626] rounded-xl hover:bg-[#fee2e2] transition">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Badge -->
    <div class="fixed bottom-10 left-80 bg-[#6cf8bb] text-[#006c49] px-4 py-2.5 rounded-full flex items-center space-x-2 shadow-lg z-50 text-[10px] font-label font-bold tracking-wide">
        <span class="material-symbols-outlined text-[16px]">auto_awesome</span>
        <span>AI OPTIMIZATION ENGINE ACTIVE</span>
    </div>

</div>
@endsection
