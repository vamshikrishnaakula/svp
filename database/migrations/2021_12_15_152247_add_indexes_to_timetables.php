<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class AddIndexesToTimetables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('timetables', function (Blueprint $table) {

            DB::statement("ALTER TABLE `timetables` CHANGE `subactivity_id` `subactivity_id` BIGINT UNSIGNED NULL DEFAULT NULL;");

            $table->foreign('activity_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');

            $table->foreign('subactivity_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');

            $table->index('date');
            $table->index('session_start');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timetables', function (Blueprint $table) {
            //
        });
    }
}
