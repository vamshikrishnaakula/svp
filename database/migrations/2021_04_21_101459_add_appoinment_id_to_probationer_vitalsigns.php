<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppoinmentIdToProbationerVitalsigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('probationer_vitalsign', function (Blueprint $table) {
            $table->foreignId('appointment_id')->after('probationer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('probationer_vitalsign', function (Blueprint $table) {
            //
        });
    }
}
