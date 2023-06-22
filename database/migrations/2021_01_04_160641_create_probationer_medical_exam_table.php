<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationerMedicalExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationer_medical_exam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Probationer_Id');
            $table->string('temperature', 100)->nullable();
            $table->string('antigentest', 100)->nullable();
            $table->string('rtpcr', 100)->nullable();
            $table->string('haemoglobin', 100)->nullable();
            $table->string('calcium', 100)->nullable();
            $table->string('vitamind', 100)->nullable();
            $table->string('vitaminb12', 100)->nullable();
            $table->string('preexistinginjury', 400)->nullable();
            $table->string('covid', 100)->nullable();
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
        Schema::dropIfExists('probationer_medical_exam');
    }
}
