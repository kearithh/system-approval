<?php

namespace App\Http\Controllers\STSKRequest;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Position;
use App\User;
use App\Branch;
use App\Department;
use Redirect;
use App\Http\Requests\UserRequest;
use App\UserImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
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
            DB::raw("CONCAT(name_km, '(',short_name,')') AS name_km")
        ])->get();

//        dd($branch);

        $user = User::leftjoin('positions', 'users.position_id', 'positions.id')
            ->leftjoin('branches', 'users.branch_id', 'branches.id');
        $branchId = \request()->branch_id;
        if ($branchId)
        {
            $user = $user->where('branches.short_name', 'like', "%$branchId%");
        }

        $keyword = \request()->keyword;
        if ($keyword)
        {
            $user = $user->orWhere('users.name', 'like', "%$keyword%")
                ->orWhere('users.username', 'like', "%$keyword%")
                ->orWhere('positions.name_km', 'like', "%$keyword%")
                ;
        }
        $user = $user
            ->select([
                'users.*',
                'branches.id as branch_id',
                DB::raw("CONCAT(branches.name_km, '(',branches.short_name,')') AS branch_name")
            ])
            ->orderBy('username', 'ASC')
            ->paginate(30);
        return view('users.index', ['users' => $user, 'branch' => $branch]);
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $companies = DB::table('companies')->get();
        $positions = Position::all(['id', 'name_km']);
        $branch = Branch::all(['id', 'name_km']);
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
        $signatureSrc = '';
        if($request->hasFile('signature')) {
            $signatureSrc = Storage::disk('local')->put('user', $request->file('signature'));
        }
        $short_signSrc = '';
        if($request->hasFile('short_signature')) {
            $short_signSrc = Storage::disk('local')->put('user', $request->file('short_signature'));
        }

        //dd($short_signSrc);
        $avatarSrc = null;
        if ($request->hasFile('avatar')) {
            $avatarSrc = Storage::disk('local')->put('user', $request->file('avatar'));
        }

        // Check position
        $position = Position::find($request->position_id);
        if (!$position) {
            $position = new Position(['name_km' => $request->position_id]);
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
                'signature' => 'storage/'.$signatureSrc,
                'short_signature' => 'storage/'.$short_signSrc,
                'avatar' => 'storage/'.$avatarSrc,
                'position_id' => $position,
            ]
        );
        //dd($data);
        $model->create($data);
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
        $companies = DB::table('companies')->get();
        $positions = Position::all(['id', 'name_km']);
        $branch = Branch::all(['id', 'name_km']);
        $department = Department::all(['id', 'name_km']);
        return view('users.edit', compact('user', 'positions', 'companies', 'branch', 'department'));
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
        $hasPassword = $request->get('password');
        $data = $request->only('company_id', 'branch_id', 'department_id', 'gender','position_id', 'name', 'username', 'email');
        if ($hasPassword) {
            $data['password'] = Hash::make($hasPassword);
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

        $user->update($data);
        return redirect()->route('user.index')->with(['status' => 2]);
    }

    /**
     * Remove the specified user from storage
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User  $user)
    {
        $user->delete();

        return redirect()->route('user.index')->with(['status' => 3]);
    }



    public function user_destroy($id)
    {
        $user=User::find($id);
        if ($user->delete()) {
            return redirect()->route('user.index')->with(['status' => 3]);
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
}
