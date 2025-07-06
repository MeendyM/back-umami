@if ($sortField !== $field)
<span class="flex items-center ml-2">
    <x-icons.down-arrow></x-icons.down-arrow>
    <x-icons.up-arrow></x-icons.up-arrow>
</span>
@elseIf ($sortAsc)
<span class="flex items-center ml-2">
    <x-icons.down-arrow></x-icons.down-arrow>
    <x-icons.up-arrow></x-icons.up-arrow>
</span>
@else
<span class="flex items-center ml-2">
    <x-icons.down-arrow></x-icons.down-arrow>
    <x-icons.up-arrow></x-icons.up-arrow>
</span>
@endif