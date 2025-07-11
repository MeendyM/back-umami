<?php

namespace App\Livewire\Sets;

use App\Models\Product;
use App\Models\Set;
use Livewire\Component;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    public $modalEdit;
    public $name, $description;
    public $search = '';
    public $selected = [];
    public $set;

    public function render()
    {
        $products = [];

        if (strlen($this->search) >= 1) {
            $products = Product::with('supplier')
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('category', 'like', "%{$this->search}%")
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
        $this->set = Set::with('products')->find($id_set);

        if ($this->set) {
            $this->modalEdit = true;
            $this->name = $this->set->name;
            $this->description = $this->set->description;
            $this->selected = $this->set->products->pluck('id_product')->toArray();
        }
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if($this->selected == []) {
            Toaster::error('Selecciona al menos un producto para el set.');
            return;
        }

        try {
            $this->set->name = $this->name;
            $this->set->description = $this->description;
            

            // Actualizar los productos del set
            $this->set->products()->sync($this->selected);

            $this->set->save();

            $this->closeModal();
            Toaster::success('Set actualizado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar el set: ' . $e->getMessage());
            Toaster::error('Error al actualizar el set. Por favor, inténtalo de nuevo.');
        }
    }

    public function cleanSelection()
    {
        $this->selected = [];
        Toaster::success('Selección de productos limpiada.');
    }
}
