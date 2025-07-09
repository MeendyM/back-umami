<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    public $modal = false;
    public $name;


    public function render()
    {
        return view('livewire.categories.create');
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
    }

    public function save()
    {
        $this->validate(
            [
                'name' => 'required|string|max:30|unique:categories,name',
            ],
            [
                'name.required' => 'El nombre es requerido.',
                'name.string' => 'El nombre debe de ser un texto.',
                'name.max' => 'El nombre no debe tener mas de 30 caracteres.',
                'name.unique' => 'El nombre de la categoria ya existe.',
            ]
        );

        try {

            Category::create([
                'name' => $this->name,
            ]);

            $this->modal = false;
            $this->clean();
            $this->dispatch('update-category'); // para actualizar la tabla de categorias
            Toaster::success("Categoria creada con éxito!.");
        } catch (\Exception $e) {
            Toaster::error("Error al crear la categoria");
            Log::info($e);
        }
    }
}
