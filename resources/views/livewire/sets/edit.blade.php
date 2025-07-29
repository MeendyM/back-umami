<div>
    <x-modal-header wire:model="modalEdit" title="Editar set">
        <form wire:submit.prevent="submit">
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

                {{-- Sección de imágenes del set --}}
                <div>
                    <div class="flex items-center">
                        <x-texts.text-small class="text-tx-black font-bold">Imagen del set</x-texts.text-small>
                        {{-- Indicador de carga para imagen existente --}}
                        @if($isLoadingImages)
                            <div class="ml-2">
                                <svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    @error('images')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                    @error('newSetImage')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror

                    {{-- Loading skeleton para imagen existente --}}
                    @if($isLoadingImages)
                        <div class="mb-4 animate-pulse">
                            <x-texts.text-small class="text-gray-400 mb-2">Cargando imagen actual...</x-texts.text-small>
                            <div class="flex justify-center">
                                <div class="w-32 h-32 bg-gray-200 rounded-lg"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Imagen existente con animación --}}
                    @if(!is_null($existingSetImage) && $imagesLoaded)
                        <div class="mb-4 animate-fade-in">
                            <x-texts.text-small class="text-gray-600 mb-2">Imagen actual:</x-texts.text-small>
                            <div class="flex justify-center">
                                <div class="relative animate-scale-in">
                                    <img src="{{ $existingSetImage['url'] }}"
                                        class="w-32 h-32 border border-gray-300 rounded-lg object-cover transition-all duration-300 hover:shadow-lg" 
                                        alt="Imagen del set" />
                                    <button type="button" 
                                        wire:click="markSetImageForDeletion()"
                                        class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow hover:bg-red-700 transition-colors duration-200"
                                        title="Eliminar imagen">
                                        &times;
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Input para nueva imagen del set --}}
                    <input type="file" 
                           accept="image/*" 
                           id="upload-input-sets-edit"
                           class="hidden" 
                           wire:model="newSetImage" />

                    {{-- Nueva imagen subida --}}
                    <div class="flex justify-center flex-wrap gap-2 mt-4">
                        @if (!is_null($newSetImagePreviewUrl))
                            <div class="relative animate-slide-up">
                                <img src="{{ $newSetImagePreviewUrl }}"
                                    class="w-32 h-32 border border-green-300 rounded-lg object-cover transition-all duration-300 hover:shadow-lg" 
                                    alt="Nueva imagen del set" />
                                <button type="button" wire:click="removeNewSetImage()"
                                    class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow hover:bg-red-700 transition-colors duration-200">
                                    &times;
                                </button>
                                {{-- Indicador de nueva imagen --}}
                                <div class="absolute bottom-0 left-0 bg-green-500 text-white text-xs px-1 rounded-tr animate-pulse">
                                    Nueva
                                </div>
                            </div>
                        @endif

                        {{-- Botón para cambiar imagen del set --}}
                        @php
                            $hasImage = !is_null($existingSetImage) || !is_null($newSetImagePreviewUrl);
                        @endphp
                        
                        @if (!$hasImage || (!is_null($existingSetImage) && is_null($newSetImagePreviewUrl)))
                            <div class="w-32 h-32 border border-dashed border-gray-300 rounded flex items-center justify-center cursor-pointer hover:border-gray-400 transition-colors duration-200"
                                onclick="document.getElementById('upload-input-sets-edit').click();"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                wire:target="newSetImage">
                                
                                {{-- Spinner cuando se está subiendo archivo --}}
                                <div wire:loading wire:target="newSetImage">
                                    <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                
                                {{-- Botón normal cuando no se está cargando --}}
                                <div wire:loading.remove wire:target="newSetImage" class="text-center">
                                    <span class="text-2xl text-gray-500 font-bold">+</span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $hasImage ? 'Cambiar' : 'Agregar' }} imagen
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
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
                                    <div class="font-semibold text-black2">
                                        {{ $product->name }}<span class="font-normal">
                                            - ${{ number_format($product->price, 2) }} -
                                            {{ $product->category->name }} - {{ $product->supplier->name }}</span>
                                    </div>
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
                        <x-texts.text-small class="font-bold mb-1">Estos productos estan en tu set</x-texts.text-small>
                        <ul>
                            @foreach ($selected as $id)
                                @php
                                    $product = \App\Models\Product::find($id);
                                @endphp
                                @if ($product)
                                    <li class="p-1 rounded flex justify-between items-center hover:bg-light-blue border-b border-light-blue">
                                        <span>{{ $product->name }} - ${{ $product->price }} - {{ $product->category->name }} -
                                            {{ $product->supplier->name }}</span>
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
                <x-primary-button wire:click="update" wire:loading.attr="disabled" wire:target="update">
                    <x-btns.loading wire:loading wire:target="update" />
                    Actualizar set
                </x-primary-button>
                <x-secondary-button type="button" wire:click="closeModal" wire:loading.attr="disabled"
                    wire:target="closeModal">
                    Cancelar
                </x-secondary-button>
            </x-slot>
        </form>
    </x-modal-header>
</div>

{{-- <style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes scale-in {
        from { 
            opacity: 0; 
            transform: scale(0.8);
        }
        to { 
            opacity: 1; 
            transform: scale(1);
        }
    }
    
    @keyframes slide-up {
        from { 
            opacity: 0; 
            transform: translateY(20px);
        }
        to { 
            opacity: 1; 
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fade-in 0.5s ease-out;
    }
    
    .animate-scale-in {
        animation: scale-in 0.4s ease-out;
        animation-fill-mode: both;
    }
    
    .animate-slide-up {
        animation: slide-up 0.3s ease-out;
        animation-fill-mode: both;
    }
</style> --}}

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('upload-input-sets-edit');
        if (!input) return;

        // Manejo de subida de archivos con validación
        input.addEventListener('change', function(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;

            const file = files[0]; // Solo tomamos el primer archivo
            
            // Validar tipo
            if (!file.type.startsWith('image/')) {
                alert(`${file.name} no es una imagen válida.`);
                event.target.value = '';
                return;
            }
            
            // Validar tamaño (2MB máximo)
            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                alert(`${file.name} es muy grande. Máximo 2MB.`);
                event.target.value = '';
                return;
            }

            // Limpiar el input después de procesar
            setTimeout(() => {
                event.target.value = '';
            }, 100);
        });

        // Opcional: Escuchar eventos personalizados
        window.addEventListener('set-image-uploaded', (event) => {
            console.log('Imagen de set actualizada:', event.detail);
        });
    });
</script>
