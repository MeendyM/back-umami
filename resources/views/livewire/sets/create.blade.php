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

                {{-- Solo en set --}}
                <div>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="only_in_set" 
                               wire:model.defer="only_in_set" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="only_in_set" class="ml-2 block text-sm text-gray-900">
                            Solo disponible en set
                        </label>
                    </div>
                    <x-texts.text-small class="text-gray-500 mt-1">
                        Si está marcado, los productos solo podrán comprarse como parte de este set
                    </x-texts.text-small>
                </div>

                {{-- Sección de imágenes del set --}}
                <div>
                    <x-texts.text-small class="text-tx-black font-bold">Imágenes del set</x-texts.text-small>
                    
                    @error('setImage')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror

                    {{-- Input para imagen del set --}}
                    <input type="file" 
                           accept="image/*" 
                           id="upload-input-sets"
                           class="hidden" 
                           wire:model="setImage" />

                    {{-- Imagen del set subida --}}
                    <div class="flex justify-center flex-wrap gap-2 mt-4">
                        @if (!is_null($setImagePreviewUrl))
                            <div class="relative animate-slide-up">
                                <img src="{{ $setImagePreviewUrl }}"
                                    class="w-24 h-24 border border-green-300 rounded-lg object-cover transition-all duration-300 hover:shadow-lg" 
                                    alt="Imagen del set" />
                                <button type="button" wire:click="removeSetImage()"
                                    class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow hover:bg-red-700 transition-colors duration-200">
                                    &times;
                                </button>
                                {{-- Indicador de imagen del set --}}
                                <div class="absolute bottom-0 left-0 bg-green-500 text-white text-xs px-1 rounded-tr animate-pulse">
                                    Set
                                </div>
                            </div>
                        @endif

                        {{-- Botón para agregar imagen del set --}}
                        @if (is_null($setImagePreviewUrl))
                            <div class="w-24 h-24 border border-dashed border-gray-300 rounded flex items-center justify-center cursor-pointer hover:border-gray-400 transition-colors duration-200"
                                onclick="document.getElementById('upload-input-sets').click();"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                wire:target="setImage">
                                
                                {{-- Spinner cuando se está subiendo archivo --}}
                                <div wire:loading wire:target="setImage">
                                    <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                
                                {{-- Botón normal cuando no se está cargando --}}
                                <div wire:loading.remove wire:target="setImage">
                                    <span class="text-2xl text-gray-500 font-bold">+</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(!is_null($setImagePreviewUrl))
                        <x-texts.text-small class="text-gray-500 text-center mt-2">
                            Imagen del set agregada
                        </x-texts.text-small>
                    @endif
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
                <x-primary-button wire:loading.attr="disabled" wire:click="save">
                    <x-btns.loading wire:loading />
                    Crear set
                </x-primary-button>
            </x-slot>
        </form>
    </x-modal-header>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('upload-input-sets');
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

        // Opcional: Escuchar eventos personalizados para feedback adicional
        window.addEventListener('set-image-uploaded', (event) => {
            console.log('Imagen de set subida:', event.detail);
        });
    });
</script>
