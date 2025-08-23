<?php

namespace App\Livewire\Suppliers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;


class Delete extends Component
{
    public $modalDelete = false;
    public $supplierId;

    public function render()
    {
        return view('livewire.suppliers.delete');
    }

    #[On('showDeleteSupplier')]
    public function showDelete($id_supplier)
    {
        $this->supplierId = $id_supplier;
        $this->modalDelete = true;
    }

    public function closeModal()
    {
        $this->modalDelete = false;
    }

    public function deleteSupplier()
    {
        DB::beginTransaction();

        try {
            //eliminar proveedor mediante borrado logico
            Supplier::where('id_supplier', $this->supplierId)->update(['is_deleted' => true]);

            Product::where('id_supplier', $this->supplierId)->update(['id_supplier' => null]);

            Toaster::success('Proveedor eliminado correctamente.');
            $this->modalDelete = false;
            $this->dispatch('update-supplier');

            DB::commit();
        } catch (\Exception $e) {
            Log::info($e);
            Toaster::error('Error al eliminar el proveedor.');
            DB::rollBack();
            // Handle the exception
        }
    }
}
