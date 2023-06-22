<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationerPhysicalExaminationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationer_physical_examination', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Probationer_Id');
            $table->string('Bloodpressure', 100)->nullable();
            $table->string('Pulse', 100)->nullable();
            $table->string('Ent', 100)->nullable();
            $table->string('Dental', 100)->nullable();
            $table->string('Heart', 100)->nullable();
            $table->string('Lungs', 100)->nullable();
            $table->string('Abdomen', 100)->nullable();
            $table->string('Eyewithleft', 100)->nullable();
            $table->string('Eyewithright', 100)->nullable();
            $table->string('Eyewithoutleft', 100)->nullable();
            $table->string('Eyewithoutright', 100)->nullable();
            $table->string('Urological', 100)->nullable();
            $table->string('Athlete', 200)->nullable();
            $table->string('Defectordeformity', 400)->nullable();
            $table->string('Scarsoperation', 400)->nullable();
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
        Schema::dropIfExists('probationer_physical_examination');
    }
}
