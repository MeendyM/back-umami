<?php

namespace App\Livewire\Discounts;

use App\Enums\TypeDiscount;
use App\Models\Discount;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    public $modalEdit;
    public $code, $type, $value, $max_uses, $minimum_purchase, $expires_at, $name, $description;
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
            $this->name = $this->discount->name;
            $this->description = $this->discount->description;
            $this->type = $this->discount->type;
            $this->value = $this->discount->value;
            $this->minimum_purchase = $this->discount->minimum_purchase;
            $this->max_uses = $this->discount->max_uses;
            $this->expires_at = Carbon::parse($this->discount->expires_at)->format('Y-m-d');
        }
    }

    public function update()
    {
        $this->validate(
            [
                'code' => 'required|string|max:10|unique:discounts,code,' . $this->discount->id_discount . ',id_discount',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|string|max:50',
                'value' => 'required|numeric|min:1|max:100',
                'minimum_purchase' => 'required|string',
                'max_uses' => 'required|integer|min:1',
                'expires_at' => 'nullable|date|after_or_equal:today',
            ],
            [
                'code.required' => 'El código es obligatorio.',
                'code.unique' => 'El código ya existe.',
                'code.max' => 'El codigo no debe ser mayor a 10 caracteres',
                'type.required' => 'El tipo de descuento es obligatorio.',
                'value.required' => 'El valor del descuento es obligatorio.',
                'value.numeric' => 'El valor del descuento debe ser un número.',
                'value.min' => 'El valor del descuento debe ser al menos 1.',
                'value.max' => 'El valor del descuento no puede ser mayor a 100.',
                'minimum_purchase.required' => 'El mínimo de compra es obligatorio.',
                'max_uses.required' => 'El número máximo de usos es obligatorio.',
                'max_uses.min' => 'El número máximo de usos debe ser al menos 1.',
                'max_uses.integer' => 'El número máximo de usos debe ser un número entero.',
                'expires_at.date' => 'La fecha de expiración debe ser una fecha válida.',
                'expires_at.after_or_equal' => 'La fecha de expiración debe ser hoy o una fecha futura.',
                'name.required' => 'El nombre del descuento es obligatorio.',
                'description.required' => 'La descripción del descuento es obligatoria.',
            ]
        );

        try {

            $this->discount->code = $this->code;
            $this->discount->name = $this->name;
            $this->discount->description = $this->description;
            $this->discount->type = $this->type;
            $this->discount->value = $this->value;
            $this->discount->max_uses = $this->max_uses;
            $this->discount->minimum_purchase = $this->minimum_purchase;
            $this->discount->expires_at = $this->expires_at ? $this->expires_at . ' 00:00:00' : null;

            $this->discount->save();

            $this->modalEdit = false;
            $this->dispatch('update-discount');
            Toaster::success('Descuento actualizado correctamente.');
        } catch (\Exception $e) {
            Toaster::error('Error al actualizar el descuento');
            Log::debug('Error al actualizar el descuento' . $e->getMessage());
        }
    }
}
