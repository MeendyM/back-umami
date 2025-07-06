<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Supplier;

class Create extends Component
{
    use WithFileUploads;

    public $modal = false;

    public $name, $description, $price, $category, $id_supplier, $is_customized = false;
    public $image, $imagePreviewUrl;
    public $suppliers;

    public function mount()
    {
        $this->suppliers = Supplier::all(); // asegúrate de tener datos en la tabla
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
        $this->category = '';
        $this->id_supplier = '';
        $this->is_customized = false;

    }

    public function save()
    {

        $this->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'is_customized' => 'required|boolean',
        ],
        [
            'name.required' => 'El nombre del producto es obligatorio.',
            'description.required' => 'La descripción del producto es obligatoria.',
            'price.required' => 'El precio del producto es obligatorio.',
            'category.required' => 'La categoría del producto es obligatoria.',
            'id_supplier.required' => 'El proveedor del producto es obligatorio.',
            'is_customized.required' => 'Debe indicar si el producto es personalizado.'
        ]);

        
         Product::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category,
            'id_supplier' => $this->id_supplier,
            'is_customized' => $this->is_customized
        ]);


        $this->modal = false;
        session()->flash('message', 'Producto creado correctamente.');
        $this->dispatch('product-created'); // puedes usarlo para actualizar tablas
    }
}
