<div>
    <x-modal-header wire:model="modalEdit" title="Editar descuento">
        <form wire:submit.prevent="submit">
            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <div class="flex justify-between items-end">
                        <x-input-form input="code" placeholder="Código del descuento">
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
                    <x-input-dropdown input="minimum_purchase" title="Mínimo de compra"
                        placeholder="Selecciona un mínimo">
                        @foreach ($minimum_purchase_options as $value => $label)
                            <x-input-dropdown-option value="{{ $value }}">{{ $label }}
                            </x-input-dropdown-option>
                        @endforeach
                    </x-input-dropdown>
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
                <x-primary-button wire:loading.attr="disabled" wire:target="update" wire:click="update">
                    <x-btns.loading wire:loading wire:target="update" />
                    Actualizar descuento
                </x-primary-button>
                <x-secondary-button type="button" wire:click="closeModal" wire:loading.attr="disabled"
                    wire:target="closeModal">
                    Cancelar
                </x-secondary-button>
            </x-slot>
        </form>
    </x-modal-header>
</div>
