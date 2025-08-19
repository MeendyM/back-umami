<?php

namespace App\Livewire\Institutions;

use Livewire\Component;
use App\Models\Institution;
use Masmerise\Toaster\Toast;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    public $modal = false;

    public $name;

    public function render()
    {
        return view('livewire.institutions.create');
    }

    public function openModal()
    {
        $this->modal = true;
        $this->clean();
    }

    public function closeModal()
    {
        $this->modal = false;
        $this->clean();
    }

    public function clean()
    {
        $this->name = '';
    }

    public function save()
    {
        $this->validate(
            [
                //validaciones sin el edit
                'name' => 'required|string|max:255|unique:institutions,name'
            ],
            [
                'name.required' => 'El nombre es obligatorio.',
                'name.string' => 'El nombre debe ser una cadena de texto.',
                'name.max' => 'El nombre no puede tener más de 255 caracteres.',
                'name.unique' => 'El nombre ya está en uso.'
            ]
        );
        try {
            //code...
            Institution::create(['name' => $this->name]);

            $this->closeModal();
            $this->modal = false;
            Toaster::success('Institución creada correctamente.');
            $this->dispatch('update-institution'); // para actualizar la tabla de instituciones
        } catch (\Exception $e) {
            Log::info($e);
            Toaster::error('Error al crear la institución');
        }
    }
}
