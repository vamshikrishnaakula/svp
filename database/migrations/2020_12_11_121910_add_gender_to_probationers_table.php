<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderToProbationersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('probationers', function (Blueprint $table) {
        //     $table->foreignId('User_id');
        //     $table->foreignId('Squad_id');
        //     $table->string('Email')->nullable();
        //     $table->string('MobileNumber')->nullable();
        // });
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
