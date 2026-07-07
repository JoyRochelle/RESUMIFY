<section
    x-data="authFooterData()"
    class="w-full md:w-2/5 flex-1 bg-surface-container-lowest flex flex-col items-center p-6 md:p-8 lg:p-12 relative md:overflow-y-auto">
    <div class="w-full max-w-md">
        {{-- Brand Anchor --}}
        <x-auth.brand class="mb-6" />

        {{-- Auth Card --}}
        <div
            class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 p-6 md:p-8 shadow-sm transition-auth-card">
            {{-- Tabs --}}
            <nav aria-label="Auth Tabs" class="flex border-b border-surface-variant mb-6">
                <a href="{{ route('login') }}" wire:navigate
                    class="flex-1 text-center pb-4 text-sm font-bold {{ request()->routeIs('login') ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }} transition-all">
                    {{ __('messages.auth.tabs.login') }}
                </a>
                <a href="{{ route('register') }}" wire:navigate
                    class="flex-1 text-center pb-4 text-sm font-bold {{ request()->routeIs('register') ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }} transition-all">
                    {{ __('messages.auth.tabs.sign_up') }}
                </a>
            </nav>

            {{-- Form Header --}}
            <div class="mb-6">
                <h3 class="text-2xl font-headline font-bold text-on-surface">{{ $title ?? __('messages.auth.login.heading') }}
                </h3>
                <p class="text-sm text-on-surface-variant mt-1">
                    {{ $subtitle ?? __('messages.auth.login.subtitle') }}
                </p>
            </div>

            {{-- Form Content --}}
            {{ $slot }}

            {{-- Social Auth --}}
            <x-auth.social-buttons class="mt-6" />
        </div>

        {{-- Footer Info --}}
        <div class="text-center mt-6">
            {{ $footer ?? '' }}
        </div>

        {{-- Secondary Footer --}}
        <div class="mt-8 flex justify-center gap-6 text-[10px] uppercase tracking-widest text-on-surface-variant/60 font-bold">
            <button type="button" @click="openModal('terms')" class="hover:text-primary transition-colors uppercase tracking-widest">{{ __('messages.auth.legal.terms') }}</button>
            <button type="button" @click="openModal('privacy')" class="hover:text-primary transition-colors uppercase tracking-widest">{{ __('messages.auth.legal.privacy') }}</button>
            <button type="button" @click="openModal('help')" class="hover:text-primary transition-colors uppercase tracking-widest">{{ __('messages.auth.legal.help') }}</button>

            {{-- Modal Overlay --}}
            <div x-show="isModalOpen" 
                 style="display: none;"
                 class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4 text-left font-body text-base capitalize tracking-normal"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="auth-footer-modal-title"
                 @keydown.escape.window="closeModal()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div @click.away="closeModal()"
                     tabindex="-1"
                     class="bg-surface w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 scale-95">
                     
                    <div class="px-6 py-4 border-b border-primary/10 flex justify-between items-center bg-surface-container-lowest">
                        <h3 id="auth-footer-modal-title" class="text-xl font-headline font-bold text-primary normal-case tracking-normal" x-text="modalTitle"></h3>
                        <button type="button" @click="closeModal()" aria-label="Close modal" class="text-primary/50 hover:text-primary transition-colors flex items-center justify-center rounded-full p-2 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">
                            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">close</span>
                        </button>
                    </div>
                    
                    <div class="px-6 py-6 max-h-[70vh] overflow-y-auto text-primary/80 font-body text-sm leading-relaxed normal-case tracking-normal" x-html="modalContent">
                    </div>
                    
                    <div class="px-6 py-4 border-t border-primary/10 bg-surface-container-lowest flex justify-end">
                        <button type="button" @click="closeModal()" class="bg-primary text-white px-5 py-2 rounded-full font-bold text-sm hover:bg-primary/90 transition-colors shadow-sm normal-case tracking-normal focus:outline-none focus:ring-2 focus:ring-secondary/40">
                            {{ __('messages.auth.legal.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function authFooterData() {
    return {
        isModalOpen: false,
        modalTitle: '',
        modalContent: '',
        
        contents: {
            privacy: {
                title: @js(__('messages.auth.legal_modals.privacy.title')),
                html: @js(__('messages.auth.legal_modals.privacy.html'))
            },
            terms: {
                title: @js(__('messages.auth.legal_modals.terms.title')),
                html: @js(__('messages.auth.legal_modals.terms.html'))
            },
            help: {
                title: @js(__('messages.auth.legal_modals.help.title')),
                html: @js(__('messages.auth.legal_modals.help.html'))
            }
        },
        
        openModal(tab) {
            this.modalTitle = this.contents[tab].title;
            this.modalContent = this.contents[tab].html;
            this.isModalOpen = true;
            document.body.style.overflow = 'hidden';
        },
        
        closeModal() {
            this.isModalOpen = false;
            setTimeout(() => {
                document.body.style.overflow = '';
            }, 300);
        }
    };
}
</script>
