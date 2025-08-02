<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notification;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationManager extends Component
{
    use WithPagination;

    // Propiedades para el formulario
    public $title = '';
    public $message = '';
    public $target_type = 'all';
    public $target_institution_id = '';
    public $showModal = false;

    // Propiedades para filtros
    public $search = '';
    public $statusFilter = 'all'; // all, sent, pending

    protected $rules = [
        'title' => 'required|string|max:255',
        'message' => 'required|string|max:1000',
        'target_type' => 'required|in:all,student,client',
        'target_institution_id' => 'nullable|exists:institutions,id_institution',
    ];

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $notification = Notification::create([
            'title' => $this->title,
            'message' => $this->message,
            'target_type' => $this->target_type,
            'target_institution_id' => $this->target_institution_id ?: null,
            'sender_id' => Auth::user()->id_user,
        ]);

        session()->flash('message', 'Notificación creada correctamente. ¿Deseas enviarla ahora?');
        
        $this->resetForm();
        $this->showModal = false;
    }

    public function sendNotification($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        
        if ($notification->is_sent) {
            session()->flash('error', 'Esta notificación ya fue enviada.');
            return;
        }

        $sentCount = $notification->sendToTargetUsers();
        
        session()->flash('message', "Notificación enviada correctamente a {$sentCount} usuarios.");
    }

    public function delete($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        
        if ($notification->is_sent) {
            session()->flash('error', 'No se puede eliminar una notificación que ya fue enviada.');
            return;
        }

        $notification->delete();
        session()->flash('message', 'Notificación eliminada correctamente.');
    }

    public function getTargetUsersCount()
    {
        if (!$this->target_type) {
            return 0;
        }

        $query = User::query();

        if ($this->target_type !== 'all') {
            $query->where('type', $this->target_type);
        } else {
            $query->whereIn('type', ['client', 'student']);
        }

        if ($this->target_institution_id) {
            $query->where('id_institution', $this->target_institution_id);
        }

        return $query->count();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->title = '';
        $this->message = '';
        $this->target_type = 'all';
        $this->target_institution_id = '';
        $this->resetErrorBag();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Notification::with(['sender', 'targetInstitution']);

        // Aplicar filtro de búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        // Aplicar filtro de estado
        if ($this->statusFilter === 'sent') {
            $query->where('is_sent', true);
        } elseif ($this->statusFilter === 'pending') {
            $query->where('is_sent', false);
        }

        $notifications = $query->latest()->paginate(10);

        // Obtener instituciones para el select
        $institutions = Institution::orderBy('name')->get();

        return view('livewire.notifications.notification-manager', [
            'notifications' => $notifications,
            'institutions' => $institutions,
            'targetUsersCount' => $this->getTargetUsersCount(),
        ]);
    }
}
