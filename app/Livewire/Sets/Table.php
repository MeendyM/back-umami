<?php

namespace App\Livewire\Sets;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Set;
use Livewire\Attributes\On;

class Table extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortAsc = true;

    protected $queryString = ['search']; // para mantener el valor al navegar

    public function updatingSearch()
    {
        $this->resetPage(); // reiniciar a la página 1 cuando se busca algo
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
            $this->sortField = $field;
        }
    }

    #[On('update-set')]
    public function updateSets()
    {
        $this->resetPage(); // reiniciar a la página 1 al actualizar productos
    }

    public function render()
    {
        $sets = Set::query()
            ->where('name', 'like', "%{$this->search}%")
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view(
            'livewire.sets.table',
            [
                'sets' => $sets,
                'optionsPerPage' => [10, 25, 50],
            ]
        );
    }
}
