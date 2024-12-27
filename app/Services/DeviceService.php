<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Carbon;

/**
 * Service class untuk mengelola device yang digunakan user
 * Mencakup registrasi device baru, aktivasi/deaktivasi, dan pengecekan device limit
 */
class DeviceService
{
    /**
     * Konstanta untuk tipe-tipe device yang didukung
     * Digunakan untuk standardisasi nilai device_type di database
     */
    private const DEVICE_TYPES = [
        'desktop' => 'desktop',
        'phone' => 'phone',
        'tablet' => 'tablet',
        'unknown' => 'unknown'
    ];

    /**
     * Flag untuk menandai apakah sedang dalam proses registrasi pembayaran
     * Jika true, beberapa validasi device limit akan dilewati
     */
    private bool $isPaymentRegistration = false;

    /**
     * Jumlah maksimal device yang diizinkan saat registrasi pembayaran
     * Nilai ini akan override max_devices dari plan user saat isPaymentRegistration = true
     */
    private ?int $paymentPlanMaxDevices = null;

    /**
     * Instance dari Jenssegers\Agent untuk deteksi informasi browser dan device
     */
    private Agent $agent;

    /**
     * Constructor service dengan dependency injection Agent
     */
    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Mengatur flag payment registration dan maksimal device yang diizinkan
     *
     * @param bool $status Status payment registration
     * @param int|null $maxDevices Jumlah maksimal device yang diizinkan
     */
    public function setPaymentRegistration(bool $status, ?int $maxDevices = null): void
    {
        $this->isPaymentRegistration = $status;
        $this->paymentPlanMaxDevices = $maxDevices;
    }

    /**
     * Mendaftarkan device baru atau mengaktifkan device yang sudah ada
     *
     * @param User $user User yang mendaftarkan device
     * @return UserDevice Device yang berhasil didaftarkan/diaktifkan
     */
    public function registerDevice(User $user): UserDevice
    {
        $deviceInfo = $this->getDeviceInfo();
        $existingDevice = $this->findExistingDevice($user, $deviceInfo);

        if ($existingDevice) {
            return $this->activateExistingDevice($existingDevice);
        }

        if (!$this->isPaymentRegistration) {
            $this->deactivateExcessDevices($user);
        }

        return $this->createNewDevice($user, $deviceInfo);
    }

    /**
     * Memperbarui timestamp aktivitas terakhir dari device
     *
     * @param User $user Pemilik device
     * @param string $deviceId ID unik device
     * @return bool Status keberhasilan update
     */
    public function updateLastActive(User $user, string $deviceId): bool
    {
        return UserDevice::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->update(['last_active' => Carbon::now()]);
    }

    /**
     * Memeriksa apakah device diizinkan untuk login
     * Mempertimbangkan device limit dan status payment registration
     *
     * @param User $user User yang akan login
     * @return bool Status izin login
     */
    public function canDeviceLogin(User $user): bool
    {
        if ($this->isPaymentRegistration) {
            return true;
        }

        if ($this->isExistingDeviceActive($user)) {
            return true;
        }

        $deviceInfo = $this->getDeviceInfo();
        if ($this->findExistingDevice($user, $deviceInfo)) {
            return true;
        }

        return $this->hasAvailableDeviceSlot($user);
    }

    /**
     * Mendapatkan jumlah maksimal device yang diizinkan untuk user
     *
     * @param User $user User yang dicek
     * @return int Jumlah maksimal device
     */
    private function getMaxDevices(User $user): int
    {
        if ($this->isPaymentRegistration && !is_null($this->paymentPlanMaxDevices)) {
            return $this->paymentPlanMaxDevices;
        }

        $currentPlan = $user->getCurrentPlan();
        return $currentPlan ? $currentPlan->max_devices : 0;
    }

