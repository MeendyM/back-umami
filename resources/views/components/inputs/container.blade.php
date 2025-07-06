@props(['element' => 'input'])
<{{ $element }}
    {{ $attributes->merge(['class' => 'appearance-none border rounded w-full py-1 px-2 text-gray focus:ring-0 focus:border-primary leading-tight focus:outline-none focus:shadow-outline border-gray2']) }}>
    {{ $slot }}
</{{ $element }}>
