<?php

namespace App\Livewire\Institutions;

use Livewire\Component;
use App\Models\Institution;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    public $modalEdit;
    public $name;
    public Institution $institution;

    public function render()
    {
        return view('livewire.institutions.edit');
    }

    public function mount()
    {
        $this->modalEdit = false;
    }

    public function closeModal()
    {
        $this->modalEdit = false;
    }

    #[On('editInstitution')]
    public function editInstitution($id_institution)
    {
        $this->institution = Institution::find($id_institution);
        if ($this->institution) {
            $this->modalEdit = true;
            $this->name = $this->institution->name;
        }
    }

    public function update()
    {
        $this->validate(
            [
                'institution.name' => 'required|string|max:255|unique:institutions,name,' . $this->institution->id_institution . ',id_institution',
            ],
            [
                'institution.name.required' => 'El nombre de la institución es obligatorio.',
                'institution.name.string' => 'El nombre de la institución debe ser una cadena de texto.',
                'institution.name.max' => 'El nombre de la institución no puede tener más de 255 caracteres.',
                'institution.name.unique' => 'El nombre de la institución ya está en uso.',
            ]
        );

        try {
            $this->institution->name = $this->name;
            $this->institution->save();
            Toaster::success('Institución actualizada correctamente.');
            $this->closeModal();
            $this->dispatch('update-institution');
        } catch (\Throwable $th) {
            Log::info('Error al actualizar la institución: ' . $th->getMessage());
            Toaster::error('Error al actualizar la institución.');
        }
    }
}
