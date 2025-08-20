<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Enums\StatusOrder;
use Livewire\Attributes\On;

class Table extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id_order';
    public $sortAsc = false;
    

    protected $queryString = ['search']; // para mantener el valor al navegar

    public function updatingSearch()
    {
        $this->resetPage(); // reiniciar a la página 1 cuando se busca algo
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
            $this->sortField = $field;
        }
    }
    
    #[On('update-order')]
    public function updateOrders()
    {
        $this->resetPage(); // reiniciar a la página 1 al actualizar órdenes
    }

    public function changeStatusToReview($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update(['status' => StatusOrder::REVIEW->value]);
            $this->dispatch('update-order');
            session()->flash('message', 'Estado cambiado a "Revisar" exitosamente.');
        }
    }

    public function changeStatusToPaid($orderId)
    {
        $order = Order::with('user')->find($orderId);
        if ($order) {
            if ($order->status === StatusOrder::DELIVERED->value) {
                session()->flash('message', 'No se puede marcar como pagado una orden ya entregada.');
                return;
            }
            $order->update(['status' => StatusOrder::PAID->value]);
            // Notificar usuario
            if ($order->user) {
                \App\Models\Notification::create([
                    'user_id' => $order->user->id_user,
                    'type' => \App\Enums\NotificationType::SUCCESS,
                    'title' => 'Orden pagada',
                    'message' => 'Tu orden #' . $order->order_code . ' ha sido marcada como pagada.',
                    'data' => [
                        'order_id' => $order->id_order,
                        'order_code' => $order->order_code,
                        'action' => 'paid',
                    ],
                ]);
            }
            $this->dispatch('update-order');
            session()->flash('message', 'Estado cambiado a "Pagado" exitosamente.');
        }
    }

    public function changeStatusToDelivered($orderId)
    {
        $order = Order::with('user')->find($orderId);
        if ($order) {
            if ($order->status !== StatusOrder::PAID->value) {
                session()->flash('message', 'Solo se puede marcar como entregado una orden pagada.');
                return;
            }
            $order->update(['status' => StatusOrder::DELIVERED->value]);
            // Notificar usuario
            if ($order->user) {
                \App\Models\Notification::create([
                    'user_id' => $order->user->id_user,
                    'type' => \App\Enums\NotificationType::INFO,
                    'title' => 'Pedido entregado',
                    'message' => 'Tu pedido #' . $order->order_code . ' ha sido entregado.',
                    'data' => [
                        'order_id' => $order->id_order,
                        'order_code' => $order->order_code,
                        'action' => 'delivered',
                    ],
                ]);
            }
            $this->dispatch('update-order');
            session()->flash('message', 'Estado cambiado a "Entregado" exitosamente.');
        }
    }

    
    public function render()
    {
        $orders = Order::with(['user.institution', 'discount'])
            ->where(function ($query) {
                $query->where('id_order', 'like', "%{$this->search}%")
                    ->orWhere('order_code', 'like', "%{$this->search}%")
                    ->orWhere('total', 'like', "%{$this->search}%")
                    ->orWhere('status', 'like', "%{$this->search}%")
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%");
                    });
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);


        return view('livewire.orders.table', [
            'orders' => $orders,
            'optionsPerPage' => [10, 25, 50],
            'statusLabels' => StatusOrder::labels(),
        ]);
    }
}
