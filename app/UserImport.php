<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        unset($rows[1]);
        $positions = Position::all();
        $companies = Company::all();
        $branches = Branch::all();
        $oldUsers = User::all();

        foreach ($rows as $row)
        {
//            dd($row);
            $oldUser = $oldUsers->where('username', '=', strtolower($row[4]))->first();
            if (is_null($oldUser)) {

                $position = Position::where('name_km', '=', $row[2])->first();
                if (is_null($position)) {
                    $position = new Position([
                        'short_name' => $row[3] ? $row[3] : $row[2],
                        'name_km' => $row[2],
                        'level' => $row[10],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $position->save();
                }
                $company = $companies->where('short_name_en', '=', $row[6])->first();
                $companyId = @$company->id;
                if (!$companyId) {
                    $companyId = 1;
                }

                $branch = $branches->where('code', '=', $row[8])->first();
                $branchId = @$branch->id;
                if (!$branchId) {
                    $branchId = NULL;
                }

                DB::table('users')->insert([
                    'position_id' => @$position->id,
                    'company_id' => $companyId,
                    'branch_id' => null,//$branchId,
                    'name' => $row[1],
                    'username' => strtolower($row[4]),
                    'email' => NULL,
                    'email_verified_at' => now(),
                    'password' => Hash::make('123456'),
                    'role' => NULL,
                    'signature' => '',
                    'gender' => $row['13'],
                    'short_signature' => '',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {

            }
        }

        [
            'ci' => 130,
            'ac' => 130,
            'pr' => 100,
            'ma' => 50,
            'fa' => 25,
            'ny' => 250,
        ];

    }
}
