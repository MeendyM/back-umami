<div>
    <div class="flex w-full justify-end">
        <x-primary-button wire:click="openModal()">
            <x-icons.create class="fill-white mr-2 w-4" />
            <span>Agregar categoria</span>
        </x-primary-button>
    </div>

    <x-modal-header wire:model="modal" title="Agregar categoria">
        <form wire:submit.prevent="submit">
            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <x-input-form input="name" placeholder="Nombre de la categoria">
                        <x-texts.text-small>Nombre</x-texts.text-small>
                    </x-input-form>
                    @error('name')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

            </div>

            {{-- Footer --}}
            <x-slot name="footer" class="space-x-1 space-y-3 flex flex-col">
                <x-primary-button wire:loading.attr="disabled" wire:click="save">
                    <x-btns.loading wire:loading />
                    Crear categoria
                </x-primary-button>

            </x-slot>
        </form>
    </x-modal-header>
</div>
