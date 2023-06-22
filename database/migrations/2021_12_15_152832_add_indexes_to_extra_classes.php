<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToExtraClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('extra_classes', function (Blueprint $table) {
            $table->foreign('activity_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');

            $table->foreign('subactivity_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');

            $table->index('date');
            $table->index('session_start');
        });

        // Extra Class meta
        Schema::table('extra_classmetas', function (Blueprint $table) {

            $table->index('attendance');
            $table->index('count');
            $table->index('grade');
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
        Schema::table('extra_classes', function (Blueprint $table) {
            //
        });
    }
}
