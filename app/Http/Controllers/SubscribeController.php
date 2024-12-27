<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth'
        ];
    }

    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function showPlans()
    {
        $plans = Plan::all();
        return view('subscription.plans', compact('plans'));
    }

    public function checkoutPlan(Plan $plan)
    {
        $user = Auth::user();
        return view('subscription.checkout', compact('plan', 'user'));
    }

    public function subscribe(Request $request)
    {
        $user = Auth::user();
        $plan = Plan::find($request->plan_id);
        $user->memberships()->create([
            'plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration),
            'active' => true,
        ]);
        // implement device handler service here
        $this->deviceService->registerDevice($user, $request);
        return redirect()->route('subscription.success');
    }

    public function subscribeSuccess()
    {
        return view('subscription.success');
    }
}
