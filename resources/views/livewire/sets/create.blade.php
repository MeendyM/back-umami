<div>
    <div class="flex w-full justify-end">
        <x-primary-button wire:click="openModal()">
            <x-icons.create class="fill-white mr-2 w-4" />
            <span>Agregar set</span>
        </x-primary-button>
    </div>

    <x-modal-header wire:model="modal" title="Agregar set">
        <form wire:submit.prevent="save">
            <div class="space-y-4">

                {{-- Nombre --}}
                <div>
                    <x-input-form input="name" placeholder="Nombre del set" wire:model.defer="name">
                        <x-texts.text-small>Nombre</x-texts.text-small>
                    </x-input-form>
                    @error('name')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div>
                    <x-input-form input="description" placeholder="Descripción del set" wire:model.defer="description">
                        <x-texts.text-small>Descripción</x-texts.text-small>
                    </x-input-form>
                    @error('description')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>


                <div x-data="{ open: false }" class="relative">

                    <x-texts.text-small class="font-bold mb-1">Selecciona productos para tu set</x-texts.text-small>
                    <div class="flex justify-between my-3">
                        <x-search wire:model="search" @focus="open = true" @click.outside="open = false">
                            Buscar...
                        </x-search>

                        <x-primary-button wire:click.prevent="cleanSelection()">
                            <x-icons.trash class="fill-white mr-2 w-4" />
                            <span>Limpiar</span>
                        </x-primary-button>
                    </div>


                    @if (strlen($search) > 0)
                        <ul x-show="open" x-transition
                            class="absolute z-10 bg-white shadow-lg rounded-lg mt-2 w-full max-h-60 overflow-auto border border-light-blue">
                            @forelse($products as $product)
                                <li wire:click="selectProduct({{ $product->id_product }})"
                                    class="px-4 py-3 hover:bg-light-blue cursor-pointer text-sm">
                                    <div class="font-semibold text-black2">{{ $product->name }}<span
                                            class="font-normal"> - ${{ number_format($product->price, 2) }} -
                                            {{ $product->category->name }} - {{ $product->supplier->name }}</span></div>

                                </li>
                            @empty
                                <li class="px-4 py-3 text-sm text-gray-500">No se encontraron productos.</li>
                            @endforelse
                        </ul>
                    @endif
                </div>

                {{-- Productos seleccionados --}}
                @if (count($selected))
                    <div class="mt-4">
                        <x-texts.text-small class="font-bold mb-1">Estos productos estaran en tu
                            set</x-texts.text-small>
                        <ul class="space-y-2">
                            @foreach ($selected as $id)
                                @php
                                    $product = \App\Models\Product::find($id);
                                @endphp
                                @if ($product)
                                    <li
                                        class="p-2 bg-gray-100 rounded flex justify-between items-center hover:bg-light-blue">
                                        <span>{{ $product->name }} - {{$product->price }} - {{$product->category->name}} - {{$product->supplier->name}}</span>
                                        <button wire:click.prevent="removeProduct({{ $product->id_product }})"
                                            class="text-principal-100 font-bold py-2 px-1 hover:text-error-red"
                                            wire:loading.attr="disabled">
                                            <x-icons.trash />
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>

            {{-- Footer --}}
            <x-slot name="footer" class="space-x-1 space-y-3 flex flex-col">
                <x-primary-button wire:loading.attr="disabled" type="submit">
                    <x-btns.loading wire:loading />
                    Crear set
                </x-primary-button>
            </x-slot>
        </form>
    </x-modal-header>
</div>
