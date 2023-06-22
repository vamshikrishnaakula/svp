<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_notes', function (Blueprint $table) {
            $table->id('note_id');
            $table->foreignId('user_id')->constrained();
            $table->string('reference');    // probationer|squad
            $table->foreignId('reference_id')->nullable();  // user_id|squad_id (user_id if refrence = probationer)
            $table->string('title');
            $table->text('text');
            $table->timestamps();

            $table->index('reference');
            $table->index('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_notes');
    }
}
