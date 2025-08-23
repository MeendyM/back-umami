<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use Masmerise\Toaster\Toaster;

class Delete extends Component
{
    public $modalDelete;
    public $productId;

    public function render()
    {
        return view('livewire.products.delete');
    }

    #[On('showDeleteProduct')]
    public function showDelete($id_product)
    {
        $this->productId = $id_product;
        $this->modalDelete = true;
    }

    public function closeModal()
    {
        $this->modalDelete = false;
    }

    public function deleteProduct()
    {
        DB::beginTransaction();
        try {
            Product::where('id_product', $this->productId)->update(['is_delete' => true]);
            DB::commit();
            Toaster::success('Producto eliminado correctamente.');
            $this->modalDelete = false;
            $this->dispatch('update-product');
        } catch (\Exception $e) {
            Log::info('Error al eliminar el producto: ' . $e->getMessage());
            DB::rollBack();
        }
    }
}
