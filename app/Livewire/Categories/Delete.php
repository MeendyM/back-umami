<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use Masmerise\Toaster\Toaster;

class Delete extends Component
{
    public $modalDelete;
    public $categoryId;

    public function render()
    {
        return view('livewire.categories.delete');
    }

    #[On('showDeleteCategory')]
    public function showDelete($id_category)
    {
        $this->categoryId = $id_category;
        $this->modalDelete = true;
    }

    public function deleteCategory()
    {
        DB::beginTransaction();

        try {
            //eliminar categoria mediente borrado logico
            Category::where('id_category', $this->categoryId)->update(['is_deleted' => true]);

            // Eliminar el id_category de los productos
            Product::where('id_category', $this->categoryId)->update(['id_category' => null]);

            Toaster::success('Categoría eliminada correctamente.');
            $this->modalDelete = false;
            $this->dispatch('update-category');

            DB::commit();
        } catch (\Exception $e) {
            Log::info($e);
            Toaster::error('Error al eliminar la categoría.');
            DB::rollBack();
            // Handle the exception
        }
    }
}
