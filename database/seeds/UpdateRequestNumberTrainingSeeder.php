<?php

use App\Training;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRequestNumberTrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = Training::where('status', config('app.approve_status_approve'))->whereNull('deleted_at')->get();
        foreach ($data as $item) {

            $codeGenerate = generateCode('training', $item->company_id, $item->id, 'TR');
            $item->code_increase = $codeGenerate['increase'];
            $item->code = $codeGenerate['newCode'];

            $item->save();

        }
    }
}
