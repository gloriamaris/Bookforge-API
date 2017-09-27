<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_type_id')->unsigned();
            $table->string('status');
            $table->timestamps(); 
            $table->engine = 'InnoDB';
        }); 
        Schema::table('payment', function (Blueprint $table) {
            $table->foreign('payment_type_id')
                ->references('id')
                ->on('payment_type')
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
        Schema::dropIfExists('payment_type');
    }
}
