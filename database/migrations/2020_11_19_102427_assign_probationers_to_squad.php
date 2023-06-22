<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssignProbationersToSquad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_probationers_to_squad', function (Blueprint $table) {
            $table->Id();
            $table->foreignId('Squad_Id');
            $table->foreignId('Probationer_Id');
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
        Schema::dropIfExists('assign_probationers_to_squad');
    }
}
