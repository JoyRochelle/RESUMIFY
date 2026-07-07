@extends('layouts.user.app')

@section('title', 'Resumify — ' . __('messages.interview.session.page_title'))

@section('content')
{{-- NOTE: no flex-1 here — as a flex item with flex-basis:0% the browser
     ignores the height property, so h-dvh never took effect and the page
     grew to content height (body-level scroll). flex-basis must stay auto. --}}
<div class="flex flex-col min-w-0 overflow-hidden h-dvh overscroll-none
            pb-[calc(4.75rem+env(safe-area-inset-bottom))] md:pb-0">

    {{-- Header bar --}}
    <header class="shrink-0 flex items-center justify-between px-4 md:px-6 py-3
                   border-b border-primary/10 bg-surface z-20">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('interview.index') }}"
               class="text-primary/50 hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div class="w-8 h-8 rounded-full bg-secondary/15 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-secondary text-[16px]">smart_toy</span>
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-primary text-sm leading-tight">{{ __('messages.interview.session.header_name') }}</p>
                <p class="text-primary/50 text-xs truncate">{{ $session->job_target }}</p>
            </div>
        </div>

        @if($session->status === 'active')
        <button onclick="openEndModal()"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200
                       text-red-600 text-xs font-medium hover:bg-red-50 transition-all">
            <span class="material-symbols-outlined text-[15px]">stop_circle</span>
            {{ __('messages.interview.session.end_session') }}
        </button>
        @else
        <span class="px-3 py-1.5 rounded-lg bg-primary/5 text-primary/40 text-xs font-medium">
            {{ __('messages.interview.session.session_ended_badge') }}
        </span>
        @endif
    </header>

    {{-- Session completed banner --}}
    @if($session->status !== 'active')
    <div class="shrink-0 px-4 py-2.5 bg-primary/5 border-b border-primary/10
                text-primary/60 text-sm flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-4 text-center">
        <span>{{ __('messages.interview.session.ended_on', ['date' => $session->ended_at?->format('d M Y, H:i')]) }}</span>

        @if($session->feedback)
            <a href="{{ route('interview.feedback', $session) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary/10
                      text-secondary text-xs font-semibold hover:bg-secondary/20 transition-all">
                <span class="material-symbols-outlined text-[15px]">analytics</span>
                {{ __('messages.interview.session.view_report') }}
            </a>
        @else
            <form method="POST" action="{{ route('interview.feedback.generate', $session) }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary
                               text-white text-xs font-semibold hover:bg-secondary/90 active:scale-[.98] transition-all">
                    <span class="material-symbols-outlined text-[15px]">analytics</span>
                    {{ __('messages.interview.session.generate_report') }}
                </button>
            </form>
        @endif
    </div>
    @endif

    {{-- Message list --}}
    <div id="messages"
         class="flex-1 overflow-y-auto overscroll-contain px-4 md:px-6 py-5 space-y-4 custom-scrollbar">

        @foreach($session->messages as $msg)
            @if($msg->role === 'assistant')
                {{-- Ms. Sarah bubble (left) --}}
                <div class="flex items-end gap-2 max-w-[85%] md:max-w-[70%]">
                    <div class="w-7 h-7 rounded-full bg-secondary/15 flex items-center justify-center shrink-0 mb-1">
                        <span class="material-symbols-outlined text-secondary text-[13px]">smart_toy</span>
                    </div>
                    <div class="bg-surface-container-low rounded-tr-2xl rounded-br-2xl rounded-tl-2xl
                                px-4 py-2.5 text-primary text-sm leading-relaxed">
                        {!! nl2br(e($msg->content)) !!}
                    </div>
                </div>
            @else
                {{-- User bubble (right) --}}
                <div class="flex items-end justify-end gap-2 max-w-[85%] md:max-w-[70%] ml-auto">
                    <div class="bg-secondary/10 rounded-tl-2xl rounded-bl-2xl rounded-tr-2xl
                                px-4 py-2.5 text-primary text-sm leading-relaxed">
                        {!! nl2br(e($msg->content)) !!}
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Typing indicator --}}
        <div id="typing" class="hidden flex items-end gap-2 max-w-[85%]">
            <div class="w-7 h-7 rounded-full bg-secondary/15 flex items-center justify-center shrink-0 mb-1">
                <span class="material-symbols-outlined text-secondary text-[13px]">smart_toy</span>
            </div>
            <div class="flex items-center gap-1.5 bg-surface-container-low
                        rounded-tr-2xl rounded-br-2xl rounded-tl-2xl px-4 py-3">
                <span class="w-2 h-2 bg-primary/30 rounded-full animate-bounce [animation-delay:0ms]"></span>
                <span class="w-2 h-2 bg-primary/30 rounded-full animate-bounce [animation-delay:150ms]"></span>
                <span class="w-2 h-2 bg-primary/30 rounded-full animate-bounce [animation-delay:300ms]"></span>
            </div>
        </div>

    </div>

    {{-- Input bar --}}
    @if($session->status === 'active')
    <div class="shrink-0 border-t border-primary/10 bg-surface px-4 md:px-6 py-3">
        <div class="flex items-end gap-3 max-w-3xl mx-auto">
            <textarea id="user-input" rows="1"
                      placeholder="{{ __('messages.interview.session.input_placeholder') }}"
                      class="flex-1 resize-none rounded-xl border border-primary/20 bg-surface-container-low
                             px-4 py-2.5 text-sm text-primary placeholder:text-primary/30
                             focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/50
                             transition max-h-40 custom-scrollbar leading-relaxed"></textarea>
            <button id="send-btn" onclick="sendMessage()"
                    class="w-10 h-10 rounded-xl bg-secondary flex items-center justify-center shrink-0
                           text-white hover:bg-secondary/90 active:scale-95 transition-all
                           disabled:opacity-40 disabled:cursor-not-allowed">
                <span id="send-icon" class="material-symbols-outlined text-[18px]">send</span>
                <span id="send-spinner" style="display:none" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
            </button>
        </div>
    </div>
    @endif

