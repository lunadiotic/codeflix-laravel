<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{
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

    public function subscribe(Request $request, Plan $plan)
    {
        $user = Auth::user();
        $user->memberships()->create([
            'plan_id' => $request->plan_id,
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