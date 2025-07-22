<?php

namespace App\Livewire\Products;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Supplier;
use Exception;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;
use App\Helpers\FirebaseStorage;
use App\Models\ProductImage;

class Create extends Component
{
    use WithFileUploads;

    public $modal = false;

    public $name, $description, $price, $id_category, $id_supplier, $is_customized = false;
    public $imagePreviewUrl = [];
    public $suppliers;
    public $categories;
    public $images = [];

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

    public function updatedImages()
    {
        $this->imagePreviewUrl = [];

        foreach ($this->images as $image) {
            $this->imagePreviewUrl[] = $image->temporaryUrl();
        }
    }

    public function cleanImages()
    {
        $this->images = [];
        $this->imagePreviewUrl = [];
    }

    public function addImage($file)
    {
        if (count($this->images) >= 5) {
            return;
        }

        $this->images[] = $file;
        $this->imagePreviewUrl[] = $file->temporaryUrl();
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        unset($this->imagePreviewUrl[$index]);

        // NO reindexamos para mantener los índices originales consistentes
        // Los índices faltantes serán manejados correctamente en el foreach
    }


    public function clean()
    {
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->id_category = '';
        $this->id_supplier = '';
        $this->is_customized = false;
        $this->cleanImages();

        //reiniciar las alertas de error
        $this->resetErrorBag();
        $this->resetValidation();
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
                'images' => 'required|array|min:1|max:5',
            ],
            [
                'name.required' => 'El nombre del producto es obligatorio.',
                'description.required' => 'La descripción del producto es obligatoria.',
                'price.required' => 'El precio del producto es obligatorio.',
                'id_category.required' => 'La categoría del producto es obligatoria.',
                'id_supplier.required' => 'El proveedor del producto es obligatorio.',
                'is_customized.required' => 'Debe indicar si el producto es personalizado.',
                'images.required' => 'Debes subir al menos una imagen.',
                'images.*.image' => 'Cada archivo debe ser una imagen.',
            ]
        );

        try {
            $urls = [];
            $firebaseStorage = new FirebaseStorage();

            foreach ($this->images as $index => $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $realPath = $image->getRealPath();

                try {
                    $res = $firebaseStorage->uploadFile($realPath, $filename);

                    $path   = $res['name'];
                    $bucket = $res['bucket'];
                    $token  = $res['downloadTokens'];

                    $downloadUrl = sprintf(
                        'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media&token=%s',
                        $bucket,
                        urlencode($path),
                        $token
                    );

                    // Aquí asignamos la URL con índice numérico
                    $urls[$index] = $downloadUrl;
                } catch (\Exception $e) {
                    Log::error("Error subiendo imagen: " . $e->getMessage());
                    Toaster::error('Error al subir una imagen');
                    return;
                }
            }

            Log::info($urls);

            $details = Product::create([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'id_category' => $this->id_category,
                'id_supplier' => $this->id_supplier,
                'is_customized' => $this->is_customized,
            ]);

            foreach ($urls as $url) {
                ProductImage::create([
                    'product_id' => $details->id_product,
                    'url' => $url,
                ]);
            }

            $this->modal = false;
            $this->clean();
            $this->dispatch('update-product');
            Toaster::success("Producto creado con éxito!.");
        } catch (Exception $e) {

            Toaster::error('Error al crear el producto');
            // Log the error or handle it as needed
            Log::error('Error creating product: ' . $e->getMessage());
        }
    }
}
