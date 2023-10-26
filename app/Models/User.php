<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use OwenIt\Auditing\Auditable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable as Auditing;

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
        'client_id'
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

    public function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Hash::make($value)
        );
    }

    protected function serializeDate(\DateTimeInterface $date): string
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
