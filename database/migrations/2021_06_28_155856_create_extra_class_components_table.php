<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraClassComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_class_components', function (Blueprint $table) {
            $table->id('session_component_id');
            $table->foreignId('classmetas_id')->constrained('extra_classmetas', 'id');
            $table->foreignId('session_id')->constrained('extra_classes', 'id');
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
        Schema::dropIfExists('extra_class_components');
    }
}
