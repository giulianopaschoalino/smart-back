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
    private function execute($fields, $params): Builder
    {
        $query = $this->model->select($fields);

        $query = static::getFilterBuilder($params)->applyFilter($query);

        if (!empty($params)) {
            if ($params['field'] === 'mes_ref' && $params['value']) {
                $query = $query->where(
                    DB::raw("TO_CHAR(TO_DATE(pld.{$params['field']}, 'YYMM'), 'MM/YYYY')"),
                    $params['type'],
                    $params['value']);
            }
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

        $params = ["type" => "=", "field" => 'mes_ref', "value" => Carbon::now()->format('m/Y')];

        return $this->execute($fields, $params)
            ->groupBy(['submarket', 'year_month', 'year_month_formatted'])
            ->get();
    }

    public function getListConsumption($params)
    {
        // TODO: Implement getListConsumption() method.
    }

    /**
     * @throws BindingResolutionException
     */
    public function getConsumptionByDaily($params): Collection|array
    {
        $fields = static::getRowField();

        return $this->execute($fields, $params)->get();

    }

    public function getConsumptionBySchedule($params)
    {
        $fields = static::getRowField();

        return $this->execute($fields, $params)->toSql();
    }

    protected static function getRowField(): array
    {
        return [
            DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * pld.dia_num), 'DD') as day_formatted"),
            DB::raw("(date('1899-12-30') + interval '1' day * pld.dia_num) as day_calc"),
            'pld.submercado as submarket',
            'pld.mes_ref as year_month',
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month_formatted"),
        ];
    }

}