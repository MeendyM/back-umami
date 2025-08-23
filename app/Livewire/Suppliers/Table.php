<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;
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

    #[On('update-supplier')]
    public function updateSuppliers()
    {
        $this->resetPage(); // reiniciar a la página 1 al actualizar productos
    }

    public function render()
    {
        $suppliers = Supplier::query()
            ->where('is_deleted', false)
            ->where('name', 'like', "%{$this->search}%")
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view(
            'livewire.suppliers.table',
            [
                'suppliers' => $suppliers,
                'optionsPerPage' => [10, 25, 50],
            ]
        );
    }
}
