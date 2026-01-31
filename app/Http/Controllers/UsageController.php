<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsumeUsageRequest;
use App\Services\UsageService;

class UsageController extends Controller
{
    public function __construct(private UsageService $usageService) {}

    public function consume(ConsumeUsageRequest $request)
    {
        $result = $this->usageService->consume(auth()->id());

        return response()->json([
            'message' => 'Usage consumed successfully',
            'data' => $result,
        ]);
    }

    public function stats()
    {
        $stats = $this->usageService->stats(auth()->id());

        return response()->json([
            'data' => $stats,
        ]);
    }
}
