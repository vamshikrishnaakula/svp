<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSquadActivityTrainer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('squad_activity_trainer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('squad_id');
            $table->foreignId('activity_id');
            $table->foreignId('staff_id');
            $table->timestamps();

            $table->foreign('squad_id')
                ->references('id')
                ->on('squads')
                ->onDelete('cascade');

            $table->foreign('activity_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');

            $table->foreign('staff_id')
                ->references('id')
                ->on('users')
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
        Schema::table('squad_activity_trainer', function (Blueprint $table) {
            //
        });
    }
}
