<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\ProductImage;
use App\Helpers\FirebaseStorage;
use Exception;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Edit extends Component
{
    use WithFileUploads;

    public $modalEdit;
    public $name, $description, $price, $id_category, $id_supplier, $is_customized;
    public $suppliers, $categories;
    public Product $product;
    
    // Para las imágenes existentes
    public $existingImages = [];
    
    // Para las nuevas imágenes
    public $newImages = [];
    public $newImagePreviewUrl = [];
    
    // Para controlar qué imágenes eliminar
    public $imagesToDelete = [];

    public function mount()
    {
        $this->modalEdit = false;
        $this->suppliers = Supplier::all();
        $this->categories = Category::all();
    }

    public function closeModal()
    {
        $this->modalEdit = false;
    }

    public function render()
    {
        return view('livewire.products.edit');
    }

    #[On('editProduct')]
    public function editProduct($id_product)
    {
        $this->product = Product::with(['images', 'category'])->find($id_product);
        if ($this->product) {
            $this->modalEdit = true;
            $this->name = $this->product->name;
            $this->description = $this->product->description;
            $this->price = $this->product->price;
            $this->id_category = $this->product->id_category;
            $this->id_supplier = $this->product->id_supplier;
            $this->is_customized = $this->product->is_customized;
            
            // Cargar las imágenes existentes
            $this->existingImages = $this->product->images->map(function ($image) {
                return [
                    'id' => $image->id_product_image,
                    'url' => $image->url,
                ];
            })->toArray();
            
            // Limpiar arrays de nuevas imágenes
            $this->newImages = [];
            $this->newImagePreviewUrl = [];
            $this->imagesToDelete = [];
        }
    }

    // Método para manejar nuevas imágenes subidas
    public function updatedNewImages()
    {
        $this->newImagePreviewUrl = [];
        foreach ($this->newImages as $image) {
            $this->newImagePreviewUrl[] = $image->temporaryUrl();
        }
    }

    // Método para eliminar una imagen nueva (antes de guardar)
    public function removeNewImage($index)
    {
        unset($this->newImages[$index]);
        unset($this->newImagePreviewUrl[$index]);
    }

    // Método para marcar una imagen existente para eliminar
    public function markImageForDeletion($imageId)
    {
        if (!in_array($imageId, $this->imagesToDelete)) {
            $this->imagesToDelete[] = $imageId;
        }
        
        // Remover de la lista de imágenes existentes para la vista
        $this->existingImages = array_filter($this->existingImages, function($image) use ($imageId) {
            return $image['id'] != $imageId;
        });
    }

    // Método para cancelar la eliminación de una imagen
    public function unmarkImageForDeletion($imageId)
    {
        $this->imagesToDelete = array_filter($this->imagesToDelete, function($id) use ($imageId) {
            return $id != $imageId;
        });
        
        // Volver a cargar las imágenes existentes (o podrías guardar una copia)
        $this->loadExistingImages();
    }

    // Método auxiliar para recargar imágenes existentes
    private function loadExistingImages()
    {
        $this->existingImages = $this->product->images()
            ->whereNotIn('id_product_image', $this->imagesToDelete)
            ->get()
            ->map(function ($image) {
                return [
                    'id' => $image->id_product_image,
                    'url' => $image->url,
                ];
            })->toArray();
    }

    public function update()
    {
        $this->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'id_category' => 'required|exists:categories,id_category',
                'id_supplier' => 'required|exists:suppliers,id_supplier',
                'is_customized' => 'boolean',
                'newImages' => 'nullable|array|max:5',
                'newImages.*' => 'image|max:2048', // máximo 2MB por imagen
            ],
            [
                'name.required' => 'El nombre del producto es obligatorio.',
                'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
                'price.numeric' => 'El precio debe ser un número válido.',
                'price.min' => 'El precio no puede ser negativo.',
                'price.required' => 'El precio del producto es obligatorio.',
                'id_category.required' => 'La categoría del producto es obligatoria.',
                'id_supplier.required' => 'El proveedor es obligatorio.',
                'newImages.max' => 'No puedes subir más de 5 imágenes.',
                'newImages.*.image' => 'Cada archivo debe ser una imagen.',
                'newImages.*.max' => 'Cada imagen no puede superar los 2MB.',
            ]
        );

        // Validar que después de eliminar y agregar, tengamos al menos 1 imagen
        $remainingImages = count($this->existingImages) - count($this->imagesToDelete);
        $totalImages = $remainingImages + count($this->newImages);
        
        if ($totalImages < 1) {
            $this->addError('images', 'El producto debe tener al menos una imagen.');
            return;
        }
        
        if ($totalImages > 5) {
            $this->addError('newImages', 'El producto no puede tener más de 5 imágenes en total.');
            return;
        }

        DB::beginTransaction();
        $uploadedFiles = []; // Para tracking de rollback

        try {
            // PASO 1: Subir nuevas imágenes a Firebase si las hay
            $newImageUrls = [];
            if (!empty($this->newImages)) {
                $firebaseStorage = new FirebaseStorage();
                
                foreach ($this->newImages as $index => $image) {
                    $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                    $realPath = $image->getRealPath();

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

                    $newImageUrls[] = $downloadUrl;
                    $uploadedFiles[] = $path;
                }
                
                Log::info('Nuevas imágenes subidas exitosamente', $newImageUrls);
            }

            // PASO 2: Actualizar datos del producto
            $this->product->name = $this->name;
            $this->product->description = $this->description;
            $this->product->price = $this->price;
            $this->product->id_category = $this->id_category;
            $this->product->id_supplier = $this->id_supplier;
            $this->product->is_customized = $this->is_customized;
            $this->product->save();

            // PASO 3: Eliminar imágenes marcadas para eliminación
            if (!empty($this->imagesToDelete)) {
                ProductImage::whereIn('id_product_image', $this->imagesToDelete)->delete();
                Log::info('Imágenes eliminadas de la base de datos', $this->imagesToDelete);
            }

            // PASO 4: Agregar nuevas imágenes a la base de datos
            foreach ($newImageUrls as $url) {
                ProductImage::create([
                    'product_id' => $this->product->id_product,
                    'url' => $url,
                ]);
            }

            DB::commit();

            $this->closeModal();
            $this->dispatch('update-product');
            Toaster::success("Producto actualizado con éxito!");

        } catch (Exception $e) {
            DB::rollBack();
            
            // Limpiar archivos subidos a Firebase si algo falló
            if (!empty($uploadedFiles)) {
                $firebaseStorage = new FirebaseStorage();
                foreach ($uploadedFiles as $filePath) {
                    try {
                        $firebaseStorage->deleteFile($filePath);
                        Log::info("Archivo eliminado de Firebase durante rollback: {$filePath}");
                    } catch (\Exception $deleteException) {
                        Log::error("Error al eliminar archivo de Firebase durante rollback: {$filePath} - " . $deleteException->getMessage());
                    }
                }
            }

            Log::error('Error al actualizar el producto: ' . $e->getMessage());
            Toaster::error("Error al actualizar el producto. Los cambios han sido cancelados.");
        }
    }
}
