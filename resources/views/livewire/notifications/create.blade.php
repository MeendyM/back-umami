<div>
    <button wire:click="openModal" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-sm">
        Crear notificación
    </button>

    <x-dialog-modal wire:model="showModal">
        <x-slot name="title">Crear notificación</x-slot>
        <x-slot name="content">
            <div class="mb-3">
                <label class="block text-sm font-medium">Título</label>
                <input type="text" wire:model.defer="title" class="w-full border rounded px-2 py-1" />
                @error('title') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Mensaje</label>
                <textarea wire:model.defer="message" class="w-full border rounded px-2 py-1"></textarea>
                @error('message') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Tipo</label>
                <select wire:model.defer="type" class="w-full border rounded px-2 py-1">
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Para</label>
                <select wire:model="target" class="w-full border rounded px-2 py-1">
                    <option value="all">Todos los usuarios</option>
                    <option value="user">Usuario específico</option>
                    <option value="institution">Usuarios de una institución</option>
                </select>
            </div>
            <div class="mb-3">
                @if($target === 'user')
                    <label class="block text-sm font-medium">Usuario</label>
                    <select wire:model.defer="user_id" class="w-full border rounded px-2 py-1">
                        <option value="">Selecciona un usuario</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id_user }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                @elseif($target === 'institution')
                    <label class="block text-sm font-medium">Institución</label>
                    <select wire:model.defer="institution_id" class="w-full border rounded px-2 py-1">
                        <option value="">Selecciona una institución</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id_institution }}">{{ $inst->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">Cancelar</x-secondary-button>
            <x-button wire:click="save" class="ml-2">Crear</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
