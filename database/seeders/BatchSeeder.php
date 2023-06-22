<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records    = [];
        $batches    = ['71 RR', '72 RR', '73 RR'];

        foreach($batches as $key=>$batchName) {
            $records[]  = [
                'BatchName' => $batchName
            ];
        }

        DB::table('batches')->insert($records);
    }
}
