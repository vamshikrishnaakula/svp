<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToSquads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('squads', function (Blueprint $table) {

            $table->foreign('Batch_id')
                ->references('id')
                ->on('batches')
                ->onDelete('cascade');

            $table->foreign('DrillInspector_Id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
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
        Schema::table('squads', function (Blueprint $table) {
            //
        });
    }
}
