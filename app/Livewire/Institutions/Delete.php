<?php

namespace App\Livewire\Institutions;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Institution;
use Illuminate\Support\Facades\DB;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Log;

class Delete extends Component
{
    public $modalDelete;
    public $institutionId;

    public function render()
    {
        return view('livewire.institutions.delete');
    }

    #[On('ShowDeleteInstitution')]
    public function showDeleteModal($id_institution)
    {
        $this->institutionId = $id_institution;
        $this->modalDelete = true;
    }

    public function deleteInstitution()
    {
        DB::beginTransaction();
        try {
            //Eliminar producto de la base de datos
            $institution = Institution::find($this->institutionId);
            if ($institution) {
                $institution->delete();
                DB::commit();
                Toaster::success('Institución eliminada correctamente.');
                $this->modalDelete = false;
                $this->dispatch('update-institution');
            } else {
                DB::rollBack();
                Toaster::error('Institución no encontrada.');
            }
        } catch (\Exception $e) {
            Log::info('Error al eliminar la institución: ' . $e->getMessage());
            DB::rollBack();
        }
    }
}
