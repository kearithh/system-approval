<?php

namespace App\Http\Middleware;

use App\Disposal;
use App\Model\GroupRequest;
use App\RequestForm;
use App\RequestHR;
use App\RequestMemo;
use App\DamagedLog;
use App\HRRequest;
use App\Loan;
use App\SaleAsset;
use App\ReturnBudget;
use App\SendReceive;
use App\RescheduleLoan;
use App\Mission;
use App\MissionItem;
use App\Training;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Authenticate extends Middleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);
        $groupRequest = new GroupRequest();

        // // get url
        // $uri = $request->url();
        // dd($uri);

        // if ($request->is('user/*/edit')) {
        //     dd($uri);
        // }

        //check url and rederect to page update profile

        if(Auth::user()->email == null && ! $request->is('user/*')){
            return redirect()->route('user.edit', ['user' => Auth::id()])->with("error","សូមបញ្ចូលអ៊ីម៉ែលក្រុមហ៊ុន និងផ្ទៀងផ្ទាត់ព័ត៌មានផ្ទាល់ខ្លួន");
        }

//        $eachMenuCompany = $groupRequest->totalEachMenuCompany();
//        dump($eachMenuCompany, @$eachMenuCompany['toapprove']['STSK']);

//        $memoRejectList = RequestMemo::rejectedList();
//        $seRejectList = RequestForm::rejectedList();
//        $geRejectList = RequestHR::rejectedList();
//        $disposalRejectList = Disposal::rejectedList();
//        $damagedRejectList = DamagedLog::rejectedList();
//
//        $memoApproval = RequestMemo::toApproveList();
//        $seApproval = RequestForm::toApproveList();
//        $geApproval = RequestHR::toApproveList();
//        $disposalApproval = Disposal::toApproveList();
//        $damagedApproval = DamagedLog::toApproveList();

//        if(Auth::id() == getCEO()->id) {
//            $eachMenuCompany['toapprove'] = 0; //$groupRequest->countToApproveListRequestEachCompanyForPresident();
//            $eachMenuCompany['rejected'] = 0;//$groupRequest->countRejectedRequestEachCompanyForPresident();
//            $eachMenuCompany['approved'] = 0;//$groupRequest->countApprovedRequestEachCompanyForPresident();
//
//            if (request()->segment(1) == 'toapprove' || request()->segment(1) == 'to_approve_report') {
//                $totalReportByCompany = $groupRequest->totalToApproveListOfReportByCompanyOfPresident();
//                // Total Report each deportment by company
//                if (@$_GET['company'] && @$_GET['type'] == 'report') {
//                    $totalReportEachDepartmentByCompanyPresident = $groupRequest->totalToApproveListOfReportEachDepartmentByCompanyPresident();
//
//                    $totalReportEachTagsByCompanyPresident = $groupRequest->totalToApproveListOfReportEachTagsByCompanyPresident();
//                }
//            } elseif (request()->segment(1) == 'rejected') {
//                $totalReportByCompany = $groupRequest->totalRejectedListOfReportByCompanyOfPresident();
//                // Total Report each deportment by company
//                if (@$_GET['company'] && @$_GET['type'] == 'report') {
//                    $totalReportEachDepartmentByCompanyPresident = $groupRequest->totalRejectedListOfReportEachDepartmentByCompanyPresident();
//                }
//            } elseif (request()->segment(1) == 'approved') {
//                $totalReportByCompany = $groupRequest->totalApprovedOfReportByCompanyOfPresident();
//                // Total Report each deportment by company
//                if (@$_GET['company'] && @$_GET['type'] == 'report') {
//                    $totalReportEachDepartmentByCompanyPresident = $groupRequest->totalApprovedListOfReportEachDepartmentByCompanyPresident();
//                }
//            }
//        }
//        dump(@$totalReportEachDepartmentByCompanyPresident);

