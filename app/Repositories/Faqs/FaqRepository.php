<?php

declare(strict_types=1);

namespace App\Repositories\Faqs;

use App\Models\Faq;
use App\Repositories\AbstractRepository;

class FaqRepository extends AbstractRepository implements FaqContractInterface
{

    public function __construct(Faq $faq)
    {
        parent::__construct($faq);
    }

}