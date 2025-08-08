<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\TypeUser;
use App\Enums\VerificationStatus;
use App\Notifications\CustomVerifyEmail;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * Override the default password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }
    use HasApiTokens;

    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * Override the default email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'email',
        'password',
        'name',
        'email_verified_at',
        'current_team_id',
        'profile_photo_path',
        'type',
        'id_institution',
        'first_steps_completed',
        'google_data',
        'google_token_expires_at',
        'verification_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'first_steps_completed' => 'boolean',
            'google_data' => 'array',
            'google_token_expires_at' => 'datetime',
            'verification_status' => VerificationStatus::class,
        ];
    }


    public function institution()
    {
        return $this->belongsTo(Institution::class, 'id_institution');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'id_user');
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'user_notifications', 'user_id', 'notification_id', 'id_user', 'id_notification')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    public function unreadNotifications()
    {
        return $this->notifications()->wherePivot('read_at', null);
    }

    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id', 'id_user');
    }
    protected static function booted() {}
}
