<?php

namespace App\Services;

use App\Repositories\RateEloquentRepository;

class RateService
{
    protected $rateRepository;

    public function __construct(RateEloquentRepository $rateRepository)
    {
        $this->rateRepository = $rateRepository;
    }

    public function storeFeedbacks($request)
    {
        $feedbacks = $request->input('feedbacks'); 

        $userId = auth()->id();
        
        return $this->rateRepository->bulkInsertRates($feedbacks, $userId);
    }
}