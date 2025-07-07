<div>

    <div class="flex justify-between my-3">
        <x-search class="focus:border-indigo-400 focus:ring-indigo-400">
            Buscar...
        </x-search>

        <div class="flex justify-end space-x-2">
            <x-secondary-button>
                Crear sets (falta decidir donde ponerlo)
            </x-secondary-button>
            <livewire:products.create key="products.create" />
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
                    <th class="px-2 py-2">Precio</th>
                    <th class="px-2 py-2">Proveedor</th>
                    <th class="px-2 py-2">Categoría</th>
                    <th class="px-2 py-2 text-center">Personalizable</th>
                    <th class="px-2 py-2 rounded-tr-md text-center"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="text-left text-sm hover:bg-light-blue">
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $product->name }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $product->description }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            ${{ number_format($product->price, 2) }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $product->supplier->name ?? '-' }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $product->category ?? '-' }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2 text-center">
                            {!! $product->is_customized ? '✅' : '❌' !!}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2 text-center text-gray">
                            <div class="flex justify-center text-sm">
                                <button class="hover:text-blue" wire:click="$dispatch('editProduct', { id_product :{{ $product->id_product }}} )"
                                    wire:loading.attr="disabled">
                                    <x-icons.edit />
                                </button>
                                <button wire:click="$emit('deleteProduct', {{ $product->id }})"
                                    class="text-principal-100 font-bold py-2 px-1 hover:text-error-red"
                                    wire:loading.attr="disabled">
                                    <x-icons.trash />
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($products->count() == 0)
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 text-center">
                No hay registros
            </div>
        @endif
    </div>

    {{-- Paginación --}}
    <div class="px-4 py-4 relative min-h-20 mt-3">
        {{ $products->links() }}
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
    <livewire:products.edit :key="'products.edit'" />
</div>
