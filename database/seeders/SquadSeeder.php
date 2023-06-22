<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Faker\Factory as Faker;

class SquadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datetime   = date("Y-m-d H:i:s");

        $faker = Faker::create();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('squads')->truncate();
        DB::table('assign_probationers_to_squad')->truncate();

        /** ----------------------------------------------------------------
         * Create Squads
         * -------------------------------------------------------------- */
        $batch_ids  = DB::table('batches')->pluck('id');
        $DI_ids     = DB::table('users')
            ->where('role', 'drillinspector')->pluck('id');


        for($i=0; $i<8; $i++) {
            $batch_id   = $faker->randomElement($batch_ids);

            $squadNo    = 1;
            $squadNoMax    = DB::table('squads')->where('Batch_Id', $batch_id)->max('SquadNumber');
            if($squadNoMax) {
                $squadNo    = (int) $squadNoMax + 1;
            }

            DB::table('squads')->insert([
                'Batch_Id'      => $batch_id,
                'DrillInspector_Id'  => $faker->randomElement($DI_ids),
                'SquadNumber'  => $squadNo,
                'created_at'  => $datetime,
                'updated_at'  => $datetime
            ]);
        }

        /** ----------------------------------------------------------------
         * Assign Probationers To Squad
         * -------------------------------------------------------------- */

        $squad_ids          = DB::table('squads')->pluck('id');
        $probationer_ids    = DB::table('probationers')->pluck('id');

        foreach($probationer_ids as $probationer_id) {

            $probationerSquad   = DB::table('assign_probationers_to_squad')
                ->where('Probationer_Id', $probationer_id)->get();

            if( count($probationerSquad) === 0 ) {
                DB::table('assign_probationers_to_squad')->insert([
                    'Squad_Id'      => $faker->randomElement($squad_ids),
                    'Probationer_Id'  => $probationer_id,
                    'created_at'  => $datetime,
                    'updated_at'  => $datetime
                ]);
            }
        }
    }
}
