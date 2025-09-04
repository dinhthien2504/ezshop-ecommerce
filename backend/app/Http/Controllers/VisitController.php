<?php

namespace App\Http\Controllers;

use App\Services\VisitService;

class VisitController extends Controller
{
    protected $visitService;

    public function __construct(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }
}
