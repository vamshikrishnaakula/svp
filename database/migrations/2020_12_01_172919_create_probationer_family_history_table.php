<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationerFamilyHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationer_family_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Probationer_Id');
            $table->boolean('Diabetes')->nullable();
            $table->boolean('HeartDiseases')->nullable();
            $table->boolean('Migrane')->nullable();
            $table->boolean('Epilepsy')->nullable();
            $table->boolean('Allergy')->nullable();
            $table->boolean('Smoking')->nullable();
            $table->boolean('Alchohol')->nullable();
            $table->boolean('Veg')->nullable();
            $table->boolean('NonVeg')->nullable();
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
        Schema::dropIfExists('probationer_family_history');
    }
}
