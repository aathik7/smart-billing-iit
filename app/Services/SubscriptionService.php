<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\UsageCounter;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * @param int $userId
     * @param int $planId
     * @return array
     */
    public function subscribe(int $userId, int $planId)
    {
        Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->update([
                'status' => 'expired',
                'end_date' => Carbon::today(),
            ]);

        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addMonth();

        $subscription = Subscription::create([
            'user_id' => $userId,
            'plan_id' => $planId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
        ]);

        UsageCounter::create([
            'user_id' => $userId,
            'subscription_id' => $subscription->id,
            'billing_cycle_start' => $startDate,
            'billing_cycle_end' => $endDate,
            'used_units' => 0,
        ]);

        $plan = $subscription->plan()->first();

        return ['subscription' => $subscription, 'plan' => $plan];
    }

    /**
     * @param int $userId
     * @return \App\Models\Subscription|null
     */
    public function getCurrentSubscription(int $userId)
    {
        return Subscription::with('plan')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();
    }
}
