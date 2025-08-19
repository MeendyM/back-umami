<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\Notification;
use App\Models\User;
use App\Models\Institution;
use App\Enums\NotificationType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Masmerise\Toaster\Toaster;

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
        $this->validate(
            [
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'required',
                'target' => 'required',
            ],
            [
                'title.required' => 'El título es obligatorio.',
                'title.string' => 'El título debe ser una cadena de texto.',
                'title.max' => 'El título no puede tener más de 255 caracteres.',
                'message.required' => 'El mensaje es obligatorio.',
                'message.string' => 'El mensaje debe ser una cadena de texto.',
                'type.required' => 'El tipo es obligatorio.',
                'target.required' => 'El objetivo es obligatorio.',
            ]
        );

        try {
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
            Toaster::success('Notificación enviada correctamente.');
        } catch (\Throwable $th) {
            Log::info($th);
            Toaster::error('Error al enviar la notificación.');
        }
    }
}
