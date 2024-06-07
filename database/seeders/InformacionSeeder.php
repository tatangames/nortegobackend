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
            'code_android' => '1.0.0',
            'code_ios' => '1.0.0',
        ]);
    }
}
