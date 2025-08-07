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

                <div>
                    <x-checkbox-toggle title="Solo en set" :input="$only_in_set" wire-model="only_in_set" />
                    @error('only_in_set')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                {{-- Sección de imágenes --}}
                <div>
                    <div class="flex items-center">
                        <x-texts.text-small class="text-tx-black font-bold">Imágenes del producto</x-texts.text-small>
                        {{-- Indicador de carga para imágenes existentes --}}
                        @if ($isLoadingImages)
                            <div class="ml-2">
                                <svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    @error('images')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                    @error('newImages')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                    @error('allNewImages')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror

                    {{-- Loading skeleton para imágenes existentes --}}
                    @if ($isLoadingImages)
                        <div class="mb-4 animate-pulse">
                            <x-texts.text-small class="text-gray-400 mb-2">Cargando imágenes
                                actuales...</x-texts.text-small>
                            <div class="flex justify-center flex-wrap gap-2">
                                <div class="w-24 h-24 bg-gray-200 rounded-lg"></div>
                                <div class="w-24 h-24 bg-gray-200 rounded-lg"></div>
                                <div class="w-24 h-24 bg-gray-200 rounded-lg"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Imágenes existentes con animación --}}
                    @if (!empty($existingImages) && $imagesLoaded)
                        <div class="mb-4 animate-fade-in">
                            <x-texts.text-small class="text-gray-600 mb-2">Imágenes actuales:</x-texts.text-small>
                            <div class="flex justify-center flex-wrap gap-2">
                                @foreach ($existingImages as $index => $image)
                                    <div class="relative animate-scale-in"
                                        style="animation-delay: {{ $index * 0.1 }}s;">
                                        <img src="{{ $image['url'] }}"
                                            class="w-24 h-24 border border-gray-300 rounded-lg object-cover transition-all duration-300 hover:shadow-lg"
                                            alt="Imagen existente" />
                                        <button type="button" wire:click="markImageForDeletion({{ $image['id'] }})"
                                            class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow hover:bg-red-700 transition-colors duration-200"
                                            title="Eliminar imagen">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Input para nuevas imágenes (UNA a la vez) --}}
                    <input type="file" accept="image/*" id="upload-input-edit" class="hidden" wire:model="newImages"
                        x-ref="fileInput" />

                    {{-- Nuevas imágenes subidas --}}
                    <div class="flex justify-center flex-wrap gap-2 mt-4">
                        @foreach ($newImagePreviewUrl as $index => $url)
                            <div class="relative animate-slide-up" style="animation-delay: {{ $index * 0.1 }}s;">
                                <img src="{{ $url }}"
                                    class="w-24 h-24 border border-green-300 rounded-lg object-cover transition-all duration-300 hover:shadow-lg"
                                    alt="Nueva imagen" />
                                <button type="button" wire:click="removeNewImage({{ $index }})"
                                    class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow hover:bg-red-700 transition-colors duration-200">
                                    &times;
                                </button>
                                {{-- Indicador de nueva imagen --}}
                                <div
                                    class="absolute bottom-0 left-0 bg-green-500 text-white text-xs px-1 rounded-tr animate-pulse">
                                    Nueva
                                </div>
                            </div>
                        @endforeach

                        {{-- Botón para agregar nuevas imágenes --}}
                        @php
                            $totalImages = count($existingImages) + count($newImagePreviewUrl);
                        @endphp

                        @if ($totalImages < 5)
                            <div class="w-24 h-24 border border-dashed border-gray-300 rounded flex items-center justify-center cursor-pointer hover:border-gray-400 transition-colors duration-200"
                                onclick="document.getElementById('upload-input-edit').click();"
                                wire:loading.class="opacity-50 cursor-not-allowed" wire:target="newImages">

                                {{-- Spinner cuando se están subiendo archivos --}}
                                <div wire:loading wire:target="newImages">
                                    <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>

                                {{-- Botón normal cuando no se está cargando --}}
                                <div wire:loading.remove wire:target="newImages">
                                    <span class="text-2xl text-gray-500 font-bold">+</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($totalImages >= 5)
                        <x-texts.text-small class="text-gray-500 text-center mt-2">
                            Máximo de 5 imágenes alcanzado
                        </x-texts.text-small>
                    @endif
                </div>

            </div>

            {{-- Footer --}}
            <x-slot name="footer" class="space-y-3 flex flex-col">
                <x-primary-button wire:click="update" wire:loading.attr="disabled" wire:target="update">
                    <x-btns.loading wire:loading wire:target="update" />
                    Actualizar producto
                </x-primary-button>
                <x-secondary-button type="button" wire:click="closeModal" wire:loading.attr="disabled"
                    wire:target="closeModal">
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

        // Escuchar evento para cargar imágenes existentes con delay
        window.addEventListener('loadImages', () => {
            setTimeout(() => {
                @this.call('loadExistingImagesWithDelay');
            }, 300); // 300ms delay para efecto visual
        });

        // Manejo de subida de archivos con validación y feedback
        input.addEventListener('change', function(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;

            // Validar archivos antes de subir
            const validFiles = files.filter(file => {
                // Validar tipo
                if (!file.type.startsWith('image/')) {
                    alert(`${file.name} no es una imagen válida.`);
                    return false;
                }

                // Validar tamaño (2MB máximo)
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert(`${file.name} es muy grande. Máximo 2MB.`);
                    return false;
                }

                return true;
            });

            if (validFiles.length === 0) {
                event.target.value = '';
                return;
            }

            // Limpiar el input después de procesar para permitir seleccionar los mismos archivos otra vez
            setTimeout(() => {
                event.target.value = '';
            }, 100);
        });

        // Opcional: Escuchar eventos personalizados para feedback adicional
        window.addEventListener('image-uploaded', (event) => {
            console.log('Imágenes subidas:', event.detail);
        });
    });
</script>
