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
        'user_id',
        'title',
        'message',
        'read',
        'type',
        'data',
    ];


    protected $casts = [
        'read' => 'boolean',
        'type' => \App\Enums\NotificationType::class,
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
