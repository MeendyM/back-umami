<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Productos') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:py-6 lg:py-8 sm:px-8 lg:px-10">
            @livewire('products.table')
        </div>
    </div>
</x-app-layout>
