<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('disposals', function (Blueprint $table) {
//            $table->bigIncrements('id');
//            $table->unsignedBigInteger('user_id');
//            $table->integer('total_item')->nullable();
//            $table->integer('status');
//            $table->unsignedBigInteger('created_by');
//            $table->integer('company_id');
//            $table->softDeletes();
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disposals');
    }
}
