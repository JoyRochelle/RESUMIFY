@php
    $user = auth()->user();
@endphp

@if($user->canCreateResume())
    <button type="button" onclick="openCreateModal()" class="inline-flex min-h-11 items-center justify-center space-x-2 rounded-lg bg-primary px-8 py-4 font-label font-bold text-tertiary transition-all duration-200 hover:bg-primary/90 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-secondary/40">
        <span>Create New Resume</span>
        <span class="material-symbols-outlined text-lg" aria-hidden="true">auto_awesome</span>
    </button>
@else
    <x-user.premium-lock
        title="Unlimited resumes"
        description="Basic includes 1 resume. Premium unlocks unlimited resumes for every role you are targeting."
        align="right">
        <span class="inline-flex min-h-11 items-center justify-center space-x-2 rounded-lg border border-[#A16207]/25 bg-[#A16207]/10 px-8 py-4 font-label font-bold text-[#7C4A03] shadow-sm transition duration-200">
            <span>Create New Resume</span>
            <span class="material-symbols-outlined text-lg icon-filled" aria-hidden="true">lock</span>
        </span>
    </x-user.premium-lock>
@endif
