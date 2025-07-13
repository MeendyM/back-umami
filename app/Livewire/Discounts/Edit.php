<?php

namespace App\Livewire\Discounts;

use App\Enums\TypeDiscount;
use App\Models\Discount;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toast;
use Masmerise\Toaster\Toaster;

class Edit extends Component
{
    public $modalEdit;
    public $code, $type, $value, $max_uses, $minimum_purchase, $expires_at;
    public $options;
    public Discount $discount;

    public function mount()
    {
        $this->modalEdit = false;
    }

    public function closeModal()
    {
        $this->modalEdit = false;
    }

    public function render()
    {
        $minimum_purchase_options = TypeDiscount::labels();

        return view('livewire.discounts.edit', [
            'minimum_purchase_options' => $minimum_purchase_options
        ]);
    }

    #[On('editDiscount')]
    public function editDiscount($id_discount)
    {
        $this->discount = Discount::find($id_discount);
        if ($this->discount) {
            $this->modalEdit = true;
            $this->code = $this->discount->code;
            $this->type = $this->discount->type;
            $this->value = $this->discount->value;
            $this->minimum_purchase = $this->discount->minimum_purchase;
            $this->max_uses = $this->discount->max_uses;
            $this->expires_at = Carbon::parse($this->discount->expires_at)->format('Y-m-d');
        }
    }
}
