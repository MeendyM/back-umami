<?php

namespace App\Livewire\OrderItems;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrderItem;
use App\Enums\OrderItemType;
use App\Enums\SupplierOrderStatus;
use Illuminate\Support\Carbon;

class Table extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;
    public string $sortField = 'id_order_item';
    public bool $sortAsc = false;
    public string $filterType = 'mandatory_sets'; // mandatory_sets | optional_sets | individual_products | all
    public bool $showGrouped = false; // Nueva propiedad para mostrar vista agrupada
    public array $expandedGroups = []; // Para controlar qué grupos están expandidos

    protected $queryString = ['search', 'filterType', 'showGrouped'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingShowGrouped(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
            $this->sortField = $field;
        }
    }

    public function markAsOrdered(int $id): void
    {
        $item = OrderItem::find($id);
        if (!$item || $item->type_order !== OrderItemType::PRODUCT) {
            return;
        }
        $item->supplier_status = SupplierOrderStatus::ORDERED;
        $item->supplier_order_date = now();
        $item->save();
        session()->flash('message', 'Item marcado como pedido.');
    }

    public function markAsDelivered(int $id): void
    {
        $item = OrderItem::find($id);
        if (!$item || $item->type_order !== OrderItemType::PRODUCT) {
            return;
        }
        $item->supplier_status = SupplierOrderStatus::DELIVERED;
        $item->save();
        session()->flash('message', 'Item marcado como entregado.');
    }

    public function markSetAsOrdered(int $id): void
    {
        $item = OrderItem::find($id);
        if (!$item || $item->type_order !== OrderItemType::SET || !$item->set?->only_in_set) {
            return;
        }
        
        // Marcar el set como pedido (esto podría ser un campo adicional en el futuro)
        // Por ahora, marcamos todos los productos hijos como pedidos
        $item->children()->update([
            'supplier_status' => SupplierOrderStatus::ORDERED,
            'supplier_order_date' => now()
        ]);
        
        session()->flash('message', 'Set completo marcado como pedido al proveedor.');
    }

    public function toggleGroupedDetails(int $index): void
    {
        if (in_array($index, $this->expandedGroups)) {
            $this->expandedGroups = array_diff($this->expandedGroups, [$index]);
        } else {
            $this->expandedGroups[] = $index;
        }
    }

    protected function getGroupedItems()
    {
        $query = $this->baseQuery();
        
        if ($this->filterType === 'mandatory_sets') {
            // Agrupar sets obligatorios por id_set (mismo set)
            return $query->get()
                ->groupBy('id_set')
                ->map(function ($items, $setId) {
                    $firstItem = $items->first();
                    return (object) [
                        'type' => 'grouped_set',
                        'id_set' => $setId,
                        'set_name' => $firstItem->set?->name ?? '-',
                        'supplier_name' => $firstItem->set?->supplier?->name ?? '-',
                        'total_quantity' => $items->sum('quantity'),
                        'items_count' => $items->count(),
                        'items' => $items,
                        'avg_supplier_status' => $this->getAverageStatus($items),
                    ];
                })
                ->values();
                
        } elseif ($this->filterType === 'optional_sets') {
            // Para sets opcionales, agrupar SOLO los productos por id_product
            // No importa si vienen de set o no, se agrupan por producto
            return $query->where('type_order', OrderItemType::PRODUCT)
                ->get()
                ->groupBy('id_product')
                ->map(function ($items, $productId) {
                    $firstItem = $items->first();
                    return (object) [
                        'type' => 'grouped_product',
                        'id_product' => $productId,
                        'product_name' => $firstItem->product?->name ?? '-',
                        'supplier_name' => $firstItem->product?->supplier?->name ?? '-',
                        'total_quantity' => $items->sum('quantity'),
                        'items_count' => $items->count(),
                        'items' => $items,
                        'avg_supplier_status' => $this->getAverageStatus($items),
                        'has_set_items' => $items->whereNotNull('id_set')->count() > 0,
                        'has_individual_items' => $items->whereNull('id_set')->count() > 0,
                    ];
                })
                ->values();
                
        } elseif ($this->filterType === 'individual_products') {
            // Para productos individuales, agrupar por id_product
            return $query->where('type_order', OrderItemType::PRODUCT)
                ->whereNull('id_set')
                ->get()
                ->groupBy('id_product')
                ->map(function ($items, $productId) {
                    $firstItem = $items->first();
                    return (object) [
                        'type' => 'grouped_product',
                        'id_product' => $productId,
                        'product_name' => $firstItem->product?->name ?? '-',
                        'supplier_name' => $firstItem->product?->supplier?->name ?? '-',
                        'total_quantity' => $items->sum('quantity'),
                        'items_count' => $items->count(),
                        'items' => $items,
                        'avg_supplier_status' => $this->getAverageStatus($items),
                    ];
                })
                ->values();
        }
        
        return collect();
    }

    protected function getAverageStatus($items)
    {
        $statuses = $items->pluck('supplier_status')->filter();
        
        if ($statuses->isEmpty()) {
            return 'not_ordered';
        }
        
        $statusCounts = $statuses->countBy(function ($status) {
            return $status?->value ?? 'not_ordered';
        });
        
        // Determinar el estado predominante
        if ($statusCounts->get('delivered', 0) === $statuses->count()) {
            return 'delivered';
        } elseif ($statusCounts->get('ordered', 0) > 0) {
            return 'ordered';
        } else {
            return 'not_ordered';
        }
    }

    protected function baseQuery()
    {
        $query = OrderItem::query()
            ->with(['set', 'children', 'product.supplier'])
            ->whereNotNull('id_order'); // solo items con orden confirmada

        switch ($this->filterType) {
            case 'mandatory_sets':
                // Sets obligatorios: solo items de tipo set donde el set tiene only_in_set = true
                $query->where('type_order', OrderItemType::SET)
                      ->whereHas('set', function ($q) {
                          $q->where('only_in_set', true);
                      });
                break;
                
            case 'optional_sets':
                // Sets opcionales: items de tipo set donde el set tiene only_in_set = false
                // Y también sus productos relacionados
                $query->where(function ($q) {
                    // Items de set opcionales
                    $q->where('type_order', OrderItemType::SET)
                      ->whereHas('set', function ($setQuery) {
                          $setQuery->where('only_in_set', false);
                      });
                    // O productos que pertenecen a sets opcionales
                    $q->orWhere(function ($productQuery) {
                        $productQuery->where('type_order', OrderItemType::PRODUCT)
                                   ->whereNotNull('id_set')
                                   ->whereHas('set', function ($setQuery) {
                                       $setQuery->where('only_in_set', false);
                                   });
                    });
                });
                break;
                
            case 'individual_products':
                // Productos individuales: productos sin set asociado
                $query->where('type_order', OrderItemType::PRODUCT)
                      ->whereNull('id_set');
                break;
                
            case 'all':
                // Todos los items
                break;
        }

        if ($this->search !== '') {
            $search = "%{$this->search}%";
            $query->where(function ($q) use ($search) {
                // Buscar por ID
                $q->where('id_order_item', 'like', $search);

                // Buscar por producto y proveedor (solo si no estamos filtrando solo sets)
                if ($this->filterType !== 'mandatory_sets') {
                    $q->orWhereHas('product', function ($p) use ($search) {
                        $p->where('name', 'like', $search)
                          ->orWhereHas('supplier', function ($s) use ($search) {
                              $s->where('name', 'like', $search);
                          });
                    });
                }

                // Buscar por nombre del set (para sets obligatorios y opcionales)
                if (in_array($this->filterType, ['mandatory_sets', 'optional_sets', 'all'])) {
                    $q->orWhereHas('set', function ($s) use ($search) {
                        $s->where('name', 'like', $search);
                    });
                }
            });
        }

        return $query;
    }

    public function render()
    {
        $items = null;
        $groupedItems = null;
        
        if ($this->showGrouped && in_array($this->filterType, ['mandatory_sets', 'optional_sets', 'individual_products'])) {
            $groupedItems = $this->getGroupedItems();
            
            // Debug temporal para ver qué se está agrupando
            if ($groupedItems->count() === 0) {
                session()->flash('debug', 'No se encontraron items para agrupar en filtro: ' . $this->filterType);
            } else {
                session()->flash('debug', 'Se agruparon ' . $groupedItems->count() . ' grupos para filtro: ' . $this->filterType);
            }
        } else {
            $query = $this->baseQuery();
            $items = $query
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage);
        }

        // Calcular estadísticas según el tipo de filtro
        $stats = $this->calculateStats();

        return view('livewire.order-items.table', [
            'items' => $items,
            'groupedItems' => $groupedItems,
            'optionsPerPage' => [10, 25, 50],
            'statusLabels' => SupplierOrderStatus::labels(),
            'stats' => $stats,
        ]);
    }

    protected function calculateStats(): array
    {
        $stats = [];
        
        switch ($this->filterType) {
            case 'mandatory_sets':
                // Contar sets obligatorios
                $stats['total_sets'] = OrderItem::whereNotNull('id_order')
                    ->where('type_order', OrderItemType::SET)
                    ->whereHas('set', function ($q) {
                        $q->where('only_in_set', true);
                    })
                    ->count();
                    
                // Si está agrupado, mostrar cuántos sets únicos hay
                if ($this->showGrouped) {
                    $stats['unique_sets'] = OrderItem::whereNotNull('id_order')
                        ->where('type_order', OrderItemType::SET)
                        ->whereHas('set', function ($q) {
                            $q->where('only_in_set', true);
                        })
                        ->distinct('id_set')
                        ->count();
                }
                break;
                
            case 'optional_sets':
                // Contar sets opcionales y productos relacionados
                $stats['total_sets'] = OrderItem::whereNotNull('id_order')
                    ->where('type_order', OrderItemType::SET)
                    ->whereHas('set', function ($q) {
                        $q->where('only_in_set', false);
                    })
                    ->count();
                    
                $stats['total_products'] = OrderItem::whereNotNull('id_order')
                    ->where('type_order', OrderItemType::PRODUCT)
                    ->whereNotNull('id_set')
                    ->whereHas('set', function ($q) {
                        $q->where('only_in_set', false);
                    })
                    ->sum('quantity');
                    
                // Si está agrupado, mostrar productos únicos
                if ($this->showGrouped) {
                    $stats['unique_products'] = OrderItem::whereNotNull('id_order')
                        ->where('type_order', OrderItemType::PRODUCT)
                        ->whereNotNull('id_set')
                        ->whereHas('set', function ($q) {
                            $q->where('only_in_set', false);
                        })
                        ->distinct('id_product')
                        ->count();
                }
                break;
                
            case 'individual_products':
                // Contar productos individuales
                $stats['total_products'] = OrderItem::whereNotNull('id_order')
                    ->where('type_order', OrderItemType::PRODUCT)
                    ->whereNull('id_set')
                    ->sum('quantity');
                    
                // Si está agrupado, mostrar productos únicos
                if ($this->showGrouped) {
                    $stats['unique_products'] = OrderItem::whereNotNull('id_order')
                        ->where('type_order', OrderItemType::PRODUCT)
                        ->whereNull('id_set')
                        ->distinct('id_product')
                        ->count();
                }
                break;
        }
        
        return $stats;
    }
}
