<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraSessionComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_session_components', function (Blueprint $table) {
            $table->id('session_component_id');
            $table->foreignId('sessionmetas_id')->constrained('extra_sessionmetas', 'id');
            $table->foreignId('session_id')->constrained('extra_sessions', 'id');
            $table->foreignId('probationer_id')->constrained('probationers', 'id');
            $table->foreignId('component_id')->constrained('activities', 'id')->nullable();
            $table->string('count', 20)->nullable();
            $table->string('grade', 20)->nullable();
            $table->unsignedTinyInteger('qualified')->nullable();
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
        Schema::dropIfExists('extra_session_components');
    }
}
