<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloudDriveFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloud_drive_folders', function (Blueprint $table) {
            $table->id('folder_id');
            $table->string('name');
            $table->foreignId('parent_id')->nullable();
            $table->string('reference');    // probationer|squad
            $table->foreignId('reference_id')->nullable();  // user_id|squad_id (user_id if refrence = probationer)
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('folder_id')
                ->on('cloud_drive_folders')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

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
        Schema::dropIfExists('cloud_drive_folders');
    }
}
