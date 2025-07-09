<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    public $modalEdit;
    public $name;
    public Category $category;

    public function render()
    {
        return view('livewire.categories.edit');
    }

    public function mount()
    {
        $this->modalEdit = false;
    }

    public function closeModal()
    {
        $this->modalEdit = false;
    }

    #[On('editCategory')]
    public function editCategory($id_category)
    {
        $this->category = Category::find($id_category);
        if ($this->category) {
            $this->modalEdit = true;
            $this->name = $this->category->name;
        }

    }

    public function update()
    {
        $this->validate(
            [
                'name' => 'required|string|max:30|unique:categories,name,' . $this->category->id_category . ',id_category',
            ],
            [
                'name.required' => 'El nombre es requerido.',
                'name.string' => 'El nombre debe de ser un texto.',
                'name.max' => 'El nombre no debe tener mas de 30 caracteres.',
                'name.unique' => 'El nombre de la categoria ya existe.',
            ]
        );

        try {
            $this->category->name = $this->name;

            $this->category->save();


            
            $this->closeModal();
            $this->dispatch('update-category'); // para actualizar la tabla de categorias
            Toaster::success("Categoria actualizada con éxito!.");
        } catch (\Exception $e) {
            Toaster::error("Error al actualizar la categoria");
            Log::info($e);
        }
    }
}
