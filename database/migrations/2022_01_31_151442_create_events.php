<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id');
            $table->string('competition', 30)->nullable();
            $table->string('category', 30)->nullable();
            $table->string('gender')->nullable();
            $table->string('event_name')->nullable();
            $table->string('events_rounds')->nullable();
            $table->string('units')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('batch_id')
            ->references('id')
            ->on('batches')
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
        Schema::dropIfExists('events');
    }
}
