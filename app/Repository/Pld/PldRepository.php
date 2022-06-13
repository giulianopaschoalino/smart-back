<?php

namespace App\Repository\Pld;

use App\Models\Pld;
use App\Repository\Repository;

class PldRepository extends Repository
{

    protected function getModel(): string
    {
        return Pld::class;
    }

    protected function model()
    {
        // TODO: Implement model() method.
    }
}
