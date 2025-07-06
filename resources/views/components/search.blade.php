<div class="relative">
    <x-icons.search class="absolute left-4 lg:left-3 top-5 lg:top-6 transform -translate-y-1/2 text-gray"></x-icons.search>
    <input wire:model.live="search" type="text" placeholder='{{ $slot }}' name="search" id="search"
        autocomplete="off"
        {{ $attributes->merge(['class' => 'form-input text-gray text-sm lg:text-base border-light-blue rounded-full shadow-sm mt-1 pl-10 block w-[500px]']) }}>
</div>
