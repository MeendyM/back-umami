<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Supplier;
use Exception;
use Livewire\Component;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    public $modalEdit;
    public $name, $description, $price, $category, $id_supplier, $is_customized;
    public $suppliers;
    public Product $product;

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
        $this->suppliers = Supplier::all();
        return view('livewire.products.edit');
    }

    #[On('editProduct')]
    public function editProduct($id_product)
    {
        $this->product = Product::find($id_product);
        if ($this->product) {
            $this->modalEdit = true;
            $this->name = $this->product->name;
            $this->description = $this->product->description;
            $this->price = $this->product->price;
            $this->category = $this->product->category;
            $this->id_supplier = $this->product->id_supplier;
            $this->is_customized = $this->product->is_customized;
            /* $this->modalEdit = true; */
        }
    }

    public function update()
    {
        $this->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'category' => 'required|string|max:255',
                'id_supplier' => 'required|exists:suppliers,id_supplier',
                'is_customized' => 'boolean',
            ],
            [
                'name.required' => 'El nombre del producto es obligatorio.',
                'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
                'description.required' => 'La descripción del producto es obligatoria.',
                'price.numeric' => 'El precio debe ser un número válido.',
                'price.min' => 'El precio no puede ser negativo.',
                'price.required' => 'El precio del producto es obligatorio.',
                'category.required' => 'La categoría del producto es obligatoria.',
                'id_supplier.required' => 'El proveedor es obligatorio.',
            ]
        );

        try {

            $this->product->name = $this->name;
            $this->product->description = $this->description;
            $this->product->price = $this->price;
            $this->product->category = $this->category;
            $this->product->id_supplier = $this->id_supplier;
            $this->product->is_customized = $this->is_customized;



            $this->product->save();

            $this->closeModal();
            $this->dispatch('update-product');
            Toaster::success("Producto editado con éxito!.");
        } catch (Exception $e) {
            Toaster::error("Algo salio mal, intentalo de nuevo.");
            Log::debug('Error al editar el producto: ' . $e->getMessage());
        }
    }
}
