@php $cardClass = auth()->user()->isAdmin() ? 'admin-card' : 'bg-white rounded-3xl border border-primary/5 shadow-sm'; @endphp
<div
    x-data="{
        scrollThread(smooth = true) {
            this.$nextTick(() => {
                if (! this.$refs.thread) {
                    return;
                }

                this.$refs.thread.scrollTo({
                    top: this.$refs.thread.scrollHeight,
                    behavior: smooth ? 'smooth' : 'auto'
                });
            });
        },
        clearReplyBox() {
            if (! this.$refs.replyBody) {
                return;
            }

            this.$refs.replyBody.value = '';
            this.$refs.replyBody.dispatchEvent(new Event('input', { bubbles: true }));
            this.$refs.replyBody.focus();
        },
        submitReplyFromKeyboard(event) {
            if (event.shiftKey) {
                return;
            }

            event.preventDefault();
            event.target.form?.requestSubmit();
        }
    }"
    x-init="
        scrollThread(false);
        new MutationObserver(() => scrollThread()).observe($refs.thread, { childList: true, subtree: true });
    "
    x-on:ticket-reply-sent.window="if ($event.detail.ticketId === '{{ $ticket->id }}') { clearReplyBox(); scrollThread(); }"
>
    {{-- Conversation --}}
    <div class="{{ $cardClass }} overflow-hidden mb-6"
         wire:poll.7s>

        <div class="p-6 border-b border-primary/5 flex items-center justify-between">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest">{{ __('messages.tickets.chat.conversation') }}</h3>
            @php $badgeMap = ['open' => 'bg-red-100 text-red-600', 'pending' => 'bg-amber-100 text-amber-600', 'awaiting_closure' => 'bg-blue-100 text-blue-600', 'closed' => 'bg-primary/10 text-primary/50']; @endphp
            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeMap[$ticket->status] ?? '' }}">
                {{ __('messages.tickets.status.' . $ticket->status) }}
            </span>
        </div>

        <div x-ref="thread" class="min-h-[24rem] max-h-[36rem] overflow-y-auto custom-scrollbar">
            @forelse($ticket->replies as $reply)
                @php $isMine = $reply->user_id === auth()->id(); @endphp
                <div class="p-6">
                    <div class="flex items-end gap-2 max-w-[92%] md:max-w-[78%] {{ $isMine ? 'ml-auto flex-row-reverse' : '' }}">
                        <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0 bg-primary/10">
                            <img src="{{ $reply->sender?->avatar_url ?? 'https://ui-avatars.com/api/?name=?&background=fcdccb&color=4f3b2f' }}"
                                 alt="{{ $reply->sender?->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="{{ $isMine ? 'bg-secondary/10 rounded-tl-2xl rounded-bl-2xl rounded-tr-2xl' : 'bg-surface-container-low rounded-tr-2xl rounded-br-2xl rounded-tl-2xl' }} px-4 py-2.5">
                            <div class="flex items-center gap-2 mb-1 {{ $isMine ? 'flex-row-reverse' : '' }}">
                                <span class="text-xs font-label font-bold text-primary">{{ $reply->sender?->name ?? __('messages.tickets.chat.unknown_sender') }}</span>
                                @if($reply->sender?->isAdmin())
                                    <span class="text-[9px] bg-primary/10 text-primary/60 px-2 py-0.5 rounded-full uppercase tracking-wider font-bold">{{ __('messages.tickets.chat.support_badge') }}</span>
                                @endif
                            </div>
                            <p class="text-sm font-label text-primary/80 whitespace-pre-wrap">{{ $reply->body }}</p>
                            <p class="text-[10px] font-label text-primary/40 mt-1 {{ $isMine ? 'text-right' : '' }}">{{ $reply->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center">
                    <span class="material-symbols-outlined text-primary/20 text-[40px] block mb-2">forum</span>
                    <p class="text-sm font-label text-primary/40">{{ __('messages.tickets.chat.no_replies') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Close flow --}}
    @error('close')
        <p class="text-xs text-red-500 mb-3">{{ $message }}</p>
    @enderror

    @if(in_array($ticket->status, ['open', 'pending']) && auth()->user()->isAdmin())
        <div class="{{ $cardClass }} p-4 mb-6 flex items-center justify-between">
            <p class="text-sm font-label text-primary/60">{{ __('messages.tickets.chat.ready_to_close') }}</p>
            <button type="button" wire:click="requestClose" wire:loading.attr="disabled" wire:target="requestClose"
                    class="admin-btn-secondary text-sm">
                {{ __('messages.tickets.chat.request_close') }}
            </button>
        </div>
    @elseif($ticket->status === 'awaiting_closure')
        @if($ticket->close_requested_by === auth()->id())
            <div class="{{ $cardClass }} p-4 mb-6">
                <p class="text-sm font-label text-primary/60">{{ __('messages.tickets.chat.waiting_other_party') }}</p>
            </div>
        @else
            <div class="{{ $cardClass }} p-4 mb-6 flex items-center justify-between">
                <p class="text-sm font-label text-primary/60">{{ __('messages.tickets.chat.other_requested_close') }}</p>
                <div class="flex gap-2">
                    <button type="button" wire:click="rejectClose" wire:loading.attr="disabled" wire:target="rejectClose"
                            class="admin-btn-secondary text-sm">
                        {{ __('messages.tickets.chat.reject') }}
                    </button>
                    <button type="button" wire:click="confirmClose" wire:loading.attr="disabled" wire:target="confirmClose"
                            class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-bold bg-primary text-tertiary hover:bg-primary/90 transition">
                        {{ __('messages.tickets.chat.confirm_close') }}
                    </button>
                </div>
            </div>
        @endif
    @endif

    {{-- Reply form --}}
    @if($ticket->status !== 'closed')
        <form
            wire:submit="sendReply"
            class="{{ $cardClass }} p-6"
        >
            <label for="ticket-chat-body-{{ $ticket->id }}" class="sr-only">{{ __('messages.tickets.chat.reply_label') }}</label>
            <textarea wire:model="body" x-ref="replyBody" x-on:keydown.enter="submitReplyFromKeyboard($event)" id="ticket-chat-body-{{ $ticket->id }}" rows="3" required
                      placeholder="{{ __('messages.tickets.chat.reply_placeholder') }}"
                      aria-label="{{ __('messages.tickets.chat.reply_label') }}"
                      class="w-full bg-surface border border-primary/10 rounded-lg px-4 py-3 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30 resize-none"></textarea>
            @error('body')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
            <div class="flex justify-end mt-4">
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="sendReply"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold bg-primary text-tertiary hover:bg-primary/90 transition disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="sendReply" class="material-symbols-outlined text-[18px]">send</span>
                    <span wire:loading wire:target="sendReply" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="sendReply">{{ __('messages.tickets.chat.send_reply') }}</span>
                    <span wire:loading wire:target="sendReply">{{ __('messages.tickets.chat.sending') }}</span>
                </button>
            </div>
        </form>
    @else
        <div class="bg-primary/5 rounded-3xl p-6 text-center">
            <span class="material-symbols-outlined text-primary/30 text-[20px] block mb-1">lock</span>
            <p class="text-sm font-label text-primary/60">{{ __('messages.tickets.chat.closed_notice') }}</p>
        </div>
    @endif
</div>
