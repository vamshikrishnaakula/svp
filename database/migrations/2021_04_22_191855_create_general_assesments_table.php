<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralAssesmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_assesments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('probationer_id');
            $table->string('punctuality', 15)->nullable();
            $table->string('behaviour', 15)->nullable();
            $table->string('teamspirit', 15)->nullable();
            $table->string('learningefforts', 15)->nullable();
            $table->string('responsibility', 15)->nullable();
            $table->string('leadership', 15)->nullable();
            $table->string('commandcontrol', 15)->nullable();
            $table->string('sportsmanship', 15)->nullable();
            $table->string('month', 5);
            $table->string('year', 5);
            $table->bigInteger('staff_id');     // User Id of the staff who created the record
            $table->string('staff_role', 20);   // User Role of the staff
            $table->timestamps();

            $table->foreign('probationer_id')
                ->references('id')
                ->on('probationers')
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
        Schema::dropIfExists('general_assesments');
    }
}
