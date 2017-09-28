<?php

use Illuminate\Support\Facades\Schema;
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
            $table->increments('id');
            $table->string('username');
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('subscription_id')->unsigned();
            $table->rememberToken();
            $table->timestamps();
            $table->engine = 'InnoDB';
        }); 
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
}
