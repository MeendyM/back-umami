<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Order;
use Livewire\Attributes\On;

class OrderItemsModal extends Component
{
    public $showModal = false;
    public $order = null;
    public $orderItems = [];
    public $groupedItems = [];

    #[On('showOrderItems')]
    public function showOrderItems($orderId)
    {
        // Igual que el API: orderItems.product y orderItems.set
        $this->order = Order::with(['orderItems.product', 'orderItems.set'])->find($orderId);
        if ($this->order) {
            // Usar la relación orderItems (no order_items)
            $items = $this->order->orderItems;
            // Agrupar igual que el API: sets y productos sueltos
            $this->orderItems = $items;
            $this->groupedItems = $this->groupOrderItems($items);
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->order = null;
        $this->orderItems = [];
        $this->groupedItems = [];
    }

    private function groupOrderItems($orderItems)
    {
        $sets = [];
        $products = [];
        foreach ($orderItems as $item) {
            // type_order puede ser string o enum, forzar a string
            $type = is_object($item->type_order) ? $item->type_order->value : $item->type_order;
            if ($type === 'set') {
                $sets[$item->id_set] = [
                    'set' => $item->set,
                    'set_item' => $item,
                    'products' => []
                ];
            }
        }
        foreach ($orderItems as $item) {
            $type = is_object($item->type_order) ? $item->type_order->value : $item->type_order;
            if ($type === 'product' && $item->id_set) {
                if (isset($sets[$item->id_set])) {
                    $sets[$item->id_set]['products'][] = $item;
                }
            } elseif ($type === 'product' && !$item->id_set) {
                $products[] = $item;
            }
        }
        return [
            'sets' => array_values($sets),
            'products' => $products
        ];
    }

    public function render()
    {
        return view('livewire.orders.order-items-modal');
    }
}
