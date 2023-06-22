<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Table Name: attendances
     *
     * Col  id          => Auto increament primary key
     * Col  probationer_id  => Foreign key (id from probationers)
     * Col  date            => Date - YYYY-MM-DD
     * Col  timetable_id    => Foreign key (id from timetables)
     * Col  attendance      => Attendance
     *                          P  - Present
     *                          MDO - Mess duty officer
     *                          NCM - No Class Missed
     *                          NAP  - Absent
     *                          L - Leave
     *                          M - Medical Leave
     *                          OT - Other
     * Col  unit        => activity mesurment unit
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('probationer_id');
            $table->date('date');               // YYYY-MM-DD
            $table->foreignId('timetable_id');  // Session id (id from timetables table)
            $table->string('attendance', 20);
            $table->string('comment', 100)->nullable();
            $table->timestamps();

            $table->foreign('probationer_id')
                ->references('id')
                ->on('probationers')
                ->onDelete('cascade');

            $table->foreign('timetable_id')
                ->references('id')
                ->on('timetables')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
