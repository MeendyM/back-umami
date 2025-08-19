<div>
    <div class="flex w-full justify-end">
        <x-primary-button wire:click="openModal()">
            <x-icons.create class="fill-white mr-2 w-4" />
            <span>Agregar notificación</span>
        </x-primary-button>
    </div>

    <x-modal-header wire:model="showModal" title="Agregar notificación">
        <form wire:submit.prevent="save">

            <div>
                <x-input-form input="title" placeholder="Título" wire:model.defer="title">
                    <x-texts.text-small>Título</x-texts.text-small>
                </x-input-form>
                @error('title')
                    <x-texts.text-error>{{ $message }}</x-texts.text-error>
                @enderror
            </div>

            <div>
                <x-input-form input="message" placeholder="Mensaje" wire:model.defer="message">
                    <x-texts.text-small>Mensaje</x-texts.text-small>
                </x-input-form>
                @error('message')
                    <x-texts.text-error>{{ $message }}</x-texts.text-error>
                @enderror
            </div>

            <div class="type">
                <x-input-dropdown input="type" title="Tipo" placeholder="Selecciona un tipo">
                    @foreach ($types as $value => $label)
                        <x-input-dropdown-option
                            value="{{ $value }}">{{ $label }}</x-input-dropdown-option>
                    @endforeach
                </x-input-dropdown>
                @error('type')
                    <x-texts.text-error>{{ $message }}</x-texts.text-error>
                @enderror
            </div>

            <div class="target">
                <x-input-dropdown input="target" title="Para" placeholder="Selecciona un destinatario">
                    <x-input-dropdown-option value="all">Todos los usuarios</x-input-dropdown-option>
                    <x-input-dropdown-option value="user">Usuario específico</x-input-dropdown-option>
                    <x-input-dropdown-option value="institution">Usuarios de una institución</x-input-dropdown-option>
                </x-input-dropdown>
                @error('target')
                    <x-texts.text-error>{{ $message }}</x-texts.text-error>
                @enderror
            </div>


            <div class="mb-3">
                @if ($target === 'user')
                    <div class="user">
                        <x-input-dropdown input="user_id" title="Usuario" placeholder="Selecciona un usuario">
                            @foreach ($users as $user)
                                <x-input-dropdown-option
                                    value="{{ $user->id_user }}">{{ $user->name }}</x-input-dropdown-option>
                            @endforeach
                        </x-input-dropdown>
                        @error('user_id')
                            <x-texts.text-error>{{ $message }}</x-texts.text-error>
                        @enderror
                    </div>
                @elseif($target === 'institution')
                    <div class="institution">
                        <x-input-dropdown input="institution_id" title="Institución"
                            placeholder="Selecciona una institución">
                            @foreach ($institutions as $inst)
                                <x-input-dropdown-option
                                    value="{{ $inst->id_institution }}">{{ $inst->name }}</x-input-dropdown-option>
                            @endforeach
                        </x-input-dropdown>
                        @error('institution_id')
                            <x-texts.text-error>{{ $message }}</x-texts.text-error>
                        @enderror
                    </div>
                @endif
            </div>
            <x-slot name="footer" class="space-x-1 space-y-3 flex flex-col">
                <x-primary-button wire:loading.attr="disabled" wire:click="save">
                    <x-btns.loading wire:loading />
                    Enviar notificación
                </x-primary-button>
                <x-secondary-button wire:click="closeModal">Cancelar</x-secondary-button>
            </x-slot>
        </form>
    </x-modal-header>

</div>