    /**
     * Mengumpulkan informasi device dari Agent
     *
     * @return array Informasi device (type, platform, browser, dll)
     */
    private function getDeviceInfo(): array
    {
        $deviceType = $this->determineDeviceType();
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

    /**
     * Menentukan tipe device berdasarkan deteksi Agent
     *
     * @return string Tipe device (desktop/phone/tablet/unknown)
     */
    private function determineDeviceType(): string
    {
        if ($this->agent->isDesktop()) {
            return self::DEVICE_TYPES['desktop'];
        }

        if ($this->agent->isPhone()) {
            return self::DEVICE_TYPES['phone'];
        }

        if ($this->agent->isTablet()) {
            return self::DEVICE_TYPES['tablet'];
        }

        return self::DEVICE_TYPES['unknown'];
    }

    /**
     * Membuat nama device yang readable berdasarkan informasi device
     *
     * @param string $deviceType Tipe device
     * @param string $platform Platform/OS
     * @param string $browser Browser yang digunakan
     * @return string Nama device yang readable
     */
    private function generateDeviceName(string $deviceType, string $platform, string $browser): string
    {
        return sprintf('%s - %s (%s)', ucfirst($deviceType), $platform, $browser);
    }

    /**
     * Mencari device yang sudah ada berdasarkan karakteristik device
     *
     * @param User $user Pemilik device
     * @param array $deviceInfo Informasi device
     * @return UserDevice|null Device yang ditemukan atau null
     */
    private function findExistingDevice(User $user, array $deviceInfo): ?UserDevice
    {
        return UserDevice::where('user_id', $user->id)
            ->where('device_type', $deviceInfo['device_type'])
            ->where('platform', $deviceInfo['platform'])
            ->where('browser', $deviceInfo['browser'])
            ->first();
    }

    /**
     * Mengaktifkan device yang sudah ada dan memperbarui last_active
     *
     * @param UserDevice $device Device yang akan diaktifkan
     * @return UserDevice Device yang sudah diaktifkan
     */
    private function activateExistingDevice(UserDevice $device): UserDevice
    {
        $device->update([
            'is_active' => true,
            'last_active' => Carbon::now()
        ]);

        session(['device_id' => $device->device_id]);
        return $device;
    }

    /**
     * Membuat record device baru di database
     *
     * @param User $user Pemilik device
     * @param array $deviceInfo Informasi device
     * @return UserDevice Device yang baru dibuat
     */
    private function createNewDevice(User $user, array $deviceInfo): UserDevice
    {
        $deviceId = Str::random(32);

        $device = UserDevice::create([
            'user_id' => $user->id,
            'device_name' => $deviceInfo['device_name'],
            'device_id' => $deviceId,
            'device_type' => $deviceInfo['device_type'],
            'platform' => $deviceInfo['platform'],
            'platform_version' => $deviceInfo['platform_version'],
            'browser' => $deviceInfo['browser'],
            'browser_version' => $deviceInfo['browser_version'],
            'last_active' => Carbon::now(),
            'is_active' => true
        ]);

        session(['device_id' => $deviceId]);
        return $device;
    }

    /**
     * Menonaktifkan device yang melebihi batas maksimal
     * Device yang paling lama tidak aktif akan dinonaktifkan
     *
     * @param User $user Pemilik device
     */
    private function deactivateExcessDevices(User $user): void
    {
        $maxDevices = $user->getCurrentPlan()->max_devices;
        $activeDevices = $user->devices()
            ->where('is_active', true)
            ->orderBy('last_active', 'desc')
            ->get();

        if ($activeDevices->count() >= $maxDevices) {
            $activeDevices->slice($maxDevices - 1)
                ->each(fn($device) => $device->update(['is_active' => false]));
        }
    }

    /**
     * Memeriksa apakah device yang tersimpan di session masih aktif
     *
     * @param User $user Pemilik device
     * @return bool Status keaktifan device
     */
    private function isExistingDeviceActive(User $user): bool
    {
        if (!session('device_id')) {
            return false;
        }

        return UserDevice::where('user_id', $user->id)
            ->where('device_id', session('device_id'))
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Memeriksa apakah masih ada slot device yang tersedia
     *
     * @param User $user User yang dicek
     * @return bool Status ketersediaan slot
     */
    private function hasAvailableDeviceSlot(User $user): bool
    {
        $maxDevices = $this->getMaxDevices($user);
        $activeDevices = $user->devices()->where('is_active', true)->count();

        return $activeDevices < $maxDevices;
    }
}
