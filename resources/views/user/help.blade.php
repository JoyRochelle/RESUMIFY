<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Help Center - Resumify</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface font-body text-primary overflow-hidden h-screen flex">
    
    @include('layouts.user.sidenavbar')

    <main class="flex-1 overflow-y-auto custom-scrollbar bg-primary/5 pb-20 md:pb-0">
        
        <div class="max-w-5xl mx-auto px-6 md:px-12 py-16">
            
            {{-- Hero Section --}}
            <section class="text-center mb-20">
                <h2 class="font-headline text-4xl md:text-6xl text-primary mb-6 tracking-tight">Help Center</h2>
                <p class="font-body text-primary/60 text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
                    Learn how to optimize your resume with artificial intelligence. We are here to help you tell your story better.
                </p>
                <div class="relative max-w-2xl mx-auto">
                    <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-primary/60">search</span>
                    </div>
                    <input class="w-full py-5 pl-16 pr-6 bg-tertiary rounded-xl shadow-[0_16px_32px_-12px_rgba(29,27,25,0.05)] border border-primary/10 focus:ring-2 focus:ring-primary/10 text-primary placeholder:text-primary/50 text-lg outline-none" placeholder="Search for solutions or ask a question..." type="text"/>
                </div>
            </section>

            {{-- Help Category Cards --}}
            <section class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-24">
                <x-user.help-card icon="person" title="Account & Subscription" description="Manage your profile, payment settings, and premium subscription plans." />
                <x-user.help-card icon="auto_fix_high" title="Editor & AI Assistant" description="Guides on using our smart features to polish your career narrative." variant="accent" :iconFilled="true" />
                <x-user.help-card icon="shield" title="Security & PDF" description="Information about data encryption and technical high-quality document export." />
            </section>

            {{-- FAQ Section --}}
            <section class="mb-24">
                <h3 class="font-headline text-3xl text-primary mb-10 text-center">Popular Questions</h3>
                <div class="space-y-4 max-w-3xl mx-auto">
                    <x-user.faq-item question="How do I improve my ATS score?" />
                    <x-user.faq-item question="How many times can I use AI Polish?" />
                    <x-user.faq-item question="Is my data secure?" />
                </div>
            </section>

            {{-- Contact CTA --}}
            <section class="relative overflow-hidden bg-tertiary rounded-2xl p-10 md:p-16 text-center border border-primary/10 shadow-sm">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-secondary/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
                
                <div class="relative z-10">
                    <h3 class="font-headline text-3xl md:text-4xl text-primary mb-4 italic">✨ Still need help?</h3>
                    <p class="text-primary/60 mb-10 max-w-lg mx-auto">Our team (and our smart assistants) are ready to answer your questions anytime.</p>
                    <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                        <x-user.button variant="pill" icon="forum" iconClass="text-xl icon-filled" class="px-8 py-4 rounded-xl text-sm">Chat with Admin</x-user.button>
                    </div>
                </div>
            </section>
        </div>

        <footer class="mt-12 pb-12 text-center text-primary/40 text-sm">
            <p>© 2026 Resumify - Curated with Integrity</p>
        </footer>
    </main>

    @include('layouts.user.mobilenavbar')
    
</body>
</html>