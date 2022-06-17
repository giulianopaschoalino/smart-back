<?php

declare(strict_types=1);

namespace App\Repositories\Pld;

use App\Models\Pld;
use App\Repositories\AbstractRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PldRepository extends AbstractRepository implements PldContractInterface
{
    public function __construct(Pld $pld)
    {
        parent::__construct($pld);
    }

    /**
     * @throws BindingResolutionException
     */
    private function execute($fields, $params = []): Builder
    {
        $query = $this->model->select($fields);

        if (!empty($params)) {
            $query = static::getFilterBuilder($params)->applyFilter($query);
        }
        return $query;
    }

    /**
     * @throws BindingResolutionException
     */
    public function getOverviewByRegion(): Collection|array
    {
        $fields = [
            'pld.submercado as submarket',
            'pld.mes_ref as year_month',
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month_formatted"),
            DB::raw("SUM(pld.valor) as value")
        ];

        // Carbon::now()->format('m/Y')
        return $this->execute($fields)
            ->where( DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY')"), '=', '04/2022')
            ->groupBy(['submarket', 'year_month', 'year_month_formatted'])
            ->get();
    }

    public function getListConsumption($params): Collection|array
    {
        $fields = [
            'pld.mes_ref as year_month',
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month_formatted"),
            DB::raw('pld_norte.value as norte'),
            DB::raw('pld_sul.value as sul'),
            DB::raw('pld_nordeste.value as nordeste'),
            DB::raw('pld_sudeste.value as sudeste'),
        ];

        $sql = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('SUM(valor) as value')
            ])
            ->where('pld.submercado', '=', 'NORTE')
            ->groupBy('submarket', 'year_month');

        $sql2 = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('SUM(valor) as value')
            ])
            ->where('pld.submercado', '=', 'SUL')
            ->groupBy('submarket', 'year_month');

        $sql3 = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('SUM(valor) as value')
            ])
            ->where('pld.submercado', '=', 'NORDESTE')
            ->groupBy('submarket', 'year_month');

        $sql4 = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('SUM(valor) as value')
            ])
            ->where('pld.submercado', '=', 'SUDESTE')
            ->groupBy('submarket', 'year_month');


        return $this->model->select($fields)->joinSub($sql, 'pld_norte', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_norte.year_month');
        })->joinSub($sql2, 'pld_sul', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_sul.year_month');
        })->joinSub($sql3, 'pld_nordeste', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_nordeste.year_month');
        })->joinSub($sql4, 'pld_sudeste', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_sudeste.year_month');
        })->distinct()->get();
    }

    /**
     * @throws BindingResolutionException
     */
    public function getConsumptionByDaily($params, $field = "mes_ref"): Collection|array
    {
        $fields = static::getRowField();

        $i = 0;
        foreach ($params['filters'] as $param) {
            if ($param['field'] === $field) {
                $params['filters'][$i]['field'] = "TO_CHAR(TO_DATE(pld.{$param['field']}, 'YYMM'), 'MM/YYYY')";
            }
            $i++;
        }

        return $this->execute($fields, $params)->get();

    }

    public function getConsumptionBySchedule($params, $field = "dia_num"): Collection|array
    {
        $fields = static::getRowField();

        $i = 0;
        foreach ($params['filters'] as $param) {
            if ($param['field'] === $field) {
                $params['filters'][$i]['field'] = "(date('1899-12-30') + interval '1' day * pld.{$param['field']})";
            }
            $i++;
        }

        return $this->execute($fields, $params)->get();
    }

    protected static function getRowField(): array
    {
        return [
            DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * pld.dia_num), 'DD') as day_formatted"),
            'hora as hour',
            DB::raw("(date('1899-12-30') + interval '1' day * pld.dia_num) as day_calc"),
            'pld.submercado as submarket',
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month"),
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month_formatted"),
        ];
    }

}