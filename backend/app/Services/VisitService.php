<?php

namespace App\Services;

use App\Repositories\VisitEloquentRepository;

class VisitService
{
    protected $visitRepository;

    public function __construct(VisitEloquentRepository $visitRepository)
    {
        $this->visitRepository = $visitRepository;
    }
}