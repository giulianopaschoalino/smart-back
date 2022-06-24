<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as Auditing;
use OwenIt\Auditing\Auditable;


class Economy extends Model implements Auditing
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'economia';

    protected $guarded = ['cod_te', 'cod_smart_unidade'];

    public $incrementing = false;

    protected $fillable = [
        'mes',
        'custo_cativo',
        'custo_livre',
        'economia_mensal',
        'economia_acumulada',
        'custo_unit',
        'dad_estimado',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d/m/Y H:i:s');
    }

    protected static function booted()
    {
        static::addGlobalScope(new UserScope());
    }

}
