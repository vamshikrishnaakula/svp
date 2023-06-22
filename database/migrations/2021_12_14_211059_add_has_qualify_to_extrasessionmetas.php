<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasQualifyToExtrasessionmetas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extra_sessionmetas', function (Blueprint $table) {
            // $table->unsignedTinyInteger('has_qualify')->nullable()->after('count');
            $table->unsignedTinyInteger('qualified')->nullable()->after('count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('extra_sessionmetas', function (Blueprint $table) {
            //
        });
    }
}
