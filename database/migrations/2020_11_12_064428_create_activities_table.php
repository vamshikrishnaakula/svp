<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Table Name: activities
     *
     * Col  id          => Auto increament primary key
     * Col  batch_id    => Foreign key (id from batches)
     * Col  name        => Activity name
     * Col  type        => activity|subactivity|component
     * Col  parent_id   => For activity:    null
     *                     For subactivity: parent activity id
     *                     For component:   parent subactivity id
     * Col  unit        => activity mesurment unit
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id');
            $table->string('name', 200);
            $table->string('type', 20)->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();

            $table->foreign('batch_id')
                ->references('id')
                ->on('batches')
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
        Schema::dropIfExists('activities');
    }
}
