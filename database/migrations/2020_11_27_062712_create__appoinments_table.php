<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppoinmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Appoinments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Probationer_Id');
            $table->foreignId('Doctor_Id');
            $table->string('Symptoms');
            $table->timestamp('Appoinment_Time');
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
        Schema::dropIfExists('Appoinments');
    }
}
