@extends('layouts.admin.app')

@section('title', 'User Management - Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 relative pb-20">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-headline font-bold text-primary mb-2">User Management</h1>
            <p class="text-sm font-label text-primary/60">Manage access, subscriptions, and AI performance for all users.</p>
        </div>
        <button class="bg-[#3e322b] text-white px-6 py-3 rounded-xl flex items-center space-x-2 text-sm font-label hover:bg-opacity-90 transition shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span>Add New User</span>
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">TOTAL USERS</h3>
            <div class="flex items-end">
                <span class="text-3xl font-headline text-primary mr-3">1,240</span>
                <span class="text-xs font-label text-secondary font-bold mb-1">+2.4%</span>
            </div>
        </div>

        <!-- Premium Users -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">PREMIUM USERS</h3>
            <div class="flex items-end">
                <span class="text-3xl font-headline text-primary mr-3">312</span>
                <span class="text-xs font-label text-primary/40 mb-1">25% of total</span>
            </div>
        </div>

        <!-- New Users -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">NEW (24H)</h3>
            <div class="flex items-end">
                <span class="text-3xl font-headline text-secondary mr-3">+15</span>
                <span class="text-xs font-label text-primary/40 mb-1">registrations today</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="flex items-center space-x-4">
        <!-- Search Input -->
        <div class="flex-1 bg-white rounded-xl border border-primary/10 flex items-center px-4 h-12 shadow-sm focus-within:border-primary/30 transition-colors">
            <span class="material-symbols-outlined text-primary/40 mr-3">search</span>
            <input type="text" placeholder="Search by name or email..." class="bg-transparent border-none focus:outline-none text-sm font-label w-full text-primary placeholder:text-primary/40">
        </div>

        <!-- Status Filter -->
        <div class="w-48 bg-white rounded-xl border border-primary/10 flex items-center justify-between px-4 h-12 shadow-sm cursor-pointer">
            <span class="text-sm font-label text-primary">All Status</span>
            <span class="material-symbols-outlined text-primary/60">expand_more</span>
        </div>

        <!-- Filter Button -->
        <button class="bg-white border border-primary/10 px-6 h-12 rounded-xl flex items-center space-x-2 text-sm font-label text-primary shadow-sm hover:bg-surface transition">
            <span class="material-symbols-outlined text-[20px]">filter_list</span>
            <span>Filter</span>
        </button>
    </div>

    <!-- User Table -->
    <div class="bg-white rounded-3xl shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-primary/5">
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest font-bold">USER</th>
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest font-bold">PLAN</th>
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest font-bold">AI CREDIT</th>
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest font-bold">JOINED DATE</th>
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest font-bold">STATUS</th>
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest font-bold text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/5">
                <!-- User 1 -->
                <tr class="hover:bg-surface/50 transition">
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('images/nion.jpg') }}" class="w-10 h-10 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Amara+Jasmine&background=fcdccb&color=4f3b2f'" />
                            <div>
                                <p class="text-sm font-label font-bold text-primary">Amara Jasmine</p>
                                <p class="text-xs font-label text-primary/40">amara.j@email.com</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="bg-[#10b981] text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">PREMIUM</span>
                    </td>
                    <td class="py-4 px-6 w-48">
                        <div class="flex justify-between text-[10px] font-label mb-1">
                            <span class="text-primary font-bold">45/50</span>
                            <span class="text-primary/40">90%</span>
                        </div>
                        <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden">
                            <div class="bg-[#4f3b2f] h-full w-[90%] rounded-full"></div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm font-headline italic text-primary/60">
                        12 Oct 2023
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full bg-secondary"></div>
                            <span class="text-xs font-label text-primary">Online</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <!-- Actions empty per mockup -->
                    </td>
                </tr>

                <!-- User 2 -->
                <tr class="hover:bg-surface/50 transition">
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('images/nion.jpg') }}" class="w-10 h-10 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Budi+Santoso&background=fcdccb&color=4f3b2f'" />
                            <div>
                                <p class="text-sm font-label font-bold text-primary">Budi Santoso</p>
                                <p class="text-xs font-label text-primary/40">budi.san@email.com</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="bg-surface-container-low text-primary/60 border border-primary/10 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">FREE</span>
                    </td>
                    <td class="py-4 px-6 w-48">
                        <div class="flex justify-between text-[10px] font-label mb-1">
                            <span class="text-primary font-bold">2/5</span>
                            <span class="text-primary/40">40%</span>
                        </div>
                        <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden">
                            <div class="bg-[#4f3b2f] h-full w-[40%] rounded-full"></div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm font-headline italic text-primary/60">
                        15 Oct 2023
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full bg-primary/20"></div>
                            <span class="text-xs font-label text-primary/60">Offline</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-right">
                    </td>
                </tr>

                <!-- User 3 -->
                <tr class="hover:bg-surface/50 transition">
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('images/nion.jpg') }}" class="w-10 h-10 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Citra+Lestari&background=fcdccb&color=4f3b2f'" />
                            <div>
                                <p class="text-sm font-label font-bold text-primary">Citra Lestari</p>
                                <p class="text-xs font-label text-primary/40">citra.les@email.com</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="bg-[#10b981] text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">PREMIUM</span>
                    </td>
                    <td class="py-4 px-6 w-48">
                        <div class="flex justify-between text-[10px] font-label mb-1">
                            <span class="text-primary font-bold">48/50</span>
                            <span class="text-primary/40">96%</span>
                        </div>
                        <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden">
                            <div class="bg-[#4f3b2f] h-full w-[96%] rounded-full"></div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm font-headline italic text-primary/60">
                        18 Oct 2023
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full bg-secondary"></div>
                            <span class="text-xs font-label text-primary">Online</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-right">
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-6 border-t border-primary/5 flex items-center justify-between text-sm font-label">
            <div class="text-primary/60">
                Showing <span class="font-bold text-primary">1-10</span> of <span class="font-bold text-primary">1,240</span> users
            </div>
            <div class="flex items-center space-x-2">
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-primary/40 hover:bg-surface transition">
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-primary font-bold bg-surface">1</button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-primary hover:bg-surface transition font-bold">2</button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-primary hover:bg-surface transition font-bold">3</button>
                <span class="text-primary/40">...</span>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-primary hover:bg-surface transition font-bold">124</button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-primary hover:bg-surface transition">
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <button class="fixed bottom-10 right-10 w-14 h-14 bg-[#10b981] rounded-full shadow-lg flex items-center justify-center text-white hover:scale-105 transition-transform z-50">
        <span class="material-symbols-outlined text-3xl">auto_awesome</span>
    </button>
    
    <!-- Bottom Centered Text Placeholder (MANUSCRIPT DESIGN SYSTEM V2.0) -->
    <div class="mt-16 text-center text-[10px] font-label text-primary/30 uppercase tracking-[0.2em] flex items-center justify-center space-x-4">
        <div class="w-16 h-px bg-primary/10"></div>
        <span>MANUSCRIPT DESIGN SYSTEM V2.0</span>
        <div class="w-16 h-px bg-primary/10"></div>
    </div>

</div>
@endsection
