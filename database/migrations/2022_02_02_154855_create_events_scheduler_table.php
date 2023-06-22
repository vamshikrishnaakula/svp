<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsSchedulerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_scheduler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id');
            $table->integer('roundno');
            $table->string('venue');
            $table->datetime('date');
            $table->timestamps();


            $table->foreign('event_id')
            ->references('id')
            ->on('events')
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
        Schema::dropIfExists('events_scheduler');
    }
}
