<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable as Auditing;
use OwenIt\Auditing\Auditable;

class User extends Authenticatable implements Auditing
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Auditable;

    protected $table = "users";

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d/m/Y H:i:s');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(Notifications::class, 'notificacoes_users',  'notification_id', 'user_id');
    }
}
