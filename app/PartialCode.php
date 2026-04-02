<?php


namespace App;


use Illuminate\Support\Facades\DB;

class PartialCode
{
    public function update_request_branch($table = 'requests')
    {
        $data = DB::table($table)
            ->join('users', "$table.user_id", '=', 'users.id')
            ->whereNotNull('users.branch_id')
            ->select(["$table.*", "users.branch_id"])
            ->get()
        ;

        foreach ($data as $key => $item) {
            DB::table("$table")
                ->where("$table.id", '=', $item->id)
                ->update([
                    "$table.branch_id" => $item->branch_id
                ])
            ;
        }
    }
}