//        $presidentApproval = [
//            'STSK' => RequestMemo::presidentApprove(1)->total()
//                    + RequestForm::presidentApprove(1)->total()
//                    + RequestHR::presidentApprove(1)->total()
//                    + Disposal::presidentApprove(1)->total()
//                    + DamagedLog::presidentApprove(1)->total()
//                    + HRRequest::presidentApprove(1)->total()
//                    + Loan::presidentApprove(1)->total()
//                    + SaleAsset::presidentApprove(1)->total()
//                    + ReturnBudget::presidentApprove(1)->total()
//                    + RescheduleLoan::presidentApprove(1)->total()
//                    + Mission::presidentApprove(1)->total()
//                    + MissionItem::presidentApprove(1)->total()
//                    + Training::presidentApprove(1)->total(),
//
//            'MFI' => RequestMemo::presidentApprove(2)->total()
//                    + RequestForm::presidentApprove(2)->total()
//                    + RequestHR::presidentApprove(2)->total()
//                    + Disposal::presidentApprove(2)->total()
//                    + DamagedLog::presidentApprove(2)->total()
//                    + HRRequest::presidentApprove(2)->total()
//                    + SaleAsset::presidentApprove(2)->total()
//                    + ReturnBudget::presidentApprove(2)->total()
//                    + Loan::presidentApprove(2)->total()
//                    + RescheduleLoan::presidentApprove(2)->total()
//                    + Mission::presidentApprove(2)->total()
//                    + MissionItem::presidentApprove(2)->total()
//                    + Training::presidentApprove(2)->total(),
//
//            'NGO' => RequestMemo::presidentApprove(3)->total()
//                    + RequestForm::presidentApprove(3)->total()
//                    + RequestHR::presidentApprove(3)->total()
//                    + Disposal::presidentApprove(3)->total()
//                    + DamagedLog::presidentApprove(3)->total()
//                    + HRRequest::presidentApprove(3)->total()
//                    + SaleAsset::presidentApprove(3)->total()
//                    + ReturnBudget::presidentApprove(3)->total()
//                    + Loan::presidentApprove(3)->total()
//                    + RescheduleLoan::presidentApprove(3)->total()
//                    + Mission::presidentApprove(3)->total()
//                    + MissionItem::presidentApprove(3)->total()
//                    + Training::presidentApprove(3)->total(),
//
//
//            'ORD' => RequestMemo::presidentApprove(4)->total()
//                    + RequestForm::presidentApprove(4)->total()
//                    + RequestHR::presidentApprove(4)->total()
//                    + Disposal::presidentApprove(4)->total()
//                    + DamagedLog::presidentApprove(4)->total()
//                    + HRRequest::presidentApprove(4)->total()
//                    + SaleAsset::presidentApprove(4)->total()
//                    + ReturnBudget::presidentApprove(4)->total()
//                    + Loan::presidentApprove(4)->total()
//                    + RescheduleLoan::presidentApprove(4)->total()
//                    + Mission::presidentApprove(4)->total()
//                    + MissionItem::presidentApprove(4)->total()
//                    + Training::presidentApprove(4)->total(),
//
//            'ST' => RequestMemo::presidentApprove(5)->total()
//                    + RequestForm::presidentApprove(5)->total()
//                    + RequestHR::presidentApprove(5)->total()
//                    + Disposal::presidentApprove(5)->total()
//                    + DamagedLog::presidentApprove(5)->total()
//                    + HRRequest::presidentApprove(5)->total()
//                    + SaleAsset::presidentApprove(5)->total()
//                    + ReturnBudget::presidentApprove(5)->total()
//                    + Loan::presidentApprove(5)->total()
//                    + RescheduleLoan::presidentApprove(5)->total()
//                    + Mission::presidentApprove(5)->total()
//                    + MissionItem::presidentApprove(5)->total()
//                    + Training::presidentApprove(5)->total(),
//
//            'MMI' => RequestMemo::presidentApprove(6)->total()
//                    + RequestForm::presidentApprove(6)->total()
//                    + RequestHR::presidentApprove(6)->total()
//                    + Disposal::presidentApprove(6)->total()
//                    + DamagedLog::presidentApprove(6)->total()
//                    + HRRequest::presidentApprove(6)->total()
//                    + SaleAsset::presidentApprove(6)->total()
//                    + ReturnBudget::presidentApprove(6)->total()
//                    + Loan::presidentApprove(6)->total()
//                    + RescheduleLoan::presidentApprove(6)->total()
//                    + Mission::presidentApprove(6)->total()
//                    + MissionItem::presidentApprove(6)->total()
//                    + Training::presidentApprove(6)->total(),
//
//            'MHT' => RequestMemo::presidentApprove(7)->total()
//                    + RequestForm::presidentApprove(7)->total()
//                    + RequestHR::presidentApprove(7)->total()
//                    + Disposal::presidentApprove(7)->total()
//                    + DamagedLog::presidentApprove(7)->total()
//                    + HRRequest::presidentApprove(7)->total()
//                    + SaleAsset::presidentApprove(7)->total()
//                    + ReturnBudget::presidentApprove(7)->total()
//                    + Loan::presidentApprove(7)->total()
//                    + RescheduleLoan::presidentApprove(7)->total()
//                    + Mission::presidentApprove(7)->total()
//                    + MissionItem::presidentApprove(7)->total()
//                    + Training::presidentApprove(7)->total(),
//
//            'TSP' => RequestMemo::presidentApprove(8)->total()
//                    + RequestForm::presidentApprove(8)->total()
//                    + RequestHR::presidentApprove(8)->total()
//                    + Disposal::presidentApprove(8)->total()
//                    + DamagedLog::presidentApprove(8)->total()
//                    + HRRequest::presidentApprove(8)->total()
//                    + SaleAsset::presidentApprove(8)->total()
//                    + ReturnBudget::presidentApprove(8)->total()
//                    + Loan::presidentApprove(8)->total()
//                    + RescheduleLoan::presidentApprove(8)->total()
//                    + Mission::presidentApprove(8)->total()
//                    + MissionItem::presidentApprove(8)->total()
//                    + Training::presidentApprove(8)->total(),
//        ];
//
//        $presidentRejected = [
//            'STSK' => RequestMemo::presidentRejectedList(1)->total()
//                    + RequestForm::presidentRejectedList(1)->total()
//                    + RequestHR::presidentRejectedList(1)->total()
//                    + Disposal::presidentRejectedList(1)->total()
//                    + DamagedLog::presidentRejectedList(1)->total()
//                    + HRRequest::presidentRejectedList(1)->total()
//                    + SaleAsset::presidentRejectedList(1)->total()
//                    + ReturnBudget::presidentRejectedList(1)->total()
//                    + Loan::presidentRejectedList(1)->total()
//                    + RescheduleLoan::presidentRejectedList(1)->total()
//                    + Mission::presidentRejectedList(1)->total()
//                    + MissionItem::presidentRejectedList(1)->total()
//                    + Training::presidentRejectedList(1)->total()
//                    + @$eachMenuCompany['reject']['STSK'],
//
//            'MFI' => RequestMemo::presidentRejectedList(2)->total()
//                    + RequestForm::presidentRejectedList(2)->total()
//                    + RequestHR::presidentRejectedList(2)->total()
//                    + Disposal::presidentRejectedList(2)->total()
//                    + DamagedLog::presidentRejectedList(2)->total()
//                    + HRRequest::presidentRejectedList(2)->total()
//                    + SaleAsset::presidentRejectedList(2)->total()
//                    + ReturnBudget::presidentRejectedList(2)->total()
//                    + Loan::presidentRejectedList(2)->total()
//                    + RescheduleLoan::presidentRejectedList(2)->total()
//                    + Mission::presidentRejectedList(2)->total()
//                    + MissionItem::presidentRejectedList(2)->total()
//                    + Training::presidentRejectedList(2)->total()
//                    + @$eachMenuCompany['reject']['MFI'],
//
//            'NGO' => RequestMemo::presidentRejectedList(3)->total()
//                    + RequestForm::presidentRejectedList(3)->total()
//                    + RequestHR::presidentRejectedList(3)->total()
//                    + Disposal::presidentRejectedList(3)->total()
//                    + DamagedLog::presidentRejectedList(3)->total()
//                    + HRRequest::presidentRejectedList(3)->total()
//                    + SaleAsset::presidentRejectedList(3)->total()
//                    + ReturnBudget::presidentRejectedList(3)->total()
//                    + Loan::presidentRejectedList(3)->total()
//                    + RescheduleLoan::presidentRejectedList(3)->total()
//                    + Mission::presidentRejectedList(3)->total()
//                    + MissionItem::presidentRejectedList(3)->total()
//                    + Training::presidentRejectedList(3)->total()
//                    + @$eachMenuCompany['reject']['NGO'],
//
//            'ORD' => RequestMemo::presidentRejectedList(4)->total()
//                    + RequestForm::presidentRejectedList(4)->total()
//                    + RequestHR::presidentRejectedList(4)->total()
//                    + Disposal::presidentRejectedList(4)->total()
//                    + DamagedLog::presidentRejectedList(4)->total()
//                    + HRRequest::presidentRejectedList(4)->total()
//                    + SaleAsset::presidentRejectedList(4)->total()
//                    + ReturnBudget::presidentRejectedList(4)->total()
//                    + Loan::presidentRejectedList(4)->total()
//                    + RescheduleLoan::presidentRejectedList(4)->total()
//                    + Mission::presidentRejectedList(4)->total()
//                    + MissionItem::presidentRejectedList(4)->total()
//                    + Training::presidentRejectedList(4)->total()
//                    + @$eachMenuCompany['reject']['ORD'],
//
//            'ST' => RequestMemo::presidentRejectedList(5)->total()
//                    + RequestForm::presidentRejectedList(5)->total()
//                    + RequestHR::presidentRejectedList(5)->total()
//                    + Disposal::presidentRejectedList(5)->total()
//                    + DamagedLog::presidentRejectedList(5)->total()
//                    + HRRequest::presidentRejectedList(5)->total()
//                    + SaleAsset::presidentRejectedList(5)->total()
//                    + ReturnBudget::presidentRejectedList(5)->total()
//                    + Loan::presidentRejectedList(5)->total()
//                    + RescheduleLoan::presidentRejectedList(5)->total()
//                    + Mission::presidentRejectedList(5)->total()
//                    + MissionItem::presidentRejectedList(5)->total()
//                    + Training::presidentRejectedList(5)->total()
//                    + @$eachMenuCompany['reject']['ST'],
//
//            'MMI' => RequestMemo::presidentRejectedList(6)->total()
//                    + RequestForm::presidentRejectedList(6)->total()
//                    + RequestHR::presidentRejectedList(6)->total()
//                    + Disposal::presidentRejectedList(6)->total()
//                    + DamagedLog::presidentRejectedList(6)->total()
//                    + HRRequest::presidentRejectedList(6)->total()
//                    + SaleAsset::presidentRejectedList(6)->total()
//                    + ReturnBudget::presidentRejectedList(6)->total()
//                    + Loan::presidentRejectedList(6)->total()
//                    + RescheduleLoan::presidentRejectedList(6)->total()
//                    + Mission::presidentRejectedList(6)->total()
//                    + MissionItem::presidentRejectedList(6)->total()
//                    + Training::presidentRejectedList(6)->total()
//                    + @$eachMenuCompany['reject']['MMI'],
//
//            'MHT' => RequestMemo::presidentRejectedList(7)->total()
//                    + RequestForm::presidentRejectedList(7)->total()
//                    + RequestHR::presidentRejectedList(7)->total()
//                    + Disposal::presidentRejectedList(7)->total()
//                    + DamagedLog::presidentRejectedList(7)->total()
//                    + HRRequest::presidentRejectedList(7)->total()
//                    + SaleAsset::presidentRejectedList(7)->total()
//                    + ReturnBudget::presidentRejectedList(7)->total()
//                    + Loan::presidentRejectedList(7)->total()
//                    + RescheduleLoan::presidentRejectedList(7)->total()
//                    + Mission::presidentRejectedList(7)->total()
//                    + MissionItem::presidentRejectedList(7)->total()
//                    + Training::presidentRejectedList(7)->total()
//                    + @$eachMenuCompany['reject']['MHT'],
//
//            'TSP' => RequestMemo::presidentRejectedList(8)->total()
//                    + RequestForm::presidentRejectedList(8)->total()
//                    + RequestHR::presidentRejectedList(8)->total()
//                    + Disposal::presidentRejectedList(8)->total()
//                    + DamagedLog::presidentRejectedList(8)->total()
//                    + HRRequest::presidentRejectedList(8)->total()
//                    + SaleAsset::presidentRejectedList(8)->total()
//                    + ReturnBudget::presidentRejectedList(8)->total()
//                    + Loan::presidentRejectedList(8)->total()
//                    + RescheduleLoan::presidentRejectedList(8)->total()
//                    + Mission::presidentRejectedList(8)->total()
//                    + MissionItem::presidentRejectedList(8)->total()
//                    + Training::presidentRejectedList(8)->total()
//                    + @$eachMenuCompany['reject']['TSP'],
//        ];
//
//        $presidentApproved = [
//            'STSK' => RequestMemo::presidentApproved(1)->total()
//                    + RequestForm::presidentApproved(1)->total()
//                    + RequestHR::presidentApproved(1)->total()
//                    + Disposal::presidentApproved(1)->total()
//                    + DamagedLog::presidentApproved(1)->total()
//                    + HRRequest::presidentApproved(1)->total()
//                    + SaleAsset::presidentApproved(1)->total()
//                    + ReturnBudget::presidentApproved(1)->total()
//                    + Loan::presidentApproved(1)->total()
//                    + RescheduleLoan::presidentApproved(1)->total()
//                    + Mission::presidentApproved(1)->total()
//                    + MissionItem::presidentApproved(1)->total()
//                    + Training::presidentApproved(1)->total()
//                    + @$eachMenuCompany['approved']['STSK'],
//
//
//            'MFI' => RequestMemo::presidentApproved(2)->total()
//                    + RequestForm::presidentApproved(2)->total()
//                    + RequestHR::presidentApproved(2)->total()
//                    + Disposal::presidentApproved(2)->total()
//                    + DamagedLog::presidentApproved(2)->total()
//                    + HRRequest::presidentApproved(2)->total()
//                    + Loan::presidentApproved(2)->total()
//                    + SaleAsset::presidentApproved(2)->total()
//                    + ReturnBudget::presidentApproved(2)->total()
//                    + RescheduleLoan::presidentApproved(2)->total()
//                    + Mission::presidentApproved(2)->total()
//                    + MissionItem::presidentApproved(2)->total()
//                    + Training::presidentApproved(2)->total()
//                    + @$eachMenuCompany['approved']['MFI'],
//
//            'NGO' => RequestMemo::presidentApproved(3)->total()
//                    + RequestForm::presidentApproved(3)->total()
//                    + RequestHR::presidentApproved(3)->total()
//                    + Disposal::presidentApproved(3)->total()
//                    + DamagedLog::presidentApproved(3)->total()
//                    + HRRequest::presidentApproved(3)->total()
//                    + Loan::presidentApproved(3)->total()
//                    + SaleAsset::presidentApproved(3)->total()
//                    + ReturnBudget::presidentApproved(3)->total()
//                    + RescheduleLoan::presidentApproved(3)->total()
//                    + Mission::presidentApproved(3)->total()
//                    + MissionItem::presidentApproved(3)->total()
//                    + Training::presidentApproved(3)->total()
//                    + @$eachMenuCompany['approved']['NGO'],
//
//            'ORD' => RequestMemo::presidentApproved(4)->total()
//                    + RequestForm::presidentApproved(4)->total()
//                    + RequestHR::presidentApproved(4)->total()
//                    + Disposal::presidentApproved(4)->total()
//                    + DamagedLog::presidentApproved(4)->total()
//                    + HRRequest::presidentApproved(4)->total()
//                    + Loan::presidentApproved(4)->total()
//                    + SaleAsset::presidentApproved(4)->total()
//                    + ReturnBudget::presidentApproved(4)->total()
//                    + RescheduleLoan::presidentApproved(4)->total()
//                    + Mission::presidentApproved(4)->total()
//                    + MissionItem::presidentApproved(4)->total()
//                    + Training::presidentApproved(4)->total()
//                    + @$eachMenuCompany['approved']['ORD'],
//
//            'ST' => RequestMemo::presidentApproved(5)->total()
//                    + RequestForm::presidentApproved(5)->total()
//                    + RequestHR::presidentApproved(5)->total()
//                    + Disposal::presidentApproved(5)->total()
//                    + DamagedLog::presidentApproved(5)->total()
//                    + HRRequest::presidentApproved(5)->total()
//                    + Loan::presidentApproved(5)->total()
//                    + SaleAsset::presidentApproved(5)->total()
//                    + ReturnBudget::presidentApproved(5)->total()
//                    + RescheduleLoan::presidentApproved(5)->total()
//                    + Mission::presidentApproved(5)->total()
//                    + MissionItem::presidentApproved(5)->total()
//                    + Training::presidentApproved(5)->total()
//                    + @$eachMenuCompany['approved']['ST'],
//
//            'MMI' => RequestMemo::presidentApproved(6)->total()
//                    + RequestForm::presidentApproved(6)->total()
//                    + RequestHR::presidentApproved(6)->total()
//                    + Disposal::presidentApproved(6)->total()
//                    + DamagedLog::presidentApproved(6)->total()
//                    + HRRequest::presidentApproved(6)->total()
//                    + Loan::presidentApproved(6)->total()
//                    + SaleAsset::presidentApproved(6)->total()
//                    + ReturnBudget::presidentApproved(6)->total()
//                    + RescheduleLoan::presidentApproved(6)->total()
//                    + Mission::presidentApproved(6)->total()
//                    + MissionItem::presidentApproved(6)->total()
//                    + Training::presidentApproved(6)->total()
//                    + @$eachMenuCompany['approved']['MMI'],
//
//            'MHT' => RequestMemo::presidentApproved(7)->total()
//                    + RequestForm::presidentApproved(7)->total()
//                    + RequestHR::presidentApproved(7)->total()
//                    + Disposal::presidentApproved(7)->total()
//                    + DamagedLog::presidentApproved(7)->total()
//                    + HRRequest::presidentApproved(7)->total()
//                    + Loan::presidentApproved(7)->total()
//                    + SaleAsset::presidentApproved(7)->total()
//                    + ReturnBudget::presidentApproved(7)->total()
//                    + RescheduleLoan::presidentApproved(7)->total()
//                    + Mission::presidentApproved(7)->total()
//                    + MissionItem::presidentApproved(7)->total()
//                    + Training::presidentApproved(7)->total()
//                    + @$eachMenuCompany['approved']['MHT'],
//
//            'TSP' => RequestMemo::presidentApproved(8)->total()
//                    + RequestForm::presidentApproved(8)->total()
//                    + RequestHR::presidentApproved(8)->total()
//                    + Disposal::presidentApproved(8)->total()
//                    + DamagedLog::presidentApproved(8)->total()
//                    + HRRequest::presidentApproved(8)->total()
//                    + Loan::presidentApproved(8)->total()
//                    + SaleAsset::presidentApproved(8)->total()
//                    + ReturnBudget::presidentApproved(8)->total()
//                    + RescheduleLoan::presidentApproved(8)->total()
//                    + Mission::presidentApproved(8)->total()
//                    + MissionItem::presidentApproved(8)->total()
//                    + Training::presidentApproved(8)->total()
//                    + @$eachMenuCompany['approved']['TSP'],
//        ];
//
//        $presidentPending = [
//            'STSK' => RequestMemo::presidentpendingList(1)->total()
//                    + RequestForm::presidentpendingList(1)->total()
//                    + RequestHR::presidentpendingList(1)->total()
//                    + Disposal::presidentpendingList(1)->total()
//                    + DamagedLog::presidentpendingList(1)->total()
//                    + HRRequest::presidentpendingList(1)->total()
//                    + Loan::presidentpendingList(1)->total()
//                    + SaleAsset::presidentpendingList(1)->total()
//                    + ReturnBudget::presidentpendingList(1)->total()
//                    + RescheduleLoan::presidentpendingList(1)->total()
//                    + Mission::presidentpendingList(1)->total()
//                    + MissionItem::presidentpendingList(1)->total()
//                    + Training::presidentpendingList(1)->total()
//                    + @$eachMenuCompany['pending']['STSK'],
//
//            'MFI' => RequestMemo::presidentpendingList(2)->total()
//                    + RequestForm::presidentpendingList(2)->total()
//                    + RequestHR::presidentpendingList(2)->total()
//                    + Disposal::presidentpendingList(2)->total()
//                    + DamagedLog::presidentpendingList(2)->total()
//                    + HRRequest::presidentpendingList(2)->total()
//                    + Loan::presidentpendingList(2)->total()
//                    + SaleAsset::presidentpendingList(2)->total()
//                    + ReturnBudget::presidentpendingList(2)->total()
//                    + RescheduleLoan::presidentpendingList(2)->total()
//                    + Mission::presidentpendingList(2)->total()
//                    + MissionItem::presidentpendingList(2)->total()
//                    + Training::presidentpendingList(2)->total()
//                    + @(integer)$eachMenuCompany['pending']['MFI'],
//
//            'NGO' => RequestMemo::presidentpendingList(3)->total()
//                    + RequestForm::presidentpendingList(3)->total()
//                    + RequestHR::presidentpendingList(3)->total()
//                    + Disposal::presidentpendingList(3)->total()
//                    + DamagedLog::presidentpendingList(3)->total()
//                    + HRRequest::presidentpendingList(3)->total()
//                    + Loan::presidentpendingList(3)->total()
//                    + SaleAsset::presidentpendingList(3)->total()
//                    + ReturnBudget::presidentpendingList(3)->total()
//                    + RescheduleLoan::presidentpendingList(3)->total()
//                    + Mission::presidentpendingList(3)->total()
//                    + MissionItem::presidentpendingList(3)->total()
//                    + Training::presidentpendingList(3)->total()
//                    + @(integer)$eachMenuCompany['pending']['NGO'],
//
//            'ORD' => RequestMemo::presidentpendingList(4)->total()
//                    + RequestForm::presidentpendingList(4)->total()
//                    + RequestHR::presidentpendingList(4)->total()
//                    + Disposal::presidentpendingList(4)->total()
//                    + DamagedLog::presidentpendingList(4)->total()
//                    + HRRequest::presidentpendingList(4)->total()
//                    + Loan::presidentpendingList(4)->total()
//                    + SaleAsset::presidentpendingList(4)->total()
//                    + ReturnBudget::presidentpendingList(4)->total()
//                    + RescheduleLoan::presidentpendingList(4)->total()
//                    + Mission::presidentpendingList(4)->total()
//                    + MissionItem::presidentpendingList(4)->total()
//                    + Training::presidentpendingList(4)->total()
//                    + @(integer)$eachMenuCompany['pending']['ORD'],
//
//            'ST' => RequestMemo::presidentpendingList(5)->total()
//                    + RequestForm::presidentpendingList(5)->total()
//                    + RequestHR::presidentpendingList(5)->total()
//                    + Disposal::presidentpendingList(5)->total()
//                    + DamagedLog::presidentpendingList(5)->total()
//                    + HRRequest::presidentpendingList(5)->total()
//                    + Loan::presidentpendingList(5)->total()
//                    + SaleAsset::presidentpendingList(5)->total()
//                    + ReturnBudget::presidentpendingList(5)->total()
//                    + RescheduleLoan::presidentpendingList(5)->total()
//                    + Mission::presidentpendingList(5)->total()
//                    + MissionItem::presidentpendingList(5)->total()
//                    + Training::presidentpendingList(5)->total()
//                    + @(integer)$eachMenuCompany['pending']['ST'],
//
//            'MMI' => RequestMemo::presidentpendingList(6)->total()
//                    + RequestForm::presidentpendingList(6)->total()
//                    + RequestHR::presidentpendingList(6)->total()
//                    + Disposal::presidentpendingList(6)->total()
//                    + DamagedLog::presidentpendingList(6)->total()
//                    + HRRequest::presidentpendingList(6)->total()
//                    + Loan::presidentpendingList(6)->total()
//                    + SaleAsset::presidentpendingList(6)->total()
//                    + ReturnBudget::presidentpendingList(6)->total()
//                    + RescheduleLoan::presidentpendingList(6)->total()
//                    + Mission::presidentpendingList(6)->total()
//                    + MissionItem::presidentpendingList(6)->total()
//                    + Training::presidentpendingList(6)->total()
//                    + @(integer)$eachMenuCompany['pending']['MMI'],
//
//            'MHT' => RequestMemo::presidentpendingList(7)->total()
//                    + RequestForm::presidentpendingList(7)->total()
//                    + RequestHR::presidentpendingList(7)->total()
//                    + Disposal::presidentpendingList(7)->total()
//                    + DamagedLog::presidentpendingList(7)->total()
//                    + HRRequest::presidentpendingList(7)->total()
//                    + Loan::presidentpendingList(7)->total()
//                    + SaleAsset::presidentpendingList(7)->total()
//                    + ReturnBudget::presidentpendingList(7)->total()
//                    + RescheduleLoan::presidentpendingList(7)->total()
//                    + Mission::presidentpendingList(7)->total()
//                    + MissionItem::presidentpendingList(7)->total()
//                    + Training::presidentpendingList(7)->total()
//                    + @(integer)$eachMenuCompany['pending']['MHT'],
//
//            'TSP' => RequestMemo::presidentpendingList(8)->total()
//                    + RequestForm::presidentpendingList(8)->total()
//                    + RequestHR::presidentpendingList(8)->total()
//                    + Disposal::presidentpendingList(8)->total()
//                    + DamagedLog::presidentpendingList(8)->total()
//                    + HRRequest::presidentpendingList(8)->total()
//                    + Loan::presidentpendingList(8)->total()
//                    + SaleAsset::presidentpendingList(8)->total()
//                    + ReturnBudget::presidentpendingList(8)->total()
//                    + RescheduleLoan::presidentpendingList(8)->total()
//                    + Mission::presidentpendingList(8)->total()
//                    + MissionItem::presidentpendingList(8)->total()
//                    + Training::presidentpendingList(8)->total()
//                    + @(integer)$eachMenuCompany['pending']['TSP'],
//        ];
///////////////////////////////////////////////////////////////////////////////////////////////
//        // Get department by company
////        if (@$_GET['company']) {
//            $type = config('app.report');
//            $status = null;
//            $reviewerOrApproverStatus = null;
//            $userId = Auth::id();
//            $labelType = '';
//            $company = DB::table('companies')->where('short_name_en', @$_GET['company'])->first();
//            $companyId = @$company->id;
//            $departmentShortName = @$_GET['department'];
//            $companyDepartment = DB::table('company_departments')->where('company_id', $companyId)->get();
//
//
//
//
//            $excludeOwner = false;
//            $uriSegment = request()->segment(1);
//            if ($uriSegment == 'pending') {
//                $status = config('app.pending');
//                $labelType = 'badge-warning';
//            } elseif ($uriSegment == 'toapprove') {
//                $status = config('app.pending');
//                $reviewerOrApproverStatus = config('app.pending');
//                $labelType = 'badge-info';
////                $excludeOwner = true;
//            } elseif ($uriSegment == 'reject') {
//                $status = config('app.rejected');
//                $reviewerOrApproverStatus = config('app.rejected');
//                $labelType = 'badge-danger';
//            } elseif ($uriSegment == 'approved') {
//                $status = config('app.approved');
//                $labelType = 'badge-success';
//            }
//
//            // totalReport
//            // totalReportEachCompany
//            // totalReportEachDepartment
//            // totalReportEachTags
//
//            $totalReportEachCompany = $groupRequest->getTotalRelatedRequestByUser(
//                $type,
//                $companyId,
//                null,
//                null,
//                $status,
//                $userId,
//                $reviewerOrApproverStatus,
//                $excludeOwner
//            );
//
////            dd($companyDepartment);
//
//            // Total Report each department
//        $totalReportEachDepartment = [];
//        foreach ($companyDepartment as $key => $item) {
//            $item->amount = $groupRequest->getTotalRelatedRequestByUser(
//                $type,
//                $companyId,
//                $item->id,
//                null,
//                $status,
//                $userId,
//                $reviewerOrApproverStatus,
//                $excludeOwner
//            );
//
//
//            $data = $groupRequest->getTotalRequestEachDepartment($type, $companyId, $status);
//            $companyDepartment->map(function ($item) use($data) {
//                // todo need update mapping fx from department_name to department_id
//                $value =  $data->where('department_name', $item->name_km)->first();
//                $item->total = @$value->total;
//                return $item;
//            });
//            if ($departmentShortName) {
//                $department = $companyDepartment->where('short_name', $departmentShortName)->first();
//                $departmentId = @$department->id;
//            }
//
//            $reportTotal = [
//                'label' => $labelType,
//                'report_each_company' => $totalReportEachCompany,
//                'report_each_department' => $totalReportEachDepartment,
//                'report_each_tags' => $totalReportEachCompany,
//            ];
//
//
//
//            $company = DB::table('companies')->where('short_name_en', @$_GET['company'])->first();
//            $companyId = @$company->id;
//            $companyDepartment = DB::table('company_departments')
//                ->where('company_id', $companyId)
//                ->get();
//
//            $status = config('app.pending');
//            $uriSegment = request()->segment(1);
//            if ($uriSegment == 'pending') {
//                $status = config('app.pending');
//            } elseif ($uriSegment == 'toapprove') {
//                $status = config('app.pending');
//
//            } elseif ($uriSegment == 'reject') {
//                $status = config('app.rejected');
//
//            } elseif ($uriSegment == 'approved') {
//                $status = config('app.approved');
//            }
//
//
//            $type = config('app.report');
//            $tags = @strtolower($_GET['tags']);
//            $departmentShortName = @$_GET['department'];
//            $groupRequest = new GroupRequest();
//            $data = $groupRequest->getTotalRequestEachDepartment($type, $companyId, $status);
//            $companyDepartment->map(function ($item) use($data) {
//                // todo need update mapping fx from department_name to department_id
//                $value =  $data->where('department_name', $item->name_km)->first();
//                $item->total = @$value->total;
//                return $item;
//            });
//            if ($departmentShortName) {
//                $department = $companyDepartment->where('short_name', $departmentShortName)->first();
//                $departmentId = @$department->id;
//            }
//            $totalGroupRequestEachTags = $groupRequest->getTotalRequestEachTags($type, $companyId, @$departmentId, $status);
//            $settingTags = config('app.tags');
//            foreach ($settingTags as $key => $value) {
//                $data =  $totalGroupRequestEachTags->where('tags', $value->slug)->first();
//                $settingTags[$key]->total = @$data->total;
//            }
//        }
/////////////////////////////////////////////////////////////////////////////////////////

