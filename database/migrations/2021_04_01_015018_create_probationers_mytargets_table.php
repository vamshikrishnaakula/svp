<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationersMytargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationers_mytargets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('probationer_id');
            $table->foreignId('activity_id');
            $table->bigInteger('subactivity_id')->nullable();
            $table->bigInteger('component_id')->nullable();
            $table->string('goal', 5);
            $table->string('month', 7); // YYYY-MM format
            $table->timestamps();

            $table->foreign('probationer_id')
                ->references('id')
                ->on('probationers')
                ->onDelete('cascade');

            $table->foreign('activity_id')
                ->references('id')
                ->on('activities')
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
        Schema::dropIfExists('probationers_mytargets');
    }
}
