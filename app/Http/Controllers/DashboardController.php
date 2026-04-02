<?php

namespace App\Http\Controllers;

use App\Position;
use App\Disposal;
use App\RequestForm;
use App\RequestHR;
use App\RequestMemo;
use App\DamagedLog;
use App\HRRequest;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     *
     */
    public function index()
    {
        // check defaul password
        // if (Hash::check(("123456"), Auth::user()->password)) {
        //     return redirect()->route('password.edit')->with("error","Please to change default password!");
        // }else{

            $totalMemo = RequestMemo::where('user_id',Auth::id())->where('deleted_at', null)->count();
            $totalSpecial = RequestForm::where('user_id',Auth::id())->where('deleted_at', null)->count();
            $totalGeneral = RequestHR::where('user_id',Auth::id())->where('deleted_at', null)->count();
            $totalDispose = Disposal::where('user_id',Auth::id())->where('deleted_at', null)->count();
            $totalDamaged = DamagedLog::where('user_id',Auth::id())->where('deleted_at', null)->count();

            $Memo = RequestMemo::where('deleted_at', null)->count();
            $Special = RequestForm::where('deleted_at', null)->count();
            $General = RequestHR::where('deleted_at', null)->count();
            $Dispose = Disposal::where('deleted_at', null)->count();
            $Damaged = DamagedLog::where('deleted_at', null)->count();
            $Letter = HRRequest::where('deleted_at', null)->count();

            $totalCompany = Company::where('type','!=',0)->get();

            $companyName = [];
            $companyColor = [];
            $companyRequest = [];
            foreach ($totalCompany as $key => $value) {

                $totalCompany[$key]->memo =  RequestMemo::where('company_id',$value->id)->where('deleted_at', null)->count();
                $totalCompany[$key]->special =  RequestForm::where('company_id',$value->id)->where('deleted_at', null)->count();
                $totalCompany[$key]->general =  RequestHR::where('company_id',$value->id)->where('deleted_at', null)->count();
                $totalCompany[$key]->disposal =  Disposal::where('company_id',$value->id)->where('deleted_at', null)->count();
                $totalCompany[$key]->damaged =  DamagedLog::where('company_id',$value->id)->where('deleted_at', null)->count();

                $companyName[$key] = $value->short_name_en;
                $companyColor[$key] = $value->color;
                $companyRequest[$key] = RequestMemo::where('company_id',$value->id)->where('deleted_at', null)->count()
                                    + RequestForm::where('company_id',$value->id)->where('deleted_at', null)->count()
                                    + RequestHR::where('company_id',$value->id)->where('deleted_at', null)->count()
                                    + Disposal::where('company_id',$value->id)->where('deleted_at', null)->count()
                                    + DamagedLog::where('company_id',$value->id)->where('deleted_at', null)->count();
            }

            $departments = DB::table('departments')->get();

            $requestType = [
                (object)[
                    'name' => 'Memo',
                    'total_request' => $totalMemo,
                    'total_pending_request' => 1,
                    'icon' => '<i class="fas fa-gavel"></i>',
                    'link' => 'pending/memo',
                ],
                (object)[
                    'name' => 'Special Expense',
                    'total_request' => $totalSpecial,
                    'total_pending_request' => 1,
                    'icon' => '<i class="fas fa-star"></i>',
                    'link' => 'pending/special-expense',
                ],
                (object)[
                    'name' => 'General Expense',
                    'total_request' => $totalGeneral,
                    'total_pending_request' => 0,
                    'icon' => '<i class="fas fa-money-check-alt"></i>',
                    'link' => 'pending/general-expense',
                ],
                (object)[
                    'name' => 'Disposal Asset',
                    'total_request' => $totalDispose,
                    'total_pending_request' => 1,
                    'icon' => '<i class="fas fa-minus-circle"></i>',
                    'link' => 'pending/disposal',
                ],
                (object)[
                    'name' => 'Damaged Asset',
                    'total_request' => $totalDamaged,
                    'total_pending_request' => 1,
                    'icon' => '<i class="fas fa-exclamation-circle"></i>',
                    'link' => 'pending/disposal',
                ]

            ];

            $totalRequests = $totalMemo + $totalSpecial + $totalGeneral + $totalDispose + $totalDamaged;

            $totalStaffs = User::where('users.user_status', config('app.user_active'))->count();
            $totalPositions = Position::count();

            // dd($companyRequest);
            return view('dashboard', compact(
                'departments',
                'requestType',
                'totalPositions',
                'totalStaffs',
                'totalRequests',
                'totalMemo',
                'totalSpecial',
                'totalGeneral',
                'totalDispose',
                'companyRequest',
                'companyName',
                'companyColor',
                'totalCompany',
                'Memo',
                'General',
                'Special',
                'Dispose',
                'Damaged',
                'Letter'
            ));
        // }
    }

    public function noneRequest()
    {
        return view('none_request');
    }
}
