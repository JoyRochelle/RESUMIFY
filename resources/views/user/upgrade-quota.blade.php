@extends('layouts.user.app')

@section('title', __('messages.upgrade_quota.page_title') . ' - Resumify')

@section('content')
    @php
        $user = auth()->user();
        $isPremium = $user->isPremium();
        $isCancelled = $subscription?->status === 'cancelled';
        $hasActiveSubscription = $subscription?->status === 'active';
    @endphp

    <main class="flex-1 overflow-y-auto custom-scrollbar bg-primary/5 pb-20 md:pb-0">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 md:px-12 py-8 md:py-16">

            {{-- Hero Section --}}
            <section class="text-center mb-10 md:mb-20 animate-fade-up">
                <h1 class="font-headline text-3xl sm:text-4xl md:text-6xl text-primary mb-6 tracking-tight">{{ __('messages.upgrade_quota.hero.title') }}
                </h1>
                <p class="font-body text-primary/70 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
                    {{ __('messages.upgrade_quota.hero.subtitle') }}
                </p>
            </section>

            @if ($isPremium && $subscription)
                <section class="mb-8 md:mb-10 rounded-2xl border border-primary/10 bg-tertiary p-5 shadow-sm md:flex md:items-center md:justify-between md:gap-6 animate-fade-up" style="animation-delay: 100ms">
                    <div>
                        <p class="font-label text-xs font-bold uppercase tracking-widest {{ $isCancelled ? 'text-amber-700' : 'text-secondary' }}">
                            {{ $isCancelled ? __('messages.upgrade_quota.status.cancellation_scheduled') : __('messages.upgrade_quota.status.premium_active') }}
                        </p>
                        <h2 class="mt-2 font-headline text-2xl font-bold text-primary">{{ __('messages.upgrade_quota.status.plan_name') }}</h2>
                        <p class="mt-2 font-body text-sm text-primary/65">
                            @if ($isCancelled && $subscription->ends_at)
                                {{ __('messages.upgrade_quota.status.active_until', ['date' => $subscription->ends_at->format('d M Y')]) }}
                            @elseif ($subscription->ends_at)
                                {{ __('messages.upgrade_quota.status.billing_ends', ['date' => $subscription->ends_at->format('d M Y')]) }}
                            @else
                                {{ __('messages.upgrade_quota.status.access_active') }}
                            @endif
                        </p>
                    </div>
                    @if ($hasActiveSubscription)
                        <form method="POST" action="{{ route('subscription.cancel') }}" class="mt-5 md:mt-0"
                            onsubmit="return confirm('{{ __('messages.upgrade_quota.status.cancel_confirm') }}');">
                            @csrf
                            <button type="submit"
                                class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-red-300 px-5 py-3 text-sm font-bold text-red-600 transition hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-300/50 md:w-auto">
                                {{ __('messages.upgrade_quota.status.cancel_plan') }}
                            </button>
                        </form>
                    @endif
                </section>
            @endif

            {{-- Pricing Cards --}}
            <section class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10 mb-12 md:mb-24 max-w-5xl mx-auto">

                <x-product.pricing-card :plan="__('messages.upgrade_quota.plans.starter')" price="Rp 0" :period="__('messages.upgrade_quota.plans.forever')" :features="[__('messages.upgrade_quota.plans.one_active_resume'), __('messages.upgrade_quota.plans.standard_templates')]" :disabledFeatures="[__('messages.upgrade_quota.plans.no_ai_enhancement')]"
                    :isCurrentPlan="!$isPremium" />

                <x-product.pricing-card :plan="__('messages.upgrade_quota.plans.premium_pro')" price="Rp 49.000" :period="__('messages.upgrade_quota.plans.month')" :features="[
                    __('messages.upgrade_quota.plans.unlimited_resumes'),
                    ['title' => __('messages.upgrade_quota.plans.ai_bullet_optimizer'), 'subtitle' => __('messages.upgrade_quota.plans.ai_bullet_optimizer_subtitle')],
                    __('messages.upgrade_quota.plans.realtime_ats_matcher'),
                    __('messages.upgrade_quota.plans.premium_pdf_export'),
                    __('messages.upgrade_quota.plans.priority_support'),
                ]" :isPremium="true"
                    :isCurrentPlan="$isPremium"
                    :buttonText="__('messages.upgrade_quota.plans.activate_premium')" type="button" :aria-label="__('messages.upgrade_quota.plans.activate_premium_aria')" data-plan-action="purchase-premium" />

            </section>

            {{-- Payment Methods --}}
            <section
                class="max-w-4xl mx-auto px-8 py-16 mb-24 bg-tertiary rounded-3xl text-center border border-primary/10 shadow-sm">
                <h2 class="font-headline text-3xl font-bold text-primary mb-10">{{ __('messages.upgrade_quota.payment.title') }}</h2>
                <div
                    class="flex flex-wrap justify-center items-center gap-10 mb-12 grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition-all">
                    <x-user.payment-method icon="credit_card" :label="__('messages.upgrade_quota.payment.credit_card')" />
                    <x-user.payment-method icon="account_balance" :label="__('messages.upgrade_quota.payment.bank_transfer')" />
                    <x-user.payment-method icon="account_balance_wallet" :label="__('messages.upgrade_quota.payment.ewallet')" />
                </div>
                <div
                    class="inline-flex items-center gap-3 py-3 px-8 bg-surface rounded-full border border-primary/10 shadow-sm">
                    <span class="material-symbols-outlined text-secondary icon-filled">verified_user</span>
                    <span class="font-label text-xs font-bold tracking-widest text-primary uppercase">{{ __('messages.upgrade_quota.payment.money_back') }}</span>
                </div>
                <p class="mt-8 text-primary/60 text-sm font-body max-w-lg mx-auto">
                    {{ __('messages.upgrade_quota.payment.security_note') }}
                </p>
            </section>

            {{-- Testimonial & Stats --}}
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-16">

                <x-user.testimonial-card
                    :quote="__('messages.upgrade_quota.social.quote')"
                    name="X" :role="__('messages.upgrade_quota.social.role')" :avatar="asset('images/sadakiyo.jpg')" />

                <div
                    class="bg-tertiary border border-primary/10 p-8 rounded-2xl flex flex-col items-center justify-center text-center shadow-sm">
                    <span class="text-4xl font-headline font-bold text-primary mb-2">15k+</span>
                    <p class="text-primary/60 text-sm font-body mb-6">{{ __('messages.upgrade_quota.social.careers_elevated') }}</p>
                    <div class="flex -space-x-3 mb-4">
                        <img alt="User 1" class="w-8 h-8 rounded-full border-2 border-surface object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuC7F939MaG5sQzATDpPyVetqjNfdi7yXyQV0KWHxHyUN5HbKvY-gqp4MpxP_9IIBgx0t6t1b3yC0H9KP-OpIz_gqA6gF4EzJAjcgxm6Kxos3V9cpVIMhNKFkcGzGzolFR-net-j6bK-CKdd_pVLsP0SdZJv3WO4ZeCm_NWXMTS7aAvx_pUGBwuKsTTPtv-4x-jpKa1nZhWS4YpNYeQ7q2Mj2dfao1Po1P6eWNbvNKPHNRDQq1XqUGU4p0ykqk1FXuuPAMEW7rgzVHuh" />
                        <img alt="User 2" class="w-8 h-8 rounded-full border-2 border-surface object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCyVtJGBskckelZE_O7j5ZFIehfar7B7fX-LBggKVVcKgsBL2_tQdcyFfgMFUsW1cf9Zqw7KKICDYTzm0jF9F0M_Nqm1Si1UB8dIfWJqxR_zr1Id46CHwmmclUZm5rEb71_4vU3uu-241O9wn0xMocdU_5HTVINp9_lVTTTxb0DJ73B-4fRk22lvGfqOoIit7wk1Ww1hKaeTs0cLuvw4aYFFKHTnXYHTiIrYB2rSS4ibmuLA9RusedUp3dKXp7TMRHtBNmO8OS4MT2v" />
                        <img alt="User 3" class="w-8 h-8 rounded-full border-2 border-surface object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAEf9UyvnsYCrxV0IHy-HuhFFUDl5XuLBEBdV-9UKRk116K785QpQqVrfP4Wpwea3F3KPb50AnDTPeeJlYJciXMGDSXs_1Jvuntct37r-I10FjJ2uOjTYfNUN_-F6mlYApHJTuHxJJvMlv6Sv1-V_BO1FgbWOAcB2YWYApHsmT6wdFdgFtvSTnSpWmW5OXCQBi5xbzCXJ690de-xh3tROXWye8cSo8fu1TPDCs0T_kbdx6WfWoMPe0Zi__Wg0SUuLFKlzqXmFBKJzGg" />
                        <div
                            class="w-8 h-8 rounded-full border-2 border-surface bg-primary flex items-center justify-center text-[10px] text-tertiary font-bold">
                            +12k</div>
                    </div>
                    <p class="text-[11px] text-primary/60 font-medium">{{ __('messages.upgrade_quota.social.join_community') }}</p>
                </div>
            </section>

        </div>

        <footer class="mt-12 pb-12 text-center text-primary/40 text-sm">
            <p>{{ __('messages.upgrade_quota.footer') }}</p>
        </footer>
    </main>
