<?php

namespace Database\Seeders;

use App\Models\Courier;
use App\Models\Customer;
use App\Models\Package;
use App\Models\PackageHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $pelanggan = Customer::first()
            ?? Customer::create(['name' => 'Pelanggan Demo', 'phone' => '081200000001', 'address' => 'Bandung']);

        $demo = Package::where('tracking_number', 'like', 'PKE-261101-9%')->first();
        $kurir = $demo
            ? Courier::find($demo->courier_id)
            : Courier::where('is_active', true)->whereDoesntHave('packages', fn($q) => $q->where('status', 'Dikirim'))->first();
        if (!$kurir) {
            $this->command->warn('Belum ada kurir. Jalankan php artisan import:data dulu.');
            return;
        }

        for ($i = 1; $i <= 20; $i++) {
            $paket = Package::firstOrCreate(['tracking_number' => 'PKE-261101-' . (9000 + $i)], [
                'received_date' => '2026-11-01',
                'customer_id' => $pelanggan->id,
                'recipient_name' => 'Penerima Demo ' . $i,
                'recipient_phone' => '081200000002',
                'recipient_address' => 'Jl. Demo No. ' . $i,
                'destination_city' => 'Bandung',
                'zone_id' => 1,
                'is_zone_manual' => false,
                'weight_kg' => 1,
                'billable_weight' => 1,
                'service_type' => 'Reguler',
                'rate_per_kg' => 8000,
                'shipping_cost' => 8000,
                'status' => 'Dikirim',
                'courier_id' => $kurir->id,
                'created_by_user_id' => $admin->id,
            ]);
            if ($paket->wasRecentlyCreated) {
                foreach ([[null, 'Diterima'], ['Diterima', 'Transit'], ['Transit', 'Dikirim']] as [$dari, $ke]) {
                    PackageHistory::create([
                        'package_id' => $paket->id,
                        'from_status' => $dari,
                        'to_status' => $ke,
                        'actor_user_id' => $admin->id,
                        'actor_role' => 'Admin',
                        'courier_id' => $kurir->id
                    ]);
                }
            }
        }

        $jumlah = Package::where('courier_id', $kurir->id)->where('status', 'Dikirim')->count();
        $this->command->info("Kurir demo: {$kurir->code} {$kurir->name}, paket Dikirim = $jumlah");
        if ($jumlah !== 20) {
            $this->command->warn('Jumlah paket Dikirim bukan 20. Periksa data impor kurir ini.');
        }
    }
}
