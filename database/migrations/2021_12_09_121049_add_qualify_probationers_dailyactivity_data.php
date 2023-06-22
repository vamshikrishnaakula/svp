<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQualifyProbationersDailyactivityData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('probationers_dailyactivity_data', function (Blueprint $table) {
            $table->unsignedTinyInteger('qualified')->nullable()->after('count');
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
