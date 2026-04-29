<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Pricing Plans - Resumify</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface font-body text-primary overflow-hidden h-screen flex flex-row">
    
    @include('layouts.sidenavbar')

    <main class="flex-1 overflow-y-auto custom-scrollbar bg-primary/5">
        
        <div class="max-w-6xl mx-auto px-6 md:px-12 py-16">
            
            {{-- Hero Section --}}
            <section class="text-center mb-20">
                <h1 class="font-headline text-5xl md:text-6xl text-primary mb-6 tracking-tight">Choose Your Success Plan ✨</h1>
                <p class="font-body text-primary/70 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
                    Elevate your career narrative with artificial intelligence. Let every line of your experience speak with authority.
                </p>
            </section>

            {{-- Pricing Cards --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-24 max-w-5xl mx-auto">
                
                <x-user_dashboard.pricing-card
                    plan="Starter"
                    price="Rp 0"
                    period="forever"
                    :features="['1 Active Resume', 'Standard Templates']"
                    :disabledFeatures="['No AI Enhancement']"
                    :isCurrentPlan="true"
                />

                <x-user_dashboard.pricing-card
                    plan="Premium PRO"
                    price="Rp 99.000"
                    period="month"
                    :features="[
                        'Unlimited Resumes',
                        ['title' => 'AI Bullet Point Optimizer', 'subtitle' => 'Optimize with high-impact keywords'],
                        'Real-time ATS Matcher',
                        'Premium PDF Export',
                        'Priority Support',
                    ]"
                    :isPremium="true"
                    buttonText="Activate Premium Now 🚀"
                />

            </section>

            {{-- Payment Methods --}}
            <section class="max-w-4xl mx-auto px-8 py-16 mb-24 bg-tertiary rounded-3xl text-center border border-primary/10 shadow-sm">
                <h2 class="font-headline text-3xl font-bold text-primary mb-10">Secure Payment Methods</h2>
                <div class="flex flex-wrap justify-center items-center gap-10 mb-12 grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition-all">
                    <x-user_dashboard.payment-method icon="credit_card" label="Credit Card" />
                    <x-user_dashboard.payment-method icon="account_balance" label="Bank Transfer" />
                    <x-user_dashboard.payment-method icon="account_balance_wallet" label="Gopay / OVO" />
                </div>
                <div class="inline-flex items-center gap-3 py-3 px-8 bg-surface rounded-full border border-primary/10 shadow-sm">
                    <span class="material-symbols-outlined text-secondary icon-filled">verified_user</span>
                    <span class="font-label text-xs font-bold tracking-widest text-primary uppercase">30-Day Money-Back Guarantee</span>
                </div>
                <p class="mt-8 text-primary/60 text-sm font-body max-w-lg mx-auto">
                    Your transactions are protected with AES-256 bit encryption. Your privacy and data security are our top priorities.
                </p>
            </section>

            {{-- Testimonial & Stats --}}
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-16">
                
                <x-user_dashboard.testimonial-card
                    quote="Resumify Premium is not just a tool, it's an investment for the future. With the AI bullet optimizer, I received interview calls within 3 days."
                    name="Andreas Calvin Hartono"
                    role="Senior Product Manager"
                    :avatar="asset('images/sadakiyo.jpg')"
                />

                <div class="bg-tertiary border border-primary/10 p-8 rounded-2xl flex flex-col items-center justify-center text-center shadow-sm">
                    <span class="text-4xl font-headline font-bold text-primary mb-2">15k+</span>
                    <p class="text-primary/60 text-sm font-body mb-6">Careers Successfully Elevated</p>
                    <div class="flex -space-x-3 mb-4">
                        <img alt="User 1" class="w-8 h-8 rounded-full border-2 border-surface object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC7F939MaG5sQzATDpPyVetqjNfdi7yXyQV0KWHxHyUN5HbKvY-gqp4MpxP_9IIBgx0t6t1b3yC0H9KP-OpIz_gqA6gF4EzJAjcgxm6Kxos3V9cpVIMhNKFkcGzGzolFR-net-j6bK-CKdd_pVLsP0SdZJv3WO4ZeCm_NWXMTS7aAvx_pUGBwuKsTTPtv-4x-jpKa1nZhWS4YpNYeQ7q2Mj2dfao1Po1P6eWNbvNKPHNRDQq1XqUGU4p0ykqk1FXuuPAMEW7rgzVHuh"/>
                        <img alt="User 2" class="w-8 h-8 rounded-full border-2 border-surface object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCyVtJGBskckelZE_O7j5ZFIehfar7B7fX-LBggKVVcKgsBL2_tQdcyFfgMFUsW1cf9Zqw7KKICDYTzm0jF9F0M_Nqm1Si1UB8dIfWJqxR_zr1Id46CHwmmclUZm5rEb71_4vU3uu-241O9wn0xMocdU_5HTVINp9_lVTTTxb0DJ73B-4fRk22lvGfqOoIit7wk1Ww1hKaeTs0cLuvw4aYFFKHTnXYHTiIrYB2rSS4ibmuLA9RusedUp3dKXp7TMRHtBNmO8OS4MT2v"/>
                        <img alt="User 3" class="w-8 h-8 rounded-full border-2 border-surface object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAEf9UyvnsYCrxV0IHy-HuhFFUDl5XuLBEBdV-9UKRk116K785QpQqVrfP4Wpwea3F3KPb50AnDTPeeJlYJciXMGDSXs_1Jvuntct37r-I10FjJ2uOjTYfNUN_-F6mlYApHJTuHxJJvMlv6Sv1-V_BO1FgbWOAcB2YWYApHsmT6wdFdgFtvSTnSpWmW5OXCQBi5xbzCXJ690de-xh3tROXWye8cSo8fu1TPDCs0T_kbdx6WfWoMPe0Zi__Wg0SUuLFKlzqXmFBKJzGg"/>
                        <div class="w-8 h-8 rounded-full border-2 border-surface bg-primary flex items-center justify-center text-[10px] text-tertiary font-bold">+12k</div>
                    </div>
                    <p class="text-[11px] text-primary/60 font-medium">Join the professional community today.</p>
                </div>
            </section>
            
        </div>

        <footer class="mt-12 pb-12 text-center text-primary/40 text-sm">
            <p>© 2026 Resumify - Curated with Integrity</p>
        </footer>
    </main>

    @include('layouts.mobilenavbar')
    
</body>
</html>