<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\ZoneRate;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@priangankilat.test'],
            ['name' => 'Admin', 'password' => 'password123', 'role' => 'admin']
        );
        User::updateOrCreate(
            ['email' => 'manajer@priangankilat.test'],
            ['name' => 'Manajer', 'password' => 'password123', 'role' => 'manajer']
        );

        $zona = [1 => ['Zona 1 Bandung Raya', 8000], 2 => ['Zona 2 Jawa Barat', 12000], 3 => ['Zona 3 Luar Jawa Barat', 20000]];
        foreach ($zona as $id => [$nama, $harga]) {
            Zone::updateOrCreate(['id' => $id], ['name' => $nama]);
            ZoneRate::updateOrCreate(['zone_id' => $id], ['price_per_kg' => $harga]);
        }
    }
}
