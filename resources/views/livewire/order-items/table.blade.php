<div class="p-4">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
        <div class="flex gap-2 items-center">
            <input type="text" wire:model.debounce.500ms="search" placeholder="Buscar por ID, producto, proveedor o set" class="border rounded px-3 py-2 w-64">
            <select wire:model="filterType" class="border rounded px-3 py-2">
                <option value="product">Solo productos</option>
                <option value="set">Solo sets</option>
                <option value="all">Todos</option>
            </select>
        </div>
        <div class="ml-auto flex gap-2 items-center text-sm text-gray-600">
            <span>Ordenar por:</span>
            <select wire:change="sortBy($event.target.value)" class="border rounded px-2 py-1">
                <option value="id_order_item">ID</option>
                <option value="supplier_status">Estado proveedor</option>
            </select>
            <button wire:click="sortBy('id_order_item')" class="px-3 py-1 border rounded">{{ $sortAsc ? 'Asc' : 'Desc' }}</button>
        </div>
    </div>

    @if ($filterType === 'product' && $totalProducts !== null)
        <div class="mb-3 text-sm text-gray-700">Total de productos: <strong>{{ $totalProducts }}</strong></div>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Tipo</th>
                    <th class="px-4 py-2 text-left">Detalle</th>
                    <th class="px-4 py-2 text-left">Proveedor</th>
                    <th class="px-4 py-2 text-left">Cantidad</th>
                    <th class="px-4 py-2 text-left">Estado Proveedor</th>
                    <th class="px-4 py-2 text-left">Fecha Pedido</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($items as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->id_order_item }}</td>
                        <td class="px-4 py-2">
                            @if ($item->type_order?->value === 'set')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-indigo-100 text-indigo-700">Set</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-700">Producto</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if ($item->type_order?->value === 'set')
                                <div>
                                    <div class="font-medium">Set: {{ $item->set?->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">Contiene {{ $item->children->count() }} productos</div>
                                </div>
                            @else
                                <div>
                                    <div class="font-medium">{{ $item->product?->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">del set: {{ $item->set?->name ?? '-' }}</div>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if ($item->type_order?->value === 'set')
                                {{ $item->set?->supplier?->name ?? '-' }}
                            @else
                                {{ $item->product?->supplier?->name ?? '-' }}
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $item->quantity }}</td>
                        <td class="px-4 py-2">
                            @if ($item->type_order?->value === 'product')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">{{ App\Enums\SupplierOrderStatus::labels()[$item->supplier_status?->value ?? 'not_ordered'] }}</span>
                            @elseif ($item->type_order?->value === 'set')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">
                                    {{ App\Enums\SupplierOrderStatus::labels()['not_ordered'] }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $item->supplier_order_date?->format('Y-m-d') ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @if ($item->type_order?->value === 'product')
                                <div class="flex gap-2">
                                    <button wire:click="markAsOrdered({{ $item->id_order_item }})" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">Item pedido</button>
                                    <button wire:click="markAsDelivered({{ $item->id_order_item }})" class="px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700">Marcar como entregado</button>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">No hay items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $items->links() }}</div>
</div>
