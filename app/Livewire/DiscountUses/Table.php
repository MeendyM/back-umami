<?php

namespace App\Livewire\DiscountUses;

use App\Models\DiscountUse;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortAsc = true;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $uses = DiscountUse::with(['discount', 'order', 'user'])
            ->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })
                    ->orWhereHas('discount', function ($q) {
                        $q->where('code', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('order', function ($q) {
                        $q->where('id_order', 'like', "%{$this->search}%");
                    });
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);


        return view('livewire.discount-uses.table', [
            'uses' => $uses,
            'optionsPerPage' => [10, 25, 50],
        ]);
    }
}
