<div>

    <div class="flex justify-between my-3">
        <x-search class="focus:border-indigo-400 focus:ring-indigo-400">
            Buscar...
        </x-search>
        <div class="flex justify-end space-x-2">
            {{-- <livewire:discounts.create key="discounts.create" /> --}}
        </div>
    </div>


    <div class="px-4 py-4 bg-white custom-box-shadow rounded-t-[16px]">
        <table class="table-auto w-full">
            <thead>
                <tr class="text-gray text-smm font-semibold text-center">
                    <th class="px-2 py-2 rounded-tl-md">
                        <div class="flex items-center">
                            <button wire:click="sortBy('code')" class="text-left">
                                Código de descuento
                            </button>
                            <x-sort-icon field="code" :sortField="$sortField" :sortAsc="$sortAsc" />
                        </div>
                    </th>
                    <th class="px-2 py-2">Orden</th>
                    <th class="px-2 py-2">Usuario</th>
                    <th class="px-2 py-2">Fecha de uso
                        <span
                            class="ml-1 inline-flex items-center justify-center w-4 h-4 text-xs rounded-full bg-gray-200 text-gray-600 cursor-help"
                            title="Fecha en la que el descuento fue utilizado">
                            ?
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($uses as $use)
                    <tr class=" text-sm hover:bg-light-blue text-center">
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $use->discount->code }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $use->id_order }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $use->user->email }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            {{ \Carbon\Carbon::parse($use->use_at)->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($uses->count() == 0)
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 text-center">
                No hay registros
            </div>
        @endif
    </div>

    {{-- Paginación --}}
    <div class="px-4 py-4 relative min-h-20 mt-3">
        {{ $uses->links() }}
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
    {{-- <livewire:discounts.delete :key="'discounts.delete'" /> --}}
    {{-- <livewire:discounts.edit :key="'discounts.edit'" /> --}}
</div>
