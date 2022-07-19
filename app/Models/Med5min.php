<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
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

    protected static function booted()
    {
        static::addGlobalScope('dados_cadastrais', function (Builder $builder){
           $builder->join(
               "dados_cadastrais",
               "dados_cadastrais.codigo_scde",
               "=",
               "med_5min.ponto"
           );
        });
    }
}
