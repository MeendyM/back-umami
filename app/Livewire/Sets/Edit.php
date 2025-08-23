<?php

namespace App\Livewire\Sets;

use App\Models\Product;
use App\Models\Set;
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
    public $name, $description;
    public $search = '';
    public $selected = [];
    public $set;
    public $only_in_set = false;
    
    // Para la imagen del set (edición) - UNA SOLA IMAGEN
    public $existingSetImage = null;
    public $newSetImage;
    public $newSetImagePreviewUrl = null;
    public $imageToDelete = false;
    
    // Estados de carga
    public $isLoadingImages = false;
    public $imagesLoaded = false;

    public function render()
    {
        $products = [];

        if (strlen($this->search) >= 1) {
            $products = Product::with('supplier', 'category')
                ->where('is_delete', false)
                ->where('name', 'like', "%{$this->search}%")
                ->limit(5)
                ->get();
        }

        return view('livewire.sets.edit', [
            'products' => $products,
        ]);
    }

    public function mount()
    {
        $this->modalEdit = false;
    }

    public function closeModal()
    {
        $this->modalEdit = false;
        
        // Limpiar estados de carga
        $this->isLoadingImages = false;
        $this->imagesLoaded = false;
        
        // Limpiar arrays de imágenes
        $this->reset([
            'existingSetImage',
            'newSetImage',
            'newSetImagePreviewUrl',
            'imageToDelete'
        ]);
        
        // Limpiar errores
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function selectProduct($id)
    {
        //Agregarle a currentSelected los nuevos productos seleccionados

        if (!in_array($id, $this->selected)) {
            $this->selected[] = $id;
        }
    }

    public function removeProduct($id)
    {
        if (($key = array_search($id, $this->selected)) !== false) {
            unset($this->selected[$key]);
        }
    }

    #[On('editSet')]
    public function editSet($id_set)
    {
        $this->isLoadingImages = true;
        $this->imagesLoaded = false;
        
        $this->set = Set::with('products')->find($id_set);

        if ($this->set) {
            $this->modalEdit = true;
            $this->name = $this->set->name;
            $this->description = $this->set->description;
            $this->only_in_set = $this->set->only_in_set ?? false;
            $this->selected = $this->set->products->pluck('id_product')->toArray();
            
            // Limpiar imagen antes de cargar nuevos datos
            $this->reset([
                'existingSetImage',
                'newSetImage',
                'newSetImagePreviewUrl',
                'imageToDelete'
            ]);
            
            // Cargar imagen existente del set
            $this->loadExistingSetImage();
        }
    }
    
    private function loadExistingSetImage()
    {
        // Los sets solo tienen una imagen principal en url_image
        if ($this->set && $this->set->url_image) {
            $this->existingSetImage = [
                'id' => 'main_image', // ID ficticio para la imagen principal
                'url' => $this->set->url_image,
            ];
        }
        
        $this->isLoadingImages = false;
        $this->imagesLoaded = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'selected' => 'required|array|min:1',
            'newSetImage' => 'nullable|sometimes|file|image|max:2048',
        ], [
            'name.required' => 'El nombre del set es obligatorio.',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
            'selected.required' => 'Debes seleccionar al menos un producto para el set.',
            'selected.min' => 'Debes seleccionar al menos un producto para el set.',
            'newSetImage.image' => 'El archivo debe ser una imagen.',
            'newSetImage.max' => 'La imagen no puede superar los 2MB.',
        ]);

        // Validar que tengamos al menos una imagen (existente o nueva)
        $hasExistingImage = !is_null($this->existingSetImage) && !$this->imageToDelete;
        $hasNewImage = !is_null($this->newSetImage);
        
        if (!$hasExistingImage && !$hasNewImage) {
            $this->addError('images', 'El set debe tener al menos una imagen.');
            return;
        }

        DB::beginTransaction();
        $uploadedFiles = []; // Para tracking de rollback

        try {
            $newImageUrl = null;
            
            // PASO 1: Subir nueva imagen a Firebase si hay una
            if (!is_null($this->newSetImage)) {
                $firebaseStorage = new FirebaseStorage();
                
                $filename = uniqid() . '.' . $this->newSetImage->getClientOriginalExtension();
                $realPath = $this->newSetImage->getRealPath();

                $res = $firebaseStorage->uploadFileToFolder($realPath, $filename, 'sets');

                $path   = $res['name'];
                $bucket = $res['bucket'];
                $token  = $res['downloadTokens'];

                $newImageUrl = sprintf(
                    'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media&token=%s',
                    $bucket,
                    urlencode($path),
                    $token
                );

                $uploadedFiles[] = $path;
                Log::info('Nueva imagen de set subida exitosamente', ['url' => $newImageUrl]);
            }

            // PASO 2: Actualizar el set
            $this->set->name = $this->name;
            $this->set->description = $this->description;
            $this->set->only_in_set = $this->only_in_set;
            
            // PASO 3: Actualizar la imagen si hay una nueva
            if ($newImageUrl) {
                $this->set->url_image = $newImageUrl;
            } elseif ($this->imageToDelete) {
                // Si se eliminó la imagen existente y no hay nueva, limpiar
                $this->set->url_image = null;
            }

            // PASO 4: Actualizar los productos del set
            $this->set->products()->sync($this->selected);

            $this->set->save();

            DB::commit();

            $this->closeModal();
            $this->dispatch('refresh-sets');
            Toaster::success('Set actualizado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();
            
            // Limpiar archivos subidos a Firebase si algo falló
            if (!empty($uploadedFiles)) {
                $firebaseStorage = new FirebaseStorage();
                foreach ($uploadedFiles as $filePath) {
                    try {
                        $firebaseStorage->deleteFile($filePath);
                        Log::info("Archivo de set eliminado de Firebase durante rollback: {$filePath}");
                    } catch (\Exception $deleteException) {
                        Log::error("Error al eliminar archivo de set de Firebase durante rollback: {$filePath} - " . $deleteException->getMessage());
                    }
                }
            }

            Log::error('Error al actualizar el set: ' . $e->getMessage());
            Toaster::error('Error al actualizar el set. Los cambios han sido cancelados.');
        }
    }

    public function cleanSelection()
    {
        $this->selected = [];
        Toaster::success('Selección de productos limpiada.');
    }

    // Método para manejar nueva imagen subida
    public function updatedNewSetImage()
    {
        if (!$this->newSetImage) {
            return;
        }
        
        Log::info('Nueva imagen de set seleccionada para edición');
        
        try {
            // Generar preview URL
            $this->newSetImagePreviewUrl = $this->newSetImage->temporaryUrl();
            
            Log::info('Preview de imagen de set generado exitosamente');
            
            // Dispatch para notificar
            $this->dispatch('setImageUploaded');
            
        } catch (\Exception $e) {
            Log::error("Error procesando nueva imagen de set en edición: " . $e->getMessage());
            $this->addError('newSetImage', 'Error al procesar la imagen seleccionada.');
        }
    }

    // Método para eliminar la nueva imagen (antes de guardar)
    public function removeNewSetImage()
    {
        Log::info("Eliminando nueva imagen de set");
        
        $this->newSetImage = null;
        $this->newSetImagePreviewUrl = null;
        
        Log::info("Nueva imagen de set eliminada");
    }

    // Método para marcar la imagen existente para eliminación
    public function markSetImageForDeletion()
    {
        $this->imageToDelete = true;
        $this->existingSetImage = null; // Remover de la vista
        Log::info("Imagen existente del set marcada para eliminación");
    }
}
