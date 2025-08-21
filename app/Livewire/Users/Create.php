<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    public $modal = false;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    public function render()
    {
        return view('livewire.users.create');
    }

    public function openModal()
    {
        $this->modal = true;
        $this->clean();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->modal = false;
    }

    public function clean()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function save()
    {
        $this->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8',
            ],
            [
                'name.required' => 'Nombre completo es requerido',
                'email.required' => 'Correo electrónico es requerido',
                'email.email' => 'Correo electrónico debe ser una dirección de correo electrónico válida',
                'email.unique' => 'Correo electrónico ya está en uso',
                'password.required' => 'Contraseña es requerida',
                'password.confirmed' => 'Confirmación de contraseña no coincide',
            ]
        );
        try {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'type' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make($this->password),
            ]);


            $this->closeModal();
            $this->clean();
            $this->dispatch('update-user');
            Toaster::success('Usuario administrador creado exitosamente.');
        } catch (\Throwable $th) {
            Log::info('Error al crear el usuario administrador: ' . $th->getMessage());
            Toaster::error('Error al crear el usuario administrador. Por favor, inténtalo de nuevo.');
        }
    }
}
