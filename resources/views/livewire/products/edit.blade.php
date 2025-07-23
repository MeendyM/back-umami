<div>
    <x-modal-header wire:model="modalEdit" title="Editar producto">
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
                    <x-input-dropdown input="id_supplier" title="Proveedor" placeholder="Selecciona un proveedor">
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

                {{-- Sección de imágenes --}}
                <div>
                    <div class="flex items-center">
                        <x-texts.text-small class="text-tx-black font-bold">Imágenes del producto</x-texts.text-small>
                    </div>
                    
                    @error('images')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                    @error('newImages')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror

                    {{-- Imágenes existentes --}}
                    @if(!empty($existingImages))
                        <div class="mb-4">
                            <x-texts.text-small class="text-gray-600 mb-2">Imágenes actuales:</x-texts.text-small>
                            <div class="flex justify-center flex-wrap gap-2">
                                @foreach ($existingImages as $image)
                                    <div class="relative">
                                        <img src="{{ $image['url'] }}"
                                            class="w-24 h-24 border border-gray-300 rounded-lg object-cover" 
                                            alt="Imagen existente" />
                                        <button type="button" 
                                            wire:click="markImageForDeletion({{ $image['id'] }})"
                                            class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow hover:bg-red-700"
                                            title="Eliminar imagen">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Input para nuevas imágenes --}}
                    <input type="file" accept="image/*" multiple id="upload-input-edit"
                        class="hidden" />

                    {{-- Nuevas imágenes subidas --}}
                    <div class="flex justify-center flex-wrap gap-2 mt-4">
                        @foreach ($newImagePreviewUrl as $index => $url)
                            <div class="relative">
                                <img src="{{ $url }}"
                                    class="w-24 h-24 border border-green-300 rounded-lg object-cover" alt="Nueva imagen" />
                                <button type="button" wire:click="removeNewImage({{ $index }})"
                                    class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow">
                                    &times;
                                </button>
                                {{-- Indicador de nueva imagen --}}
                                <div class="absolute bottom-0 left-0 bg-green-500 text-white text-xs px-1 rounded-tr">
                                    Nueva
                                </div>
                            </div>
                        @endforeach

                        {{-- Botón para agregar nuevas imágenes --}}
                        @php
                            $totalImages = count($existingImages) + count($newImagePreviewUrl);
                        @endphp
                        
                        @if ($totalImages < 5)
                            <div class="w-24 h-24 border border-dashed border-gray-300 rounded flex items-center justify-center cursor-pointer hover:border-gray-400"
                                onclick="document.getElementById('upload-input-edit').click();">
                                <span class="text-2xl text-gray-500 font-bold">+</span>
                            </div>
                        @endif
                    </div>

                    @if($totalImages >= 5)
                        <x-texts.text-small class="text-gray-500 text-center mt-2">
                            Máximo de 5 imágenes alcanzado
                        </x-texts.text-small>
                    @endif
                </div>

            </div>

            {{-- Footer --}}
            <x-slot name="footer" class="space-y-3 flex flex-col">
                <x-primary-button wire:click="update" wire:loading.attr="disabled" wire:target="update">
                    <x-btns.loading wire:loading wire:target="update"  />
                    Actualizar producto
                </x-primary-button>
                <x-secondary-button type="button" wire:click="closeModal" wire:loading.attr="disabled" wire:target="closeModal">
                    Cancelar
                </x-secondary-button>
            </x-slot>
        </form>
    </x-modal-header>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('upload-input-edit');
        if (!input) return;

        input.addEventListener('change', function(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;

            files.forEach(file => {
                @this.upload('newImages', file,
                    () => {
                        // éxito - la imagen se agregará automáticamente al array
                    }, 
                    error => alert('Error al subir imagen: ' + error)
                );
            });

            event.target.value = '';
        });
    });
</script>
