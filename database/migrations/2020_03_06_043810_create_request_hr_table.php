<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestHrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_hr', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('code_increase')->nullable();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->float('total')->nullable();
            $table->float('total_khr')->nullable();
            $table->integer('status');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('company_id');
            $table->mediumText('remark')->nullable();
            $table->string('import')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('request_hr');
    }
}
