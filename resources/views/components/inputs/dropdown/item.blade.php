@props(['active' => false])
@php
    $classes = $active
        ? 'w-full text-left px-4 py-2 text-sm text-gray-900 bg-gray-400'
        : 'w-full text-left px-4 py-2 text-sm text-gray2 hover:bg-gray-400 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900';
    $element = $active ? 'p' : 'button';
@endphp

<{{ $element }} {{ $attributes->merge([
    'class' => $classes,
]) }}>
    {{ $slot }}
</{{ $element }}>
