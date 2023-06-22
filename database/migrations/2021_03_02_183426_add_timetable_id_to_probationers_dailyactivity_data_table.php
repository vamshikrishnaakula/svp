<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimetableIdToProbationersDailyactivityDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('probationers_dailyactivity_data', function (Blueprint $table) {
         $table->foreignId('timetable_id')->nullable()->after('probationer_id');
         $table->date('date')->after('attendance');
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
