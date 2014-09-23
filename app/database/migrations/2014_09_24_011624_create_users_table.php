<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->string('reg_ip');
            $table->integer('reg_time');
            $table->string('login_ip')->nullable();
            $table->integer('login_time')->nullable();
            $table->string('act_ip')->nullable();
            $table->integer('act_time')->nullable();
            $table->string('remember_token')->nullable();
            $table->unsignedBigInteger('user_group_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }

}
