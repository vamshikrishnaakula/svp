<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutdoorPrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outdoor_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('p_id');
            $table->string('doctor_name');
            $table->string('hospital_name');
            $table->string('report_name');
            $table->timestamps();

            $table->foreign('p_id')
            ->references('id')
            ->on('labreports')
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
        Schema::dropIfExists('outdoor_prescriptions');
    }
}
