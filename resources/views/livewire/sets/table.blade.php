<div>

    <div class="flex justify-between my-3">
        <x-search class="focus:border-indigo-400 focus:ring-indigo-400">
            Buscar...
        </x-search>

        <div class="flex justify-end space-x-2">
            <livewire:sets.create key="sets.create" />
        </div>
    </div>


    <div class="px-4 py-4 bg-white custom-box-shadow rounded-t-[16px]">
        <table class="table-auto w-full">
            <thead>
                <tr class="text-gray text-smm font-semibold text-left">
                    <th class="px-2 py-2 rounded-tl-md">
                        <div class="flex items-center">
                            <button wire:click="sortBy('name')" class="text-left">
                                Nombre
                            </button>
                            <x-sort-icon field="name" :sortField="$sortField" :sortAsc="$sortAsc" />
                        </div>
                    </th>
                    <th class="px-2 py-2">Descripción</th>
                    <th class="px-2 py-2 rounded-tr-md text-center"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sets as $set)
                    <tr class="text-left text-sm hover:bg-light-blue">
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $set->name }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $set->description }}</td>
            
                        <td class="border-b border-t border-light-blue px-2 py-2 text-center text-gray">
                            <div class="flex justify-center text-sm">
                                {{-- <button class="hover:text-blue" wire:click="$dispatch('editProduct', { id_product :{{ $product->id_product }}} )"
                                    wire:loading.attr="disabled">
                                    <x-icons.edit />
                                </button>
                                <button wire:click="$dispatch('showDeleteProduct', {id_product :{{ $product->id_product }} } )"
                                    class="text-principal-100 font-bold py-2 px-1 hover:text-error-red"
                                    wire:loading.attr="disabled">
                                    <x-icons.trash />
                                </button> --}}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($sets->count() == 0)
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 text-center">
                No hay registros
            </div>
        @endif
    </div>

    {{-- Paginación --}}
    <div class="px-4 py-4 relative min-h-20 mt-3">
        {{ $sets->links() }}
        <div class="w-[65px] absolute bottom-6 right-72">
            <x-inputs.dropdown class="py-1 px-1 border border-gray text-smm">
                <x-bgs.flex-center-between class="w-full">
                    <span x-text="$wire.perPage"></span>
                    <x-icons.drop />
                </x-bgs.flex-center-between>
                <x-slot name="options" class="-top-28">
                    @foreach ($optionsPerPage as $option)
                        <x-inputs.dropdown.item wire:key="option-perPage-{{ $option }}" :active="$option == $perPage"
                            x-on:click="$wire.set('perPage', {{ $option }}); show = false">
                            {{ $option }}
                        </x-inputs.dropdown.item>
                    @endforeach
                </x-slot>
            </x-inputs.dropdown>
        </div>
    </div>
    {{-- <livewire:sets.delete :key="'sets.delete'" />
    <livewire:sets.edit :key="'sets.edit'" /> --}}
</div>
