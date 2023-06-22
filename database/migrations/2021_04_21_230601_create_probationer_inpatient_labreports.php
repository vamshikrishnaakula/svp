<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationerInpatientLabreports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationer_inpatient_labreports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('probationer_id');
            $table->foreignId('inpatient_id');
            $table->integer('prescription_number');
            $table->string('labreports', 100);
            $table->date('date');
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
        Schema::dropIfExists('probationer_inpatient_labreports');
    }
}
