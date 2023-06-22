<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionNumberInProbationers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('probationers', function (Blueprint $table) {
            $table->integer('position_number')->unsigned()->nullable()->after('RollNumber');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('probationers', function (Blueprint $table) {
            //
        });
    }
}
