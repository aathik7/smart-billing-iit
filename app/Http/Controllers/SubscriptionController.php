<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * @var SubscriptionService
     */
    private SubscriptionService $subscriptionService;

    /**
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @param SubscribeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(SubscribeRequest $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $response = $this->subscriptionService->subscribe(
            auth()->id(),
            $request->plan_id
        );

        return response()->json([
            'message' => 'Subscription activated successfully',
            'data' => [
                'plan' => $response['plan']['name'],
                'start_date' => $response['subscription']['start_date']->toDateString(),
                'end_date' => $response['subscription']['end_date']->toDateString(),
                'status' => $response['subscription']['status'],
            ],
        ], 201);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function current()
    {
        $subscription = $this->subscriptionService->getCurrentSubscription(auth()->id());
        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription found',
            ], 404);
        }

        return response()->json([
            'data' => [
                'plan' => $subscription->plan->name,
                'price' => (float) $subscription->plan->price,
                'monthly_limit' => $subscription->plan->monthly_limit,
                'start_date' => $subscription->start_date->toDateString(),
                'end_date' => $subscription->end_date->toDateString(),
                'status' => $subscription->status,
            ],
        ]);
    }
}
