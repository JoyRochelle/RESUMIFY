<footer class="w-full mt-auto border-t border-primary/15 bg-nav-footer">
    <div class="flex flex-col md:flex-row justify-between items-center px-12 py-12 w-full max-w-7xl mx-auto font-body text-sm leading-6">
        {{-- Brand & Copyright --}}
        <div class="mb-8 md:mb-0 text-center md:text-left">
            {{-- Menggunakan font-headline untuk Brand --}}
            <div class="flex items-center gap-2 text-xl font-headline font-bold text-primary mb-2">
                <img src="{{ asset('images/logo.jpg') }}" alt="Resumify Logo" class="h-8 w-8 rounded-lg object-cover shadow-sm">
                <span>Resumify</span>
            </div>
            <p class="text-outline">© 2024 Resumify. The Curated Manuscript.</p>
        </div>

        {{-- Footer Links --}}
        <div class="flex flex-wrap justify-center gap-8">
            <a class="text-outline hover:text-secondary transition-colors duration-300" href="#">Privacy Policy</a>
            <a class="text-outline hover:text-secondary transition-colors duration-300" href="#">Terms of Service</a>
            <a class="text-outline hover:text-secondary transition-colors duration-300" href="#">Cookie Policy</a>
            <a class="text-outline hover:text-secondary transition-colors duration-300" href="#">Contact</a>
        </div>
    </div>
</footer>