<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToProbationers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('probationers', function (Blueprint $table) {

            $table->foreign('squad_id')
                ->references('id')
                ->on('squads')
                ->onDelete('cascade');

            $table->index('RollNumber');
            $table->index('position_number');
        });
        Schema::enableForeignKeyConstraints();
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
