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
        // Cek apakah device dengan karakteristik yang sama sudah ada
        $deviceInfo = $this->getDeviceInfo();
        $existingDevice = $this->findExistingDevice($user, $deviceInfo);

        if ($existingDevice) {
            // Jika device sudah ada, aktifkan kembali dan update last_active
            $existingDevice->update([
                'is_active' => true,
                'last_active' => now()
            ]);
            session(['device_id' => $existingDevice->device_id]);
            return $existingDevice;
        }

        // Jika device baru
        $deviceId = $this->generateDeviceId();

        // Nonaktifkan device lama jika melebihi batas
        $this->deactivateExcessDevices($user);

        $device = UserDevice::create([
            'user_id' => $user->id,
            'device_name' => $deviceInfo['device_name'],
            'device_id' => $deviceId,
            'device_type' => $deviceInfo['device_type'],
            'platform' => $deviceInfo['platform'],
            'platform_version' => $deviceInfo['platform_version'],
            'browser' => $deviceInfo['browser'],
            'browser_version' => $deviceInfo['browser_version'],
            'last_active' => now(),
            'is_active' => true
        ]);

        session(['device_id' => $deviceId]);
        return $device;
    }

    private function findExistingDevice(User $user, array $deviceInfo)
    {
        return UserDevice::where('user_id', $user->id)
            ->where('device_type', $deviceInfo['device_type'])
            ->where('platform', $deviceInfo['platform'])
            ->where('browser', $deviceInfo['browser'])
            ->first();
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
            'platform' => $platform,
            'platform_version' => $this->agent->version($platform),
            'browser' => $browser,
            'browser_version' => $this->agent->version($browser)
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

    public function canDeviceLogin(User $user, $request)
    {
        $currentPlan = $user->getCurrentPlan();

        if (!$currentPlan) {
            return false;
        }

        // Jika sudah ada device_id di session, berarti device ini sudah teregistrasi
        if (session('device_id')) {
            $device = UserDevice::where('user_id', $user->id)
                ->where('device_id', session('device_id'))
                ->where('is_active', true)
                ->first();

            if ($device) {
                return true;
            }
        }

        // Cek device dengan karakteristik yang sama
        $deviceInfo = $this->getDeviceInfo();
        $existingDevice = $this->findExistingDevice($user, $deviceInfo);

        if ($existingDevice) {
            return true;
        }

        // Jika device baru, cek jumlah device aktif
        $maxDevices = $currentPlan->max_devices;
        $activeDevices = $user->devices()->where('is_active', true)->count();

        return $activeDevices < $maxDevices;
    }
}