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
