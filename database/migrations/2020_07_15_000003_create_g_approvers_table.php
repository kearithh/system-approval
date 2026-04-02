<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGApproversTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('g_approvers', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('request_id');

			$table->unsignedBigInteger('approver_id');
			$table->string('approver_name')->nullable();
			$table->string('approver_position')->nullable();
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
        Schema::dropIfExists('g_approvers_histories');
        DB::unprepared("CREATE TABLE g_approvers_histories LIKE g_approvers;");
        // alter table
        DB::unprepared("ALTER TABLE g_approvers_histories MODIFY COLUMN id int(11) NOT NULL,
		    DROP PRIMARY KEY, ENGINE = MyISAM, ADD action VARCHAR(8) DEFAULT 'insert' FIRST,
		    ADD revision INT(6) NOT NULL AUTO_INCREMENT AFTER action,
		    ADD PRIMARY KEY (id, revision);");

        DB::unprepared("DROP TRIGGER IF EXISTS g_approvers_creating;");
        DB::unprepared("DROP TRIGGER IF EXISTS g_approvers_updating;");
        DB::unprepared("DROP TRIGGER IF EXISTS g_approvers_deleting;");

        //create after insert trigger
        DB::unprepared("CREATE TRIGGER g_approvers_creating AFTER INSERT ON g_approvers FOR EACH ROW
			INSERT INTO g_approvers_histories SELECT 'insert', NULL, d.*
			FROM g_approvers AS d WHERE d.id = NEW.id;");

        DB::unprepared("CREATE TRIGGER g_approvers_updating AFTER UPDATE ON g_approvers FOR EACH ROW
			 INSERT INTO g_approvers_histories SELECT 'update', NULL, d.*
			 FROM g_approvers AS d WHERE d.id = NEW.id;");

        DB::unprepared("CREATE TRIGGER g_approvers_deleting BEFORE DELETE ON g_approvers FOR EACH ROW
			 INSERT INTO g_approvers_histories SELECT 'delete', NULL, d.*
			 FROM g_approvers AS d WHERE d.id = OLD.id;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('g_approvers');
	}
}
