<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Order;
use App\Models\Receip;
use Livewire\Attributes\On;
use App\Enums\StatusOrder;

class ReceiptsModal extends Component
{
    public $showModal = false;
    public $order = null;
    public $receipts = [];
    public $totalPaid = 0;

    #[On('showReceipts')]
    public function showReceipts($orderId)
    {
        $this->order = Order::with(['user', 'receips'])->find($orderId);
        
        if ($this->order) {
            $this->receipts = $this->order->receips;
            $this->totalPaid = $this->receipts->sum('amount');
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->order = null;
        $this->receipts = [];
        $this->totalPaid = 0;
    }

    public function render()
    {
        return view('livewire.orders.receipts-modal',[            'statusLabels' => StatusOrder::labels(),
]);
    }
}
