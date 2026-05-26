@extends('layouts.admin.app')

@section('title', 'Admin Dashboard - Resumify')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">Total Revenue</h3>
            <div class="text-3xl font-headline text-primary mb-4">Rp 30.8M</div>
            <div class="flex items-center text-sm font-label text-secondary">
                <span class="material-symbols-outlined text-[16px] mr-1">trending_up</span>
                <span>+12%</span>
                <span class="text-primary/40 ml-2">vs last month</span>
            </div>
        </div>

        <!-- AI API Costs -->
        <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">AI API Costs</h3>
            <div class="flex items-end mb-4">
                <span class="text-3xl font-headline text-primary mr-2">$142.50</span>
                <span class="text-sm font-label text-primary/40 mb-1">spent</span>
            </div>
            <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden mb-2">
                <div class="bg-primary h-full w-[65%] rounded-full"></div>
            </div>
            <p class="text-[10px] font-label text-primary/40">65% of monthly limit used</p>
        </div>

        <!-- Active Support Tickets -->
        <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">Active Support Tickets</h3>
            <div class="text-3xl font-headline text-primary mb-4">14</div>
            <div class="flex items-center">
                <div class="flex -space-x-2 mr-3">
                    <img class="w-6 h-6 rounded-full border-2 border-white object-cover" src="{{ asset('images/nion.jpg') }}" onerror="this.src='https://ui-avatars.com/api/?name=Agent+1&background=fcdccb&color=4f3b2f'" />
                    <img class="w-6 h-6 rounded-full border-2 border-white object-cover" src="{{ asset('images/nion.jpg') }}" onerror="this.src='https://ui-avatars.com/api/?name=Agent+2&background=fcdccb&color=4f3b2f'" />
                </div>
                <span class="text-[10px] font-label text-primary/40">assigned to 2 agents</span>
            </div>
        </div>

        <!-- System Health -->
        <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-secondary uppercase tracking-widest mb-4">System Health</h3>
            <div class="flex items-center text-3xl font-headline text-primary mb-4">
                <div class="w-3 h-3 bg-secondary rounded-full mr-3 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></div>
                Optimal
            </div>
            <p class="text-[10px] font-label text-secondary leading-tight">All AI nodes performing at 99.9% uptime</p>
        </div>
    </div>

    <!-- Middle Row: Chart & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Chart Area -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-xl font-headline font-bold text-primary">Traffic vs AI Usage</h2>
                <div class="flex space-x-4">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-[#4f3b2f] mr-2"></div>
                        <span class="text-[10px] font-label font-bold text-primary">Direct Traffic</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-[#10b981] mr-2"></div>
                        <span class="text-[10px] font-label font-bold text-primary">AI Optimization Requests</span>
                    </div>
                </div>
            </div>

            <!-- Chart Placeholder (SVG) -->
            <div class="relative h-[250px] w-full">
                <!-- Grid Lines -->
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                    <div class="h-px w-full bg-primary/5"></div>
                    <div class="h-px w-full bg-primary/5"></div>
                    <div class="h-px w-full bg-primary/5"></div>
                    <div class="h-px w-full bg-primary/5"></div>
                </div>
                
                <!-- SVG Curves -->
                <svg class="absolute inset-0 h-full w-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                    <!-- Direct Traffic (Brown) -->
                    <path d="M0,80 C20,70 30,85 50,75 C70,60 80,40 100,65" fill="none" stroke="#4f3b2f" stroke-width="2" class="opacity-90"/>
                    <!-- AI Usage (Green) -->
                    <path d="M0,85 C25,80 35,90 55,75 C75,55 85,55 100,60" fill="none" stroke="#10b981" stroke-width="2" class="opacity-90"/>
                </svg>

                <!-- X Axis Labels -->
                <div class="absolute bottom-0 w-full flex justify-between text-[10px] font-label text-primary/30 mt-4 translate-y-6">
                    <span>MON</span><span>TUE</span><span>WED</span><span>THU</span><span>FRI</span><span>SAT</span><span>SUN</span>
                </div>
            </div>
            <div class="h-6"></div> <!-- Spacer for labels -->
        </div>

        <!-- Quick Actions -->
        <div class="bg-surface-container-low rounded-3xl p-8 border border-primary/5">
            <h2 class="text-xl font-headline text-primary mb-6">Quick Actions</h2>
            <div class="space-y-4 mb-8">
                <button class="w-full bg-white rounded-xl p-4 flex items-center justify-between hover:shadow-md transition-shadow group">
                    <div class="flex items-center space-x-3 text-primary">
                        <span class="material-symbols-outlined text-[20px]">description</span>
                        <span class="text-sm font-label font-bold">Generate Report</span>
                    </div>
                    <span class="material-symbols-outlined text-primary/40 group-hover:text-primary transition-colors text-[20px]">chevron_right</span>
                </button>
                <button class="w-full bg-white rounded-xl p-4 flex items-center justify-between hover:shadow-md transition-shadow group">
                    <div class="flex items-center space-x-3 text-primary">
                        <span class="material-symbols-outlined text-[20px]">auto_fix_high</span>
                        <span class="text-sm font-label font-bold">Update AI Model</span>
                    </div>
                    <span class="material-symbols-outlined text-primary/40 group-hover:text-primary transition-colors text-[20px]">chevron_right</span>
                </button>
                <button class="w-full bg-white rounded-xl p-4 flex items-center justify-between hover:shadow-md transition-shadow group">
                    <div class="flex items-center space-x-3 text-primary">
                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        <span class="text-sm font-label font-bold">Add New Template</span>
                    </div>
                    <span class="material-symbols-outlined text-primary/40 group-hover:text-primary transition-colors text-[20px]">chevron_right</span>
                </button>
            </div>
            <div class="bg-white p-5 rounded-xl text-[11px] font-headline text-primary/60 italic leading-relaxed border border-primary/5">
                "Design is not just what it looks like and feels like. Design is how it works."
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-10">
        <!-- User Activity -->
        <div class="bg-white rounded-3xl p-8 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h2 class="text-lg font-headline text-primary mb-6">User Activity</h2>
            <div class="space-y-6">
                <!-- Item 1 -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-primary font-bold text-sm">AS</div>
                        <div>
                            <p class="text-sm font-label font-bold text-primary">Adi Saputra</p>
                            <p class="text-[10px] font-label text-primary/40">Jakarta, ID</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-label text-secondary">Active Now</span>
                </div>
                <!-- Item 2 -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-primary font-bold text-sm">RM</div>
                        <div>
                            <p class="text-sm font-label font-bold text-primary">Rina Melati</p>
                            <p class="text-[10px] font-label text-primary/40">Surabaya, ID</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-label text-primary/40">2m ago</span>
                </div>
                <!-- Item 3 -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-primary font-bold text-sm">B</div>
                        <div>
                            <p class="text-sm font-label font-bold text-primary">Budi J.</p>
                            <p class="text-[10px] font-label text-primary/40">Bandung, ID</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-label text-primary/40">15m ago</span>
                </div>
            </div>
        </div>

        <!-- Support Queue -->
        <div class="bg-white rounded-3xl p-8 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h2 class="text-lg font-headline text-primary mb-6">Support Queue</h2>
            <div class="space-y-4">
                <!-- Ticket 1 -->
                <div class="bg-surface p-4 rounded-xl relative border border-primary/5">
                    <div class="absolute -right-2 -top-2 bg-[#dc2626] text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-sm z-10">3 Pending</div>
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-xs font-label font-bold text-primary">Payment Issue</p>
                        <span class="text-[9px] text-primary/40 text-right leading-tight">Just<br>now</span>
                    </div>
                    <p class="text-[11px] font-label text-primary/60 truncate">"Why was my subscription charged twice..."</p>
                </div>
                <!-- Ticket 2 -->
                <div class="bg-surface p-4 rounded-xl border border-primary/5">
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-xs font-label font-bold text-primary">AI Hallucination</p>
                        <span class="text-[9px] text-primary/40 text-right">5m ago</span>
                    </div>
                    <p class="text-[11px] font-label text-primary/60 truncate">"The AI keeps adding fake experience to my..."</p>
                </div>
                <!-- Ticket 3 -->
                <div class="bg-surface p-4 rounded-xl border border-primary/5">
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-xs font-label font-bold text-primary">Template Export</p>
                        <span class="text-[9px] text-primary/40 text-right">12m ago</span>
                    </div>
                    <p class="text-[11px] font-label text-primary/60 truncate">"PDF layout is broken when exporting in..."</p>
                </div>
            </div>
        </div>

        <!-- Template Performance -->
        <div class="bg-white rounded-3xl p-8 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h2 class="text-lg font-headline text-primary mb-6">Template Performance</h2>
            <div class="space-y-6">
                <!-- Item 1 -->
                <div>
                    <div class="flex justify-between text-[10px] font-label uppercase tracking-widest font-bold mb-2">
                        <span class="text-primary">THE EXECUTIVE</span>
                        <span class="text-primary/60 text-[9px]">8.2k downloads</span>
                    </div>
                    <div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
                        <div class="bg-primary h-full w-[85%] rounded-full"></div>
                    </div>
                </div>
                <!-- Item 2 -->
                <div>
                    <div class="flex justify-between text-[10px] font-label uppercase tracking-widest font-bold mb-2">
                        <span class="text-primary">MINIMALIST NOIR</span>
                        <span class="text-primary/60 text-[9px]">6.1k downloads</span>
                    </div>
                    <div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
                        <div class="bg-primary h-full w-[65%] rounded-full"></div>
                    </div>
                </div>
                <!-- Item 3 -->
                <div>
                    <div class="flex justify-between text-[10px] font-label uppercase tracking-widest font-bold mb-2">
                        <span class="text-primary">THE ACADEMIC</span>
                        <span class="text-primary/60 text-[9px]">4.4k downloads</span>
                    </div>
                    <div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
                        <div class="bg-primary h-full w-[45%] rounded-full"></div>
                    </div>
                </div>
                <!-- Item 4 -->
                <div>
                    <div class="flex justify-between text-[10px] font-label uppercase tracking-widest font-bold mb-2">
                        <span class="text-primary">CREATIVE SPARK</span>
                        <span class="text-primary/60 text-[9px]">3.8k downloads</span>
                    </div>
                    <div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
                        <div class="bg-primary h-full w-[35%] rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
