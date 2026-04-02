<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Mission;
use App\MissionItem;
use App\Position;
use App\User;
use App\Company;
use App\Branch;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use function Composer\Autoload\includeFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ApiController extends BaseApiController
{
    // request (hint, date_from, date_to and company_code)
    public function mission(Request $request)
    {
        $hint = $request->hint; 

        if (!$this->checkHintUser($hint)) {
            return $this->myResponse(0, 'Authentication failed!');
        }

        $mission = mission::join('users', 'users.id', '=', 'mission.user_id')
            ->leftjoin('companies', 'companies.id', '=', 'mission.company_id')
            ->whereNotIn('mission.status', [config('app.approve_status_reject')])
            ->whereNull('mission.deleted_at');

        $company = $request->company_code;
        if ($company != null) { // All
            $mission = $mission ->where('companies.code', $company);  
        }

        if($request->date_from != null && $request->date_to != null) {
            $date_from = strtotime($request->date_from);
            $startDate = Carbon::createFromTimestamp($date_from)->format('Y-m-d');

            $date_to = strtotime($request->date_to);
            $endDate = Carbon::createFromTimestamp($date_to)->format('Y-m-d');

            $mission = $mission->where(function($query) use ($startDate, $endDate){
                $query->whereRaw("'$startDate' between start_date and end_date or '$endDate' between start_date and end_date");
            });
        }
        else if($request->date_from != null) {
            $date_from = strtotime($request->date_from);
            $startDate = Carbon::createFromTimestamp($date_from)->format('Y-m-d');

            $mission = $mission->where(function($query) use ($startDate){
                $query->whereRaw("'$startDate' between start_date and end_date");
            });
        }
        else if($request->date_to != null) {
            $date_to = strtotime($request->date_to);
            $endDate = Carbon::createFromTimestamp($date_to)->format('Y-m-d');

            $mission = $mission->where(function($query) use ($endDate){
                $query->whereRaw("'$endDate' between start_date and end_date");
            });
        }

        $mission = $mission->select([
                    'mission.id',
                    'mission.start_date',
                    'mission.end_date',
                    'mission.staffs as staffs',
                    'companies.code as company_code',
                    'companies.name as company_name',
                    'users.name as requester_name'
                ])
                ->orderBy('mission.id', 'DESC')
                ->get();

        $data = [];
        $i = 1;
        if ($mission->count() > 0) {
            foreach($mission as $key) {
                $staffs = is_array($key->staffs) ? $key->staffs : json_decode($key->staffs);
                foreach($staffs as $val){
                    $uuid = @User::find($val->staff_id)->system_user_id;
                    $data[] = [
                        'no' => $i++,
                        'mission_id' => $key->id,
                        'uuid' => @$uuid,
                        'staff_name' => $val->staff_name,
                        'start_date' => $key->start_date,
                        'end_date' => $key->end_date,
                        'company_code' => $key->company_code,
                        'company_name' => $key->company_name,
                        'requester_name' => $key->requester_name
                    ];
                }
            }
        }

        return $this->myResponse($data);
    }
    
    // request (hint)
    public function allUser(Request $request)
    {
        $hint = $request->hint; 

        if (!$this->checkHintUser($hint)) {
            return $this->myResponse(0, 'Authentication failed!');
        }

        $data = User::join('positions', 'users.position_id', 'positions.id')
            ->whereNull('users.delete_at')
            ->where('users.user_status', config('app.user_active'))
            ->select([
                'users.id',
                'users.name',
                'users.username',
                'users.email',
                'positions.name_km as position_name'
            ])
            ->orderBy('users.username', 'ASC')
            ->get();

        return $this->myResponse($data);
    }

    // request (hint, id)
    public function userById(Request $request)
    {
        $hint = $request->hint; 

        if (!$this->checkHintUser($hint)) {
            return $this->myResponse(0, 'Authentication failed!');
        }

        $data = User::join('positions', 'users.position_id', 'positions.id')
            ->where('users.id', $request->id)
            ->whereNull('users.delete_at')
            ->where('users.user_status', config('app.user_active'))
            ->select([
                'users.id',
                'users.name',
                'users.username',
                'users.email',
                'positions.name_km as position_name'
            ])
            ->first();

        return $this->myResponse($data);
    }

}
