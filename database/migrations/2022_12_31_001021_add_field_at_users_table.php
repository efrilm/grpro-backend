<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldAtUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_id')->after('id');
            $table->string('no_telp')->after('password');
            $table->string('onesignal_token')->after('no_telp')->nullable();
            $table->string('photo')->after('onesignal_token');
            $table->integer('is_sales')->after('photo');
            $table->integer('is_active')->after('is_sales');
            $table->integer('is_queue')->after('is_active');
            $table->string('address')->after('is_queue')->nullable();
            $table->enum('role', ['1', '2', '3', '4'])->after('address');
            $table->string('project_code')->after('role');
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
