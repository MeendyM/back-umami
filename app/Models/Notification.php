<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_notification';

    protected $fillable = [
        'title',
        'message',
        'target_type',
        'target_institution_id',
        'sender_id',
        'sent_at',
        'is_sent',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    // Relación con el usuario que envía (admin)
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'id_user');
    }

    // Relación con la institución objetivo
    public function targetInstitution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'target_institution_id', 'id_institution');
    }

    // Relación many-to-many con usuarios que han recibido la notificación
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_notifications', 'notification_id', 'user_id', 'id_notification', 'id_user')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    // Obtener usuarios objetivo según los filtros
    public function getTargetUsers()
    {
        $query = User::query();

        // Filtrar por tipo de usuario
        if ($this->target_type !== 'all') {
            $query->where('type', $this->target_type);
        } else {
            // Si es 'all', incluir solo client y student (no admin)
            $query->whereIn('type', ['client', 'student']);
        }

        // Filtrar por institución si está especificada
        if ($this->target_institution_id) {
            $query->where('id_institution', $this->target_institution_id);
        }

        return $query->get();
    }

    // Enviar notificación a usuarios objetivo
    public function sendToTargetUsers()
    {
        if ($this->is_sent) {
            return false; // Ya fue enviada
        }

        $targetUsers = $this->getTargetUsers();
        
        if ($targetUsers->isEmpty()) {
            return 0;
        }
        
        // Crear registros en la tabla pivot
        $userNotifications = $targetUsers->map(function ($user) {
            return [
                'user_id' => $user->id_user,
                'notification_id' => $this->id_notification,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Insertar en lote para mejor rendimiento
        DB::table('user_notifications')->insert($userNotifications->toArray());

        // Marcar como enviada
        $this->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        return $targetUsers->count();
    }

    // Scope para notificaciones enviadas
    public function scopeSent($query)
    {
        return $query->where('is_sent', true);
    }

    // Scope para notificaciones pendientes
    public function scopePending($query)
    {
        return $query->where('is_sent', false);
    }
}
