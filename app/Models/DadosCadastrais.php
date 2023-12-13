<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as Auditing;
use OwenIt\Auditing\Auditable;

class DadosCadastrais extends Model implements Auditing
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'dados_cadastrais';

    protected $guarded = ['cod_smart_unidade', 'cod_smart_cliente'];

    public $incrementing = false;

    protected $fillable = [
        'cliente',
        'unidade',
        'codigo_scde',
        'demanda_p',
        'demanda_fp',
        'status_empresa',
        'status_unidade',
        'data_de_migracao',
        'cod_smart_cliente',
        'cod_smart_unidade',
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
