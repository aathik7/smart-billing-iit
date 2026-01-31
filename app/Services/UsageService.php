<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\UsageCounter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UsageService
{
    public function consume(int $userId)
    {
        return DB::transaction(function () use ($userId) {

            // 1. Get active subscription
            $subscription = Subscription::with('plan')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->first();

            if (!$subscription) {
                throw new HttpException(403, 'No active subscription');
            }

            // 2. Get current billing cycle
            $today = Carbon::today();

            $usageCounter = UsageCounter::where('subscription_id', $subscription->id)
                ->whereDate('billing_cycle_start', '<=', $today)
                ->whereDate('billing_cycle_end', '>', $today)
                ->first();

            // 3. If billing cycle expired, create new one
            if (!$usageCounter) {
                $usageCounter = UsageCounter::create([
                    'user_id' => $userId,
                    'subscription_id' => $subscription->id,
                    'billing_cycle_start' => $today,
                    'billing_cycle_end' => $today->copy()->addMonth(),
                    'used_units' => 0,
                ]);
            }

            // 4. Check usage limit (if not unlimited)
            $monthlyLimit = $subscription->plan->monthly_limit;

            if (!is_null($monthlyLimit)) {
                if ($usageCounter->used_units >= $monthlyLimit) {
                    throw new HttpException(429, 'Usage limit exceeded');
                }
            }

            // 5. Consume usage
            $usageCounter->increment('used_units');

            return [
                'used_units' => $usageCounter->used_units,
                'monthly_limit' => $monthlyLimit,
                'remaining_units' => is_null($monthlyLimit)
                    ? null
                    : max($monthlyLimit - $usageCounter->used_units, 0),
            ];
        });
    }

    public function stats(int $userId)
    {
        $subscription = Subscription::with('plan')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            throw new HttpException(404, 'No active subscription');
        }

        $usageCounter = UsageCounter::where('subscription_id', $subscription->id)
            ->latest('billing_cycle_start')
            ->first();

        return [
            'plan' => $subscription->plan->name,
            'billing_cycle' => [
                'start' => $usageCounter->billing_cycle_start->toDateString(),
                'end' => $usageCounter->billing_cycle_end->toDateString(),
            ],
            'used_units' => $usageCounter->used_units,
            'monthly_limit' => $subscription->plan->monthly_limit,
            'remaining_units' => is_null($subscription->plan->monthly_limit)
                ? null
                : max(
                    $subscription->plan->monthly_limit - $usageCounter->used_units,
                    0
                ),
        ];
    }
}
