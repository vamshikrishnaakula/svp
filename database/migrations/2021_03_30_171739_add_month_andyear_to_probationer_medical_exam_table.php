<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonthAndyearToProbationerMedicalExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('probationer_medical_exam', function (Blueprint $table) {
            // $table->integer('month')->nullable()->after('covid');
            // $table->integer('year')->nullable()->after('covid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('probationer_medical_exam', function (Blueprint $table) {
            //
        });
    }
}
