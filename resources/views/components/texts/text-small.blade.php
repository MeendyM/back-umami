@props(['element' => 'p'])
<{{$element}} {{ $attributes->merge(['class' => 'text-smm text-tx-black']) }}>
    {{ $slot }}
</{{$element}}>
