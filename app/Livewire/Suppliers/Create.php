<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    public $modal = false;
    public $name;

    public function render()
    {
        return view('livewire.suppliers.create');
    }

    public function openModal()
    {
        $this->modal = true;
        $this->clean();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->modal = false;
        
    }

    public function clean()
    {
        $this->name = '';
    }

    public function save()
    {
        $this->validate(
            [
                'name' => 'required|string|max:30|unique:suppliers,name',
            ],
            [
                'name.required' => 'El nombre es requerido.',
                'name.string' => 'El nombre debe de ser un texto.',
                'name.max' => 'El nombre no debe tener mas de 30 caracteres.',
                'name.unique' => 'El nombre del proveedor ya existe.',
            ]
        );

        try {
            Supplier::create([
                'name' => $this->name,
            ]);

            $this->modal = false;
            $this->clean();
            $this->dispatch('update-supplier');
            Toaster::success("Proveedor creado con éxito!.");
        } catch (\Exception $e) {
            Toaster::error("Error al crear el proveedor");
            Log::info($e);
        }
    }
}
