<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimetablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id');
            $table->foreignId('squad_id');
            $table->foreignId('activity_id')->nullable();
            $table->foreignId('subactivity_id')->nullable();
            $table->date('date');               // YYYY-MM-DD
            $table->string('session_type', 20)->nullable()->default('regular');   // regular|extra
            $table->integer('session_start')->nullable();   // UNIX TimeStamp
            $table->integer('session_end')->nullable();   // UNIX TimeStamp
            $table->timestamps();

            $table->foreign('batch_id')
                ->references('id')
                ->on('batches')
                ->onDelete('cascade');

            $table->foreign('squad_id')
                ->references('id')
                ->on('squads')
                ->onDelete('cascade');


            // $table->foreign('activity_id')
            //     ->references('id')
            //     ->on('activities')
            //     ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timetables');
    }
}
