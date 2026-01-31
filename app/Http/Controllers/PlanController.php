<?php

namespace App\Http\Controllers;

use App\Services\PlanService;

class PlanController extends Controller
{
    /**
     * @var PlanService
     */
    private PlanService $planService;

    /**
     * PlanController constructor.
     * @param PlanService $planService
     */
    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(['data' => $this->planService->getAllPlans()]);
    }
}
