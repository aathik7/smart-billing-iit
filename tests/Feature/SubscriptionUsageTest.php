<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Services\SubscriptionService;
use App\Services\UsageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SubscriptionUsageTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $subscriptionService;
    protected UsageService $usageService;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations
        $this->artisan('migrate');

        // Seed plans
        Plan::insert([
            ['name' => 'Free', 'price' => 0, 'monthly_limit' => 2],
            ['name' => 'Pro', 'price' => 999, 'monthly_limit' => 10],
            ['name' => 'Enterprise', 'price' => 4999, 'monthly_limit' => null],
        ]);

        $this->subscriptionService = app(SubscriptionService::class);
        $this->usageService = app(UsageService::class);
    }

    /** @test */
    public function user_cannot_consume_usage_without_subscription()
    {
        $user = User::factory()->create();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('No active subscription');

        $this->usageService->consume($user->id);
    }

    /** @test */
    public function user_can_subscribe_to_a_plan()
    {
        $user = User::factory()->create();
        $plan = Plan::where('name', 'Free')->first();

        $result = $this->subscriptionService->subscribe($user->id, $plan->id);

        $this->assertEquals('active', $result['subscription']->status);
        $this->assertEquals($plan->id, $result['subscription']->plan_id);
    }

    /** @test */
    public function free_plan_blocks_usage_after_limit()
    {
        $user = User::factory()->create();
        $plan = Plan::where('name', 'Free')->first();

        $this->subscriptionService->subscribe($user->id, $plan->id);

        // Limit = 2
        $this->usageService->consume($user->id);
        $this->usageService->consume($user->id);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Usage limit exceeded');

        $this->usageService->consume($user->id);
    }

    /** @test */
    public function enterprise_plan_never_blocks_usage()
    {
        $user = User::factory()->create();
        $plan = Plan::where('name', 'Enterprise')->first();

        $this->subscriptionService->subscribe($user->id, $plan->id);

        for ($i = 0; $i < 100; $i++) {
            $this->usageService->consume($user->id);
        }

        $this->assertTrue(true); // no exception thrown
    }
}