@endsection

@push('scripts')
    <script src="{{ config('services.midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        (() => {
            const purchaseButtonSelector = '[data-plan-action="purchase-premium"]';

            function notify(message, type = 'success') {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message, type } }));
            }

            function setProcessing(button, isProcessing) {
                button.disabled = isProcessing;
                button.textContent = isProcessing ? @json(__('messages.upgrade_quota.js.processing')) : @json(__('messages.upgrade_quota.plans.activate_premium'));
            }

            async function handlePremiumPurchase(event) {
                const button = event.currentTarget;

                if (button.disabled) return;

                setProcessing(button, true);

                try {
                    const response = await fetch('{{ route('payment.create') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.snap_token) {
                        if (window.snap && typeof window.snap.pay === 'function') {
                            window.snap.pay(data.snap_token, {
                                onSuccess: function(result) {
                                    notify(@json(__('messages.upgrade_quota.js.payment_success')));
                                    window.location.href = '{{ route('dashboard') }}';
                                },
                                onPending: function(result) {
                                    notify(@json(__('messages.upgrade_quota.js.payment_pending')), "info");
                                },
                                onError: function(result) {
                                    notify(@json(__('messages.upgrade_quota.js.payment_failed')), "error");
                                },
                                onClose: function() {
                                    notify(@json(__('messages.upgrade_quota.js.popup_closed')), "info");
                                }
                            });
                        } else if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            notify(@json(__('messages.upgrade_quota.js.popup_not_ready')), "error");
                        }
                    } else {
                        notify(data.error || @json(__('messages.upgrade_quota.js.init_failed')), "error");
                    }
                } catch (error) {
                    console.error("Payment error:", error);
                    notify(@json(__('messages.upgrade_quota.js.generic_error')), "error");
                } finally {
                    setProcessing(button, false);
                }
            }

            function bindPremiumPurchaseButton() {
                document.querySelectorAll(purchaseButtonSelector).forEach((button) => {
                    if (button.dataset.boundPayment === 'true') return;

                    button.dataset.boundPayment = 'true';
                    button.addEventListener('click', handlePremiumPurchase);
                });
            }

            bindPremiumPurchaseButton();
            document.addEventListener('DOMContentLoaded', bindPremiumPurchaseButton);
            document.addEventListener('livewire:navigated', bindPremiumPurchaseButton);
        })();
    </script>
@endpush
