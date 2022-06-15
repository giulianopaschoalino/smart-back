<?php

declare(strict_types=1);

namespace App\Repositories\DadosTe;

use App\Models\DadosTe;
use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;


class DadosTeRepository extends AbstractRepository implements DadosTeContractInterface
{

    public function __construct(DadosTe $dadosTe)
    {
        parent::__construct($dadosTe);
    }

    private function execute($params): Builder
    {
        $query = $this->model
            ->select(
                'dados_te.mes',
                'dados_te.cod_smart_unidade',
                'dados_te.operacao',
                'dados_te.tipo',
                'dados_te.perfil_contr as contraparte',
                'dados_te.montante_nf',
                'dados_te.preco_nf',
                'dados_te.nf_c_icms'
            )
            ->join(
                "dados_cadastrais",
                "dados_cadastrais.cod_smart_unidade",
                "=",
                "dados_te.cod_smart_unidade"
            );

        if (!empty($params)) {
            $query = static::getFilterBuilder($params)->applyFilter($query);
        }

        return $query;
    }

    public function getOperationSummary($params): Collection|array
    {
        $params = static::filterRow($params);
        return $this->execute($params)->get();
    }

    public static function filterRow($params, $field = 'mes'): array
    {
        $arr['filters'] = collect($params['filters'])
            ->map(function ($value, $key) use ($field) {
                if ($value['field'] === $field) {
                    Arr::set( $value, "field", "TO_CHAR(TO_DATE(dados_te.{$value['field']}, 'YYMM'), 'MM/YYYY')");
                    $value['row'] = true;
                }
                return $value;
            })->all();
        return $arr;
    }
}
