<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApproveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approve', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('reviewer_position_id')->nullable();
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->string('position')->nullable();
            $table->string('comment')->nullable();
            $table->bigInteger('created_by');
            $table->integer('type')->nullable()->comment('1 expense, 2 memo, 3 disposal');
            $table->integer('status')->default(1)->comment('1 draft, 2 approved, 3 rejected');
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('approve');
    }
}
