<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviewers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_id');

            $table->unsignedBigInteger('reviewer_id');
            $table->string('reviewer_name')->nullable();
            $table->string('reviewer_position')->nullable();

            $table->integer('status')->default(1)->comment('1 draft, 2 approved, 3 rejected');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();
//            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('request_id')->references('id')->on('stsk_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviewers');
    }
}
