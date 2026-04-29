@props([
    'plan',
    'price',
    'period',
    'features' => [],
    'disabledFeatures' => [],
    'isPremium' => false,
    'buttonText' => 'Select Plan',
    'isCurrentPlan' => false,
])

<div @class([
    'bg-tertiary rounded-2xl p-10 flex flex-col shadow-sm transition-all',
    'border border-primary/10 hover:shadow-lg' => !$isPremium,
    'border-2 border-secondary premium-glow shadow-xl relative overflow-hidden hover:scale-[1.01]' => $isPremium,
])>
    @if($isPremium)
    <div class="absolute top-6 right-[-35px] bg-secondary text-tertiary px-10 py-1 rotate-45 text-[10px] font-bold tracking-widest uppercase">
        MOST POPULAR
    </div>
    @endif

    <div class="mb-8">
        <div class="flex items-center gap-2 mb-2">
            <h3 class="font-headline text-2xl font-bold text-primary">{{ $plan }}</h3>
            @if($isPremium)
            <span class="bg-secondary/10 text-secondary px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                <span class="material-symbols-outlined text-[12px] icon-filled">auto_awesome</span>
                AI Powered
            </span>
            @endif
        </div>
        <div class="flex items-baseline gap-1">
            <span class="text-4xl font-headline font-bold text-primary">{{ $price }}</span>
            <span class="text-primary/60 font-body">/ {{ $period }}</span>
        </div>
    </div>

    <ul class="space-y-5 mb-12 flex-1">
        @foreach($features as $feature)
        <li class="flex items-start gap-3">
            @if($isPremium)
            <span class="material-symbols-outlined text-secondary text-xl icon-filled">check_circle</span>
            @else
            <span class="material-symbols-outlined text-primary/40 text-xl">check_circle</span>
            @endif
            @if(is_array($feature))
            <div class="flex flex-col">
                <span class="font-body {{ $isPremium ? 'text-primary font-medium' : 'text-primary/80' }}">{{ $feature['title'] }}</span>
                @if(isset($feature['subtitle']))
                <span class="text-xs text-secondary italic">{{ $feature['subtitle'] }}</span>
                @endif
            </div>
            @else
            <span class="font-body {{ $isPremium ? 'text-primary font-medium' : 'text-primary/80' }}">{{ $feature }}</span>
            @endif
        </li>
        @endforeach
        @foreach($disabledFeatures as $feature)
        <li class="flex items-start gap-3 opacity-40">
            <span class="material-symbols-outlined text-primary/40 text-xl">cancel</span>
            <span class="font-body text-primary/80 line-through">{{ $feature }}</span>
        </li>
        @endforeach
    </ul>

    @if($isCurrentPlan)
    <x-user.button variant="ghost" class="w-full py-4 rounded-xl cursor-not-allowed opacity-60 border border-primary/10" disabled>
        Current Plan
    </x-user.button>
    @else
    <x-user.button variant="primary" class="w-full py-4 rounded-xl shadow-lg {{ $isPremium ? '!bg-secondary hover:opacity-90' : '' }}">
        {{ $buttonText }}
    </x-user.button>
    @endif
</div>
