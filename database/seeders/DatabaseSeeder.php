<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use Database\Factories\ProbationerFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('batches')->truncate();
        DB::table('probationers')->truncate();

        //$this->call(UserSeeder::class);
        $this->call(BatchSeeder::class);
        $this->call(ProbationerSeeder::class);
        $this->call(SquadSeeder::class);
    }
}
