<?php

namespace Database\Seeders;

//use App\Models\probationer;
use Database\Factories\ProbationerFactory;
use Illuminate\Database\Seeder;

class ProbationerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProbationerFactory::times(100)->create();
    }
}
