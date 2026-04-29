<html class="light" lang="en">

<head></head>

<body class="bg-surface text-on-surface font-body selection:bg-secondary-fixed selection:text-on-secondary-fixed">

    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Resumify | The Curated Manuscript</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&amp;family=Manrope:wght@200..800&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-tertiary-fixed-variant": "#454747",
                        "on-background": "#1d1b19",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "outline-variant": "#d2c4bc",
                        "on-error": "#ffffff",
                        "secondary-container": "#6cf8bb",
                        "inverse-primary": "#dec1b0",
                        "on-primary-fixed": "#28180e",
                        "on-secondary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-high": "#ece7e3",
                        "outline": "#81756e",
                        "primary-container": "#4f3b2f",
                        "on-tertiary": "#ffffff",
                        "inverse-on-surface": "#f5f0ec",
                        "primary-fixed": "#fcdccb",
                        "tertiary": "#282a2a",
                        "surface": "#fdf8f4",
                        "primary": "#37251b",
                        "surface-container": "#f2ede9",
                        "on-primary-container": "#c1a596",
                        "on-tertiary-container": "#aaabac",
                        "on-primary": "#ffffff",
                        "on-secondary-fixed": "#002113",
                        "surface-container-highest": "#e6e2de",
                        "tertiary-container": "#3e4040",
                        "on-error-container": "#93000a",
                        "on-tertiary-fixed": "#1a1c1c",
                        "tertiary-fixed-dim": "#c6c6c7",
                        "secondary-fixed": "#6ffbbe",
                        "on-surface": "#1d1b19",
                        "on-secondary-container": "#00714d",
                        "background": "#fdf8f4",
                        "surface-dim": "#ded9d5",
                        "surface-bright": "#fdf8f4",
                        "surface-tint": "#705a4c",
                        "secondary-fixed-dim": "#4edea3",
                        "primary-fixed-dim": "#dec1b0",
                        "surface-container-low": "#f8f3ef",
                        "tertiary-fixed": "#e2e2e2",
                        "on-secondary-fixed-variant": "#005236",
                        "secondary": "#10b981",
                        "on-primary-fixed-variant": "#574236",
                        "surface-variant": "#e6e2de",
                        "on-surface-variant": "#4f453f",
                        "inverse-surface": "#32302e"
                    },
                    fontFamily: {
                        "headline": ["Newsreader", "serif"],
                        "body": ["Manrope", "sans-serif"],
                        "label": ["Manrope", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .font-serif {
            font-family: 'Newsreader', serif;
        }

        .font-sans {
            font-family: 'Manrope', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
    <!-- TopNavBar -->
    <nav class="bg-[#fdf8f4] dark:bg-[#1d1b19] border-b border-[#4f3b2f]/10 sticky top-0 z-50">
        <div class="flex justify-between items-center w-full px-8 py-4 max-w-7xl mx-auto">
            <div
                class="text-2xl font-bold text-[#4f3b2f] dark:text-[#f2ede9] flex items-center gap-1 font-serif newsreader tracking-tight">
                Resumify
            </div>
            <div class="hidden md:flex items-center gap-8 font-sans text-sm">
                <a class="text-[#4f3b2f] font-bold border-b-2 border-[#10b981] pb-1 hover:text-[#10b981] transition-colors duration-300"
                    href="#">Features</a>
                <a class="text-[#81756e] dark:text-[#a0948e] hover:text-[#10b981] transition-colors duration-300"
                    href="#">Templates</a>
                <a class="text-[#81756e] dark:text-[#a0948e] hover:text-[#10b981] transition-colors duration-300"
                    href="#">Pricing</a>
            </div>
            <div class="flex items-center gap-6 font-sans text-sm">
                <a href="{{ route('login') }}" class="text-[#4f3b2f] font-semibold hover:text-[#10b981] transition-colors">Login</a>
                <a href="{{ route('register') }}"
                    class="bg-primary text-on-primary px-6 py-2.5 rounded-lg font-bold shadow-sm active:opacity-80 active:scale-95 transition-all">
                    Create Free Resume ✨
                </a>
            </div>
        </div>
    </nav>
    <main>
        <!-- Hero Section: Split 40/60 -->
        <section class="min-h-[870px] flex flex-col md:flex-row overflow-hidden">
            <!-- Left Side: Copy -->
            <div class="w-full md:w-[40%] bg-surface flex items-center px-8 md:px-16 py-20">
                <div class="max-w-md">
                    <h1 class="text-6xl md:text-7xl font-serif text-primary leading-tight tracking-tight mb-8">
                        Write Your Success Story ✨
                    </h1>
                    <p class="text-xl text-outline mb-10 leading-relaxed font-sans">
                        Adapt your resume to job openings with artificial intelligence.
                    </p>
                    <a href="{{ route('register') }}"
                        class="group flex items-center gap-3 bg-primary text-on-primary px-8 py-4 rounded-lg text-lg font-bold transition-all hover:pr-10">
                        Upgrade Your Resume
                        <span
                            class="material-symbols-outlined transition-transform group-hover:translate-x-2">arrow_forward</span>
                    </a>
                </div>
            </div>
            <!-- Right Side: Interactive Area -->
            <div class="w-full md:w-[60%] bg-[#f1f5f9] relative flex items-center justify-center p-8 md:p-12">
                <div class="relative w-full max-w-2xl">
                    <!-- Resume Card -->
                    <div
                        class="bg-surface-container-lowest p-10 rounded-lg shadow-xl shadow-on-surface/5 relative z-10 aspect-[1/1.41] transform rotate-1">
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h2 class="text-3xl font-serif text-primary tracking-tight">Raditya Pratama</h2>
                                <p class="text-secondary font-semibold font-sans tracking-wide uppercase text-xs mt-1">
                                    Senior Product Designer</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-outline font-sans">Jakarta, Indonesia</p>
                                <p class="text-[10px] text-outline font-sans">raditya@resumify.ai</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="h-px bg-surface-container w-full"></div>
                            <div>
                                <h3 class="text-xs font-bold text-primary font-sans mb-3 uppercase tracking-widest">WORK
                                    EXPERIENCE</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between">
                                        <div class="w-2/3">
                                            <p class="text-sm font-bold text-primary">Lead Designer</p>
                                            <p class="text-xs text-outline italic">TechNova Solutions</p>
                                        </div>
                                        <p class="text-[10px] text-outline">2021 — Present</p>
                                    </div>
                                    <div
                                        class="space-y-1 bg-secondary-fixed/10 p-3 rounded-lg border-l-4 border-secondary">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="material-symbols-outlined text-[14px] text-secondary"
                                                style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                                            <span class="text-[10px] font-bold text-on-secondary-container">AI
                                                Optimized</span>
                                        </div>
                                        <p class="text-xs text-on-surface leading-relaxed">
                                            Leading a design team of 12 people and increased user conversion rates by
                                            34% through systematic A/B testing.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ATS Matcher Overlay (Visual Indicator) -->
                        <div
                            class="absolute -right-8 top-1/4 glass-effect p-6 rounded-xl border border-white shadow-2xl w-56 transform -rotate-2">
                            <p class="text-[10px] font-bold text-primary font-sans mb-4 tracking-widest uppercase">ATS
                                MATCH SCORE</p>
                            <div class="flex items-end gap-1.5 h-16 mb-4">
                                <div class="w-full bg-error/20 h-1/4 rounded-sm"></div>
                                <div class="w-full bg-error/40 h-2/4 rounded-sm"></div>
                                <div class="w-full bg-secondary/40 h-3/4 rounded-sm"></div>
                                <div class="w-full bg-secondary h-full rounded-sm"></div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] text-outline font-bold">Low</span>
                                <span class="text-xl font-serif font-bold text-secondary">High</span>
                            </div>
                        </div>
                    </div>
                    <!-- Input Fields Overlay -->
                    <div
                        class="absolute -bottom-6 -left-12 bg-primary p-8 rounded-xl shadow-2xl w-80 text-on-primary transform -rotate-1 z-20">
                        <h4 class="text-xs font-bold mb-6 tracking-widest uppercase opacity-60">Input Editor</h4>
                        <div class="space-y-5">
                            <div class="border-b border-on-primary-container/30 pb-1">
                                <label
                                    class="block text-[10px] text-on-primary-container uppercase font-bold mb-1">NAME</label>
                                <p class="text-sm font-serif">Raditya Pratama</p>
                            </div>
                            <div class="border-b border-on-primary-container/30 pb-1">
                                <label
                                    class="block text-[10px] text-on-primary-container uppercase font-bold mb-1">COMPANY</label>
                                <p class="text-sm font-serif">TechNova Solutions</p>
                            </div>
                            <div class="border-b border-on-primary-container/30 pb-1">
                                <label
                                    class="block text-[10px] text-on-primary-container uppercase font-bold mb-1">DESCRIPTION</label>
                                <p class="text-xs font-sans opacity-80 leading-snug">Leading design team and increasing
                                    conversions by 34%...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Features Section -->
        <section class="py-32 px-8 max-w-7xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div
                    class="bg-surface-container-lowest p-10 rounded-lg group hover:translate-y-[-8px] transition-all duration-500">
                    <div class="w-14 h-14 bg-secondary-fixed/30 rounded-full flex items-center justify-center mb-8">
                        <span class="material-symbols-outlined text-secondary text-3xl"
                            style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                    </div>
                    <h3 class="text-2xl font-serif text-primary mb-4">AI Bullet Point Generator</h3>
                    <p class="text-outline font-sans leading-relaxed">
                        Write your achievements instantly with data-driven suggestions that stand out to recruiters.
                    </p>
                </div>
                <!-- Card 2 -->
                <div
                    class="bg-surface-container-lowest p-10 rounded-lg group hover:translate-y-[-8px] transition-all duration-500">
                    <div class="w-14 h-14 bg-secondary-fixed/30 rounded-full flex items-center justify-center mb-8">
                        <span class="material-symbols-outlined text-secondary text-3xl">analytics</span>
                    </div>
                    <h3 class="text-2xl font-serif text-primary mb-4">ATS Match Score Scanner</h3>
                    <p class="text-outline font-sans leading-relaxed">
                        Evaluate your resume against job descriptions in real-time to ensure it passes filtration
                        systems.
                    </p>
                </div>
                <!-- Card 3 -->
                <div
                    class="bg-surface-container-lowest p-10 rounded-lg group hover:translate-y-[-8px] transition-all duration-500">
                    <div class="w-14 h-14 bg-secondary-fixed/30 rounded-full flex items-center justify-center mb-8">
                        <span class="material-symbols-outlined text-secondary text-3xl">article</span>
                    </div>
                    <h3 class="text-2xl font-serif text-primary mb-4">Premium Templates</h3>
                    <p class="text-outline font-sans leading-relaxed">
                        A collection of professionally curated templates for various industries and career levels.
                    </p>
                </div>
            </div>
        </section>
        <!-- Final CTA: The Editorial Banner -->
        <section class="py-24 px-8">
            <div
                class="max-w-5xl mx-auto bg-primary rounded-2xl overflow-hidden relative min-h-[400px] flex items-center p-12 md:p-20">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-1/2 h-full opacity-10 pointer-events-none">
                </div>
                <div class="relative z-10 max-w-2xl text-left">
                    <h2 class="font-serif text-4xl md:text-5xl text-on-primary mb-6 leading-tight">Ready to build your
                        story?</h2>
                    <p class="text-lg text-on-primary-container mb-10 leading-relaxed font-sans opacity-90">
                        Join thousands of professionals who have accelerated their career with Resumify.
                    </p>
                    <a href="{{ route('register') }}"
                        class="bg-surface-container-lowest text-primary px-10 py-4 rounded-lg font-bold text-lg shadow-xl hover:bg-surface-container-low transition-all active:scale-95 inline-block">
                        Start for Free Now
                    </a>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer Identical to SCREEN_30 -->
    <footer class="w-full mt-auto border-t border-[#81756e]/20 bg-[#f2ede9] dark:bg-[#12100e]">
        <div
            class="flex flex-col md:flex-row justify-between items-center px-12 py-12 w-full max-w-7xl mx-auto font-['Manrope'] text-sm leading-6">
            <div class="mb-8 md:mb-0 text-center md:text-left">
                <div class="text-lg font-semibold text-[#4f3b2f] dark:text-[#f2ede9] mb-2">Resumify</div>
                <p class="text-[#81756e] dark:text-[#a3948b]">© 2024 Resumify. The Curated Manuscript.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-8">
                <a class="text-[#81756e] dark:text-[#a3948b] hover:text-[#10b981] transition-colors hover:opacity-80"
                    href="#">Privacy Policy</a>
                <a class="text-[#81756e] dark:text-[#a3948b] hover:text-[#10b981] transition-colors hover:opacity-80"
                    href="#">Terms of Service</a>
                <a class="text-[#81756e] dark:text-[#a3948b] hover:text-[#10b981] transition-colors hover:opacity-80"
                    href="#">Cookie Policy</a>
                <a class="text-[#81756e] dark:text-[#a3948b] hover:text-[#10b981] transition-colors hover:opacity-80"
                    href="#">Contact</a>
            </div>
        </div>
    </footer>
    ```
</body>

</html>
