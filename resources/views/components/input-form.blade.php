@props(['input', 'type', 'id', 'value', 'placeholder', 'disabled', 'class'])

@php
$type = $type ?? 'text';
$id = $id ?? $input;
$placeholder = $placeholder ?? '';
$disabled = $disabled ?? false;
$class = $class ?? 'mt-2 mb-2';
$background = $disabled ? "bg-bgk-gray" : "";
$classes =
'border rounded-full w-full py-2 px-4 '.$background.' text-tx-black border border-light-blue text-sm focus:ring-0 focus:border-black2 leading-tight focus:outline-none focus:shadow-outline disabled:cursor-not-allowed disabled:text-gray2 placeholder:text-gray2 border-gray2 text-black2 font-dm_sans text-sm lg:text-base ring-0';
@endphp
@switch($type)
@case('radio')
<div class="flex mr-5 mt-2 items-center">
    <input type="radio" class="text-primary focus:ring-transparent ml-2 disabled:text-gray2 text-sm lg:text-base" wire:model.live="{{ $input }}" value="{{ $value }}" @disabled($disabled)>
    <label for="{{ $id }}" class="block ml-2"><x-texts.text-small>{{ $slot }}</x-texts.text-small></label>
</div>
@break

@case('password')
<x-inputs.container-label class="{{ $class }}" x-data="{ show: false }">
    {{ $slot }}
    <x-slot name="container">
        <x-inputs.input-options>
            <input type="password" placeholder="{{ $placeholder }}" wire:model.live="{{ $input }}" x-bind:type="show ? 'text' : 'password'" <?php echo $type == 'number' ? 'max="10" min = "1"' : ''; ?> @class([$classes, 'pr-10'=> true])
            @disabled($disabled) autocomplete="off">

            <x-slot name="right">
                <button type="button" x-on:click="show = !show" class="text-gray2 hover:text-hover focus:outline-none bg-white mr-4">
                    <x-icons.eye />
                </button>
            </x-slot>
        </x-inputs.input-options>
    </x-slot>
</x-inputs.container-label>
@break

@case('postalCode')
<x-inputs.container-label class="{{ $class }}">
    {{ $slot }}
    <x-slot name="container">
        <x-inputs.input-options>
            @if (isset($left))
            <x-slot name="left">
                {{ $left }}
            </x-slot>
            @endif
            <input type="{{ $type }}" placeholder="{{ $placeholder }}" wire:model.live="{{ $input }}" type="text" @class([$classes, 'pl-20'=> isset($left)]) @disabled($disabled) autocomplete="off" minlength="5" maxlength="5">
        </x-inputs.input-options>
    </x-slot>
</x-inputs.container-label>
@break

@default
<x-inputs.container-label class="{{ $class }}">
    {{ $slot }}
    <x-slot name="container">
        <x-inputs.input-options>
            @if (isset($left))
            <x-slot name="left">
                {{ $left }}
            </x-slot>
            @endif
            <input type="{{ $type }}" placeholder="{{ $placeholder }}" wire:model="{{ $input }}" <?php echo $type == 'number' ? 'min = "0"' : ''; ?> @class([$classes, 'pl-20'=> isset($left)]) @disabled($disabled) autocomplete="off">
        </x-inputs.input-options>
    </x-slot>
</x-inputs.container-label>
@endswitch