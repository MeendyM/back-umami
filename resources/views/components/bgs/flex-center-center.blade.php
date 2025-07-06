@props(['element' => 'div'])
<{{ $element }} {{ $attributes->merge(['class' => 'flex items-center justify-center']) }}>
    {{ $slot }}
</{{ $element }}>
