<?php

namespace App\Livewire\Sets;

use App\Models\Product;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    public $modal = false;

    public $name;
    public $description;
    public $search = '';

    public $selected = [];

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
}
