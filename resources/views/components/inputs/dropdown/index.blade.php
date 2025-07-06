<div class="relative" x-data="{ show: false }">
    <x-inputs.container element="button" x-on:click="show = !show" {{ $attributes }}>
        {{ $slot }}
    </x-inputs.container>
    @if ($options)
        <div x-show="show" style="display: none;">
            <div x-on:click.away="show = false"
                {{ $options->attributes->merge(['class' => 'absolute left-0 w-full bg-white border border-gray rounded-lg shadow-lg z-10']) }}>
                {{ $options }}
            </div>
        </div>
    @endif
</div>
