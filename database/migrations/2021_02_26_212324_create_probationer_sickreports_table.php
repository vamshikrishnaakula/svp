<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationerSickreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationer_sickreports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Probationer_Id');
            $table->text('sickreport');
            $table->string('date', 12)->nullable();
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
        Schema::dropIfExists('probationer_sickreports');
    }
}
