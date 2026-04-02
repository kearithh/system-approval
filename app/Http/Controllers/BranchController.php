<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Position;
use App\User;
use App\Http\Requests\UserRequest;
use App\UserImport;
use App\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Redirect;

class BranchController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $branchs = Branch::where('deleted_at',null)->paginate(15);
        return view('branch.index', compact('branchs'));
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $companies = DB::table('companies')->get();
        return view('branch.create', compact('companies'));
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            "code" => "required",
            "company_id" => "required",
            "short_name" => "required",
            "name_km" => "required",
            "name_en" => "required"

        ]);
        $branch=new Branch();
        $branch->code = $request->code;
        $branch->company_id = $request->company_id;
        $branch->short_name = $request->short_name;
        $branch->name_km = $request->name_km;
        $branch->name_en = $request->name_en;
        $branch->created_by = Auth::user()->name;
        if ($branch->save()) {
            return redirect()->route('branch.index')->with(['status' => 1]);
        }
        else{
            return Redirect::back()->with(['status' => 0]);
        }

        //return redirect()->route('user.index')->withStatus(__('User successfully created.'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $companies = DB::table('companies')->get();
        $branch = Branch::find($id);
        return view('branch.edit', compact('branch', 'companies'));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request,[
            "code" => "required",
            "company_id" => "required",
            "short_name" => "required",
            "name_km" => "required",
            "name_en" => "required"
        ]);
        $branch = Branch::find($id);
        $branch->code = $request->code;
        $branch->company_id = $request->company_id;
        $branch->short_name = $request->short_name;
        $branch->name_km = $request->name_km;
        $branch->name_en = $request->name_en;
        $branch->updated_by = Auth::user()->name;
        if ($branch->save()) {
            return redirect()->route('branch.index')->with(['status' => 2]);
        }
        else{
            return Redirect::back()->with(['status' => 0]);
        }

    }

    /**
     * Remove the specified user from storage
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function destroy(User  $user)
    // {
    //     $user->delete();

    //     return redirect()->route('user.index')->withStatus(__('User successfully deleted.'));
    // }



    public function destroy($id)
    {
        $branch=Branch::find($id);
        $branch->deleted_at = Carbon::now();
        $branch->deleted_by = Auth::user()->name;
        if ($branch->save()) {
            return Redirect::back()->with(['status' => 3]);
        }
        else{
            return Redirect::back()->with('error','Please try again');
        }
    }


}
