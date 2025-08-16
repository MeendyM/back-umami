<div>
    <div class="flex justify-between my-3">
        <x-search class="focus:border-indigo-400 focus:ring-indigo-400">
            Buscar...
        </x-search>
        <div class="flex gap-2">
            <livewire:notifications.create key="notifications.create" />
            <select wire:model="filterType" class="border rounded px-2 py-1 text-sm">
                <option value="">Todos los tipos</option>
                @foreach (App\Enums\NotificationType::labels() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="px-4 py-4 bg-white custom-box-shadow rounded-t-[16px]">
        <table class="table-auto w-full">
            <thead>
                <tr class="text-gray text-smm font-semibold text-left">
                    <th class="px-2 py-2 rounded-tl-md">
                        <div class="flex items-center">
                            <button wire:click="sortBy('title')" class="text-left">
                                Título
                            </button>
                            <x-sort-icon field="title" :sortField="$sortField" :sortAsc="$sortAsc" />
                        </div>
                    </th>
                    <th class="px-2 py-2">Mensaje</th>
                    <th class="px-2 py-2">Usuario</th>
                    <th class="px-2 py-2">Leída</th>
                    <th class="px-2 py-2">Tipo</th>
                    <th class="px-2 py-2">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notifications as $notification)
                    <tr class="text-left text-sm hover:bg-light-blue">
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $notification->title }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $notification->message }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2">{{ $notification->user->name ?? '-' }}</td>
                        <td class="border-b border-t border-light-blue px-2 py-2 text-center">
                            {!! $notification->read ? '✅' : '❌' !!}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2 text-center">
                            {{ $notification->type?->value ?? '-' }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2 text-center">
                            {{ $notification->created_at?->format('Y-m-d H:i') ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($notifications->count() == 0)
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 text-center">
                No hay registros
            </div>
        @endif
    </div>

    {{-- Paginación --}}
    <div class="px-4 py-4 relative min-h-20 mt-3">
        {{ $notifications->links() }}
        <div class="w-[65px] absolute bottom-6 right-72">
            <x-inputs.dropdown class="py-1 px-1 border border-gray text-smm">
                <x-bgs.flex-center-between class="w-full">
                    <span x-text="$wire.perPage"></span>
                    <x-icons.drop />
                </x-bgs.flex-center-between>
                <x-slot name="options" class="-top-28">
                    @foreach ($optionsPerPage as $option)
                        <x-inputs.dropdown.item wire:key="option-perPage-{{ $option }}" :active="$option == $perPage"
                            x-on:click="$wire.set('perPage', {{ $option }}); show = false">
                            {{ $option }}
                        </x-inputs.dropdown.item>
                    @endforeach
                </x-slot>
            </x-inputs.dropdown>
        </div>
    </div>
</div>
