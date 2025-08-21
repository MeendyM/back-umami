<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Institution;
use Livewire\Attributes\On;

class UserTable extends Component
{
    use WithPagination;

    public $search = '';
    public $institutionFilter = '';

    protected $updatesQueryString = ['search', 'institutionFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingInstitutionFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->institutionFilter = '';
        $this->resetPage();
    }

    #[On('update-user')]
    public function updateUsers()
    {
        $this->resetPage(); // reiniciar a la página 1 al actualizar usuarios
    }

    public function render()
    {
        $query = User::with('institution');

        // Aplicar filtro de búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhereHas('institution', function($subQuery) {
                      $subQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Aplicar filtro de institución
        if ($this->institutionFilter) {
            if ($this->institutionFilter === 'sin_institucion') {
                $query->whereNull('id_institution');
            } else {
                $query->where('id_institution', $this->institutionFilter);
            }
        }

        $users = $query->paginate(10);

        // Obtener todas las instituciones para el select
        $institutions = Institution::orderBy('name')->get();

        return view('livewire.users.user-table', [
            'users' => $users,
            'institutions' => $institutions
        ]);
    }
}