//        $totalReportTypeEachMenu = $groupRequest->totalEachMenuCompanyType();

//        $companyDepartment = $groupRequest->totalEachMenuCompanyTypeDepartment();

        if (request()->segment(1) == 'toapprove' || request()->segment(1) == 'to_approve_report' || request()->segment(1) == 'to_approve_group_support') {
            $label = 'badge-info';
        } elseif (request()->segment(1) == 'reject') {
            $label = 'badge-danger';
        } elseif (request()->segment(1) == 'approved') {
            $label = 'badge-success';
        } elseif (request()->segment(1) == 'pending') {
            $label = 'badge-warning';
        }


        view()->share('viewShare', [
            'president_approval' => @$presidentApproval,
            'president_rejected' => @$presidentRejected,
            'president_approved' => @$presidentApproved,
            'president_pending' => @$presidentPending,

            'memo_approval' => @$memoApproval,
            'se_approval' => @$seApproval,
            'ge_approval' => @$geApproval,
            'disposal_approval' => @$disposalApproval,
            'damagedlog_approval' => @$damagedApproval,

            'reject_memo_approval' => @$memoRejectList,
            'reject_se_approval' => @$seRejectList,
            'reject_ge_approval' => @$geRejectList,
            'reject_disposal_approval' => @$disposalRejectList,
            'reject_damagedlog_approval' => @$damagedRejectList,
            'company_departments' => @$totalReportEachDepartmentByCompanyPresident,
            'setting_tags' => @$totalReportEachTagsByCompanyPresident,
            'report' => @$totalReportTypeEachMenu,


            'label' => @$label,
            'total_report_by_company' => @$totalReportByCompany
        ]);
        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
