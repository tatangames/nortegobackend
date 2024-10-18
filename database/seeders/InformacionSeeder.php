<?php

namespace Database\Seeders;

use App\Models\Informacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InformacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Informacion::create([
            'android_modal' => '1',
            'ios_modal' => '1',
            'version_android' => '1.0',
            'version_ios' => '1.0',
        ]);
    }
}
