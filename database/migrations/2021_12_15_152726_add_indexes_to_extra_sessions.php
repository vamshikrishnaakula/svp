<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToExtraSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('extra_sessions', function (Blueprint $table) {

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

        // Extra Sesion meta
        Schema::table('extra_sessionmetas', function (Blueprint $table) {

            $table->foreign('timetable_id')
                ->references('id')
                ->on('timetables')
                ->onDelete('cascade');

            $table->index('attendance');
            $table->index('count');
            $table->index('grade');
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
        Schema::table('extra_sessions', function (Blueprint $table) {
            //
        });
    }
}
