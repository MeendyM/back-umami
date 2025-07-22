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
                <div class="category">
                    <x-input-dropdown input="id_category" title="Categoría" placeholder="Selecciona una categoría">
                        @foreach ($categories as $category)
                            <x-input-dropdown-option value="{{ $category->id_category }}">{{ $category->name }}
                            </x-input-dropdown-option>
                        @endforeach
                    </x-input-dropdown>
                    @error('id_category')
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

                {{-- checbox --}}
                <div>
                    <x-checkbox-toggle title="Producto personalizable" :input="$is_customized" wire-model="is_customized" />

                    @error('is_customized')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <div class="flex items-center">
                    <x-texts.text-small class="text-tx-black font-bold">Sube imagenes de tu
                        producto</x-texts.text-small>

                    {{-- <a href="#" class="text-sm text-[#0F4BBD] ml-2 underline"
                        onclick="document.getElementById('upload-input').click();">Subir imagen</a> --}}

                    <input type="file" accept="image/*" multiple id="upload-input"
                        class="hidden" />
                </div>
                @error('images')
                    <x-texts.text-error>{{ $message }}</x-texts.text-error>
                @enderror

                <div class="flex justify-center flex-wrap gap-2 mt-4">
                    @foreach ($imagePreviewUrl as $index => $url)
                        <div class="relative">
                            <img src="{{ $url }}"
                                class="w-24 h-24 border border-gray-300 rounded-lg object-cover" alt="Preview" />
                            <button type="button" wire:click="removeImage({{ $index }})"
                                class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow">
                                &times;
                            </button>
                        </div>
                    @endforeach

                    @if (count($imagePreviewUrl) < 5)
                        <div class="w-24 h-24 border border-dashed border-gray-300 rounded flex items-center justify-center cursor-pointer"
                            onclick="document.getElementById('upload-input').click();">
                            <span class="text-2xl text-gray-500 font-bold">+</span>
                        </div>
                    @endif
                </div>

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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('upload-input');
        if (!input) return;

        input.addEventListener('change', function(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;

            files.forEach(file => {
                @this.upload('images', file,
                    () => {}, // éxito
                    error => alert('Error al subir imagen: ' + error)
                );
            });

            event.target.value = '';
        });
    });
</script>
