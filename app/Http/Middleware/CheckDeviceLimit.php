<?php

namespace App\Http\Middleware;

use App\Services\DeviceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDeviceLimit
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Cek apakah bisa login di device ini
        if (!$this->deviceService->canDeviceLogin($user, $request)) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['device' => 'Anda telah mencapai batas maksimum device. Silakan logout dari device lain.']);
        }

        // Jika belum ada device_id di session, register device baru
        if (!session('device_id')) {
            $this->deviceService->registerDevice($user, $request);
        }

        return $next($request);
    }
}