<?php

use App\Approve;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UpdateApproverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set("memory_limit", -1);

        $data = Approve::whereNull('user_object')
                ->whereNotNull('reviewer_id')
                ->where('reviewer_id', '!=', 0)
                ->select(['id', 'reviewer_id'])
                ->get();
        foreach ($data as $item) {
            $item->user_object = @userObject($item['reviewer_id']);
            $item->save();
        }

    }
}
