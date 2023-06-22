<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloudDriveFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloud_drive_files', function (Blueprint $table) {
            $table->id('file_id');
            $table->foreignId('folder_id');
            $table->string('original_name');
            $table->string('disk');
            $table->string('file_path');
            $table->string('file_extn');
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('folder_id')
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cloud_drive_files');
    }
}