</div>

{{-- End Session Confirmation Modal --}}
@if($session->status === 'active')
<div id="end-modal"
     class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm
            flex items-center justify-center px-4"
     onclick="if(event.target===this) closeEndModal()">
    <div class="bg-surface rounded-2xl p-6 max-w-sm w-full shadow-xl
                transform transition-all duration-200 scale-95 opacity-0"
         id="end-modal-card">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-500 text-[20px]">stop_circle</span>
            </div>
            <div>
                <p class="font-semibold text-primary text-sm">{{ __('messages.interview.session.end_modal_title') }}</p>
                <p class="text-primary/50 text-xs">{{ __('messages.interview.session.end_modal_subtitle') }}</p>
            </div>
        </div>
        <p class="text-primary/70 text-sm mb-5">
            {{ __('messages.interview.session.end_modal_body') }}
        </p>
        <div class="flex gap-3">
            <button onclick="closeEndModal()"
                    class="flex-1 px-4 py-2.5 rounded-xl border border-primary/20 text-primary/70
                           text-sm font-medium hover:border-primary/40 transition">
                {{ __('messages.interview.session.cancel') }}
            </button>
            <form method="POST" action="{{ route('interview.end', $session) }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full px-4 py-2.5 rounded-xl bg-red-500 text-white
                               text-sm font-semibold hover:bg-red-600 active:scale-[.98] transition">
                    {{ __('messages.interview.session.end_session') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    const messageList = document.getElementById('messages');
    const userInput   = document.getElementById('user-input');
    const sendBtn     = document.getElementById('send-btn');
    const sendIcon    = document.getElementById('send-icon');
    const sendSpinner = document.getElementById('send-spinner');
    const typing      = document.getElementById('typing');

    const SESSION_ID  = '{{ $session->id }}';
    const MESSAGE_URL = '{{ route("interview.message", $session) }}';
    const STREAM_URL  = '{{ route("interview.stream", $session) }}';
    const CSRF        = '{{ csrf_token() }}';

    // Scroll to bottom on load
    scrollToBottom();

    // Textarea auto-resize + Enter to send
    if (userInput) {
        userInput.addEventListener('input', () => {
            userInput.style.height = 'auto';
            userInput.style.height = userInput.scrollHeight + 'px';
        });

        userInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Mobile keyboards pan the visual viewport to keep the focused
        // textarea visible; on close that pan isn't always undone, leaving
        // the h-dvh chat shifted up. Re-anchor once the keyboard is gone.
        userInput.addEventListener('blur', () => {
            window.scrollTo(0, 0);
            setTimeout(scrollToBottom, 150);
        });
    }

    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', () => {
            window.scrollTo(0, 0);
            scrollToBottom();
        });
    }

    function scrollToBottom() {
        if (messageList) {
            messageList.scrollTop = messageList.scrollHeight;
        }
    }

    function setLoading(on) {
        if (!sendBtn) return;
        sendBtn.disabled = on;
        sendIcon.style.display = on ? 'none' : 'inline-block';
        sendSpinner.style.display = on ? 'inline-block' : 'none';
        if (userInput) userInput.disabled = on;
    }

    function appendBubble(role, text) {
        const isAssistant = role === 'assistant';
        const wrapper = document.createElement('div');

        if (isAssistant) {
            wrapper.className = 'flex items-end gap-2 max-w-[85%] md:max-w-[70%]';
            wrapper.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-secondary/15 flex items-center justify-center shrink-0 mb-1">
                    <span class="material-symbols-outlined text-secondary text-[13px]">smart_toy</span>
                </div>
                <div class="bg-surface-container-low rounded-tr-2xl rounded-br-2xl rounded-tl-2xl
                            px-4 py-2.5 text-primary text-sm leading-relaxed">${escHtml(text).replace(/\n/g, '<br>')}</div>
            `;
        } else {
            wrapper.className = 'flex items-end justify-end gap-2 max-w-[85%] md:max-w-[70%] ml-auto';
            wrapper.innerHTML = `
                <div class="bg-secondary/10 rounded-tl-2xl rounded-bl-2xl rounded-tr-2xl
                            px-4 py-2.5 text-primary text-sm leading-relaxed">${escHtml(text).replace(/\n/g, '<br>')}</div>
            `;
        }

        // Insert before typing indicator
        messageList.insertBefore(wrapper, typing);
        scrollToBottom();
    }

    function escHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function createStreamingBubble() {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-end gap-2 max-w-[85%] md:max-w-[70%]';
        wrapper.innerHTML = `
            <div class="w-7 h-7 rounded-full bg-secondary/15 flex items-center justify-center shrink-0 mb-1">
                <span class="material-symbols-outlined text-secondary text-[13px]">smart_toy</span>
            </div>
            <div class="bg-surface-container-low rounded-tr-2xl rounded-br-2xl rounded-tl-2xl
                        px-4 py-2.5 text-primary text-sm leading-relaxed"></div>
        `;
        messageList.insertBefore(wrapper, typing);
        scrollToBottom();
        return wrapper.querySelector('div:last-child');
    }

    async function sendMessage() {
        if (!userInput) return;
        const text = userInput.value.trim();
        if (!text) return;

        userInput.value = '';
        userInput.style.height = 'auto';

        appendBubble('user', text);
        setLoading(true);
        typing.classList.remove('hidden');
        scrollToBottom();

        const controller = new AbortController();
        const timeoutId  = setTimeout(() => controller.abort(), 100000);

        try {
            const res = await fetch(STREAM_URL, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept':       'text/event-stream',
                },
                body:   JSON.stringify({ content: text }),
                signal: controller.signal,
            });

            clearTimeout(timeoutId);
            typing.classList.add('hidden');

            if (!res.ok) {
                appendBubble('assistant', @json(__('messages.interview.session.error_occurred')));
                setLoading(false);
                return;
            }

            const bubble  = createStreamingBubble();
            const reader  = res.body.getReader();
            const decoder = new TextDecoder();
            let buffer    = '';
            let fullText  = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });

                const lines = buffer.split('\n');
                buffer = lines.pop();

                for (const line of lines) {
                    if (!line.startsWith('data: ')) continue;
                    try {
                        const data = JSON.parse(line.slice(6));
                        if (data.token) {
                            fullText += data.token;
                            bubble.textContent = fullText;
                            scrollToBottom();
                        }
                        if (data.done) {
                            bubble.innerHTML = escHtml(fullText).replace(/\n/g, '<br>');
                            reader.cancel();
                            break;
                        }
                        if (data.error) {
                            bubble.textContent = @json(__('messages.interview.session.error_occurred'));
                            reader.cancel();
                            break;
                        }
                    } catch (_) { /* skip malformed SSE line */ }
                }
            }

        } catch (err) {
            clearTimeout(timeoutId);
            typing.classList.add('hidden');
            appendBubble('assistant',
                err.name === 'AbortError'
                    ? @json(__('messages.interview.session.timeout_error'))
                    : @json(__('messages.interview.session.connection_lost')));
        }

        setLoading(false);
    }

    // End session modal
    const endModal     = document.getElementById('end-modal');
    const endModalCard = document.getElementById('end-modal-card');

    function openEndModal() {
        if (!endModal) return;
        endModal.classList.remove('hidden');
        requestAnimationFrame(() => {
            endModalCard.classList.remove('scale-95', 'opacity-0');
            endModalCard.classList.add('scale-100', 'opacity-100');
        });
    }

    function closeEndModal() {
        if (!endModal) return;
        endModalCard.classList.remove('scale-100', 'opacity-100');
        endModalCard.classList.add('scale-95', 'opacity-0');
        setTimeout(() => endModal.classList.add('hidden'), 200);
    }
</script>
@endpush
@endsection
