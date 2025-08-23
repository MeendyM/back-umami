<?php

namespace App\Livewire\Sets;

use App\Models\ProductSet;
use App\Models\Set;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Delete extends Component
{
    public $modalDelete;
    public $setId;

    public function render()
    {
        return view('livewire.sets.delete');
    }

    #[On('showDeleteSet')]
    public function showDelete($id_set)
    {
        $this->setId = $id_set;
        $this->modalDelete = true;
    }

    public function deleteSet()
    {
        DB::beginTransaction();

        try {
            //colocar como true el campo 'is_deleted' en la tabla 'sets'
            Set::where('id_set', $this->setId)->update(['is_deleted' => true]);

            //Eliminar los registros de la tabla pivote product_set
            ProductSet::where('id_set', $this->setId)->delete();

            Toaster::success('Set eliminado correctamente.');
            $this->modalDelete = false;
            $this->dispatch('update-set');

            DB::commit();
        } catch (\Exception $e) {
            Log::info($e);
            Toaster::error('Error al eliminar el set.');
            DB::rollBack();
            // Handle the exception
        }
    }
}
