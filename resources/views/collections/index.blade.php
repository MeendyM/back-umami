<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Colecciones') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="w-auto mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <h1>Sets</h1>
                    @livewire('sets.table')
                </div>
                <div>
                    <h1>Categorias</h1>
                    @livewire('categories.table')
                </div>
                <div>
                    <h1>Proovedores</h1>
                    @livewire('suppliers.table')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
