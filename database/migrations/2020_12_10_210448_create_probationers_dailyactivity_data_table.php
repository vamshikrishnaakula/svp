<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationersDailyactivityDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationers_dailyactivity_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Batch_id');
            $table->foreignId('squad_id');
            $table->foreignId('staff_id');
            $table->foreignId('activity_id');
            $table->foreignId('subactivity_id')->nullable();
            $table->foreignId('component_id')->nullable();
            $table->foreignId('probationer_id');
            $table->string('grade', 20)->nullable();
            $table->string('count', 100)->nullable();
            $table->string('attendance', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('probationers_dailyactivity_data');
    }
}
