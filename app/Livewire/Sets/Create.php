<?php

namespace App\Livewire\Sets;

use App\Models\Product;
use App\Models\Set;
use App\Helpers\FirebaseStorage;
use Exception;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    use WithFileUploads;

    public $modal = false;

    public $name;
    public $description;
    public $search = '';
    public $only_in_set = false;

    public $selected = [];
    
    // Para la imagen del set - UNA SOLA IMAGEN
    public $setImage;
    public $setImagePreviewUrl = null;

    public function render()
    {
        $products = [];

        if (strlen($this->search) >= 1) {
            $products = Product::with('supplier', 'category')
                ->where('name', 'like', "%{$this->search}%")
                ->orWhereHas('category', function ($query) {
                    $query->where('name', 'like', "%{$this->search}%");
                })
                ->limit(5)
                ->get();
        }

        return view('livewire.sets.create', [
            'products' => $products,
        ]);
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
        $this->search = '';
        $this->only_in_set = false;
        $this->selected = [];
        
        // Limpiar arrays de imágenes
        $this->reset([
            'setImage',
            'setImagePreviewUrl'
        ]);
    }

    public function selectProduct($id)
    {
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

    public function cleanSelection()
    {
        $this->selected = [];
        Toaster::success('Selección de productos limpiada.');
    }

    // Método para manejar imagen subida
    public function updatedSetImage()
    {
        if (!$this->setImage) {
            return;
        }
        
        Log::info('Imagen de set seleccionada');
        
        try {
            // Generar preview URL
            $this->setImagePreviewUrl = $this->setImage->temporaryUrl();
            
            Log::info('Preview de imagen de set generado exitosamente');
            
            // Dispatch para notificar
            $this->dispatch('setImageUploaded');
            
        } catch (\Exception $e) {
            Log::error("Error procesando imagen de set: " . $e->getMessage());
            $this->addError('setImage', 'Error al procesar la imagen seleccionada.');
        }
    }

    // Método para eliminar la imagen del set
    public function removeSetImage()
    {
        Log::info("Eliminando imagen de set");
        
        $this->setImage = null;
        $this->setImagePreviewUrl = null;
        
        Log::info("Imagen de set eliminada");
    }

    public function save()
    {
        $this->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'selected' => 'required|array|min:1',
                'setImage' => 'required|file|image|max:2048',
            ],
            [
                'name.required' => 'El nombre del set es obligatorio.',
                'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
                'selected.required' => 'Debes seleccionar al menos un producto para el set.',
                'selected.min' => 'Debes seleccionar al menos un producto para el set.',
                'setImage.required' => 'El set debe tener una imagen.',
                'setImage.image' => 'El archivo debe ser una imagen.',
                'setImage.max' => 'La imagen no puede superar los 2MB.',
            ]
        );

        DB::beginTransaction();
        $uploadedFiles = []; // Para tracking de rollback

        try {
            // PASO 1: Subir imagen a Firebase en la carpeta 'sets'
            $imageUrl = null;
            if (!is_null($this->setImage)) {
                $firebaseStorage = new FirebaseStorage();
                
                $filename = uniqid() . '.' . $this->setImage->getClientOriginalExtension();
                $realPath = $this->setImage->getRealPath();

                $res = $firebaseStorage->uploadFileToFolder($realPath, $filename, 'sets');

                $path   = $res['name'];
                $bucket = $res['bucket'];
                $token  = $res['downloadTokens'];

                $imageUrl = sprintf(
                    'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media&token=%s',
                    $bucket,
                    urlencode($path),
                    $token
                );

                $uploadedFiles[] = $path;
                Log::info('Imagen de set subida exitosamente', ['url' => $imageUrl]);
            }

            // PASO 2: Crear el set en la base de datos
            $set = Set::create([
                'name' => $this->name,
                'description' => $this->description,
                'url_image' => $imageUrl, // Usar la imagen como imagen principal
                'only_in_set' => $this->only_in_set,
            ]);

            // PASO 3: Asociar productos al set
            $set->products()->attach($this->selected);

            DB::commit();

            $this->closeModal();
            $this->dispatch('update-set');
            Toaster::success("Set creado con éxito!");

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

            Log::error('Error al crear el set: ' . $e->getMessage());
            Toaster::error("Error al crear el set. Los cambios han sido cancelados.");
        }
    }
}
