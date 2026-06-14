@extends('layouts.user.app')

@section('title', 'Resumify — Mock Interview')

@section('content')
<div class="flex-1 flex flex-col min-w-0 overflow-hidden">

    <x-user.page-header title="Mock Interview HRD" backUrl="{{ route('dashboard') }}" />

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mx-4 mt-4 px-4 py-3 bg-secondary/10 border border-secondary/30 text-secondary rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex-1 overflow-y-auto px-4 py-6 md:px-8">
        <div class="max-w-xl mx-auto">

            {{-- Intro card --}}
            <div class="bg-surface-container-low rounded-2xl p-5 mb-6 border border-primary/10">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-secondary/15 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-secondary text-[20px]">smart_toy</span>
                    </div>
                    <div>
                        <p class="font-semibold text-primary text-sm">Halo! Saya Bu Sari</p>
                        <p class="text-primary/60 text-sm mt-1">
                            Saya akan mewawancarai Anda berdasarkan isi CV Anda yang sebenarnya —
                            bukan pertanyaan generik. Pilih CV dan posisi yang ingin Anda latih.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="bg-surface rounded-2xl border border-primary/10 p-6 space-y-5">

                {{-- Quota bar --}}
                @php $user = auth()->user(); @endphp
                <div class="flex items-center justify-between text-xs text-primary/50 pb-1">
                    <span>AI Credits tersisa</span>
                    <div class="flex items-center gap-2">
                        <div class="w-24 h-1.5 bg-primary/10 rounded-full overflow-hidden">
                            <div class="h-full bg-secondary rounded-full transition-all"
                                 style="width: {{ $user->getQuotaPercentage() }}%"></div>
                        </div>
                        <span class="font-bold text-primary">
                            {{ $user->getQuotaRemaining() }}/{{ $user->getQuotaLimit() }}
                        </span>
                    </div>
                </div>

                {{-- Resume selector --}}
                <div class="space-y-1.5">
                    <label for="cv-select" class="text-sm font-medium text-primary">
                        Pilih CV
                    </label>
                    @if($cvs->isEmpty())
                        <p class="text-sm text-primary/50 py-2">
                            Anda belum memiliki CV.
                            <a href="{{ route('dashboard') }}" class="text-secondary underline">Buat CV dulu</a>.
                        </p>
                    @else
                        <select id="cv-select"
                                class="w-full px-4 py-2.5 rounded-xl border border-primary/20 bg-surface text-primary text-sm
                                       focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/50 transition">
                            @foreach($cvs as $cv)
                                @php
                                    $targetJob = $cv->sections->where('type', 'target_job')->first();
                                    $prefill   = $targetJob->content['job_title'] ?? '';
                                @endphp
                                <option value="{{ $cv->id }}" data-job-target="{{ $prefill }}">
                                    {{ $cv->title }}
                                    @if($prefill) · {{ $prefill }} @endif
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                {{-- Job target --}}
                <div class="space-y-1.5">
                    <label for="job-target" class="text-sm font-medium text-primary">
                        Posisi yang dilamar
                    </label>
                    <input id="job-target" type="text" maxlength="200"
                           placeholder="cth. Backend Engineer, Product Manager…"
                           class="w-full px-4 py-2.5 rounded-xl border border-primary/20 bg-surface text-primary text-sm
                                  placeholder:text-primary/30 focus:outline-none focus:ring-2 focus:ring-secondary/30
                                  focus:border-secondary/50 transition" />
                </div>

                {{-- Error banner --}}
                <div id="error-banner"
                     class="hidden px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm"></div>

                {{-- Submit --}}
                <button id="start-btn" onclick="startInterview()"
                        class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                               bg-secondary text-white font-semibold text-sm
                               hover:bg-secondary/90 active:scale-[.98] transition-all duration-150
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="start-icon" class="material-symbols-outlined text-[18px]">play_arrow</span>
                    <span class="material-symbols-outlined text-[18px] animate-spin hidden" id="start-spinner">progress_activity</span>
                    <span id="start-label">Mulai Wawancara</span>
                </button>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const cvSelect      = document.getElementById('cv-select');
    const jobTargetInput = document.getElementById('job-target');
    const startBtn      = document.getElementById('start-btn');
    const startIcon     = document.getElementById('start-icon');
    const startSpinner  = document.getElementById('start-spinner');
    const startLabel    = document.getElementById('start-label');
    const errorBanner   = document.getElementById('error-banner');

    // Pre-fill job target from selected CV's stored job title
    if (cvSelect) {
        const prefill = () => {
            const opt = cvSelect.selectedOptions[0];
            if (opt && opt.dataset.jobTarget) {
                jobTargetInput.value = opt.dataset.jobTarget;
            }
        };
        cvSelect.addEventListener('change', prefill);
        prefill();
    }

    function setLoading(on) {
        startBtn.disabled = on;
        startIcon.classList.toggle('hidden', on);
        startSpinner.classList.toggle('hidden', !on);
        startLabel.textContent = on ? 'Memulai…' : 'Mulai Wawancara';
    }

    function showError(msg) {
        errorBanner.textContent = msg;
        errorBanner.classList.remove('hidden');
    }

    async function startInterview() {
        errorBanner.classList.add('hidden');

        const cvId      = cvSelect ? cvSelect.value : null;
        const jobTarget = jobTargetInput.value.trim();

        if (!cvId) { showError('Pilih CV terlebih dahulu.'); return; }
        if (!jobTarget) { showError('Isi posisi yang dilamar.'); return; }

        setLoading(true);

        try {
            const res = await fetch('{{ route("interview.start") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ cv_id: cvId, job_target: jobTarget }),
            });

            const data = await res.json();

            if (data.success) {
                window.location.href = `/interview/sessions/${data.session_id}`;
            } else {
                showError(data.message || 'Gagal memulai sesi. Coba lagi.');
                setLoading(false);
            }
        } catch (err) {
            showError('Terjadi kesalahan jaringan. Coba lagi.');
            setLoading(false);
        }
    }
</script>
@endpush
@endsection
