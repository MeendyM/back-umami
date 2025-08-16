<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Receip;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Enums\ReceipPaymentType;
use App\Helpers\FirebaseStorage;
use Masmerise\Toaster\Toaster;

class CashReceipModal extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $orderId;
    public $amount;
    public $image;
    public $imagePreviewUrl;
    public $existingImageUrl;

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'image' => 'nullable|image|max:4096',
    ];

    protected $listeners = ['showCashReceipModal' => 'openModal'];

    public function openModal($orderId)
    {
        $this->resetForm();
        $this->orderId = $orderId;
        $this->showModal = true;
    }



    public function updatedImage()
    {
        $this->imagePreviewUrl = $this->image ? $this->image->temporaryUrl() : null;
    }

    public function save()
    {
        $this->validate();
        $order = Order::with('receips')->find($this->orderId);
        if (!$order) {
            Toaster::error('No se encontró la orden.');
            return;
        }
        $totalPaid = $order->receips->sum('amount');
        $saldoPendiente = $order->final_total - $totalPaid;
        if ($this->amount > $saldoPendiente) {
            Toaster::error('El abono no puede ser mayor al saldo pendiente ($' . number_format($saldoPendiente, 2) . ').');
            return;
        }
        $firebaseStorage = new FirebaseStorage();
        $url = '';
        if ($this->image) {
            $filename = uniqid() . '.' . $this->image->getClientOriginalExtension();
            $realPath = $this->image->getRealPath();
            $res = $firebaseStorage->uploadFile($realPath, $filename);
            $bucket = $res['bucket'];
            $token = $res['downloadTokens'];
            $path = $res['name'];
            $url = sprintf(
                'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media&token=%s',
                $bucket,
                urlencode($path),
                $token
            );
        }
        $userId = $order?->id_user;
        $receip = Receip::create([
            'id_order' => $order?->id_order,
            'id_user' => $userId,
            'amount' => $this->amount,
            'url_img' => $url,
            'status' => \App\Enums\ReceipStatus::APPROVED->value,
            'payment_type' => ReceipPaymentType::CASH->value,
            'id_transaction' => '',
        ]);
        // Notificar usuario de éxito
        if ($order && $order->user) {
            \App\Models\Notification::create([
                'user_id' => $order->id_user,
                'type' => \App\Enums\NotificationType::SUCCESS,
                'title' => 'Abono en efectivo aprobado',
                'message' => 'Tu abono en efectivo fue registrado y aprobado exitosamente.',
                'data' => [
                    'order_id' => $order->id_order,
                    'receip_id' => $receip->id_receip,
                    'action' => 'approved',
                ],
            ]);
        }
        // Cambiar status de la orden a 'paying' si existe
        $order->status = \App\Enums\StatusOrder::PAYING->value;
        $order->save();
        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('update-order');
        Toaster::success('Abono en efectivo registrado correctamente.');
    }

    public function resetForm()
    {
        $this->amount = null;
        $this->image = null;
        $this->imagePreviewUrl = null;
        // ...existing code...
        $this->existingImageUrl = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.orders.cash-receip-modal');
    }
}
