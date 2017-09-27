<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->date('expiration');
            $table->integer('payment_id')->unsigned();
            $table->timestamps(); 
            $table->engine = 'InnoDB';
        }); 

        Schema::table('subscription', function (Blueprint $table) {
            $table->foreign('payment_id')
                ->references('id')
                ->on('payment')
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
        Schema::dropIfExists('subscription');
    }
}
