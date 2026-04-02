<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApproverCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approver_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('approver_id');
            $table->string('name');
            $table->text('desc');
            $table->string('original_name');
            $table->string('src');

            $table->timestamps();
            $table->foreign('approver_id')->references('id')->on('approvers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approver_comments');
    }
}
