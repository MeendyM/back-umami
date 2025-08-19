<?php

namespace App\Livewire\Institutions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Institution;
use Livewire\Attributes\On;

class InstitutionCrud extends Component
{
    use WithPagination;

    public $search = '';
    public $name = '';
    public $editingId = null;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255|unique:institutions,name',
    ];

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[On('update-institution')]
    public function updateInstitutions()
    {
        $this->resetPage(); // reiniciar a la página 1 al actualizar instituciones
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $institution = Institution::findOrFail($id);
        $this->editingId = $id;
        $this->name = $institution->name;
        $this->showModal = true;
        
        // Actualizar regla de validación para edición
        $this->rules['name'] = 'required|string|max:255|unique:institutions,name,' . $id . ',id_institution';
    }

    public function delete($id)
    {
        try {
            Institution::findOrFail($id)->delete();
            session()->flash('message', 'Institución eliminada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'No se puede eliminar la institución porque está siendo utilizada.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->rules['name'] = 'required|string|max:255|unique:institutions,name';
        $this->resetErrorBag();
    }

    public function render()
    {
        $institutions = Institution::where('name', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.institutions.institution-crud', [
            'institutions' => $institutions
        ]);
    }
}
