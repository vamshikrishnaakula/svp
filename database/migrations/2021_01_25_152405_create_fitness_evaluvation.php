<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFitnessEvaluvation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fitness_evaluvation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Probationer_Id');
            $table->integer('weight')->nullable();
            $table->string('bmi', 10)->nullable();
            $table->string('bodyfat', 10)->nullable();
            $table->string('fitnessscore', 10)->nullable();
            $table->string('endurancegrade', 10)->nullable();
            $table->string('strengthgrade', 10)->nullable();
            $table->string('flexibilitygrade', 10)->nullable();
            $table->string('month', 10);
            $table->string('year', 10);
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
        Schema::dropIfExists('fitness_evaluvation');
    }
}
