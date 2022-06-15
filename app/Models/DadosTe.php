<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as Auditing;
use OwenIt\Auditing\Auditable;


class DadosTe extends Model implements Auditing
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = "dados_te";

    protected $guarded = ['cod_te', 'cod_smart_unidade'];

    protected $fillable = [
        'mes',
        'operacao',
        'tipo',
        'montante_nf',
        'preco_nf',
        'nf_c_icms',
        'perfil_contr',
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
