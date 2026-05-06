@extends('layouts.admin.app')

@section('title', 'Support Center - Admin Dashboard')

@section('content')
<div class="flex h-[calc(100vh-120px)] -mt-4 -mb-4">
    <!-- Left Inbox Panel -->
    <div class="w-80 md:w-96 bg-white rounded-3xl border border-primary/5 flex flex-col shadow-[0_2px_10px_rgba(79,59,47,0.03)] z-10 overflow-hidden">
        <div class="p-6 border-b border-primary/5">
            <h2 class="text-xl font-headline font-bold text-primary mb-6">Support Center Inbox</h2>
            
            <div class="relative mb-6">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-primary/40 text-[18px]">search</span>
                <input type="text" placeholder="Search tickets or users..." class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low border-none rounded-xl text-sm font-label focus:outline-none text-primary placeholder:text-primary/40">
            </div>

            <div class="flex space-x-4">
                <button class="bg-[#3e322b] text-white px-5 py-1.5 rounded-full text-xs font-label">All</button>
                <button class="text-primary/60 hover:bg-surface-container-low px-4 py-1.5 rounded-full text-xs font-label transition">Pending</button>
                <button class="text-primary/60 hover:bg-surface-container-low px-4 py-1.5 rounded-full text-xs font-label transition">Solved</button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <!-- Ticket 1 -->
            <div onclick="openChat('Eleanor Vance', 'Payment Issue', 'https://ui-avatars.com/api/?name=Eleanor+Vance&background=fcdccb&color=4f3b2f')" class="p-6 border-b border-primary/5 hover:bg-surface-container-low transition cursor-pointer">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/nion.jpg') }}" class="w-10 h-10 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Eleanor+Vance&background=fcdccb&color=4f3b2f'" />
                        <div>
                            <p class="text-sm font-label font-bold text-primary">Eleanor Vance</p>
                            <div class="flex items-center space-x-2">
                                <p class="text-[10px] font-label font-bold text-primary/80">Payment Issue</p>
                                <div class="w-1.5 h-1.5 rounded-full bg-[#dc2626]"></div>
                            </div>
                        </div>
                    </div>
                    <span class="text-[9px] text-primary/40">2m ago</span>
                </div>
                <p class="text-xs font-label text-primary/60 truncate pl-13">I'm having trouble with the premium billing...</p>
            </div>

            <!-- Ticket 2 -->
            <div onclick="openChat('Marcus Kane', 'AI Error', 'https://ui-avatars.com/api/?name=Marcus+Kane&background=fcdccb&color=4f3b2f')" class="p-6 border-b border-primary/5 hover:bg-surface-container-low transition cursor-pointer">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/nion.jpg') }}" class="w-10 h-10 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Marcus+Kane&background=fcdccb&color=4f3b2f'" />
                        <div>
                            <p class="text-sm font-label font-bold text-primary">Marcus Kane</p>
                            <div class="flex items-center space-x-2">
                                <p class="text-[10px] font-label font-bold text-primary/80">AI Error</p>
                                <div class="w-1.5 h-1.5 rounded-full bg-secondary"></div>
                            </div>
                        </div>
                    </div>
                    <span class="text-[9px] text-primary/40">15m ago</span>
                </div>
                <p class="text-xs font-label text-primary/60 truncate pl-13">The bullet point generator keeps freezing on...</p>
            </div>

            <!-- Ticket 3 -->
            <div onclick="openChat('Sarah Lin', 'Resume Download', 'https://ui-avatars.com/api/?name=Sarah+Lin&background=fcdccb&color=4f3b2f')" class="p-6 border-b border-primary/5 hover:bg-surface-container-low transition cursor-pointer">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/nion.jpg') }}" class="w-10 h-10 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Sarah+Lin&background=fcdccb&color=4f3b2f'" />
                        <div>
                            <p class="text-sm font-label font-bold text-primary">Sarah Lin</p>
                            <div class="flex items-center space-x-2">
                                <p class="text-[10px] font-label font-bold text-primary/80">Resume Download</p>
                                <div class="w-1.5 h-1.5 rounded-full bg-secondary"></div>
                            </div>
                        </div>
                    </div>
                    <span class="text-[9px] text-primary/40">1h ago</span>
                </div>
                <p class="text-xs font-label text-primary/60 truncate pl-13">How do I export my resume to LaTeX format?</p>
            </div>

            <!-- Ticket 4 -->
            <div onclick="openChat('Julian Thorne', 'Login Issues', 'https://ui-avatars.com/api/?name=Julian+Thorne&background=fcdccb&color=4f3b2f')" class="p-6 hover:bg-surface-container-low transition cursor-pointer">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/nion.jpg') }}" class="w-10 h-10 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Julian+Thorne&background=fcdccb&color=4f3b2f'" />
                        <div>
                            <p class="text-sm font-label font-bold text-primary">Julian Thorne</p>
                            <div class="flex items-center space-x-2">
                                <p class="text-[10px] font-label font-bold text-primary/80">Login Issues</p>
                                <div class="w-1.5 h-1.5 rounded-full bg-[#dc2626]"></div>
                            </div>
                        </div>
                    </div>
                    <span class="text-[9px] text-primary/40">3h ago</span>
                </div>
                <p class="text-xs font-label text-primary/60 truncate pl-13">Locked out of my account after three attempts...</p>
            </div>
        </div>
    </div>

    <!-- Right Content Panel -->
    <div class="flex-1 flex flex-col pl-6">
        <!-- Placeholder -->
        <div id="chat-placeholder" class="flex-1 flex flex-col items-center justify-center bg-white rounded-3xl border border-primary/5 shadow-[0_2px_10px_rgba(79,59,47,0.03)]">
            <span class="material-symbols-outlined text-6xl text-primary/10 mb-4">forum</span>
            <p class="text-lg font-headline text-primary/40">Select a conversation to view</p>
        </div>

        <!-- Chat View -->
        <div id="chat-view" class="hidden flex-1 flex-col bg-white rounded-3xl border border-primary/5 shadow-[0_2px_10px_rgba(79,59,47,0.03)] overflow-hidden">
            
            <!-- Chat Header -->
            <div class="p-6 border-b border-primary/5 flex justify-between items-center bg-surface/30">
                <div class="flex items-center space-x-4">
                    <img id="chat-avatar" src="" class="w-12 h-12 rounded-full object-cover" />
                    <div>
                        <h3 id="chat-name" class="text-lg font-headline text-primary">Name</h3>
                        <p id="chat-issue" class="text-xs font-label text-primary/60">Issue Type</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-primary/60 hover:text-primary transition">
                        <span class="material-symbols-outlined text-xl">more_vert</span>
                    </button>
                    <button class="bg-secondary/10 text-secondary px-5 py-2 rounded-full text-sm font-label font-bold flex items-center hover:bg-secondary hover:text-white transition">
                        <span class="material-symbols-outlined text-sm mr-2">check_circle</span>
                        Mark Resolved
                    </button>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-surface/10 custom-scrollbar">
                <!-- User Message -->
                <div class="flex justify-start">
                    <div class="max-w-[70%] bg-surface-container-low rounded-2xl rounded-tl-sm p-4 text-sm font-body text-primary">
                        <p id="chat-message-content">Hello, I need help with my account.</p>
                        <span class="text-[10px] text-primary/40 mt-2 block">Today, 10:42 AM</span>
                    </div>
                </div>

                <!-- Admin Reply (Placeholder Example) -->
                <div class="flex justify-end">
                    <div class="max-w-[70%] bg-primary text-white rounded-2xl rounded-tr-sm p-4 text-sm font-body">
                        <p>Hi there! I'm looking into this for you right now. Could you please provide your transaction ID?</p>
                        <span class="text-[10px] text-white/50 mt-2 block text-right">Today, 10:45 AM</span>
                    </div>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="p-6 border-t border-primary/5 bg-white">
                <div class="flex items-center space-x-4 bg-surface-container-low p-2 pr-4 rounded-2xl">
                    <button class="w-10 h-10 rounded-full flex items-center justify-center text-primary/40 hover:text-primary transition">
                        <span class="material-symbols-outlined text-xl">attach_file</span>
                    </button>
                    <input type="text" placeholder="Type your reply here..." class="flex-1 bg-transparent border-none focus:outline-none text-sm font-body text-primary placeholder:text-primary/40">
                    <button class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white hover:bg-[#3e322b] transition">
                        <span class="material-symbols-outlined text-lg">send</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function openChat(name, issue, avatarUrl) {
        document.getElementById('chat-placeholder').classList.add('hidden');
        document.getElementById('chat-view').classList.remove('hidden');
        document.getElementById('chat-view').classList.add('flex');
        
        document.getElementById('chat-name').innerText = name;
        document.getElementById('chat-issue').innerText = issue;
        
        // Use default avatar logic if the user's image fails or is nion.jpg
        document.getElementById('chat-avatar').src = avatarUrl;
        
        // Mock message content based on the name
        let message = "";
        if(name === "Eleanor Vance") message = "I'm having trouble with the premium billing. My card was charged twice.";
        else if(name === "Marcus Kane") message = "The bullet point generator keeps freezing on the skills section.";
        else if(name === "Sarah Lin") message = "How do I export my resume to LaTeX format? I can only find PDF.";
        else message = "Locked out of my account after three attempts. Can you reset my password?";
        
        document.getElementById('chat-message-content').innerText = message;
    }
</script>
@endsection
