<?php

namespace App\Livewire\Products;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Supplier;
use Error;
use Exception;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    use WithFileUploads;

    public $modal = false;

    public $name, $description, $price, $id_category, $id_supplier, $is_customized = false;
    public $image, $imagePreviewUrl;
    public $suppliers;
    public $categories;

    public function mount()
    {
        $this->suppliers = Supplier::all();
        $this->categories = Category::all();

    }

    public function render()
    {
        return view('livewire.products.create');
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
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->id_category = '';
        $this->id_supplier = '';
        $this->is_customized = false;
    }

    public function save()
    {

        $this->validate(
            [
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'id_category' => 'required|exists:categories,id_category',
                'id_supplier' => 'required|exists:suppliers,id_supplier',
                'is_customized' => 'required|boolean',
            ],
            [
                'name.required' => 'El nombre del producto es obligatorio.',
                'description.required' => 'La descripción del producto es obligatoria.',
                'price.required' => 'El precio del producto es obligatorio.',
                'id_category.required' => 'La categoría del producto es obligatoria.',
                'id_supplier.required' => 'El proveedor del producto es obligatorio.',
                'is_customized.required' => 'Debe indicar si el producto es personalizado.'
            ]
        );

        try {
            Product::create([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'id_category' => $this->id_category,
                'id_supplier' => $this->id_supplier,
                'is_customized' => $this->is_customized
            ]);


            $this->modal = false;
            $this->clean();
            $this->dispatch('update-discount');
            Toaster::success("Producto creado con éxito!.");

        } catch (Exception $e) {

            Toaster::error('Error al crear el producto');
            // Log the error or handle it as needed
            Log::error('Error creating product: ' . $e->getMessage());

        }
    }
}
