<footer x-data="footerData()" class="w-full mt-auto border-t border-primary/15 bg-nav-footer">
    <div class="flex flex-col md:flex-row justify-between items-center px-12 py-12 w-full max-w-7xl mx-auto font-body text-sm leading-6 relative z-10">
        {{-- Brand & Copyright --}}
        <div class="mb-8 md:mb-0 text-center md:text-left">
            {{-- Menggunakan font-headline untuk Brand --}}
            <div class="flex items-center gap-2 text-xl font-headline font-bold text-primary mb-2 justify-center md:justify-start">
                <img src="{{ asset('images/logo.jpg') }}" alt="Resumify Logo" class="h-8 w-8 rounded-lg object-cover shadow-sm">
                <span>Resumify</span>
            </div>
            <p class="text-outline">© {{ date('Y') }} Resumify. The Curated Manuscript.</p>
        </div>

        {{-- Footer Links --}}
        <div class="flex flex-wrap justify-center gap-8">
            <button @click="openModal('privacy')" class="text-outline hover:text-secondary transition-colors duration-300">Privacy Policy</button>
            <button @click="openModal('terms')" class="text-outline hover:text-secondary transition-colors duration-300">Terms of Service</button>
            <button @click="openModal('cookie')" class="text-outline hover:text-secondary transition-colors duration-300">Cookie Policy</button>
            <button @click="openModal('contact')" class="text-outline hover:text-secondary transition-colors duration-300">Contact</button>
        </div>
    </div>

    {{-- Modal Overlay --}}
    <div x-show="isModalOpen" 
         style="display: none;"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="closeModal()" 
             class="bg-surface w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95">
             
            <div class="px-6 py-4 border-b border-primary/10 flex justify-between items-center bg-surface-container-lowest">
                <h3 class="text-xl font-headline font-bold text-primary" x-text="modalTitle"></h3>
                <button @click="closeModal()" class="text-primary/50 hover:text-primary transition-colors flex items-center justify-center rounded-full p-1 hover:bg-primary/5">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            
            <div class="px-6 py-6 max-h-[70vh] overflow-y-auto text-primary/80 font-body text-sm leading-relaxed" x-html="modalContent">
            </div>
            
            <div class="px-6 py-4 border-t border-primary/10 bg-surface-container-lowest flex justify-end">
                <button @click="closeModal()" class="bg-primary text-white px-5 py-2 rounded-full font-bold text-sm hover:bg-primary/90 transition-colors shadow-sm">
                    Close
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
                title: 'Privacy Policy',
                html: `
                    <p class="mb-4">At Resumify, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">1. Information We Collect</h4>
                    <p class="mb-4">We may collect personal identification information from Users in a variety of ways, including, but not limited to, when Users visit our site, register on the site, place an order, and in connection with other activities, services, features or resources we make available on our Site.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">2. How We Use Collected Information</h4>
                    <p class="mb-4">Resumify may collect and use Users' personal information for the following purposes:</p>
                    <ul class="list-disc pl-5 mb-4 space-y-1">
                        <li>To improve customer service</li>
                        <li>To personalize user experience</li>
                        <li>To process payments securely</li>
                        <li>To send periodic emails regarding your manuscript updates</li>
                    </ul>
                    <h4 class="font-bold text-primary mb-2 mt-6">3. Data Security</h4>
                    <p>We adopt appropriate data collection, storage and processing practices and security measures to protect against unauthorized access, alteration, disclosure or destruction of your personal information, username, password, transaction information and data stored on our Site.</p>
                `
            },
            terms: {
                title: 'Terms of Service',
                html: `
                    <p class="mb-4">Welcome to Resumify. By accessing this website, we assume you accept these terms and conditions. Do not continue to use Resumify if you do not agree to take all of the terms and conditions stated on this page.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">1. License</h4>
                    <p class="mb-4">Unless otherwise stated, Resumify and/or its licensors own the intellectual property rights for all material on Resumify. All intellectual property rights are reserved. You may access this from Resumify for your own personal use subjected to restrictions set in these terms and conditions.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">2. User Accounts</h4>
                    <p class="mb-4">When you create an account with us, you must provide us information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">3. Limitation of Liability</h4>
                    <p>In no event shall Resumify, nor any of its officers, directors and employees, shall be held liable for anything arising out of or in any way connected with your use of this Website whether such liability is under contract.</p>
                `
            },
            cookie: {
                title: 'Cookie Policy',
                html: `
                    <p class="mb-4">Our website uses cookies to distinguish you from other users of our website. This helps us to provide you with a good experience when you browse our website and also allows us to improve our site.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">1. What are cookies?</h4>
                    <p class="mb-4">A cookie is a small file of letters and numbers that we store on your browser or the hard drive of your computer if you agree. Cookies contain information that is transferred to your computer's hard drive.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">2. How we use cookies</h4>
                    <p class="mb-4">We use the following cookies:</p>
                    <ul class="list-disc pl-5 mb-4 space-y-1">
                        <li><strong>Strictly necessary cookies:</strong> Required for the operation of our website, such as secure login areas.</li>
                        <li><strong>Analytical or performance cookies:</strong> Allow us to recognise and count the number of visitors.</li>
                        <li><strong>Functionality cookies:</strong> Used to recognise you when you return to our website and remember your preferences.</li>
                    </ul>
                    <p>You can block cookies by activating the setting on your browser that allows you to refuse the setting of all or some cookies.</p>
                `
            },
            contact: {
                title: 'Contact Us',
                html: `
                    <p class="mb-6 text-[15px]">We would love to hear from you. If you have any questions, concerns, or feedback regarding Resumify, please reach out to our support team.</p>
                    
                    <div class="bg-primary/5 p-4 rounded-xl mb-4 border border-primary/10">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-secondary">mail</span>
                            <span class="font-bold text-primary">Email Support</span>
                        </div>
                        <p class="text-primary/70 ml-9">hello@resumify.com<br>support@resumify.com</p>
                    </div>

                    <div class="bg-primary/5 p-4 rounded-xl mb-6 border border-primary/10">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-secondary">location_on</span>
                            <span class="font-bold text-primary">Office Headquarters</span>
                        </div>
                        <p class="text-primary/70 ml-9">123 Innovation Drive<br>Tech District, San Francisco<br>CA 94105, United States</p>
                    </div>
                    
                    <p class="text-sm italic text-primary/60">Our support team usually responds within 24-48 business hours.</p>
                `
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