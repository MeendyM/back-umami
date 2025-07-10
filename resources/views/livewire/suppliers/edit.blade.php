<div>
    <x-modal-header wire:model="modalEdit" title="Editar proovedor">
        <form wire:submit.prevent="submit">
            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <x-input-form input="name" placeholder="Nombre de la proovedor">
                        <x-texts.text-small>Nombre</x-texts.text-small>
                    </x-input-form>
                    @error('name')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

            </div>

            {{-- Footer --}}
            <x-slot name="footer" class="space-x-1 space-y-3 flex flex-col">
                <x-primary-button wire:click="update" wire:loading.attr="disabled" wire:target="update">
                    <x-btns.loading wire:loading wire:target="update" />
                    Actualizar proovedor
                </x-primary-button>
                <x-secondary-button type="button" wire:click="closeModal" wire:loading.attr="disabled"
                    wire:target="closeModal">
                    Cancelar
                </x-secondary-button>
            </x-slot>
        </form>
    </x-modal-header>
</div>
