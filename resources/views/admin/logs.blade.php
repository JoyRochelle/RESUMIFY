@extends('layouts.admin.app')

@section('title', 'System & Finance Logs - Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 relative pb-20">

    <!-- Header Section -->
    <div>
        <h1 class="text-3xl font-headline font-bold text-primary mb-2">System & Finance Logs</h1>
        <div class="flex items-center text-[10px] font-label text-primary/40 uppercase tracking-widest font-bold">
            <div class="w-2 h-2 rounded-full bg-secondary mr-2"></div>
            REAL-TIME INFRASTRUCTURE OVERSIGHT
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- API Billing -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <div class="flex justify-between items-start mb-4">
                <span class="material-symbols-outlined text-[#dc2626] text-[20px]">account_balance_wallet</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">MONTH-TO-DATE</span>
            </div>
            <p class="text-xs font-label text-primary/60 mb-1">Estimated API Billing (MTD)</p>
            <div class="text-3xl font-headline text-[#dc2626] mb-4">$142.50</div>
            <div class="flex items-center text-[10px] font-label text-[#dc2626]">
                <span class="material-symbols-outlined text-[14px] mr-1">trending_up</span>
                <span>Above average threshold (+12%)</span>
            </div>
        </div>

        <!-- Average Token -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <span class="material-symbols-outlined text-primary text-[20px]">toll</span>
                    <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">EFFICIENCY RATIO</span>
                </div>
                <p class="text-xs font-label text-primary/60 mb-1">Average Token/User</p>
                <div class="text-3xl font-headline text-primary mb-4">1.2k tokens</div>
            </div>
            <div class="flex space-x-1 h-1 w-full">
                <div class="h-full w-1/5 bg-primary rounded-full"></div>
                <div class="h-full w-1/5 bg-primary rounded-full"></div>
                <div class="h-full w-1/5 bg-surface-container-low rounded-full"></div>
                <div class="h-full w-1/5 bg-surface-container-low rounded-full"></div>
                <div class="h-full w-1/5 bg-surface-container-low rounded-full"></div>
            </div>
        </div>

        <!-- Profit Margin -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <div class="flex justify-between items-start mb-4">
                <span class="material-symbols-outlined text-secondary text-[20px]">bar_chart</span>
                <span class="text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">GROSS REVENUE</span>
            </div>
            <p class="text-xs font-label text-primary/60 mb-1">Profit Margin</p>
            <div class="text-3xl font-headline text-secondary mb-4">+68%</div>
            <div class="flex items-center text-[10px] font-label text-secondary">
                <span class="material-symbols-outlined text-[14px] mr-1">trending_up</span>
                <span>Optimized infrastructure costs</span>
            </div>
        </div>
    </div>

    <!-- AI Usage Logs -->
    <div class="bg-white rounded-3xl shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 overflow-hidden">
        <div class="p-6 border-b border-primary/5 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-[#6cf8bb]/10 flex items-center justify-center text-[#006c49]">
                    <span class="material-symbols-outlined">auto_awesome</span>
                </div>
                <div>
                    <h2 class="text-lg font-headline font-bold text-primary">AI Usage Logs</h2>
                    <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest font-bold">LLM INTERACTION RECORDS</p>
                </div>
            </div>
            <button class="text-secondary font-label text-sm font-bold hover:underline">
                Export CSV
            </button>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface">
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">TIMESTAMP</th>
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">USER</th>
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">FEATURE</th>
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold text-right">TOKENS USED</th>
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">MODEL</th>
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold text-right">COST (USD)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/5">
                <tr class="hover:bg-surface/50 transition text-xs font-label">
                    <td class="py-4 px-6 text-primary/60 font-headline">2023-11-24 14:22:01</td>
                    <td class="py-4 px-6 font-bold text-primary">andreas.h@domain.com</td>
                    <td class="py-4 px-6">
                        <span class="bg-[#6cf8bb] text-[#006c49] text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">AIPOLISH</span>
                    </td>
                    <td class="py-4 px-6 text-right font-headline text-primary">842</td>
                    <td class="py-4 px-6 text-primary/60">GPT-4o</td>
                    <td class="py-4 px-6 text-right font-headline text-primary font-bold">$0.0126</td>
                </tr>
                <tr class="hover:bg-surface/50 transition text-xs font-label">
                    <td class="py-4 px-6 text-primary/60 font-headline">2023-11-24 14:18:45</td>
                    <td class="py-4 px-6 font-bold text-primary">sarah.clarke@web.id</td>
                    <td class="py-4 px-6">
                        <span class="bg-[#4f3b2f] text-white text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">ATS ANALYSIS</span>
                    </td>
                    <td class="py-4 px-6 text-right font-headline text-primary">1,420</td>
                    <td class="py-4 px-6 text-primary/60">Gemini Pro</td>
                    <td class="py-4 px-6 text-right font-headline text-primary font-bold">$0.0071</td>
                </tr>
                <tr class="hover:bg-surface/50 transition text-xs font-label">
                    <td class="py-4 px-6 text-primary/60 font-headline">2023-11-24 14:15:12</td>
                    <td class="py-4 px-6 font-bold text-primary">m.yusuf_99@provider.net</td>
                    <td class="py-4 px-6">
                        <span class="bg-[#6cf8bb] text-[#006c49] text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">AIPOLISH</span>
                    </td>
                    <td class="py-4 px-6 text-right font-headline text-primary">612</td>
                    <td class="py-4 px-6 text-primary/60">GPT-4o</td>
                    <td class="py-4 px-6 text-right font-headline text-primary font-bold">$0.0092</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-3xl shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 overflow-hidden">
        <div class="p-6 border-b border-primary/5 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <div>
                    <h2 class="text-lg font-headline font-bold text-primary">Transaction History</h2>
                    <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest font-bold">USER BILLING & GATEWAYS</p>
                </div>
            </div>
            <button class="bg-surface-container-low text-primary px-4 py-2 rounded-xl text-[10px] font-label font-bold uppercase tracking-widest hover:bg-primary/10 transition">
                FILTER
            </button>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface">
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">INVOICE ID</th>
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">USER</th>
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">PACKAGE</th>
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold text-right">AMOUNT</th>
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">GATEWAY</th>
                    <th class="py-3 px-6 text-[9px] font-label text-primary/40 uppercase tracking-widest font-bold">STATUS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/5">
                <tr class="hover:bg-surface/50 transition text-xs font-label">
                    <td class="py-4 px-6 text-primary/60 font-headline">#INV-2023-8842</td>
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('images/nion.jpg') }}" class="w-6 h-6 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Clara+Oswald&background=fcdccb&color=4f3b2f'" />
                            <span class="font-bold text-primary">Clara Oswald</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-primary font-bold">Premium Annual</td>
                    <td class="py-4 px-6 text-right font-headline text-primary font-bold">$89.00</td>
                    <td class="py-4 px-6 text-primary/60">Stripe</td>
                    <td class="py-4 px-6">
                        <span class="bg-[#dcfce7] text-[#166534] border border-[#bbf7d0] text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">PAID</span>
                    </td>
                </tr>
                <tr class="hover:bg-surface/50 transition text-xs font-label">
                    <td class="py-4 px-6 text-primary/60 font-headline">#INV-2023-8841</td>
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('images/nion.jpg') }}" class="w-6 h-6 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=James+Wilson&background=fcdccb&color=4f3b2f'" />
                            <span class="font-bold text-primary">James Wilson</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-primary font-bold">Premium Monthly</td>
                    <td class="py-4 px-6 text-right font-headline text-primary font-bold">$12.50</td>
                    <td class="py-4 px-6 text-primary/60">Midtrans</td>
                    <td class="py-4 px-6">
                        <span class="bg-surface-container-low text-primary/60 border border-primary/10 text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">PENDING</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Floating Action Button (AI Assistant) -->
    <button class="fixed bottom-10 right-10 w-14 h-14 bg-[#10b981] rounded-full shadow-lg flex items-center justify-center text-white hover:scale-105 transition-transform z-50">
        <span class="material-symbols-outlined text-3xl">smart_toy</span>
    </button>

</div>
@endsection
