<?php

namespace Database\Seeders;

use App\Models\EstadoBasico;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoBasicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstadoBasico::create([
            'nombre' => 'Activo',
        ]);

        EstadoBasico::create([
            'nombre' => 'Finalizado',
        ]);
    }
}
