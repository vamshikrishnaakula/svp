<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationerPrescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationer_prescription', function (Blueprint $table) {
            $table->id();
            $table->foreignId('probationer_id');
            $table->string('drug', 100);
            $table->string('dosage', 100);
            $table->string('frequency', 10);
            $table->string('duration', 20);
            $table->string('instructions', 400);
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
        Schema::dropIfExists('probationer_prescription');
    }
}
