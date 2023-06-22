<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationerGeneralInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationer_general_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Probationer_Id');
            $table->integer('Height')->nullable();
            $table->integer('Weight')->nullable();
            $table->integer('Expi')->nullable();
            $table->integer('Ins')->nullable();
            $table->integer('Expansion')->nullable(); 
            $table->text('PastHistory')->nullable(); 
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
        Schema::dropIfExists('probationer_general_info');
    }
}
