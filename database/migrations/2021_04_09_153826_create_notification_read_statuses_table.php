<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationReadStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_read_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id');
            $table->foreignId('user_id');
            $table->boolean('read_status');
            $table->timestamps();

            $table->foreign('notification_id')
                ->references('id')
                ->on('notifications')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('notification_read_statuses');
    }
}
