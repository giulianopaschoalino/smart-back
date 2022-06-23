<?php

declare(strict_types=1);

namespace App\Repositories\Med5min;

use App\Models\Med5min;
use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;


class Med5minRepository extends AbstractRepository implements Med5minContractInterface
{

    public function __construct(Med5min $med5min)
    {
        parent::__construct($med5min);
    }

    private function execute($fields, $params): Builder
    {
        $query = $this->model->select($fields);

        if (!empty($params)) {
            $query = static::getFilterBuilder($params)->applyFilter($query);
        }

        return $query;
    }

}
