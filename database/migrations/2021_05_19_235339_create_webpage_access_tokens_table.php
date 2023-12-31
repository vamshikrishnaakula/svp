<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebpageAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webpage_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('token');
            $table->timestamp('expires_at', $precision = 0);
            $table->timestamps();

            $table->index('token');

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
        Schema::dropIfExists('webpage_access_tokens');
    }
}
