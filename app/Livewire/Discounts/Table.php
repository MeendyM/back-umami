<?php

namespace App\Livewire\Discounts;

use Livewire\Component;
use Livewire\WithPagination;
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

    #[On('update-discount')]
    public function updateDiscounts()
    {
        $this->resetPage(); // reiniciar a la página 1 al actualizar decscuentos
    }

    public function render()
    {
        return view('livewire.discounts.table');
    }
}
