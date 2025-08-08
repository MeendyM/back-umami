<div>
    <div class="flex w-full justify-end">
        <x-primary-button wire:click="openModal()">
            <x-icons.create class="fill-white mr-2 w-4" />
            <span>Agregar descuento</span>
        </x-primary-button>
    </div>

    <x-modal-header wire:model="modal" title="Agregar descuento">
        <form wire:submit.prevent="submit">
            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <div class="flex justify-between items-end">
                        <x-input-form input="code" class=" uppercase" placeholder="Código del descuento">
                            <x-texts.text-small>Código</x-texts.text-small>
                        </x-input-form>

                        <x-primary-button wire:loading.attr="disabled" wire:target="generateCode"
                            wire:click.prevent="generateCode()" class="mb-2">
                            <x-btns.loading wire:loading wire:target="generateCode" />
                            Generar código
                        </x-primary-button>
                    </div>


                    @error('code')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <div>
                    <x-input-form input="name" placeholder="Nombre">
                        <x-texts.text-small>Nombre del descuento</x-texts.text-small>
                    </x-input-form>
                    @error('name')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <div>
                    <x-input-form input="description" placeholder="Descripción">
                        <x-texts.text-small>Descripción del descuento</x-texts.text-small>
                    </x-input-form>
                    @error('description')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <div>
                    <x-input-form input="type" placeholder="Tipo">
                        <x-texts.text-small>Tipo de descuento</x-texts.text-small>
                    </x-input-form>
                    @error('type')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <div>
                    <x-input-form input="value" type="number" placeholder="Ej. 10.00">
                        <x-texts.text-small>Valor del decuento</x-texts.text-small>
                    </x-input-form>
                    @error('value')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <div>
                    <x-input-form input="minimum_purchase" type="number" placeholder="Ej. 100.00">
                        <x-texts.text-small>Mínimo de compra</x-texts.text-small>
                    </x-input-form>
                    @error('minimum_purchase')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <div>
                    <x-input-form input="max_uses" type="number" placeholder="Ej. 5">
                        <x-texts.text-small>Máximo de usos</x-texts.text-small>
                    </x-input-form>
                    @error('max_uses')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <x-input-form input="expires_at" type="date" placeholder="Fecha de expiración">
                    <x-texts.text-small>Fecha de expiración</x-texts.text-small>
                </x-input-form>

                @error('expires_at')
                    <x-texts.text-error>{{ $message }}</x-texts.text-error>
                @enderror

            </div>

            {{-- Footer --}}
            <x-slot name="footer" class="space-x-1 space-y-3 flex flex-col">
                <x-primary-button wire:loading.attr="disabled" wire:target="save" wire:click="save">
                    <x-btns.loading wire:loading wire:target="save" />
                    Crear descuento
                </x-primary-button>

            </x-slot>
        </form>
    </x-modal-header>
</div>
