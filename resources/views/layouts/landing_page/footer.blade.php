<footer x-data="footerData()" class="w-full mt-auto border-t border-primary/15 bg-nav-footer">
    <div class="flex flex-col md:flex-row justify-between items-center px-12 py-12 w-full max-w-7xl mx-auto font-body text-sm leading-6 relative z-10">
        {{-- Brand & Copyright --}}
        <div class="mb-8 md:mb-0 text-center md:text-left">
            {{-- Menggunakan font-headline untuk Brand --}}
            <div class="flex items-center gap-2 text-xl font-headline font-bold text-primary mb-2 justify-center md:justify-start">
                <img src="{{ asset('images/logo.jpg') }}" alt="Resumify Logo" class="h-8 w-8 rounded-lg object-cover shadow-sm">
                <span>Resumify</span>
            </div>
            <p class="text-outline">© {{ date('Y') }} {{ __('messages.landing.footer.copyright') }}</p>
        </div>

        {{-- Footer Links --}}
        <div class="flex flex-wrap justify-center gap-8">
            <button type="button" @click="openModal('privacy')" class="inline-flex min-h-11 items-center rounded-lg px-2 text-outline hover:text-secondary transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40">{{ __('messages.landing.footer.privacy_policy') }}</button>
            <button type="button" @click="openModal('terms')" class="inline-flex min-h-11 items-center rounded-lg px-2 text-outline hover:text-secondary transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40">{{ __('messages.landing.footer.terms_of_service') }}</button>
            <button type="button" @click="openModal('cookie')" class="inline-flex min-h-11 items-center rounded-lg px-2 text-outline hover:text-secondary transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40">{{ __('messages.landing.footer.cookie_policy') }}</button>
            <button type="button" @click="openModal('contact')" class="inline-flex min-h-11 items-center rounded-lg px-2 text-outline hover:text-secondary transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40">{{ __('messages.landing.footer.contact') }}</button>
        </div>
    </div>

    {{-- Modal Overlay --}}
    <div x-show="isModalOpen" 
         style="display: none;"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
         role="dialog"
         aria-modal="true"
         aria-labelledby="landing-footer-modal-title"
         @keydown.escape.window="closeModal()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="closeModal()"
             tabindex="-1"
             class="bg-surface w-full max-w-2xl rounded-lg border border-primary/10 shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95">
             
            <div class="px-6 py-4 border-b border-primary/10 flex justify-between items-center bg-surface-container-lowest">
                <h3 id="landing-footer-modal-title" class="text-xl font-headline font-bold text-primary" x-text="modalTitle"></h3>
                <button type="button" @click="closeModal()" aria-label="Close modal" class="inline-flex min-h-11 min-w-11 items-center justify-center text-primary/50 hover:text-primary transition-colors rounded-full hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">close</span>
                </button>
            </div>
            
            <div class="px-6 py-6 max-h-[70vh] overflow-y-auto text-primary/80 font-body text-sm leading-relaxed" x-html="modalContent">
            </div>
            
            <div class="px-6 py-4 border-t border-primary/10 bg-surface-container-lowest flex justify-end">
                <button type="button" @click="closeModal()" class="inline-flex min-h-11 items-center justify-center bg-primary text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-primary/90 transition-colors focus:outline-none focus:ring-2 focus:ring-secondary/40">
                    {{ __('messages.landing.footer.close') }}
                </button>
            </div>
        </div>
    </div>
</footer>

<script>
function footerData() {
    return {
        isModalOpen: false,
        modalTitle: '',
        modalContent: '',
        
        contents: {
            privacy: {
                title: {!! json_encode(__('messages.landing.footer.legal.privacy.title')) !!},
                html: {!! json_encode(__('messages.landing.footer.legal.privacy.html')) !!}
            },
            terms: {
                title: {!! json_encode(__('messages.landing.footer.legal.terms.title')) !!},
                html: {!! json_encode(__('messages.landing.footer.legal.terms.html')) !!}
            },
            cookie: {
                title: {!! json_encode(__('messages.landing.footer.legal.cookie.title')) !!},
                html: {!! json_encode(__('messages.landing.footer.legal.cookie.html')) !!}
            },
            contact: {
                title: {!! json_encode(__('messages.landing.footer.legal.contact.title')) !!},
                html: {!! json_encode(__('messages.landing.footer.legal.contact.html')) !!}
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
            }, 200);
        }
    };
}
</script>
