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
    public $name, $description, $price, $id_category = '', $id_supplier = '', $is_customized = false, $only_in_set = false;
    public $suppliers, $categories;
    public Product $product;

    // Para las imágenes existentes
    public $existingImages = [];

    // Para las nuevas imágenes (una a la vez)
    public $newImages;
    public $newImagePreviewUrl = [];

    // Array para acumular todas las nuevas imágenes
    public $allNewImages = [];

    // Para controlar qué imágenes eliminar
    public $imagesToDelete = [];

    // Para estados de carga y animaciones
    public $isLoadingImages = false;
    public $imagesLoaded = false;

    public function closeModal()
    {
        $this->modalEdit = false;

        // Limpiar estados de carga y animaciones
        $this->isLoadingImages = false;
        $this->imagesLoaded = false;

        // Limpiar arrays de imágenes - usar reset() para arrays indexados
        $this->reset([
            'existingImages',
            'newImages',
            'newImagePreviewUrl',
            'imagesToDelete',
            'allNewImages'
        ]);

        // Limpiar errores
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        // Recargar categorías y proveedores filtrados en cada render
        $this->suppliers = Supplier::where('is_deleted', false)->get();
        $this->categories = Category::where('is_deleted', false)->get();
        
        return view('livewire.products.edit');
    }

    #[On('editProduct')]
    public function editProduct($id_product)
    {
        $this->isLoadingImages = true;
        $this->imagesLoaded = false;

        $this->product = Product::with(['images', 'category'])->find($id_product);
        if ($this->product) {
            $this->modalEdit = true;
            $this->name = $this->product->name;
            $this->description = $this->product->description;
            $this->price = $this->product->price;
            $this->id_category = $this->product->id_category;
            $this->id_supplier = $this->product->id_supplier;
            $this->is_customized = $this->product->is_customized;
            $this->only_in_set = $this->product->only_in_set;

            // Limpiar arrays de imágenes antes de cargar nuevos datos
            $this->reset([
                'existingImages',
                'newImages',
                'newImagePreviewUrl',
                'imagesToDelete',
                'allNewImages'
            ]);

            // Usar dispatch para cargar imágenes después de renderizar el modal
            $this->dispatch('loadImages');
        }
    }

    #[On('imagesReady')]
    public function loadExistingImagesWithDelay()
    {
        // Cargar las imágenes existentes con un pequeño delay para animación
        $this->existingImages = $this->product->images->map(function ($image) {
            return [
                'id' => $image->id_product_image,
                'url' => $image->url,
            ];
        })->toArray();

        $this->isLoadingImages = false;
        $this->imagesLoaded = true;
    }

    // Método para manejar nueva imagen subida (una a la vez)
    public function updatedNewImages()
    {
        if (!$this->newImages) {
            return;
        }

        Log::info('Nueva imagen seleccionada', [
            'total_acumuladas' => count($this->allNewImages),
            'previews_actuales' => count($this->newImagePreviewUrl)
        ]);

        try {
            // Agregar la nueva imagen al array acumulado
            $this->allNewImages[] = $this->newImages;

            // Agregar el preview URL
            $this->newImagePreviewUrl[] = $this->newImages->temporaryUrl();

            Log::info('Imagen agregada exitosamente', [
                'total_acumuladas' => count($this->allNewImages),
                'total_previews' => count($this->newImagePreviewUrl)
            ]);

            // Limpiar el input para permitir seleccionar otra imagen
            $this->newImages = null;

            // Dispatch para notificar
            $this->dispatch('imageUploaded');
        } catch (\Exception $e) {
            Log::error("Error procesando nueva imagen: " . $e->getMessage());
            $this->addError('newImages', 'Error al procesar la imagen seleccionada.');
        }
    }

    // Método para eliminar una imagen nueva (antes de guardar)
    public function removeNewImage($index)
    {
        Log::info("Eliminando imagen en índice {$index}", [
            'antes_count_images' => count($this->allNewImages),
            'antes_count_previews' => count($this->newImagePreviewUrl)
        ]);

        // Eliminar de ambos arrays
        if (isset($this->allNewImages[$index])) {
            unset($this->allNewImages[$index]);
        }
        if (isset($this->newImagePreviewUrl[$index])) {
            unset($this->newImagePreviewUrl[$index]);
        }

        // Reindexar los arrays para evitar huecos en los índices
        $this->allNewImages = array_values($this->allNewImages);
        $this->newImagePreviewUrl = array_values($this->newImagePreviewUrl);

        Log::info("Imagen eliminada", [
            'despues_count_images' => count($this->allNewImages),
            'despues_count_previews' => count($this->newImagePreviewUrl)
        ]);
    }

    // Método para marcar una imagen existente para eliminar
    public function markImageForDeletion($imageId)
    {
        if (!in_array($imageId, $this->imagesToDelete)) {
            $this->imagesToDelete[] = $imageId;
        }

        // Remover de la lista de imágenes existentes para la vista
        $this->existingImages = array_filter($this->existingImages, function ($image) use ($imageId) {
            return $image['id'] != $imageId;
        });
    }

    // Método para cancelar la eliminación de una imagen
    public function unmarkImageForDeletion($imageId)
    {
        $this->imagesToDelete = array_filter($this->imagesToDelete, function ($id) use ($imageId) {
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
                'only_in_set' => 'boolean',
                'allNewImages' => 'nullable|array|max:5',
                'allNewImages.*' => 'sometimes|file|image|max:2048', // máximo 2MB por imagen
            ],
            [
                'name.required' => 'El nombre del producto es obligatorio.',
                'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
                'price.numeric' => 'El precio debe ser un número válido.',
                'price.min' => 'El precio no puede ser negativo.',
                'price.required' => 'El precio del producto es obligatorio.',
                'id_category.required' => 'La categoría del producto es obligatoria.',
                'id_supplier.required' => 'El proveedor es obligatorio.',
                'allNewImages.max' => 'No puedes subir más de 5 imágenes.',
                'allNewImages.*.image' => 'Cada archivo debe ser una imagen.',
                'allNewImages.*.max' => 'Cada imagen no puede superar los 2MB.',
            ]
        );

        // Validar que después de eliminar y agregar, tengamos al menos 1 imagen
        $remainingImages = count($this->existingImages) - count($this->imagesToDelete);
        $totalImages = $remainingImages + count($this->allNewImages);

        if ($totalImages < 1) {
            $this->addError('images', 'El producto debe tener al menos una imagen.');
            return;
        }

        if ($totalImages > 5) {
            $this->addError('allNewImages', 'El producto no puede tener más de 5 imágenes en total.');
            return;
        }

        DB::beginTransaction();
        $uploadedFiles = []; // Para tracking de rollback

        try {
            // PASO 1: Subir nuevas imágenes a Firebase si las hay
            $newImageUrls = [];
            if (!empty($this->allNewImages)) {
                $firebaseStorage = new FirebaseStorage();

                foreach ($this->allNewImages as $index => $image) {
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
            $this->product->only_in_set = $this->only_in_set;
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
