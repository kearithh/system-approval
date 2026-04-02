<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->string('company_name')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('department_name')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('status')->default(config('app.draft'));
            $table->integer('review_status')->default(0);
            $table->string('name');
            $table->string('reviewer');
            $table->string('tags')->nullable()->comment('daily,weekly,monthly,yearly');

            $table->json('properties')->nullable()->comment("
                [
                    '{key}' => '{value}'
                ]
            ");
            $table->json('attachments')->nullable()->comment("
            [
                [
                    'org_name' => 'original file name',
                    'src' => 'path to store file',
                    'uploaded_at' => 'upload date'
                ],
            ]");

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->json('approvable')->nullable();
            $table->json('rejectable')->nullable();
            $table->json('viewable')->nullable();
            $table->json('editable')->nullable();
            $table->json('deletable')->nullable();
        });

        $db1 = env('DB_DATABASE');
        $db2= $db1;
        // create the history table
        Schema::dropIfExists("$db2.g_requests_histories");
        DB::unprepared("CREATE TABLE $db2.g_requests_histories LIKE $db1.g_requests;");
        // alter table
        DB::unprepared("ALTER TABLE $db2.g_requests_histories MODIFY COLUMN id int(11) NOT NULL,
		    DROP PRIMARY KEY, ENGINE = MyISAM, ADD action VARCHAR(8) DEFAULT 'insert' FIRST,
		    ADD revision INT(6) NOT NULL AUTO_INCREMENT AFTER action,
		    ADD PRIMARY KEY (id, revision);");

        DB::unprepared("DROP TRIGGER IF EXISTS g_requests_creating;");
        DB::unprepared("DROP TRIGGER IF EXISTS g_requests_updating;");
        DB::unprepared("DROP TRIGGER IF EXISTS g_requests_deleting;");

        //create after insert trigger
        DB::unprepared("CREATE TRIGGER g_requests_creating AFTER INSERT ON g_requests FOR EACH ROW
			INSERT INTO g_requests_histories SELECT 'insert', NULL, d.*
			FROM g_requests AS d WHERE d.id = NEW.id;");

        //create after update trigger
        DB::unprepared("CREATE TRIGGER g_requests_updating AFTER UPDATE ON g_requests FOR EACH ROW
			 INSERT INTO g_requests_histories SELECT 'update', NULL, d.*
			 FROM g_requests AS d WHERE d.id = NEW.id;");

        //create before delete trigger
        DB::unprepared("CREATE TRIGGER g_requests_deleting BEFORE DELETE ON g_requests FOR EACH ROW
			 INSERT INTO g_requests_histories SELECT 'delete', NULL, d.*
			 FROM g_requests AS d WHERE d.id = OLD.id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('g_requests');
    }
}
