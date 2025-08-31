<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FingerprintDevice;

class FingerprintDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devices = [
            [
                'name' => 'Device Main Office',
                'device_id' => 'DEV001',
                'location' => 'Main Office - Lobby',
                'ip_address' => '192.168.1.100',
                'port' => 4370,
                'status' => 'active',
                'last_sync' => now(),
            ],
            [
                'name' => 'Device Branch 1',
                'device_id' => 'DEV002',
                'location' => 'Branch 1 - Reception',
                'ip_address' => '192.168.2.100',
                'port' => 4370,
                'status' => 'active',
                'last_sync' => now(),
            ],
            [
                'name' => 'Device Branch 2',
                'device_id' => 'DEV003',
                'location' => 'Branch 2 - Security',
                'ip_address' => '192.168.3.100',
                'port' => 4370,
                'status' => 'active',
                'last_sync' => now(),
            ],
        ];

        foreach ($devices as $device) {
            FingerprintDevice::create($device);
        }
    }
}
