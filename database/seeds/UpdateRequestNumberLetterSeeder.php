<?php

use App\HRRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRequestNumberLetterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = HRRequest::where('status', config('app.approve_status_approve'))->whereNull('deleted_at')->get();
        foreach ($data as $item) {

            $codeGenerate = generateCode('hr_requests', $item->company_id, $item->id, 'LT');
            $item->code_increase = $codeGenerate['increase'];
            $item->code = $codeGenerate['newCode'];

            $item->save();

        }
    }
}
