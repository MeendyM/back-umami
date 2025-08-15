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

    // Para UI de revisión
    public $reviewInputId = null;
    public $reviewMessage = '';

    #[On('showReceipts')]
    public function showReceipts($orderId)
    {
        $this->order = Order::with(['user', 'receips'])->find($orderId);
        if ($this->order) {
            $this->receipts = $this->order->receips;
            $this->totalPaid = collect($this->receipts)->sum('amount');
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->order = null;
        $this->receipts = [];
        $this->totalPaid = 0;
        $this->reviewInputId = null;
        $this->reviewMessage = '';
    }

    public function showReviewInput($receipId)
    {
        $this->reviewInputId = $receipId;
        $this->reviewMessage = '';
    }

    public function approveReceip($receipId)
    {
        $receip = Receip::find($receipId);
        if ($receip && $receip->status->value === 'sent') {
            $receip->status = \App\Enums\ReceipStatus::APPROVED;
            $receip->save();
            // Notificar usuario
            $this->notifyUser($receip, 'approved');
            $this->showReceipts($this->order->id_order);
            $this->dispatch('toaster', ['type' => 'success', 'message' => 'Recibo aprobado.']);
        }
    }

    public function sendToReview($receipId)
    {
        $receip = Receip::find($receipId);
        if ($receip && $receip->status->value === 'sent') {
            $receip->status = \App\Enums\ReceipStatus::REVIEW;
            $receip->save();
            // Notificar usuario con motivo
            $this->notifyUser($receip, 'review', $this->reviewMessage);
            $this->showReceipts($this->order->id_order);
            $this->dispatch('toaster', ['type' => 'warning', 'message' => 'Recibo enviado a revisión.']);
            $this->reviewInputId = null;
            $this->reviewMessage = '';
        }
    }

    private function notifyUser($receip, $action, $message = null)
    {
        $user = $this->order->user;
        if (!$user) return;
        $data = [
            'order_id' => $this->order->id_order,
            'receip_id' => $receip->id_receip,
            'action' => $action,
        ];
        if ($message) {
            $data['message'] = $message;
        }
        \App\Models\Notification::create([
            'user_id' => $this->order->id_user,
            'type' => $action === 'approved' ? \App\Enums\NotificationType::SUCCESS : \App\Enums\NotificationType::WARNING,
            'title' => $action === 'approved' ? 'Recibo aprobado' : 'Recibo en revisión',
            'message' => $action === 'approved' ? 'Tu comprobante fue aprobado.' : ($message ?: 'Tu comprobante requiere revisión.'),
            'data' => $data,
        ]);
    }

    public function render()
    {
        return view('livewire.orders.receipts-modal', [
            'statusLabels' => StatusOrder::labels(),
        ]);
    }
}
