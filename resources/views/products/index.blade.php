<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Productos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="min-w-7xl  mx-auto sm:px-6 lg:px-8">
            <div class="bg-white h-[90vh] shadow-xl sm:rounded-lg">
                <div class="flex justify-center items-center w-full h-full">
                    @livewire('dashboard.products')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
