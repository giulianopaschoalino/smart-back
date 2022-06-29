<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Auditing;

class Notifications extends Model implements Auditing
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'notificacoes';

    protected $guarded = ['id'];

    protected $fillable = [
        'title',
        'body',
        'user_id'
    ];

    protected $hidden = [
        'updated_at',
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
