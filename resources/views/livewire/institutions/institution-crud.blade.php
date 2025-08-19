<div class="p-6">
    <!-- Mensajes de estado -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">

        <x-search class="focus:border-indigo-400 focus:ring-indigo-400">
            Buscar...
        </x-search>

        <div class="flex items-center space-x-4">
            <livewire:institutions.create key="institutions.create" />
        </div>
    </div>

    <!-- Tabla -->
    <div class="px-4 py-4 bg-white custom-box-shadow rounded-t-[16px]">
        <table class="table-auto w-full">
            <thead>
                <tr class="text-gray text-smm font-semibold text-left">
                    <th class="px-2 py-2">ID</th>
                    <th class="px-2 py-2">Nombre</th>
                    <th class="px-2 py-2">Fecha Creación</th>
                    <th class="px-2 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($institutions as $institution)
                    <tr class="text-left text-sm hover:bg-light-blue">
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            {{ $institution->id_institution }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            {{ $institution->name }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            {{ $institution->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            <div class="flex space-x-2">
                                <button class="hover:text-blue"
                                    wire:click="$dispatch('editInstitution', { id_institution :{{ $institution->id_institution }} } )"
                                    wire:loading.attr="disabled">
                                    <x-icons.edit />
                                </button>
                                <button
                                    wire:click="$dispatch('ShowDeleteInstitution', {id_institution :{{ $institution->id_institution }} } )"
                                    class="text-principal-100 font-bold py-2 px-1 hover:text-error-red"
                                    wire:loading.attr="disabled">
                                    <x-icons.trash />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            No se encontraron instituciones.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $institutions->links() }}
    </div>

    <livewire:institutions.delete :key="'institutions.delete'" />
    <livewire:institutions.edit :key="'institutions.edit'" />
</div>
