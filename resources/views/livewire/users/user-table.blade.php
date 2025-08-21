<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">

        <x-search class="focus:border-indigo-400 focus:ring-indigo-400">
            Buscar...
        </x-search>
        <div class="flex flex-row gap-3">
            <livewire:users.create />
            <!-- Filtros -->
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                <!-- Filtro de Institución -->
                <div class="relative">
                    <select wire:model.live="institutionFilter"
                        class="block w-full sm:w-48 pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Todas las instituciones</option>
                        <option value="sin_institucion">Sin institución</option>
                        @foreach ($institutions as $institution)
                            <option value="{{ $institution->id_institution }}">{{ $institution->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón Limpiar Filtros -->
                @if ($search || $institutionFilter)
                    <button wire:click="clearFilters"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        Limpiar
                    </button>
                @endif
            </div>
        </div>


    </div>

    <!-- Tabla -->
    <div class="px-4 py-4 bg-white custom-box-shadow rounded-t-[16px]">
        <table class="table-auto w-full">
            <thead>
                <tr class="text-gray text-smm font-semibold text-left">
                    <th class="px-2 py-2">ID</th>
                    <th class="px-2 py-2">Nombre
                    </th>
                    <th class="px-2 py-2">Email
                    </th>
                    <th class="px-2 py-2">Tipo</th>
                    <th class="px-2 py-2">
                        Institución</th>
                    <th class="px-2 py-2">Fecha
                        Registro</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="text-left text-sm hover:bg-light-blue">
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            {{ $user->id_user }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full object-cover"
                                        src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            {{ $user->email }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            <span
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if ($user->type->value === 'admin') bg-purple-100 text-purple-800
                                @elseif($user->type->value === 'student') bg-blue-100 text-blue-800
                                @elseif($user->type->value === 'client') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ \App\Enums\TypeUser::labels()[$user->type->value] ?? $user->type->value }}
                            </span>
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            {{ $user->institution ? $user->institution->name : 'Sin institución' }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            {{ $user->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No se encontraron usuarios.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
