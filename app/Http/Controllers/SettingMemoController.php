<?php

namespace App\Http\Controllers;

use App\SettingMemo;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Redirect;

class SettingMemoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = SettingMemo::join('companies', 'setting_memo.company_id', '=', 'companies.id')
                ->select(
                    'setting_memo.*',
                    'companies.name as company_name'
                )
                ->paginate(30);
        return view('setting_memo.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        return view('setting_memo.create', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = SettingMemo::create([
            'company_id' => $request->input('company_id'),
            'no' => $request->input('no')
        ]);

        return redirect()->route('setting_memo.index')->with(['status' => 1]);
    }

     /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $data = SettingMemo::find($id);
        return view('setting_memo.edit', compact('data', 'company'));
    }


    public function update(Request $request, $id)
    {
        $data = SettingMemo::find($id);
        $data->company_id = $request->company_id;
        $data->no = $request->no;
        if ($data->save()) {
            return redirect()->route('setting_memo.index')->with(['status' => 2]);
        }
        else{
            return Redirect::back()->with(['status' => 0]);
        }

    }


    public function destroy($id)
    {
        $data=SettingMemo::find($id);
        if ($data->delete()) {
            return Redirect::back()->with(['status' => 3]);
        }
        else{
            return Redirect::back()->with('error','Please try again');
        }
    }

}
