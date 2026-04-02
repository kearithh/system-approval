<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGReviewersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('g_reviewers', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('request_id');

			$table->unsignedBigInteger('reviewer_id');
			$table->string('reviewer_name')->nullable();
			$table->string('reviewer_position')->nullable();
			$table->string('comment')->nullable();
			$table->json('attachments')->nullable()->comment("
			[
				[
					'org_name' => 'original file name',
					'src' => 'path to store file',
					'uploaded_at' => 'upload date'
				],
			]");

			$table->string('status')->default(config('app.pending'))->comment('status store as string key');
			$table->timestamp('approved_at')->nullable();
			$table->timestamp('rejected_at')->nullable();

			$table->timestamps();
//			$table->foreign('reference_id')->references('id')->on('re_references')->onDelete('cascade');
		});
        // create the history table
        Schema::dropIfExists('g_reviewers_histories');
        DB::unprepared("CREATE TABLE g_reviewers_histories LIKE g_reviewers;");
        // alter table
        DB::unprepared("ALTER TABLE g_reviewers_histories MODIFY COLUMN id int(11) NOT NULL,
		    DROP PRIMARY KEY, ENGINE = MyISAM, ADD action VARCHAR(8) DEFAULT 'insert' FIRST,
		    ADD revision INT(6) NOT NULL AUTO_INCREMENT AFTER action,
		    ADD PRIMARY KEY (id, revision);");

        DB::unprepared("DROP TRIGGER IF EXISTS g_reviewers_creating;");
        DB::unprepared("DROP TRIGGER IF EXISTS g_reviewers_updating;");
        DB::unprepared("DROP TRIGGER IF EXISTS g_reviewers_deleting;");

        //create after insert trigger
        DB::unprepared("CREATE TRIGGER g_reviewers_creating AFTER INSERT ON g_reviewers FOR EACH ROW
			INSERT INTO g_reviewers_histories SELECT 'insert', NULL, d.*
			FROM g_reviewers AS d WHERE d.id = NEW.id;");

        DB::unprepared("CREATE TRIGGER g_reviewers_updating AFTER UPDATE ON g_reviewers FOR EACH ROW
			 INSERT INTO g_reviewers_histories SELECT 'update', NULL, d.*
			 FROM g_reviewers AS d WHERE d.id = NEW.id;");

        DB::unprepared("CREATE TRIGGER g_reviewers_deleting BEFORE DELETE ON g_reviewers FOR EACH ROW
			 INSERT INTO g_reviewers_histories SELECT 'delete', NULL, d.*
			 FROM g_reviewers AS d WHERE d.id = OLD.id;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('g_reviewers');
	}
}
