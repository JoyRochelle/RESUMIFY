@props([
    'tour',
    'steps' => [],
    'autoStart' => true,
])

@php
    // Each step names a translation key under messages.tour.<tour>.steps.<key>
    // and a selector for the element it points at. Steps whose element is not
    // on the page are skipped at runtime.
    $tourSteps = collect($steps)
        ->map(fn ($step) => [
            'target' => $step['target'] ?? null,
            'placement' => $step['placement'] ?? 'auto',
            'expand' => (bool) ($step['expand'] ?? false),
            'title' => __("messages.tour.{$tour}.steps.{$step['key']}.title"),
            'body' => __("messages.tour.{$tour}.steps.{$step['key']}.body"),
        ])
        ->values();
@endphp

@if ($tourSteps->isNotEmpty())
    <div data-tour-root hidden>
        {{-- Click catcher. The dimming itself comes from the spotlight's ring. --}}
        <div data-tour-backdrop class="fixed inset-0 z-[60]" aria-hidden="true"></div>

        <div data-tour-spotlight
             class="pointer-events-none fixed z-[61] shadow-[0_0_0_9999px_rgba(29,27,25,0.62)] ring-2 ring-secondary transition-[top,left,width,height] duration-200 ease-out motion-reduce:transition-none"
             aria-hidden="true"></div>

        <div data-tour-tooltip
             class="fixed z-[62] max-w-[calc(100vw-1.5rem)] rounded-2xl border border-primary/10 bg-surface p-5 shadow-2xl"
             role="dialog"
             aria-modal="true"
             aria-labelledby="tour-title-{{ $tour }}"
             aria-describedby="tour-body-{{ $tour }}">

            <div class="mb-3 flex items-center justify-between gap-4">
                <span data-tour-counter class="font-label text-[11px] font-bold uppercase tracking-widest text-secondary"></span>
                <button type="button" data-tour-skip
                        class="inline-flex h-8 w-8 items-center justify-center rounded-full text-primary/50 transition-colors hover:bg-primary/5 hover:text-primary focus:outline-none focus:ring-2 focus:ring-secondary/40"
                        aria-label="{{ __('messages.tour.ui.close') }}">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">close</span>
                </button>
            </div>

            <h2 id="tour-title-{{ $tour }}" data-tour-title class="mb-2 font-headline text-lg font-bold text-primary"></h2>
            <p id="tour-body-{{ $tour }}" data-tour-body class="font-label text-sm leading-relaxed text-primary/70"></p>

            <div class="mt-4 h-1 w-full overflow-hidden rounded-full bg-primary/10">
                <div data-tour-progress class="h-full rounded-full bg-secondary transition-[width] duration-200 ease-out motion-reduce:transition-none" style="width: 0%"></div>
            </div>

            <div class="mt-4 flex items-center justify-between gap-3">
                <button type="button" data-tour-skip
                        class="font-label text-xs font-semibold text-primary/50 underline-offset-4 transition-colors hover:text-primary hover:underline focus:outline-none focus:ring-2 focus:ring-secondary/40">
                    {{ __('messages.tour.ui.skip') }}
                </button>

                <div class="flex items-center gap-2">
                    <button type="button" data-tour-back
                            class="inline-flex min-h-9 items-center gap-1 rounded-xl border border-primary/15 px-3 py-2 font-label text-xs font-bold text-primary transition-colors hover:border-primary/30 focus:outline-none focus:ring-2 focus:ring-secondary/40">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">arrow_back</span>
                        {{ __('messages.tour.ui.back') }}
                    </button>
                    <button type="button" data-tour-next
                            class="inline-flex min-h-9 items-center gap-1 rounded-xl bg-secondary px-4 py-2 font-label text-xs font-bold text-white shadow-sm transition-colors hover:bg-secondary/90 focus:outline-none focus:ring-2 focus:ring-secondary/40">
                        <span data-tour-next-label>{{ __('messages.tour.ui.next') }}</span>
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.guidedTourConfig = {
            id: @json($tour),
            autoStart: @json((bool) $autoStart),
            i18n: @json(__('messages.tour.ui')),
            steps: @json($tourSteps),
        };
    </script>
@endif
