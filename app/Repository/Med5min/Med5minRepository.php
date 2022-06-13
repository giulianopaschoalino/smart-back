<?php

namespace App\Repository\Med5min;

use App\Models\Med5min;
use App\Repository\Repository;

class Med5minRepository extends Repository
{

    protected function getModel(): string
    {
        return Med5min::class;
    }

    protected function model()
    {
        // TODO: Implement model() method.
    }
}
