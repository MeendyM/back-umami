@props(['id', 'maxWidth', 'title', 'button1', 'button2', 'btn_text1', 'btn_text2', 'footer' => null, 'close'=>'closeModal()'])

@php
$id = $id ?? md5($attributes->wire('model'));

$maxWidth = [
'sm' => 'sm:max-w-sm',
'md' => 'sm:max-w-md',
'lg' => 'sm:max-w-lg',
'xl' => 'sm:max-w-xl',
'2xl' => 'sm:max-w-2xl',
][$maxWidth ?? 'lg'];

$button1 = $button1 ?? 'closeModal()';

$button2 = $button2 ?? 'save()';

$title = $title ?? 'Modal title';
$btn_text1 = $btn_text1 ?? 'Cancelar';
$btn_text2 = $btn_text2 ?? 'Guardar';
@endphp

<div x-data="{ show: @entangle($attributes->wire('model')) }" x-on:close.stop="show = false" x-on:keydown.escape.window="show = false" x-show="show" id="{{ $id }}" class="jetstream-modal fixed inset-0 px-4 py-6 sm:px-0 z-50 flex items-start overflow-y-auto" style="display: none;">
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-modal-bg-black opacity-[.60]"></div>
    </div>

    <div x-show="show" class="mb-6 bg-bgk-gray rounded-lg shadow-xl transform transition-all w-full {{ $maxWidth }} mx-auto" x-trap.inert.noscroll="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="bg-primary relative pt-8">
            <h2 class="text-blue text-xl font-semibold text-center">{{ $title }}</h2>
            <span class="text-white absolute z-65 -top-7 right-0 cursor-pointer" wire:click="{{$close}}"><x-icons.close /></span>
        </div>

        <!-- Contenedor con scroll que incluye el contenido y el footer -->
        <div class="p-4 md:p-5 space-y-4 overflow-y-auto max-h-[85vh]">
            <div class="text-left">
                {{ $slot }}
            </div>
            
            @if ($footer)
            <!-- Footer personalizado que se desplaza con el contenido -->
            <div {{ $footer->attributes->merge(['class' => 'flex justify-center']) }}>
                {{ $footer }}
            </div>
            @else
            <!-- Footer por defecto que se desplaza con el contenido -->
            <div class="flex space-x-1 space-y-1 flex-col-reverse md:flex-row mt-4">
                <div class="flex flex-1">
                    <x-btns.btn2 wire:click="{{ $button1 }}" type="button">
                        {{ $btn_text1 }}
                    </x-btns.btn2>
                </div>
                <div class="flex flex-1">
                    <x-btns.btn1 wire:click.prevent="{{ $button2 }}" type="button">
                        <x-btns.loading wire:loading />
                        {{ $btn_text2 }}
                    </x-btns.btn1>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
