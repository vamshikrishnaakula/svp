<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationerInpatientProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probationer_inpatient_procedure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('probationer_id');
            $table->foreignId('inpatient_id');
            $table->integer('prescription_number');
            $table->date('date');  
            $table->string('procedure', 400);
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
        Schema::dropIfExists('probationer_inpatient_procedure');
    }
}
