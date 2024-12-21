<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class DeviceService
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    public function registerDevice(User $user, $request)
    {
        $deviceId = $this->generateDeviceId();
        $deviceInfo = $this->getDeviceInfo();

        $this->deactivateExcessDevices($user);

        return UserDevice::create([
            'user_id' => $user->id,
            'device_name' => $deviceInfo['device_name'],
            'device_id' => $deviceId,
            'device_type' => $deviceInfo['device_type'],
            'last_active' => now(),
            'is_active' => true
        ]);
    }

    private function getDeviceInfo()
    {
        $deviceType = 'unknown';
        if ($this->agent->isDesktop()) {
            $deviceType = 'desktop';
        } elseif ($this->agent->isPhone()) {
            $deviceType = 'phone';
        } elseif ($this->agent->isTablet()) {
            $deviceType = 'tablet';
        }

        $platform = $this->agent->platform();
        $browser = $this->agent->browser();

        return [
            'device_name' => $this->generateDeviceName($deviceType, $platform, $browser),
            'device_type' => $deviceType,
        ];
    }

    private function generateDeviceName($deviceType, $platform, $browser)
    {
        return sprintf(
            '%s - %s (%s)',
            ucfirst($deviceType),
            $platform,
            $browser
        );
    }

    public function deactivateExcessDevices(User $user)
    {
        $maxDevices = $user->getCurrentPlan()->max_devices;
        $activeDevices = $user->devices()->where('is_active', true)
            ->orderBy('last_active', 'desc')
            ->get();

        if ($activeDevices->count() >= $maxDevices) {
            // Nonaktifkan device paling lama tidak aktif
            $devicesToDeactivate = $activeDevices->slice($maxDevices - 1);
            foreach ($devicesToDeactivate as $device) {
                $device->update(['is_active' => false]);
            }
        }
    }

    private function generateDeviceId()
    {
        return Str::random(32);
    }

    public function updateLastActive(User $user, $deviceId)
    {
        return UserDevice::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->update(['last_active' => now()]);
    }

    public function canDeviceLogin(User $user)
    {
        $maxDevices = $user->getCurrentPlan()->max_devices;
        $activeDevices = $user->devices()->where('is_active', true)->count();

        return $activeDevices < $maxDevices;
    }
}