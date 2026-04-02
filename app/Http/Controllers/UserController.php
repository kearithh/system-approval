<?php

namespace App\Http\Controllers;

use App\User;
use Redirect;
use App\Branch;
use App\Company;
use App\Position;
use Carbon\Carbon;
use App\Department;
use App\UserImport;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use App\Traits\TelegramNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use App\Model\ContractMagement\NotificationChannel;

class UserController extends Controller
{
    use TelegramNotification;
    /**
     * Display a listing of the users
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        $branch = Branch::select([
            'short_name',
            DB::raw("CONCAT(name_en, '(',short_name,')') AS name_km")
        ])->get();

        $company = Company::select([
                'short_name_en AS short_name',
                DB::raw("CONCAT(long_name, '(',short_name_en,')') AS name_km")
            ])
            ->orderBy('sort', 'ASC')
            ->get();

        $user = User::leftjoin('companies', 'users.company_id', '=', 'companies.id')
            ->leftjoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        // $keyword = \request()->keyword;
        // if ($keyword)
        // {
        //     $user = $user->orWhere('users.name', 'like', "%$keyword%")
        //         ->orWhere('users.username', 'like', "%$keyword%")
        //         ->orWhere('positions.name_km', 'like', "%$keyword%")
        //         ;
        // }

        $keyword = \request()->keyword;
        if ($keyword)
        {
            $user = $user->whereRaw('(users.name_en like "%' .$keyword. '%"
                        or users.username like "%' .$keyword. '%"
                        or users.system_user_id like "%' .$keyword. '%"
                        or positions.name_en like "%' .$keyword. '%" )');
        }

        $status = \request()->status;
        if ($status != null)
        {
            $user = $user->where('users.user_status', $status);
        }

        $companyId = \request()->company_id;
        if ($companyId)
        {
            $user = $user->where('companies.short_name_en', "$companyId");
        }

        $branchId = \request()->branch_id;
        if ($branchId)
        {
            $user = $user->where('branches.short_name', 'like', "%$branchId%");
        }

        $user = $user
            ->select([
                'users.*',
                DB::raw("CONCAT(branches.name_en, '(',branches.short_name,')') AS branch_name"),
                'companies.name_en AS company_name',
                'positions.name_en AS positions_name'
            ])
            ->orderBy('username', 'ASC')
            ->paginate(30);

        return view('users.index', ['users' => $user, 'branch' => $branch, 'company' => $company]);
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $companies = Company::select(['id', 'name'])->orderBy('sort', 'ASC')->get();
        $positions = Position::all(['id', 'name_km']);
        $branch = Branch::all(['id', 'name_km', 'short_name']);
        $department = Department::all(['id', 'name_km']);
        return view('users.create', compact('positions', 'companies', 'branch', 'department'));
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request, User $model)
    {
        if (!Auth::user()->role > 0) {
            return redirect()->back();
        }

        $user = User::where('username', $request->username)->first();
        if ($user) {
            return redirect()->back()->with("error","User is existing");
        }

        $signatureSrc = '';
        if($request->hasFile('signature')) {
            $signatureSrc = Storage::disk('local')->put('user', $request->file('signature'));
        }

        $short_signSrc = '';
        if($request->hasFile('short_signature')) {
            $short_signSrc = Storage::disk('local')->put('user', $request->file('short_signature'));
        }

        $avatarSrc = null;
        if ($request->hasFile('avatar')) {
            $avatarSrc = Storage::disk('local')->put('user', $request->file('avatar'));
        }

        // Check position
        $position_id = str_replace(' ', '', $request->position_id);

        // $position = Position::where('id', $position_id)
        //                     ->orWhere('name_km', $position_id)
        //                     ->orWhere('name_km', $position_id)
        //                     ->first();

        $position = Position::where('id', $position_id)
            ->orWhereRaw("REPLACE(`name_km`, ' ', '') = ? ", $position_id)
            ->orWhereRaw("REPLACE(`name_en`, ' ', '') = ? ", $position_id)
            ->first();

        if (!$position) {
            $position = new Position([
                    'name_km' => $request->position_id,
                    'name_en' => $request->position_id,
                    'level' => 100
                ]);
            $position->save();
            $position = $position->id;
        }
        else {
            $position = $position->id;
        }

        $data = array_merge(
            $request->all(),
            [
                'password' => Hash::make($request->get('password')),
                'password_last_change' => strtotime(now()),
                'signature' => 'storage/'.$signatureSrc,
                'short_signature' => 'storage/'.$short_signSrc,
                'avatar' => 'storage/'.$avatarSrc,
                'position_id' => $position,
                'role_type'    =>$request->role_type,
            ]
        );

        //dd($data);
        $users = $model->create($data);
        NotificationChannel::create([
            'user_id'    => $users->id,
            'data'       => json_encode([
                "is_channel_telegram"       => 0,
                "is_channel_email"          => $users->email,
                "contract_management_role"  =>  $request->role_type

            ]),
        ]);
        return redirect()->route('user.index')->with(['status' => 1]);
    }

    /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $companies = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $positions = Position::all(['id', 'name_km']);
        $branch = Branch::all(['id', 'name_km', 'short_name']);
        $department = Department::all(['id', 'name_km']);
        $notification = NotificationChannel::where('user_id', $user->id)->first();
        $request_type = config('app.request_types');
        return view('users.edit', compact('user', 'positions', 'companies', 'branch', 'department', 'request_type','notification'));
    }

    public function passEdit()
    {
        //set fix update defaul password

        // if(config('app.force_update_password')) {
        //     return view('users.force_update_password');
        // }

        return view('users.update_password');
    }

    public function passUpdate(request $request)
    {
        // $this->validate($request,[
        //     'current-password' => 'required',
        //     'new-password' => 'required|string|min:6',
        //     'confirm-password'=>'required|same:new-password'
        // ]);

        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }

        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }

        if(strcmp($request->get('new-password'), $request->get('confirm-password')) != 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password and Conform Password does not matches. Please try again");
        }

        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->password_last_change = strtotime(now());
        $user->save();
        return redirect()->back()->with("success","Password changed successfully !");

        // $user->update($data);
        // return redirect()->route('dashboard');
    }
    /**
     * @param ProfileRequest $request
     * @param User $user
     * @return mixed
     */
    public function update(ProfileRequest $request, User  $user)
    {
        // Check position
        $position_id = str_replace(' ', '', $request->position_id);

        $position = Position::where('id', $position_id)
            ->orWhereRaw("REPLACE(`name_km`, ' ', '') = ? ", $position_id)
            ->orWhereRaw("REPLACE(`name_en`, ' ', '') = ? ", $position_id)
            ->first();
        if (!$position) {
            $position = new Position([
                    'name_km' => $request->position_id,
                    'name_en' => $request->position_id,
                    'level' => 100
                ]);
            $position->save();
            $position = $position->id;
        }
        else {
            $position = $position->id;
        }

        $data = $request->only('company_id', 'branch_id', 'department_id', 'gender', 'name', 'email');
        $data['position_id'] = $position;

        // 1 = super admin, 2 = admin
        if (@admin_action()) {

            $data['username'] = $request->username;
            $data['system_user_id'] = $request->system_user_id;
            $data['view_approved_request'] = $request->view_approved_request;
            $data['edit_pending_request'] = $request->edit_pending_request;
            $data['manage_template_report'] = $request->manage_template_report;
            $data['role'] = $request->role;
            $data['role_type'] = $request->role_type;

            $action_object = [];
            if (@$request->action_object["can_abrogation"]) {
                $action_object["can_abrogation"] = 1;
            }
            if (@$request->action_object["can_deabrogation"]) {
                $action_object["can_deabrogation"] = 1;
            }
            $data['action_object'] = $action_object;

            $hasPassword = $request->get('password');
            if ($hasPassword) {
                $data['password'] = Hash::make($hasPassword);
                $data['password_last_change'] = strtotime(now());
            }

            $avatarSrc = null;
            if ($request->hasFile('avatar')) {
                $avatarSrc = Storage::disk('local')->put('user', $request->file('avatar'));
                $data['avatar'] = 'storage/'.$avatarSrc;

            }

            $signatureSrc = null;
            if ($request->hasFile('signature')) {
                $signatureSrc = Storage::disk('local')->put('user', $request->file('signature'));
                $data['signature'] = 'storage/'.$signatureSrc;

            }

            $short_signSrc = null;
            if ($request->hasFile('short_signature')) {
                $short_signSrc = Storage::disk('local')->put('user', $request->file('short_signature'));
                $data['short_signature'] = 'storage/'.$short_signSrc;
            }

            if(Auth::id() != $user->id){
                $data['user_status'] = $request->user_status;

                if($request->user_status == 0){
                    $data['delete_at'] = Carbon::now();
                }else{
                    $data['delete_at'] = null;
                }

            }

            NotificationChannel::updateOrCreate([
                'user_id' => $user->id,
            ],[
                'data'       => json_encode([
                    "is_channel_telegram"       => $request->telegrame_id,
                    "is_channel_email"          => $user->email,
                    "contract_management_role"  =>  $request->role_type

                ]),
            ]);

            if($user->update($data)){
                return redirect()->route('user.index')->with(['status' => 2]);
            }
        }

        else{

            NotificationChannel::updateOrCreate([
                'user_id' => $user->id,
            ],[
                'data'       => json_encode([
                    "is_channel_telegram"       => $request->telegrame_id,
                    "is_channel_email"          => $user->email,
                    "contract_management_role"  =>  $request->role_type

                ]),
            ]);
            if($user->update($data)){
                //return Redirect::back()->with(['status' => 2]);
                return redirect()->route('dashboard')->with(['status' => 2]);
            }
        }

        return Redirect::back()->with(['status' => 4]);

    }


    /**
     * Remove the specified user from storage
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User  $user)
    {
        // $user->delete();

        return redirect()->route('user.index')->with(['status' => 3]);
    }



    public function user_destroy($id)
    {
        $user = User::find($id);
        $user->delete_at = Carbon::now();
        $user->user_status = 0;
        if ($user->save()) {
            return Redirect::back()->with(['status' => 3]);
        }
        else{
            return Redirect::back()->with(['status' => 0]);
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function appPlayerIdAjax(Request $request)
    {
        User::where('id', Auth::id())->update([
            'notification_id' => $request->input('player_id')
        ]);

        return ['status' => 1];

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function import(Request $request)
    {
        $this->validate($request, [
            'staff_file'  => 'required|mimes:xls,xlsx'
        ]);
        $path = $request->file('staff_file')->getRealPath();
        Excel::import(new UserImport, $path);
        return back()->with('success', 'Excel Data Imported successfully.');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userGuide()
    {
        return view('users.userGuide');
    }
    public function sendNotification()
    {
        $this->sendMessageToTelegram(request()->teleg_id,"Your telegram is connected with system successfully");
    }
}
