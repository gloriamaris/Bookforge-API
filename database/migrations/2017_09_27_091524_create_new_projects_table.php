<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_template_id')->unsigned();
            $table->timestamps(); 
            $table->engine = 'InnoDB';
        }); 

        Schema::table('new_projects', function (Blueprint $table) {
            $table->foreign('project_template_id')
                ->references('id')
                ->on('project_templates')
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
        Schema::dropIfExists('new_projects');
        Schema::enableForeignKeyConstraints();
    }
}
