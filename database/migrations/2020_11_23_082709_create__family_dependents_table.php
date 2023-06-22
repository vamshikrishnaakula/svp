<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamilyDependentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FamilyDependents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Probationer_Id');
            $table->string('DependentName');
            $table->string('DependentAge');
            $table->string('DependentGender');
            $table->string('DependentRelationship');
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
        Schema::dropIfExists('FamilyDependents');
    }
}
