<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id');
            $table->foreignId('activity_id')->nullable();
            $table->foreignId('subactivity_id')->nullable();
            $table->foreignId('drillinspector_id')->nullable();
            $table->date('date');               // YYYY-MM-DD
            $table->integer('session_start')->nullable();   // UNIX TimeStamp
            $table->integer('session_end')->nullable();   // UNIX TimeStamp
            $table->timestamps();

            $table->foreign('batch_id')
                ->references('id')
                ->on('batches')
                ->onDelete('cascade');

            $table->foreign('drillinspector_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        Schema::create('extra_classmetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_class_id');
            $table->foreignId('probationer_id');
            $table->string('attendance', 20)->nullable();
            $table->string('count', 20)->nullable();
            $table->string('grade', 20)->nullable();
            $table->timestamps();

            $table->foreign('extra_class_id')
                ->references('id')
                ->on('extra_classes')
                ->onDelete('cascade');

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
        Schema::dropIfExists('extra_classes');
        Schema::dropIfExists('extra_classmetas');
    }
}
