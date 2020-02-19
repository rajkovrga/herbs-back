<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Herbs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('herbs',function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('srb_name',100)->unique();
            $table->string('lat_name',100)->unique();
            $table->boolean('toxic')->default(0);
            $table->boolean('endangered')->default(0);
            $table->text('desc');
            $table->text('image_url');
            $table->unsignedBigInteger('period_id')->nullable();
            $table->unsignedBigInteger('pickpart_id')->nullable();
            $table->foreign('period_id')->references('id')->on('periods')->onDelete("set null");
            $table->foreign('pickpart_id')->references('id')->on('pickparts')->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('herbs');

    }
}
