<?php

namespace App\Livewire\Discounts;

use Livewire\Component;
use Illuminate\Support\Str;

use App\Enums\TypeDiscount;
use App\Models\Discount;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Create extends Component
{

    public $modal = false;

    public $code, $type, $value, $minimum_purchase, $max_uses, $expires_at;

    public function render()
    {
        $options = collect(TypeDiscount::cases())->mapWithKeys(function ($case) {
            return [$case->value => TypeDiscount::labels()[$case->value]];
        });

        return view('livewire.discounts.create', [
            'options' => $options,
        ]);
    }

    public function openModal()
    {
        $this->modal = true;
        $this->clean();
    }

    public function closeModal()
    {
        $this->modal = false;
    }

    public function clean()
    {
        $this->code = '';
        $this->type = '';
        $this->value = '';
        $this->minimum_purchase = '';
        $this->max_uses = '';
        $this->expires_at = '';
    }

    public function generateCode()
    {
        $this->code = strtoupper(Str::random(5));
    }

    public function save()
    {

        $this->validate(
            [
                'code' => 'required|string|max:10|unique:discounts,code',
                'type' => 'required|string|max:255',
                'value' => 'required|numeric|min:1|max:100',
                'minimum_purchase' => 'required|string',
                'max_uses' => 'required|integer|min:1',
                'expires_at' => 'nullable|date|after_or_equal:today',
            ],
            [
                'code.required' => 'El código es obligatorio.',
                'code.unique' => 'El código ya existe.',
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
            ]
        );

        try {
            Discount::create([
                'code' => strtoupper($this->code),
                'type' => $this->type,
                'value' => $this->value,
                'minimum_purchase' => $this->minimum_purchase,
                'max_uses' => $this->max_uses,
                'expires_at' => $this->expires_at ? $this->expires_at . ' 00:00:00' : null,
            ]);

            $this->modal = false;
            $this->clean();
            $this->dispatch('update-discount');
            Toaster::success("Descuento creado con éxito!");
        } catch (\Exception $e) {
            Toaster::error('Error al crear el descuento');
            // Log the error or handle it as needed
            Log::error('Error creating discount: ' . $e->getMessage());
        }
    }
}
