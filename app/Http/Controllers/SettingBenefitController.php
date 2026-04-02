<?php

namespace App\Http\Controllers;

use App\Benefit;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Redirect;

class SettingBenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = Company::select([
            'id AS id',
            DB::raw("CONCAT(long_name, '(',short_name_en,')') AS name_km")
        ])->get();

        $data = Benefit::join('companies', 'benefit_ot.company_id', '=', 'companies.id');

        $companyId = \request()->company_id;
        if ($companyId)
        {
            $data = $data->where('benefit_ot.company_id', "$companyId");
        }

        $type = \request()->type;
        if ($type)
        {
            $data = $data->where('benefit_ot.type', "$type");
        }
        $data = $data->select(
                    'benefit_ot.*',
                    'companies.name as company_name'
                )
                ->paginate(30);
        return view('setting_benefit_ot.index', compact('data', 'company'));
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
        return view('setting_benefit_ot.create', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $benefit = new Benefit();
        $benefit->created_by = Auth::id();
        $benefit->type = $request->type;
        $benefit->benefit = $request->benefit;
        $benefit->company_id = $request->company_id;

        if ($benefit->save()) {

            return redirect()->route('benefit_ot.index')->with(['status' => 1]);
        }
        return redirect()->back()->with(['status' => 4]);
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
        $data = Benefit::find($id);
        return view('setting_benefit_ot.edit', compact('data', 'company'));
    }


    public function update(Request $request, $id)
    {
        $benefit = Benefit::find($id);
        $benefit->updated_by = Auth::id();
        $benefit->type = $request->type;
        $benefit->benefit = $request->benefit;
        $benefit->company_id = $request->company_id;
        if ($benefit->save()) {
            return redirect()->route('benefit_ot.index')->with(['status' => 2]);
        }
        else{
            return Redirect::back()->with(['status' => 0]);
        }

    }


    public function destroy($id)
    {
        $data = Benefit::find($id);
        if ($data->delete()) {
            return Redirect::back()->with(['status' => 3]);
        }
        else{
            return Redirect::back()->with('error','Please try again');
        }
    }

}
