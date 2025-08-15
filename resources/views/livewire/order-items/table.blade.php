<div class="p-4">
    {{-- Enlaces rápidos para cambiar de vista --}}
    <div class="mb-4 flex flex-wrap gap-2">
        <button wire:click="$set('filterType', 'mandatory_sets')" 
                class="px-3 py-2 rounded text-sm {{ $filterType === 'mandatory_sets' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Sets Obligatorios
        </button>
        <button wire:click="$set('filterType', 'optional_sets')" 
                class="px-3 py-2 rounded text-sm {{ $filterType === 'optional_sets' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Sets Opcionales
        </button>
        <button wire:click="$set('filterType', 'individual_products')" 
                class="px-3 py-2 rounded text-sm {{ $filterType === 'individual_products' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Productos Individuales
        </button>
        <button wire:click="$set('filterType', 'all_products')" 
                class="px-3 py-2 rounded text-sm {{ $filterType === 'all_products' ? 'bg-teal-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Todos los Productos
        </button>
     
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
        <div class="flex gap-2 items-center">
            <input type="text" wire:model.debounce.500ms="search" placeholder="Buscar por ID, producto, proveedor o set" class="border rounded px-3 py-2 w-64">
           
        </div>
        <div class="ml-auto flex gap-2 items-center text-sm text-gray-600">
            {{-- Toggle para vista agrupada --}}
            @if (in_array($filterType, ['mandatory_sets', 'optional_sets', 'individual_products', 'all_products']))
                <label class="flex items-center gap-2 mr-4">
                    <input type="checkbox" wire:model="showGrouped" class="rounded">
                    <span class="text-sm">Vista agrupada</span>
                </label>
            @endif
            
            <span>Ordenar por:</span>
            <select wire:change="sortBy($event.target.value)" class="border rounded px-2 py-1">
                <option value="id_order_item">ID</option>
                <option value="supplier_status">Estado proveedor</option>
            </select>
            <button wire:click="sortBy('id_order_item')" class="px-3 py-1 border rounded">{{ $sortAsc ? 'Asc' : 'Desc' }}</button>
        </div>
    </div>

    {{-- Debug messages --}}
    @if (session('debug'))
        <div class="mb-4 p-3 bg-yellow-50 rounded border border-yellow-200">
            <div class="text-sm text-yellow-700">{{ session('debug') }}</div>
        </div>
    @endif

    {{-- Success/Error messages --}}
    @if (session('message'))
        <div class="mb-4 p-3 bg-green-50 rounded border border-green-200">
            <div class="text-sm text-green-700">{{ session('message') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 rounded border border-red-200">
            <div class="text-sm text-red-700">{{ session('error') }}</div>
        </div>
    @endif

    {{-- Estadísticas y descripción según el tipo de filtro --}}
    @if (isset($stats) && count($stats) > 0)
        <div class="mb-4 p-4 bg-blue-50 rounded border border-blue-200">
            <div class="text-sm text-blue-700">
                @if ($filterType === 'mandatory_sets')
                    <div class="font-medium mb-1">Sets Obligatorios</div>
                    <div class="mb-2 text-blue-600">Sets que solo se pueden pedir completos al proveedor</div>
                    @if ($showGrouped && isset($stats['unique_sets']))
                        <strong>{{ $stats['unique_sets'] }}</strong> tipos de sets diferentes, 
                        <strong>{{ $stats['total_sets'] }}</strong> items totales
                    @else
                        <strong>{{ $stats['total_sets'] ?? 0 }}</strong> sets que deben pedirse completos
                    @endif
                @elseif ($filterType === 'optional_sets')
                    <div class="font-medium mb-1">Sets Opcionales</div>
                    <div class="mb-2 text-blue-600">Sets donde los productos se pueden pedir por separado</div>
                    @if ($showGrouped && isset($stats['unique_products']))
                        <strong>{{ $stats['unique_products'] }}</strong> tipos de productos diferentes, 
                        <strong>{{ $stats['total_products'] ?? 0 }}</strong> unidades totales, 
                        <strong>{{ $stats['total_sets'] ?? 0 }}</strong> sets
                    @else
                        <strong>{{ $stats['total_sets'] ?? 0 }}</strong> sets, <strong>{{ $stats['total_products'] ?? 0 }}</strong> productos relacionados
                    @endif
                @elseif ($filterType === 'individual_products')
                    <div class="font-medium mb-1">Productos Individuales</div>
                    <div class="mb-2 text-blue-600">Productos que no pertenecen a ningún set</div>
                    @if ($showGrouped && isset($stats['unique_products']))
                        <strong>{{ $stats['unique_products'] }}</strong> tipos de productos diferentes, 
                        <strong>{{ $stats['total_products'] ?? 0 }}</strong> unidades totales
                    @else
                        <strong>{{ $stats['total_products'] ?? 0 }}</strong> productos sin set asociado
                    @endif
                @elseif ($filterType === 'all_products')
                    <div class="font-medium mb-1">Todos los Productos</div>
                    <div class="mb-2 text-blue-600">Productos de sets opcionales + productos individuales</div>
                    @if ($showGrouped && isset($stats['unique_products']))
                        <strong>{{ $stats['unique_products'] }}</strong> tipos de productos diferentes, 
                        <strong>{{ $stats['total_products'] ?? 0 }}</strong> unidades totales
                        <div class="text-xs mt-1 text-blue-500">
                            ({{ $stats['total_products_from_sets'] ?? 0 }} de sets + {{ $stats['total_individual_products'] ?? 0 }} individuales)
                        </div>
                    @else
                        <strong>{{ $stats['total_products'] ?? 0 }}</strong> productos totales
                        <div class="text-xs mt-1 text-blue-500">
                            ({{ $stats['total_products_from_sets'] ?? 0 }} de sets + {{ $stats['total_individual_products'] ?? 0 }} individuales)
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        @if ($showGrouped && $groupedItems)
            {{-- Vista agrupada --}}
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">
                            @if ($filterType === 'mandatory_sets')
                                Set
                            @else
                                Producto
                            @endif
                        </th>
                        <th class="px-4 py-2 text-left">Proveedor</th>
                        <th class="px-4 py-2 text-left">Cantidad Total</th>
                        <th class="px-4 py-2 text-left">Items Agrupados</th>
                        <th class="px-4 py-2 text-left">Estado General</th>
                        @if ($filterType === 'optional_sets')
                            <th class="px-4 py-2 text-left">Origen</th>
                        @elseif ($filterType === 'all_products')
                            <th class="px-4 py-2 text-left">Origen</th>
                        @endif
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($groupedItems as $grouped)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">
                                <div class="font-medium">
                                    @if ($grouped->type === 'grouped_set')
                                        {{ $grouped->set_name }}
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-red-100 text-red-700 ml-2">Set Obligatorio</span>
                                    @else
                                        {{ $grouped->product_name }}
                                        @if ($filterType === 'optional_sets')
                                            @if (isset($grouped->has_set_items) && $grouped->has_set_items && isset($grouped->has_individual_items) && $grouped->has_individual_items)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-purple-100 text-purple-700 ml-2">Set + Individual</span>
                                            @elseif (isset($grouped->has_set_items) && $grouped->has_set_items)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-700 ml-2">De Set</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-700 ml-2">Individual</span>
                                            @endif
                                        @elseif ($filterType === 'all_products')
                                            @if (isset($grouped->has_set_items) && $grouped->has_set_items && isset($grouped->has_individual_items) && $grouped->has_individual_items)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-purple-100 text-purple-700 ml-2">Mixto</span>
                                            @elseif (isset($grouped->has_set_items) && $grouped->has_set_items)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700 ml-2">De Set Opcional</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-700 ml-2">Individual</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-700 ml-2">Producto</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2">{{ $grouped->supplier_name }}</td>
                            <td class="px-4 py-2">
                                <span class="text-lg font-bold text-blue-600">{{ $grouped->total_quantity }}</span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-sm text-gray-600">{{ $grouped->items_count }} items</span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs 
                                    @if(str_starts_with($grouped->avg_supplier_status, 'mixed'))
                                        bg-orange-100 text-orange-700
                                    @elseif($grouped->avg_supplier_status === 'not_ordered') 
                                        bg-gray-100 text-gray-700
                                    @elseif($grouped->avg_supplier_status === 'ordered') 
                                        bg-blue-100 text-blue-700
                                    @elseif($grouped->avg_supplier_status === 'delivered') 
                                        bg-green-100 text-green-700
                                    @endif">
                                    @if($grouped->avg_supplier_status === 'mixed_delivered')
                                        Mixto (algunos entregados)
                                    @elseif($grouped->avg_supplier_status === 'mixed_ordered')
                                        Mixto (algunos pedidos)
                                    @else
                                        {{ App\Enums\SupplierOrderStatus::labels()[$grouped->avg_supplier_status] ?? 'Estado mixto' }}
                                    @endif
                                </span>
                            </td>
                            @if ($filterType === 'optional_sets')
                                <td class="px-4 py-2">
                                    @if (isset($grouped->has_set_items) && $grouped->has_set_items && isset($grouped->has_individual_items) && $grouped->has_individual_items)
                                        <span class="text-sm text-purple-600 font-medium">Mixto</span>
                                    @elseif (isset($grouped->has_set_items) && $grouped->has_set_items)
                                        <span class="text-sm text-yellow-600">Solo de sets</span>
                                    @else
                                        <span class="text-sm text-green-600">Solo individual</span>
                                    @endif
                                </td>
                            @elseif ($filterType === 'all_products')
                                <td class="px-4 py-2">
                                    @if (isset($grouped->has_set_items) && $grouped->has_set_items && isset($grouped->has_individual_items) && $grouped->has_individual_items)
                                        <span class="text-sm text-purple-600 font-medium">Mixto</span>
                                    @elseif (isset($grouped->has_set_items) && $grouped->has_set_items)
                                        <span class="text-sm text-blue-600">De sets opcionales</span>
                                    @else
                                        <span class="text-sm text-green-600">Solo individual</span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-4 py-2">
                                <div class="flex flex-col gap-2">
                                    {{-- Acciones grupales --}}
                                    <div class="flex flex-col gap-1">
                                        @if(in_array($grouped->avg_supplier_status, ['not_ordered', 'mixed_ordered']))
                                            <button wire:click="markGroupAsOrdered({{ $loop->index }})" 
                                                    class="px-2 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-xs">
                                                @if($grouped->avg_supplier_status === 'mixed_ordered')
                                                    Marcar todos como pedidos
                                                @else
                                                    Marcar grupo como pedido
                                                @endif
                                            </button>
                                        @endif
                                        
                                        @if(in_array($grouped->avg_supplier_status, ['ordered', 'mixed_ordered', 'mixed_delivered']))
                                            <button wire:click="markGroupAsDelivered({{ $loop->index }})" 
                                                    class="px-2 py-1 rounded bg-green-600 text-white hover:bg-green-700 text-xs">
                                                @if(str_starts_with($grouped->avg_supplier_status, 'mixed'))
                                                    Marcar todos como entregados
                                                @else
                                                    Marcar grupo como entregado
                                                @endif
                                            </button>
                                        @endif
                                        
                                        {{-- Botón para resetear estado --}}
                                        @if($grouped->avg_supplier_status !== 'not_ordered')
                                            <button wire:click="markGroupAsNotOrdered({{ $loop->index }})" 
                                                    class="px-2 py-1 rounded bg-gray-600 text-white hover:bg-gray-700 text-xs"
                                                    onclick="return confirm('¿Estás seguro de que quieres resetear el estado de todos los items?')">
                                                Resetear grupo
                                            </button>
                                        @endif
                                    </div>
                                    
                                    {{-- Botón de detalles --}}
                                    <button wire:click="toggleGroupedDetails({{ $loop->index }})" 
                                            class="px-3 py-1 rounded bg-gray-600 text-white hover:bg-gray-700 text-xs">
                                        @if (in_array($loop->index, $expandedGroups))
                                            Ocultar detalles
                                        @else
                                            Ver detalles ({{ $grouped->items_count }})
                                        @endif
                                    </button>
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Fila expandible con detalles --}}
                        @if (in_array($loop->index, $expandedGroups))
                            <tr>
                                <td colspan="{{ in_array($filterType, ['optional_sets', 'all_products']) ? '7' : '6' }}" class="px-4 py-2 bg-gray-50">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-medium text-sm text-gray-700">Detalles de items individuales:</h4>
                                            @if(str_starts_with($grouped->avg_supplier_status, 'mixed'))
                                                <div class="text-xs text-orange-600 bg-orange-100 px-2 py-1 rounded">
                                                    <strong>Estado Mixto:</strong> Los items tienen diferentes estados
                                                </div>
                                            @endif
                                        </div>
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach ($grouped->items as $item)
                                                <div class="flex justify-between items-center p-2 bg-white rounded border text-xs">
                                                    <div class="flex-1">
                                                        <span class="font-medium">ID: {{ $item->id_order_item }}</span>
                                                        <span class="mx-2">|</span>
                                                        <span>Cantidad: {{ $item->quantity }}</span>
                                                        @if ($item->id_set)
                                                            <span class="mx-2">|</span>
                                                            <span class="text-yellow-600">Set: {{ $item->set?->name }}</span>
                                                        @else
                                                            <span class="mx-2">|</span>
                                                            <span class="text-green-600">Individual</span>
                                                        @endif
                                                        @if ($item->supplier_order_date)
                                                            <span class="mx-2">|</span>
                                                            <span>Fecha: {{ $item->supplier_order_date->format('Y-m-d') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs 
                                                            @if($item->supplier_status?->value === 'not_ordered') bg-gray-100 text-gray-700
                                                            @elseif($item->supplier_status?->value === 'ordered') bg-blue-100 text-blue-700
                                                            @elseif($item->supplier_status?->value === 'delivered') bg-green-100 text-green-700
                                                            @endif">
                                                            {{ App\Enums\SupplierOrderStatus::labels()[$item->supplier_status?->value ?? 'not_ordered'] }}
                                                        </span>
                                                        
                                                        @if ($item->type_order?->value === 'product')
                                                            @if($item->supplier_status?->value === 'not_ordered')
                                                                <button wire:click="markAsOrdered({{ $item->id_order_item }})" class="px-2 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-xs">Pedir</button>
                                                            @elseif($item->supplier_status?->value === 'ordered')
                                                                <button wire:click="markAsDelivered({{ $item->id_order_item }})" class="px-2 py-1 rounded bg-green-600 text-white hover:bg-green-700 text-xs">Entregado</button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ in_array($filterType, ['optional_sets', 'all_products']) ? '7' : '6' }}" class="px-4 py-6 text-center text-gray-500">
                                No hay items para agrupar
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @else
            {{-- Vista normal --}}
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
                                @if ($item->set?->only_in_set)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-red-100 text-red-700">Set Obligatorio</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-indigo-100 text-indigo-700">Set Opcional</span>
                                @endif
                            @else
                                @if ($item->id_set)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-700">Producto de Set</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-700">Producto Individual</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if ($item->type_order?->value === 'set')
                                <div>
                                    <div class="font-medium">{{ $item->set?->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if ($item->set?->only_in_set)
                                            Set completo obligatorio
                                        @else
                                            Contiene {{ $item->children->count() }} productos (opcional)
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div>
                                    <div class="font-medium">{{ $item->product?->name ?? '-' }}</div>
                                    @if ($item->id_set)
                                        <div class="text-xs text-gray-500">del set: {{ $item->set?->name ?? '-' }}</div>
                                    @else
                                        <div class="text-xs text-gray-500">Producto individual</div>
                                    @endif
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
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs 
                                    @if($item->supplier_status?->value === 'not_ordered') bg-gray-100 text-gray-700
                                    @elseif($item->supplier_status?->value === 'ordered') bg-blue-100 text-blue-700
                                    @elseif($item->supplier_status?->value === 'delivered') bg-green-100 text-green-700
                                    @endif">
                                    {{ App\Enums\SupplierOrderStatus::labels()[$item->supplier_status?->value ?? 'not_ordered'] }}
                                </span>
                            @elseif ($item->type_order?->value === 'set')
                                @if ($item->set?->only_in_set)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-orange-100 text-orange-700">
                                        Pendiente pedido completo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">
                                        Ver productos individuales
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $item->supplier_order_date?->format('Y-m-d') ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @if ($item->type_order?->value === 'product')
                                <div class="flex gap-2">
                                    @if($item->supplier_status?->value === 'not_ordered')
                                        <button wire:click="markAsOrdered({{ $item->id_order_item }})" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-xs">Marcar como pedido</button>
                                    @elseif($item->supplier_status?->value === 'ordered')
                                        <button wire:click="markAsDelivered({{ $item->id_order_item }})" class="px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700 text-xs">Marcar como entregado</button>
                                    @else
                                        <span class="text-green-600 text-xs font-medium">Entregado</span>
                                    @endif
                                </div>
                            @elseif ($item->type_order?->value === 'set' && $item->set?->only_in_set)
                                <div class="flex gap-2">
                                    <button wire:click="markSetAsOrdered({{ $item->id_order_item }})" class="px-3 py-1 rounded bg-purple-600 text-white hover:bg-purple-700 text-xs">Pedir set completo</button>
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
        @endif
    </div>

    @if (!$showGrouped && $items)
        <div class="mt-3">{{ $items->links() }}</div>
    @endif
</div>
