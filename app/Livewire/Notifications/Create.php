<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\Notification;
use App\Models\User;
use App\Models\Institution;
use App\Enums\NotificationType;
use Illuminate\Support\Arr;

class Create extends Component
{
    public $title = '';
    public $message = '';
    public $type = 'info';
    public $target = 'all'; // all, user, institution
    public $user_id = null;
    public $institution_id = null;
    public $showModal = false;

    public function render()
    {
        return view('livewire.notifications.create', [
            'users' => User::select('id_user', 'name')->orderBy('name')->get(),
            'institutions' => Institution::select('id_institution', 'name')->orderBy('name')->get(),
            'types' => NotificationType::labels(),
        ]);
    }

    public function openModal()
    {
        $this->reset(['title', 'message', 'type', 'target', 'user_id', 'institution_id']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required',
            'target' => 'required',
        ]);

        $users = collect();
        if ($this->target === 'all') {
            $users = User::all();
        } elseif ($this->target === 'user' && $this->user_id) {
            $users = User::where('id_user', $this->user_id)->get();
        } elseif ($this->target === 'institution' && $this->institution_id) {
            $users = User::where('id_institution', $this->institution_id)->get();
        }

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id_user,
                'title' => $this->title,
                'message' => $this->message,
                'type' => $this->type,
                'read' => false,
                'data' => [],
            ]);
        }

        $this->showModal = false;
        $this->dispatch('notification-created');
        session()->flash('message', 'Notificación creada correctamente.');
    }
}
