<div>
    <div class="flex w-full justify-end">
        <x-primary-button wire:click="openModal()">
            <x-icons.create class="fill-white mr-2 w-4" />
            <span>Agregar administrador</span>
        </x-primary-button>
    </div>

    <x-modal-header wire:model="modal" title="Agregar administrador">
        <form wire:submit.prevent="submit">
            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <x-input-form input="name" placeholder="Nombre">
                        <x-texts.text-small>Nombre completo</x-texts.text-small>
                    </x-input-form>
                    @error('name')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <x-input-form input="email" placeholder="example@example.com">
                        <x-texts.text-small>Correo electrónico</x-texts.text-small>
                    </x-input-form>
                    @error('email')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <div>
                    <x-input-form input="password" type='password' placeholder="********">
                        <x-texts.text-small>Contraseña</x-texts.text-small>
                    </x-input-form>
                    @error('password')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

                <div>
                    <x-input-form input="password_confirmation" type='password' placeholder="********">
                        <x-texts.text-small>Confirmar contraseña</x-texts.text-small>
                    </x-input-form>
                    @error('password_confirmation')
                        <x-texts.text-error>{{ $message }}</x-texts.text-error>
                    @enderror
                </div>

            </div>

            {{-- Footer --}}
            <x-slot name="footer" class="space-x-1 space-y-3 flex flex-col">
                <x-primary-button wire:loading.attr="disabled" wire:click="save">
                    <x-btns.loading wire:loading />
                    Crear administrador
                </x-primary-button>
            </x-slot>
        </form>
    </x-modal-header>
</div>
