<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as Auditing;
use OwenIt\Auditing\Auditable;


class Med5min extends Model implements Auditing
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = "med_5min";

    protected $guarded = ['id'];

    protected $fillable = [
        'origem',
        'dia_num',
        'minuto',
        'ativa_consumo',
        'ativa_geracao',
        'reativa_consumo',
        'reativa_geracao',
        'ponto',
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
}
