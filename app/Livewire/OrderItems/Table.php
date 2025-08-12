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
    public string $filterType = 'product'; // product | set | all

    protected $queryString = ['search', 'filterType'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
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

    protected function baseQuery()
    {
        $query = OrderItem::query()
            ->with(['set', 'children', 'product.supplier'])
            ->whereNotNull('id_order'); // solo items con orden confirmada

        if ($this->filterType === 'product') {
            $query->where('type_order', OrderItemType::PRODUCT);
        } elseif ($this->filterType === 'set') {
            $query->where('type_order', OrderItemType::SET);
        }

        if ($this->search !== '') {
            $search = "%{$this->search}%";
            $query->where(function ($q) use ($search) {
                // Buscar por ID
                $q->where('id_order_item', 'like', $search);

                if ($this->filterType !== 'set') {
                    // Buscar por producto y proveedor
                    $q->orWhereHas('product', function ($p) use ($search) {
                        $p->where('name', 'like', $search)
                          ->orWhereHas('supplier', function ($s) use ($search) {
                              $s->where('name', 'like', $search);
                          });
                    });
                }

                if ($this->filterType !== 'product') {
                    // Buscar por nombre del set
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
        $query = $this->baseQuery();

        // Calcular total de productos (solo cuando se filtra por productos)
        $totalProducts = null;
        if ($this->filterType === 'product') {
            $totalProducts = (clone $query)->sum('quantity');
        }

        $items = $query
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.order-items.table', [
            'items' => $items,
            'optionsPerPage' => [10, 25, 50],
            'statusLabels' => SupplierOrderStatus::labels(),
            'totalProducts' => $totalProducts,
        ]);
    }
}
