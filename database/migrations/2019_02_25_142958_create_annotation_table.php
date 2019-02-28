<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->string('filename');
            $table->integer('status');
            $table->string('filelocation');
            $table->integer('member_id')->unsigned();
            $table->timestamps();
        });
        Schema::table('annotations', function (Blueprint $table){
            $table->foreign('member_id')
                ->references('id')
                ->on('members')
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
        Schema::dropIfExists('annotations');
    }
}
