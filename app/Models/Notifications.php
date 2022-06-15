<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'notificacoes';

    protected $guarded = ['id'];

    protected $fillable = [
        'title',
        'body',
        'user_id'
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d/m/Y H:i:s');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notificacoes_users', 'notification_id', 'user_id',);
    }

}
