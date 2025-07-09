<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    public $modalEdit;
    public $name;
    public Supplier $supplier;

    public function render()
    {
        return view('livewire.suppliers.edit');
    }

    public function mount()
    {
        $this->modalEdit = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->modalEdit = false;
    }

    #[On('editSupplier')]
    public function editSupplier($id_supplier)
    {
        $this->supplier = Supplier::find($id_supplier);
        if ($this->supplier) {
            $this->modalEdit = true;
            $this->name = $this->supplier->name;
        }
    }

    public function update()
    {
        $this->validate(
            [
                'name' => 'required|string|max:30|unique:suppliers,name,' . $this->supplier->id_supplier . ',id_supplier',
            ],
            [
                'name.required' => 'El nombre es requerido.',
                'name.string' => 'El nombre debe de ser un texto.',
                'name.max' => 'El nombre no debe tener mas de 30 caracteres.',
                'name.unique' => 'El nombre del proveedor ya existe.',
            ]
        );

        try {
            $this->supplier->name = $this->name;

            $this->supplier->save();

            $this->modalEdit = false;
            $this->dispatch('update-supplier');
            Toaster::success("Proveedor actualizado con éxito!.");
        } catch (\Exception $e) {
            Log::error('Error al actualizar el proveedor: ' . $e->getMessage());
            Toaster::error("Error al actualizar el proveedor.");
        }
    }
}
