<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_type_id')->unsigned();
            $table->string('status');
            $table->timestamps(); 
            $table->engine = 'InnoDB';
        }); 
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('payment_type_id')
                ->references('id')
                ->on('payment_types')
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
        Schema::dropIfExists('payments');
        Schema::enableForeignKeyConstraints();

    }
}
