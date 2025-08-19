@props(['input','id','title','placeholder'])
@php
$id = $id ?? $input;
@endphp
@if ($title)
<label for="{{ $input }}" class="text-smm text-tx-black font-bold mb-4">{{ $title}}</label>
@endif
<select wire:model.live="{{$input}}" class=" rounded-full w-full py-2 px-4 text-tx-black  border-light-blue focus:ring-0 focus:border-black2 leading-tight focus:outline-none focus:shadow-outline disabled:cursor-not-allowed disabled:text-gray2 placeholder:text-gray2 border-gray2 text-black2 font-dm_sans text-sm lg:text-base ring-0">
    <option value=""disabled selected class="text-gray">{{$placeholder}}</option>
    {{ $slot }}
</select>