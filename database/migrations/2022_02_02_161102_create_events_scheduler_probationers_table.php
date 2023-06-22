<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsSchedulerProbationersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_scheduler_probationers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_scheduler_id');
            $table->foreignId('probationers_id');
            $table->string('result');
            $table->string('status');
            $table->timestamps();

            $table->foreign('event_scheduler_id')
            ->references('id')
            ->on('events_scheduler')
            ->onDelete('cascade');

            $table->foreign('probationers_id')
            ->references('id')
            ->on('probationers')
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
        Schema::dropIfExists('events_scheduler_probationers');
    }
}
