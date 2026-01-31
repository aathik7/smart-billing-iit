<?php

namespace App\Services;

use App\Models\Plan;

class PlanService
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPlans()
    {
        return Plan::select('id', 'name', 'price', 'monthly_limit')
            ->orderBy('price')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => (float) $plan->price,
                    'monthly_limit' => $plan->monthly_limit,
                    'is_unlimited' => is_null($plan->monthly_limit),
                ];
            });
    }
}
