<div>
    <div class="flex w-full justify-end">
        <x-primary-button wire:click="openModal()">
            <x-icons.create class="fill-white mr-2 w-4" />
            <span>Agregar producto</span>
        </x-primary-button>
    </div>

    <x-modal-header wire:model="modal" title="Agregar producto">
        <form wire:submit.prevent="submit">
            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <x-input-form input="name" placeholder="Nombre del producto">
                        <x-texts.text-small>Nombre</x-texts.text-small>
                    </x-input-form>
                    @error('name')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <div>
                    <x-input-form input="description" placeholder="Descripción">
                        <x-texts.text-small>Descripción</x-texts.text-small>
                    </x-input-form>
                    @error('description')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                {{-- Categoria --}}
                <div>
                    <x-input-form input="category" placeholder="Categoria del producto">
                        <x-texts.text-small>Categoria</x-texts.text-small>
                    </x-input-form>
                    @error('category')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                {{-- Precio --}}
                <div>
                    <x-input-form input="price" type="number" step="0.01" placeholder="Precio (ej. 100.00)">
                        <x-texts.text-small>Precio</x-texts.text-small>
                    </x-input-form>
                    @error('price')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                {{-- Proveedor (Dropdown) --}}

                <div class="supplier">
                    <x-input-dropdown input="id_supplier" title="Proovedor" placeholder="Selecciona un proovedor">
                        @foreach ($suppliers as $supplier)
                            <x-input-dropdown-option value="{{ $supplier->id_supplier }}">{{ $supplier->name }}
                            </x-input-dropdown-option>
                        @endforeach
                    </x-input-dropdown>
                    @error('id_supplier')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                {{-- Categoría (texto plano) --}}
                <div>
                    <x-checkbox-toggle title="Producto personalizable" :input="$is_customized" wire-model="is_customized" />

                    @error('is_customized')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                {{-- Imagen (subida de archivo) --}}
                {{-- <div class="flex items-center">
                    <x-texts.text-small>Imagen del producto</x-texts.text-small>
                    <a href="#" class="text-sm text-[#0F4BBD] ml-2 underline"
                        onclick="document.getElementById('image-input').click();">Subir imagen</a>
                    <input type="file" wire:model.live="image" id="image-input" class="hidden" />
                </div>
                @error('image')
                    <x-texts.text-error>{{ $message }}</x-texts.text-error>
                @enderror

                
                <div class="flex justify-center">
                    @if ($imagePreviewUrl)
                        <div class="relative mt-4">
                            <img src="{{ $imagePreviewUrl }}" class="w-48 h-auto border border-gray-300 rounded-lg"
                                alt="Preview" />
                            <button type="button" wire:click="removeImage"
                                class="absolute top-0 right-0 mt-2 mr-2 bg-[#2448B1] text-white rounded-full w-6 h-6 flex items-center justify-center">&times;</button>
                        </div>
                    @endif
                </div> --}}
            </div>

            {{-- Footer --}}
            <x-slot name="footer" class="space-x-1 space-y-3 flex flex-col">
                <x-primary-button wire:loading.attr="disabled" wire:click="save">
                    <x-btns.loading wire:loading />
                    Crear producto
                </x-primary-button>
            </x-slot>
        </form>
    </x-modal-header>
</div>
