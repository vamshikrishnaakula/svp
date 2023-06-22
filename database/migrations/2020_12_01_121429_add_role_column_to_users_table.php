<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class AddRoleColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->date('Dob');    // YYYY-MM-DD
            // $table->string('MobileNumber');
            // $table->enum('role',  ['superadmin','admin','drillinspector','receptionist','doctor','probationer','faculty','si','adi'])->default('probationer');
            DB::statement("ALTER TABLE `users` CHANGE `role` `role` ENUM('superadmin','admin','drillinspector','receptionist','doctor','probationer','faculty','si','adi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'probationer';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
