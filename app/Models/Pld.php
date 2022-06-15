<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as Auditing;
use OwenIt\Auditing\Auditable;


class Pld extends Model implements Auditing
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = "pld";

    protected $guarded = ['id'];

    protected $fillable = [
        'dia_num',
        'hora',
        'submercado',
        'valor',
        'mes_ref',
        'dia_da_semana',
        'created_at',
        'updated_at',
        'deleted_at',
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


}
