<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationerInvestigationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationer_investigation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Probationer_Id');
            $table->string('Urine', 200)->nullable();
            $table->string('Bloodgroup', 50)->nullable();
            $table->string('Rhfactor', 50)->nullable();
            $table->string('Xray', 50)->nullable();
            $table->string('Immunization', 200)->nullable();
            $table->string('Tetanus1', 200)->nullable();
            $table->string('Tetanus2', 200)->nullable();
            $table->string('Tetanus3', 200)->nullable();
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
        Schema::dropIfExists('probationer_investigation');
    }
}
