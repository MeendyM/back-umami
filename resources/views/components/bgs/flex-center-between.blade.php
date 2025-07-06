@props(['element' => 'div'])
<{{ $element }} {{ $attributes->merge(['class' => 'flex items-center justify-between']) }}>
    {{ $slot }}
</{{ $element }}>
