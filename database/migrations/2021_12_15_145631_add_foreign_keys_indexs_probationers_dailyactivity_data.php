<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class AddForeignKeysIndexsProbationersDailyactivityData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('probationers_dailyactivity_data', function (Blueprint $table) {

            // Change timetable_id column to bigint
            DB::statement("ALTER TABLE `probationers_dailyactivity_data` CHANGE `timetable_id` `timetable_id` BIGINT UNSIGNED NULL DEFAULT NULL;");

            // Add index kyes
            $table->foreign('Batch_id')
                ->references('id')
                ->on('batches')
                ->onDelete('cascade');

            $table->foreign('squad_id')
                ->references('id')
                ->on('squads')
                ->onDelete('cascade');

            $table->foreign('staff_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('activity_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');

            $table->foreign('subactivity_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');

            $table->foreign('component_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');

            $table->foreign('probationer_id')
                ->references('id')
                ->on('probationers')
                ->onDelete('cascade');

            $table->foreign('timetable_id')
                ->references('id')
                ->on('timetables')
                ->onDelete('cascade');


                $table->index('date');
                $table->index('grade');
                $table->index('attendance');
                $table->index('count');
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
        Schema::table('probationers_dailyactivity_data', function (Blueprint $table) {
            //
        });
    }
}
