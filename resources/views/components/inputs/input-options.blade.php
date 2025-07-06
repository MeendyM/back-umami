<div class="w-full relative" {{ $attributes }}>
    @if (isset($left))
        <div {{ $left->attributes->merge(['class' => 'absolute inset-y-0 left-0 flex items-center justify-center']) }}>
            {{ $left }}
        </div>
    @endif
    {{ $slot }}
    @if (isset($right))
        <div
            {{ $right->attributes->merge(['class' => 'absolute inset-y-0 right-0 flex items-center justify-center']) }}>
            {{ $right }}
        </div>
    @endif
</div>
