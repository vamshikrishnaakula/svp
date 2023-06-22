<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class SubactivityNullableProbationersDailyactivityData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('probationers_dailyactivity_data', function (Blueprint $table) {
            // Change subactivity_id and component_id to be nullable

            DB::statement("ALTER TABLE `probationers_dailyactivity_data` CHANGE `subactivity_id` `subactivity_id` BIGINT UNSIGNED NULL;");
            DB::statement("ALTER TABLE `probationers_dailyactivity_data` CHANGE `component_id` `component_id` BIGINT UNSIGNED NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('probationers_dailyactivity_data', function (Blueprint $table) {
            //
        });
    }
}
