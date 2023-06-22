<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id');
            $table->foreignId('batch_id');
            $table->foreignId('squad_id')->nullable();

            $table->string('RollNumber')->nullable();
            $table->string('Cadre')->nullable();
            $table->string('Name')->nullable();
            $table->string('Email')->nullable();
            $table->string('MobileNumber')->nullable();
            $table->string('Dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('Religion')->nullable();
            $table->string('Category')->nullable();
            $table->string('MartialStatus')->nullable();
            $table->string('MotherName')->nullable();
            $table->string('Moccupation')->nullable();
            $table->string('FatherName')->nullable();
            $table->string('Foccupation')->nullable();
            $table->string('Stateofdomicile')->nullable();
            $table->string('Hometown')->nullable();
            $table->string('District')->nullable();
            $table->string('HomeAddress')->nullable();
            $table->string('State')->nullable();
            $table->string('Pincode')->nullable();
            $table->string('phoneNumberStd')->nullable();
            $table->string('MobileContactNumber')->nullable();
            $table->string('OtherState')->nullable();
            $table->string('EmergencyName')->nullable();
            $table->string('EmergencyPhone')->nullable();
            $table->string('EmergencyEmailId')->nullable();
            $table->string('EmergencyAddress')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

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
        Schema::dropIfExists('probationers');
    }
}
