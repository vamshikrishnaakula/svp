<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFitnessMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fitness_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('probationer_id');
            $table->string('fitness_name', 20)->nullable();
            $table->string('fitness_value', 30)->nullable();
            $table->date('date');
            $table->timestamps();


            // $table->foreign('probationer_id')
            // ->references('id')
            // ->on('probationers')
            // ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fitness_meta');
    }
}
