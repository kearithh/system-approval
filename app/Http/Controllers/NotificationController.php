<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Company;
use App\Department;
use App\DamagedLog;
use App\Disposal;
use App\HRRequest;
use App\Loan;
use App\Mission;
use App\MissionItem;
use App\MissionClearance;
use App\Model\GroupRequest;
use App\RequestForm;
use App\RequestPR;
use App\RequestPO;
use App\RequestGRN;
use App\RequestHR;
use App\RequestMemo;
use App\RescheduleLoan;
use App\ReturnBudget;
use App\SaleAsset;
use App\SendReceive;
use App\Training;
use App\RequestOT;
use App\Penalty;
use App\EmployeePenalty;
use App\CashAdvance;
use App\Resign;
use App\GeneralRequest;
use App\RequestCreateUser;
use App\TransferAsset;
use App\SettingReviewerApprover;
use App\Association;
use App\Survey;
use App\CustomLetter;
use App\Policy;
use App\RequestDisableUser;
use App\BorrowingLoan;
use App\WithdrawalCollateral;
use App\VillageLoan;
use App\RequestGasoline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class NotificationController extends Controller
{
    private function president(){
        ini_set("memory_limit", -1);
        $company = \request()->company;
        $type = \request()->type;
        $data = [
            'company' => $company,
            'type' => $type,
        ];

        $groupRequest = new GroupRequest();
        $data['report_menu'] = $groupRequest->countToApproveListRequestEachCompanyForPresident();
        $eachMenuCompany['rejected'] = $groupRequest->countRejectedRequestEachCompanyForPresident();
        $eachMenuCompany['approved'] = $groupRequest->countApprovedRequestEachCompanyForPresident();
//        $totalReportEachDepartmentByCompanyPresident = $groupRequest->totalToApproveListOfReportEachDepartmentByCompanyPresident($company, $type);
//        $totalReportEachTagsByCompanyPresident = $groupRequest->getToApproveListOfReportEachTagsByCompanyPresident();

        $data['to_approve'] = [
            // 'STSK' => RequestMemo::presidentApprove(1)->total()
            //     + RequestForm::presidentApprove(1)->total()
            //     + RequestHR::presidentApprove(1)->total()
            //     + Disposal::presidentApprove(1)->total()
            //     + DamagedLog::presidentApprove(1)->total()
            //     + HRRequest::presidentApprove(1)->total()
            //     + SaleAsset::presidentApprove(1)->total()
            //     + SendReceive::presidentApprove(1)->total()
            //     + ReturnBudget::presidentApprove(1)->total()
            //     + Mission::presidentApprove(1)->total()
            //     + MissionItem::presidentApprove(1)->total()
            //     + MissionClearance::CountToApprove(1)
            //     + Training::presidentApprove(1)->total()
            //     + RequestOT::presidentApprove(1)->total()
            //     + EmployeePenalty::presidentApprove(1)->total()
            //     + CashAdvance::presidentApprove(1)->total()
            //     + Resign::presidentApprove(1)->total()
            //     + RequestCreateUser::CountToApprove(1)
            //     + TransferAsset::CountToApprove(1)
            //     + SettingReviewerApprover::CountToApprove(1)
            //     + CustomLetter::CountToApprove(1)
            //     + Policy::CountToApprove(1)
            //     + RequestGasoline::CountToApprove(1)
            //     + RequestDisableUser::CountToApprove(1),

            // 'MFI' => RequestMemo::presidentApprove(2)->total()
            //     + RequestForm::presidentApprove(2)->total()
            //     + RequestHR::presidentApprove(2)->total()
            //     + Disposal::presidentApprove(2)->total()
            //     + DamagedLog::presidentApprove(2)->total()
            //     + HRRequest::presidentApprove(2)->total()
            //     + SaleAsset::presidentApprove(2)->total()
            //     + SendReceive::presidentApprove(2)->total()
            //     + ReturnBudget::presidentApprove(2)->total()
            //     + Loan::CountToApprove(2)
            //     + RescheduleLoan::presidentApprove(2)->total()
            //     + Mission::presidentApprove(2)->total()
            //     + MissionItem::presidentApprove(2)->total()
            //     + MissionClearance::CountToApprove(2)
            //     + Training::presidentApprove(2)->total()
            //     + RequestOT::presidentApprove(2)->total()
            //     + Penalty::presidentApprove(2)->total()
            //     + Penalty::presidentApproveInterest(2)->total()
            //     + Penalty::presidentApproveAssociation(2)->total()
            //     + EmployeePenalty::presidentApprove(2)->total()
            //     + CashAdvance::presidentApprove(2)->total()
            //     + Resign::presidentApprove(2)->total()
            //     + GeneralRequest::CountToApprove(2)
            //     + RequestCreateUser::CountToApprove(2)
            //     + TransferAsset::CountToApprove(2)
            //     + SettingReviewerApprover::CountToApprove(2)
            //     + Association::CountToApprove(2)
            //     + Survey::CountToApprove(2)
            //     + CustomLetter::CountToApprove(2)
            //     + Policy::CountToApprove(2)
            //     + RequestDisableUser::CountToApprove(2)
            //     + WithdrawalCollateral::CountToApprove(2)
            //     + RequestGasoline::CountToApprove(2)
            //     + VillageLoan::CountToApprove(2),

            // 'NGO' => RequestMemo::presidentApprove(3)->total()
            //     + RequestForm::presidentApprove(3)->total()
            //     + RequestHR::presidentApprove(3)->total()
            //     + Disposal::presidentApprove(3)->total()
            //     + DamagedLog::presidentApprove(3)->total()
            //     + HRRequest::presidentApprove(3)->total()
            //     + SaleAsset::presidentApprove(3)->total()
            //     + SendReceive::presidentApprove(3)->total()
            //     + ReturnBudget::presidentApprove(3)->total()
            //     + Loan::CountToApprove(3)
            //     + RescheduleLoan::presidentApprove(3)->total()
            //     + Mission::presidentApprove(3)->total()
            //     + MissionItem::presidentApprove(3)->total()
            //     + MissionClearance::CountToApprove(3)
            //     + Training::presidentApprove(3)->total()
            //     + RequestOT::presidentApprove(3)->total()
            //     + Penalty::presidentApprove(3)->total()
            //     + Penalty::presidentApproveInterest(3)->total()
            //     + Penalty::presidentApproveAssociation(3)->total()
            //     + EmployeePenalty::presidentApprove(3)->total()
            //     + CashAdvance::presidentApprove(3)->total()
            //     + Resign::presidentApprove(3)->total()
            //     + GeneralRequest::CountToApprove(3)
            //     + RequestCreateUser::CountToApprove(3)
            //     + TransferAsset::CountToApprove(3)
            //     + SettingReviewerApprover::CountToApprove(3)
            //     + Association::CountToApprove(3)
            //     + Survey::CountToApprove(3)
            //     + CustomLetter::CountToApprove(3)
            //     + Policy::CountToApprove(3)
            //     + RequestDisableUser::CountToApprove(3)
            //     + WithdrawalCollateral::CountToApprove(3)
            //     + RequestGasoline::CountToApprove(3)
            //     + VillageLoan::CountToApprove(3),

            // 'PWS' => RequestMemo::presidentApprove(14)->total()
            //     + RequestForm::presidentApprove(14)->total()
            //     + RequestHR::presidentApprove(14)->total()
            //     + Disposal::presidentApprove(14)->total()
            //     + DamagedLog::presidentApprove(14)->total()
            //     + HRRequest::presidentApprove(14)->total()
            //     + SaleAsset::presidentApprove(14)->total()
            //     + SendReceive::presidentApprove(14)->total()
            //     + ReturnBudget::presidentApprove(14)->total()
            //     + Loan::CountToApprove(14)
            //     + RescheduleLoan::presidentApprove(14)->total()
            //     + Mission::presidentApprove(14)->total()
            //     + MissionItem::presidentApprove(14)->total()
            //     + MissionClearance::CountToApprove(14)
            //     + Training::presidentApprove(14)->total()
            //     + RequestOT::presidentApprove(14)->total()
            //     + Penalty::presidentApprove(14)->total()
            //     + Penalty::presidentApproveInterest(14)->total()
            //     + Penalty::presidentApproveAssociation(14)->total()
            //     + EmployeePenalty::presidentApprove(14)->total()
            //     + CashAdvance::presidentApprove(14)->total()
            //     + Resign::presidentApprove(14)->total()
            //     + GeneralRequest::CountToApprove(14)
            //     + RequestCreateUser::CountToApprove(14)
            //     + TransferAsset::CountToApprove(14)
            //     + SettingReviewerApprover::CountToApprove(14)
            //     + Association::CountToApprove(14)
            //     + Survey::CountToApprove(14)
            //     + CustomLetter::CountToApprove(14)
            //     + Policy::CountToApprove(14)
            //     + RequestDisableUser::CountToApprove(14),
            //     + WithdrawalCollateral::CountToApprove(14)
            //     + RequestGasoline::CountToApprove(14)
            //     + VillageLoan::CountToApprove(14),

            'ORD' => RequestMemo::presidentApprove(4)->total()
                + RequestForm::presidentApprove(4)->total()
                + RequestPR::presidentApprove(4)->total()
                + RequestPO::presidentApprove(4)->total()
                + RequestGRN::presidentApprove(4)->total()
                + RequestHR::presidentApprove(4)->total()
                + Disposal::presidentApprove(4)->total()
                + DamagedLog::presidentApprove(4)->total()
                + HRRequest::presidentApprove(4)->total()
                + SaleAsset::presidentApprove(4)->total()
                + SendReceive::presidentApprove(4)->total()
                + ReturnBudget::presidentApprove(4)->total()
                + Training::presidentApprove(4)->total()
                + RequestOT::presidentApprove(4)->total()
                + EmployeePenalty::presidentApprove(4)->total()
                + CashAdvance::presidentApprove(4)->total()
                + Resign::presidentApprove(4)->total()
                + RequestCreateUser::CountToApprove(4)
                + TransferAsset::CountToApprove(4)
                + SettingReviewerApprover::CountToApprove(4)
                + CustomLetter::CountToApprove(4)
                + Policy::CountToApprove(4)
                + RequestDisableUser::CountToApprove(4),

            'ORD2' => RequestMemo::presidentApprove(16)->total()
                + RequestForm::presidentApprove(16)->total()
                + RequestPR::presidentApprove(16)->total()
                + RequestPO::presidentApprove(16)->total()
                + RequestGRN::presidentApprove(16)->total()
                + RequestHR::presidentApprove(16)->total()
                + Disposal::presidentApprove(16)->total()
                + DamagedLog::presidentApprove(16)->total()
                + HRRequest::presidentApprove(16)->total()
                + SaleAsset::presidentApprove(16)->total()
                + SendReceive::presidentApprove(16)->total()
                + ReturnBudget::presidentApprove(16)->total()
                + Training::presidentApprove(16)->total()
                + RequestOT::presidentApprove(16)->total()
                + EmployeePenalty::presidentApprove(16)->total()
                + CashAdvance::presidentApprove(16)->total()
                + Resign::presidentApprove(16)->total()
                + RequestCreateUser::CountToApprove(16)
                + TransferAsset::CountToApprove(16)
                + SettingReviewerApprover::CountToApprove(16)
                + CustomLetter::CountToApprove(16)
                + Policy::CountToApprove(16)
                + RequestDisableUser::CountToApprove(16),

            // 'ST' => RequestMemo::presidentApprove(5)->total()
            //     + RequestForm::presidentApprove(5)->total()
            //     + RequestHR::presidentApprove(5)->total()
            //     + Disposal::presidentApprove(5)->total()
            //     + DamagedLog::presidentApprove(5)->total()
            //     + HRRequest::presidentApprove(5)->total()
            //     + SaleAsset::presidentApprove(5)->total()
            //     + SendReceive::presidentApprove(5)->total()
            //     + ReturnBudget::presidentApprove(5)->total()
            //     + Training::presidentApprove(5)->total()
            //     + RequestOT::presidentApprove(5)->total()
            //     + EmployeePenalty::presidentApprove(5)->total()
            //     + CashAdvance::presidentApprove(5)->total()
            //     + Resign::presidentApprove(5)->total()
            //     + RequestCreateUser::CountToApprove(5)
            //     + TransferAsset::CountToApprove(5)
            //     + SettingReviewerApprover::CountToApprove(5)
            //     + CustomLetter::CountToApprove(5)
            //     + Policy::CountToApprove(5)
            //     + RequestDisableUser::CountToApprove(5),

            // 'MMI' => RequestMemo::presidentApprove(6)->total()
            //     + RequestForm::presidentApprove(6)->total()
            //     + RequestHR::presidentApprove(6)->total()
            //     + Disposal::presidentApprove(6)->total()
            //     + DamagedLog::presidentApprove(6)->total()
            //     + HRRequest::presidentApprove(6)->total()
            //     + SaleAsset::presidentApprove(6)->total()
            //     + SendReceive::presidentApprove(6)->total()
            //     + ReturnBudget::presidentApprove(6)->total()
            //     + Mission::presidentApprove(6)->total()
            //     + MissionItem::presidentApprove(6)->total()
            //     + MissionClearance::CountToApprove(6)
            //     + Training::presidentApprove(6)->total()
            //     + RequestOT::presidentApprove(6)->total()
            //     + EmployeePenalty::presidentApprove(6)->total()
            //     + CashAdvance::presidentApprove(6)->total()
            //     + Resign::presidentApprove(6)->total()
            //     + RequestCreateUser::CountToApprove(6)
            //     + TransferAsset::CountToApprove(6)
            //     + SettingReviewerApprover::CountToApprove(6)
            //     + CustomLetter::CountToApprove(6)
            //     + Policy::CountToApprove(6)
            //     + RequestGasoline::CountToApprove(6)
            //     + RequestDisableUser::CountToApprove(6),

            // 'MHT' => RequestMemo::presidentApprove(7)->total()
            //     + RequestForm::presidentApprove(7)->total()
            //     + RequestHR::presidentApprove(7)->total()
            //     + Disposal::presidentApprove(7)->total()
            //     + DamagedLog::presidentApprove(7)->total()
            //     + HRRequest::presidentApprove(7)->total()
            //     + SaleAsset::presidentApprove(7)->total()
            //     + SendReceive::presidentApprove(7)->total()
            //     + ReturnBudget::presidentApprove(7)->total()
            //     + Training::presidentApprove(7)->total()
            //     + RequestOT::presidentApprove(7)->total()
            //     + EmployeePenalty::presidentApprove(7)->total()
            //     + CashAdvance::presidentApprove(7)->total()
            //     + Resign::presidentApprove(7)->total()
            //     + RequestCreateUser::CountToApprove(7)
            //     + TransferAsset::CountToApprove(7)
            //     + SettingReviewerApprover::CountToApprove(7)
            //     + CustomLetter::CountToApprove(7)
            //     + Policy::CountToApprove(7)
            //     + RequestDisableUser::CountToApprove(7),

            // 'TSP' => RequestMemo::presidentApprove(8)->total()
            //     + RequestForm::presidentApprove(8)->total()
            //     + RequestHR::presidentApprove(8)->total()
            //     + Disposal::presidentApprove(8)->total()
            //     + DamagedLog::presidentApprove(8)->total()
            //     + HRRequest::presidentApprove(8)->total()
            //     + SaleAsset::presidentApprove(8)->total()
            //     + SendReceive::presidentApprove(8)->total()
            //     + ReturnBudget::presidentApprove(8)->total()
            //     + Training::presidentApprove(8)->total()
            //     + RequestOT::presidentApprove(8)->total()
            //     + EmployeePenalty::presidentApprove(8)->total()
            //     + CashAdvance::presidentApprove(8)->total()
            //     + Resign::presidentApprove(8)->total()
            //     + RequestCreateUser::CountToApprove(8)
            //     + TransferAsset::CountToApprove(8)
            //     + SettingReviewerApprover::CountToApprove(8)
            //     + CustomLetter::CountToApprove(8)
            //     + Policy::CountToApprove(8)
            //     + RequestDisableUser::CountToApprove(8),

            // 'President' => RequestMemo::presidentApprove(9)->total()
            //     + RequestForm::presidentApprove(9)->total()
            //     + RequestHR::presidentApprove(9)->total()
            //     + Disposal::presidentApprove(9)->total()
            //     + DamagedLog::presidentApprove(9)->total()
            //     + HRRequest::presidentApprove(9)->total()
            //     + SaleAsset::presidentApprove(9)->total()
            //     + SendReceive::presidentApprove(9)->total()
            //     + ReturnBudget::presidentApprove(9)->total()
            //     + Training::presidentApprove(9)->total()
            //     + RequestOT::presidentApprove(9)->total()
            //     + EmployeePenalty::presidentApprove(9)->total()
            //     + CashAdvance::presidentApprove(9)->total()
            //     + Resign::presidentApprove(9)->total()
            //     + RequestCreateUser::CountToApprove(9)
            //     + TransferAsset::CountToApprove(9)
            //     + SettingReviewerApprover::CountToApprove(9)
            //     + CustomLetter::CountToApprove(9)
            //     + Policy::CountToApprove(9)
            //     + RequestDisableUser::CountToApprove(9),

            // 'MDN' => RequestMemo::CountToApprove(10)
            //     + RequestForm::presidentApprove(10)->total()
            //     + RequestHR::presidentApprove(10)->total()
            //     + Disposal::presidentApprove(10)->total()
            //     + DamagedLog::presidentApprove(10)->total()
            //     + HRRequest::presidentApprove(10)->total()
            //     + SaleAsset::presidentApprove(10)->total()
            //     + SendReceive::presidentApprove(10)->total()
            //     + ReturnBudget::presidentApprove(10)->total()
            //     + Mission::presidentApprove(10)->total()
            //     + MissionItem::presidentApprove(10)->total()
            //     + Training::presidentApprove(10)->total()
            //     + RequestOT::presidentApprove(10)->total()
            //     + EmployeePenalty::presidentApprove(10)->total()
            //     + CashAdvance::presidentApprove(10)->total()
            //     + Resign::presidentApprove(10)->total()
            //     + RequestCreateUser::CountToApprove(10)
            //     + TransferAsset::CountToApprove(10)
            //     + SettingReviewerApprover::CountToApprove(10)
            //     + CustomLetter::CountToApprove(10)
            //     + Policy::CountToApprove(10)
            //     + RequestDisableUser::CountToApprove(10),

            // 'PTK' => RequestMemo::CountToApprove(11)
            //     + RequestForm::presidentApprove(11)->total()
            //     + RequestHR::presidentApprove(11)->total()
            //     + Disposal::presidentApprove(11)->total()
            //     + DamagedLog::presidentApprove(11)->total()
            //     + HRRequest::presidentApprove(11)->total()
            //     + SaleAsset::presidentApprove(11)->total()
            //     + SendReceive::presidentApprove(11)->total()
            //     + ReturnBudget::presidentApprove(11)->total()
            //     + Mission::presidentApprove(11)->total()
            //     + MissionItem::presidentApprove(11)->total()
            //     + Training::presidentApprove(11)->total()
            //     + RequestOT::presidentApprove(11)->total()
            //     + EmployeePenalty::presidentApprove(11)->total()
            //     + CashAdvance::presidentApprove(11)->total()
            //     + Resign::presidentApprove(11)->total()
            //     + RequestCreateUser::CountToApprove(11)
            //     + TransferAsset::CountToApprove(11)
            //     + SettingReviewerApprover::CountToApprove(11)
            //     + CustomLetter::CountToApprove(11)
            //     + Policy::CountToApprove(11)
            //     + RequestDisableUser::CountToApprove(11),

            // 'NIYA' => RequestMemo::CountToApprove(12)
            //     + RequestForm::presidentApprove(12)->total()
            //     + RequestHR::presidentApprove(12)->total()
            //     + Disposal::presidentApprove(12)->total()
            //     + DamagedLog::presidentApprove(12)->total()
            //     + HRRequest::presidentApprove(12)->total()
            //     + SaleAsset::presidentApprove(12)->total()
            //     + SendReceive::presidentApprove(12)->total()
            //     + ReturnBudget::presidentApprove(12)->total()
            //     + Mission::presidentApprove(12)->total()
            //     + MissionItem::presidentApprove(12)->total()
            //     + Training::presidentApprove(12)->total()
            //     + RequestOT::presidentApprove(12)->total()
            //     + EmployeePenalty::presidentApprove(12)->total()
            //     + CashAdvance::presidentApprove(12)->total()
            //     + Resign::presidentApprove(12)->total()
            //     + RequestCreateUser::CountToApprove(12)
            //     + TransferAsset::CountToApprove(12)
            //     + SettingReviewerApprover::CountToApprove(12)
            //     + CustomLetter::CountToApprove(12)
            //     + Policy::CountToApprove(12)
            //     + RequestDisableUser::CountToApprove(12),

            // 'DMS' => RequestMemo::CountToApprove(13)
            //     + RequestForm::presidentApprove(13)->total()
            //     + RequestHR::presidentApprove(13)->total()
            //     + Disposal::presidentApprove(13)->total()
            //     + DamagedLog::presidentApprove(13)->total()
            //     + HRRequest::presidentApprove(13)->total()
            //     + SaleAsset::presidentApprove(13)->total()
            //     + SendReceive::presidentApprove(13)->total()
            //     + ReturnBudget::presidentApprove(13)->total()
            //     + Mission::presidentApprove(13)->total()
            //     + MissionItem::presidentApprove(13)->total()
            //     + Training::presidentApprove(13)->total()
            //     + RequestOT::presidentApprove(13)->total()
            //     + EmployeePenalty::presidentApprove(13)->total()
            //     + CashAdvance::presidentApprove(13)->total()
            //     + Resign::presidentApprove(13)->total()
            //     + RequestCreateUser::CountToApprove(13)
            //     + TransferAsset::CountToApprove(13)
            //     + SettingReviewerApprover::CountToApprove(13)
            //     + CustomLetter::CountToApprove(13)
            //     + Policy::CountToApprove(13),

            // 'BRC' => RequestMemo::CountToApprove(15)
            //     + RequestForm::presidentApprove(15)->total()
            //     + RequestHR::presidentApprove(15)->total()
            //     + Disposal::presidentApprove(15)->total()
            //     + DamagedLog::presidentApprove(15)->total()
            //     + HRRequest::presidentApprove(15)->total()
            //     + SaleAsset::presidentApprove(15)->total()
            //     + SendReceive::presidentApprove(15)->total()
            //     + ReturnBudget::presidentApprove(15)->total()
            //     + Training::presidentApprove(15)->total()
            //     + RequestOT::presidentApprove(15)->total()
            //     + EmployeePenalty::presidentApprove(15)->total()
            //     + CashAdvance::presidentApprove(15)->total()
            //     + Resign::presidentApprove(15)->total()
            //     + RequestCreateUser::CountToApprove(15)
            //     + TransferAsset::CountToApprove(15)
            //     + SettingReviewerApprover::CountToApprove(15)
            //     + CustomLetter::CountToApprove(15)
            //     + Policy::CountToApprove(15)
            //     + BorrowingLoan::CountToApprove(15),
        ];

        $data['rejected'] = [
            // 'STSK' => RequestMemo::presidentRejectedList(1)->total()
            //     + RequestForm::presidentRejectedList(1)->total()
            //     + RequestHR::presidentRejectedList(1)->total()
            //     + Disposal::presidentRejectedList(1)->total()
            //     + DamagedLog::presidentRejectedList(1)->total()
            //     + HRRequest::presidentRejectedList(1)->total()
            //     + SaleAsset::presidentRejectedList(1)->total()
            //     + SendReceive::presidentRejectedList(1)->total()
            //     + ReturnBudget::presidentRejectedList(1)->total()
            //     + Mission::presidentRejectedList(1)->total()
            //     + MissionItem::presidentRejectedList(1)->total()
            //     + MissionClearance::CountRejected(1)
            //     + Training::presidentRejectedList(1)->total()
            //     + RequestOT::presidentRejectedList(1)->total()
            //     + EmployeePenalty::presidentRejectedList(1)->total()
            //     + CashAdvance::presidentRejectedList(1)->total()
            //     + Resign::presidentRejectedList(1)->total()
            //     + GeneralRequest::presidentRejectedList(1)->total()
            //     + TransferAsset::CountRejected(1)
            //     + SettingReviewerApprover::CountRejected(1)
            //     + CustomLetter::CountRejected(1)
            //     + Policy::CountRejected(1)
            //     + RequestGasoline::CountRejected(1)
            //     + RequestDisableUser::CountRejected(1)
            //     + @$eachMenuCompany['rejected']['STSK'],

            // 'MFI' => RequestMemo::presidentRejectedList(2)->total()
            //     + RequestForm::presidentRejectedList(2)->total()
            //     + RequestHR::presidentRejectedList(2)->total()
            //     + Disposal::presidentRejectedList(2)->total()
            //     + DamagedLog::presidentRejectedList(2)->total()
            //     + HRRequest::presidentRejectedList(2)->total()
            //     + SaleAsset::presidentRejectedList(2)->total()
            //     + SendReceive::presidentRejectedList(2)->total()
            //     + ReturnBudget::presidentRejectedList(2)->total()
            //     + Loan::CountRejected(2)
            //     + RescheduleLoan::presidentRejectedList(2)->total()
            //     + Mission::presidentRejectedList(2)->total()
            //     + MissionItem::presidentRejectedList(2)->total()
            //     + MissionClearance::CountRejected(2)
            //     + Training::presidentRejectedList(2)->total()
            //     + RequestOT::presidentRejectedList(2)->total()
            //     + Penalty::presidentRejectedList(2)->total()
            //     + Penalty::presidentRejectedListInterest(2)->total()
            //     + Penalty::presidentRejectedListAssociation(2)->total()
            //     + EmployeePenalty::presidentRejectedList(2)->total()
            //     + CashAdvance::presidentRejectedList(2)->total()
            //     + Resign::presidentRejectedList(2)->total()
            //     + GeneralRequest::CountRejected(2)
            //     + RequestCreateUser::CountRejected(2)
            //     + TransferAsset::CountRejected(2)
            //     + SettingReviewerApprover::CountRejected(2)
            //     + Association::CountRejected(2)
            //     + Survey::CountRejected(2)
            //     + CustomLetter::CountRejected(2)
            //     + Policy::CountRejected(2)
            //     + RequestDisableUser::CountRejected(2)
            //     + WithdrawalCollateral::CountRejected(2)
            //     + VillageLoan::CountRejected(2)
            //     + RequestGasoline::CountRejected(2)
            //     + @$eachMenuCompany['rejected']['MFI'],

            // 'NGO' => RequestMemo::presidentRejectedList(3)->total()
            //     + RequestForm::presidentRejectedList(3)->total()
            //     + RequestHR::presidentRejectedList(3)->total()
            //     + Disposal::presidentRejectedList(3)->total()
            //     + DamagedLog::presidentRejectedList(3)->total()
            //     + HRRequest::presidentRejectedList(3)->total()
            //     + SaleAsset::presidentRejectedList(3)->total()
            //     + SendReceive::presidentRejectedList(3)->total()
            //     + ReturnBudget::presidentRejectedList(3)->total()
            //     + Loan::CountRejected(3)
            //     + RescheduleLoan::presidentRejectedList(3)->total()
            //     + Mission::presidentRejectedList(3)->total()
            //     + MissionItem::presidentRejectedList(3)->total()
            //     + MissionClearance::CountRejected(3)
            //     + Training::presidentRejectedList(3)->total()
            //     + RequestOT::presidentRejectedList(3)->total()
            //     + Penalty::presidentRejectedList(3)->total()
            //     + Penalty::presidentRejectedListInterest(3)->total()
            //     + Penalty::presidentRejectedListAssociation(3)->total()
            //     + EmployeePenalty::presidentRejectedList(3)->total()
            //     + CashAdvance::presidentRejectedList(3)->total()
            //     + Resign::presidentRejectedList(3)->total()
            //     + GeneralRequest::CountRejected(3)
            //     + RequestCreateUser::CountRejected(3)
            //     + TransferAsset::CountRejected(3)
            //     + SettingReviewerApprover::CountRejected(3)
            //     + Association::CountRejected(3)
            //     + Survey::CountRejected(3)
            //     + CustomLetter::CountRejected(3)
            //     + Policy::CountRejected(3)
            //     + RequestDisableUser::CountRejected(3)
            //     + WithdrawalCollateral::CountRejected(3)
            //     + VillageLoan::CountRejected(3)
            //     + RequestGasoline::CountRejected(3)
            //     + @$eachMenuCompany['rejected']['NGO'],

            // 'PWS' => RequestMemo::presidentRejectedList(14)->total()
            //     + RequestForm::presidentRejectedList(14)->total()
            //     + RequestHR::presidentRejectedList(14)->total()
            //     + Disposal::presidentRejectedList(14)->total()
            //     + DamagedLog::presidentRejectedList(14)->total()
            //     + HRRequest::presidentRejectedList(14)->total()
            //     + SaleAsset::presidentRejectedList(14)->total()
            //     + SendReceive::presidentRejectedList(14)->total()
            //     + ReturnBudget::presidentRejectedList(14)->total()
            //     + Loan::CountRejected(14)
            //     + RescheduleLoan::presidentRejectedList(14)->total()
            //     + Mission::presidentRejectedList(14)->total()
            //     + MissionItem::presidentRejectedList(14)->total()
            //     + MissionClearance::CountRejected(14)
            //     + Training::presidentRejectedList(14)->total()
            //     + RequestOT::presidentRejectedList(14)->total()
            //     + Penalty::presidentRejectedList(14)->total()
            //     + Penalty::presidentRejectedListInterest(14)->total()
            //     + Penalty::presidentRejectedListAssociation(14)->total()
            //     + EmployeePenalty::presidentRejectedList(14)->total()
            //     + CashAdvance::presidentRejectedList(14)->total()
            //     + Resign::presidentRejectedList(14)->total()
            //     + GeneralRequest::CountRejected(14)
            //     + RequestCreateUser::CountRejected(14)
            //     + TransferAsset::CountRejected(14)
            //     + SettingReviewerApprover::CountRejected(14)
            //     + Association::CountRejected(14)
            //     + Survey::CountRejected(14)
            //     + CustomLetter::CountRejected(14)
            //     + Policy::CountRejected(14)
            //     + RequestDisableUser::CountRejected(14)
            //     + WithdrawalCollateral::CountRejected(14)
            //     + VillageLoan::CountRejected(14)
            //     + RequestGasoline::CountRejected(14)
            //     + @$eachMenuCompany['rejected']['PWS'],

            'ORD' => RequestMemo::presidentRejectedList(4)->total()
                + RequestForm::presidentRejectedList(4)->total()
                + RequestPR::presidentRejectedList(4)->total()
                + RequestPO::presidentRejectedList(4)->total()
                + RequestGRN::presidentRejectedList(4)->total()
                + RequestHR::presidentRejectedList(4)->total()
                + Disposal::presidentRejectedList(4)->total()
                + DamagedLog::presidentRejectedList(4)->total()
                + HRRequest::presidentRejectedList(4)->total()
                + SaleAsset::presidentRejectedList(4)->total()
                + SendReceive::presidentRejectedList(4)->total()
                + ReturnBudget::presidentRejectedList(4)->total()
                + Training::presidentRejectedList(4)->total()
                + RequestOT::presidentRejectedList(4)->total()
                + EmployeePenalty::presidentRejectedList(4)->total()
                + CashAdvance::presidentRejectedList(4)->total()
                + Resign::presidentRejectedList(4)->total()
                + RequestCreateUser::CountRejected(4)
                + TransferAsset::CountRejected(4)
                + SettingReviewerApprover::CountRejected(4)
                + CustomLetter::CountRejected(4)
                + Policy::CountRejected(4)
                + RequestDisableUser::CountRejected(4)
                + @$eachMenuCompany['rejected']['ORD'],

            'ORD2' => RequestMemo::presidentRejectedList(16)->total()
                + RequestForm::presidentRejectedList(16)->total()
                + RequestPR::presidentRejectedList(16)->total()
                + RequestPO::presidentRejectedList(16)->total()
                + RequestGRN::presidentRejectedList(16)->total()
                + RequestHR::presidentRejectedList(16)->total()
                + Disposal::presidentRejectedList(16)->total()
                + DamagedLog::presidentRejectedList(16)->total()
                + HRRequest::presidentRejectedList(16)->total()
                + SaleAsset::presidentRejectedList(16)->total()
                + SendReceive::presidentRejectedList(16)->total()
                + ReturnBudget::presidentRejectedList(16)->total()
                + Training::presidentRejectedList(16)->total()
                + RequestOT::presidentRejectedList(16)->total()
                + EmployeePenalty::presidentRejectedList(16)->total()
                + CashAdvance::presidentRejectedList(16)->total()
                + Resign::presidentRejectedList(16)->total()
                + RequestCreateUser::CountRejected(16)
                + TransferAsset::CountRejected(16)
                + SettingReviewerApprover::CountRejected(16)
                + CustomLetter::CountRejected(16)
                + Policy::CountRejected(16)
                + RequestDisableUser::CountRejected(16)
                + @$eachMenuCompany['rejected']['ORD2'],

            // 'ST' => RequestMemo::presidentRejectedList(5)->total()
            //     + RequestForm::presidentRejectedList(5)->total()
            //     + RequestHR::presidentRejectedList(5)->total()
            //     + Disposal::presidentRejectedList(5)->total()
            //     + DamagedLog::presidentRejectedList(5)->total()
            //     + HRRequest::presidentRejectedList(5)->total()
            //     + SaleAsset::presidentRejectedList(5)->total()
            //     + SendReceive::presidentRejectedList(5)->total()
            //     + ReturnBudget::presidentRejectedList(5)->total()
            //     + Training::presidentRejectedList(5)->total()
            //     + RequestOT::presidentRejectedList(5)->total()
            //     + EmployeePenalty::presidentRejectedList(5)->total()
            //     + CashAdvance::presidentRejectedList(5)->total()
            //     + Resign::presidentRejectedList(5)->total()
            //     + RequestCreateUser::CountRejected(5)
            //     + TransferAsset::CountRejected(5)
            //     + SettingReviewerApprover::CountRejected(5)
            //     + CustomLetter::CountRejected(5)
            //     + Policy::CountRejected(5)
            //     + RequestDisableUser::CountRejected(5)
            //     + @$eachMenuCompany['rejected']['ST'],

            // 'MMI' => RequestMemo::presidentRejectedList(6)->total()
            //     + RequestForm::presidentRejectedList(6)->total()
            //     + RequestHR::presidentRejectedList(6)->total()
            //     + Disposal::presidentRejectedList(6)->total()
            //     + DamagedLog::presidentRejectedList(6)->total()
            //     + HRRequest::presidentRejectedList(6)->total()
            //     + SaleAsset::presidentRejectedList(6)->total()
            //     + SendReceive::presidentRejectedList(6)->total()
            //     + ReturnBudget::presidentRejectedList(6)->total()
            //     + Mission::presidentRejectedList(6)->total()
            //     + MissionItem::presidentRejectedList(6)->total()
            //     + MissionClearance::CountRejected(6)
            //     + Training::presidentRejectedList(6)->total()
            //     + RequestOT::presidentRejectedList(6)->total()
            //     + EmployeePenalty::presidentRejectedList(6)->total()
            //     + CashAdvance::presidentRejectedList(6)->total()
            //     + Resign::presidentRejectedList(6)->total()
            //     + RequestCreateUser::CountRejected(6)
            //     + TransferAsset::CountRejected(6)
            //     + SettingReviewerApprover::CountRejected(6)
            //     + CustomLetter::CountRejected(6)
            //     + Policy::CountRejected(6)
            //     + RequestDisableUser::CountRejected(6)
            //     + RequestGasoline::CountRejected(6)
            //     + @$eachMenuCompany['rejected']['MMI'],

            // 'MHT' => RequestMemo::presidentRejectedList(7)->total()
            //     + RequestForm::presidentRejectedList(7)->total()
            //     + RequestHR::presidentRejectedList(7)->total()
            //     + Disposal::presidentRejectedList(7)->total()
            //     + DamagedLog::presidentRejectedList(7)->total()
            //     + HRRequest::presidentRejectedList(7)->total()
            //     + SaleAsset::presidentRejectedList(7)->total()
            //     + SendReceive::presidentRejectedList(7)->total()
            //     + ReturnBudget::presidentRejectedList(7)->total()
            //     + Training::presidentRejectedList(7)->total()
            //     + RequestOT::presidentRejectedList(7)->total()
            //     + EmployeePenalty::presidentRejectedList(7)->total()
            //     + CashAdvance::presidentRejectedList(7)->total()
            //     + Resign::presidentRejectedList(7)->total()
            //     + RequestCreateUser::CountRejected(7)
            //     + TransferAsset::CountRejected(7)
            //     + SettingReviewerApprover::CountRejected(7)
            //     + CustomLetter::CountRejected(7)
            //     + Policy::CountRejected(7)
            //     + RequestDisableUser::CountRejected(7)
            //     + @$eachMenuCompany['rejected']['MHT'],

            // 'TSP' => RequestMemo::presidentRejectedList(8)->total()
            //     + RequestForm::presidentRejectedList(8)->total()
            //     + RequestHR::presidentRejectedList(8)->total()
            //     + Disposal::presidentRejectedList(8)->total()
            //     + DamagedLog::presidentRejectedList(8)->total()
            //     + HRRequest::presidentRejectedList(8)->total()
            //     + SaleAsset::presidentRejectedList(8)->total()
            //     + SendReceive::presidentRejectedList(8)->total()
            //     + ReturnBudget::presidentRejectedList(8)->total()
            //     + Training::presidentRejectedList(8)->total()
            //     + RequestOT::presidentRejectedList(8)->total()
            //     + EmployeePenalty::presidentRejectedList(8)->total()
            //     + CashAdvance::presidentRejectedList(8)->total()
            //     + Resign::presidentRejectedList(8)->total()
            //     + RequestCreateUser::CountRejected(8)
            //     + TransferAsset::CountRejected(8)
            //     + SettingReviewerApprover::CountRejected(8)
            //     + CustomLetter::CountRejected(8)
            //     + Policy::CountRejected(8)
            //     + RequestDisableUser::CountRejected(8)
            //     + @$eachMenuCompany['rejected']['TSP'],

            // 'President' => RequestMemo::presidentRejectedList(9)->total()
            //     + RequestForm::presidentRejectedList(9)->total()
            //     + RequestHR::presidentRejectedList(9)->total()
            //     + Disposal::presidentRejectedList(9)->total()
            //     + DamagedLog::presidentRejectedList(9)->total()
            //     + HRRequest::presidentRejectedList(9)->total()
            //     + SaleAsset::presidentRejectedList(9)->total()
            //     + SendReceive::presidentRejectedList(9)->total()
            //     + ReturnBudget::presidentRejectedList(9)->total()
            //     + Training::presidentRejectedList(9)->total()
            //     + RequestOT::presidentRejectedList(9)->total()
            //     + EmployeePenalty::presidentRejectedList(9)->total()
            //     + CashAdvance::presidentRejectedList(9)->total()
            //     + Resign::presidentRejectedList(9)->total()
            //     + RequestCreateUser::CountRejected(9)
            //     + TransferAsset::CountRejected(9)
            //     + SettingReviewerApprover::CountRejected(9)
            //     + CustomLetter::CountRejected(9)
            //     + Policy::CountRejected(9)
            //     + RequestDisableUser::CountRejected(9)
            //     + @$eachMenuCompany['rejected']['President'],

            // 'MDN' => RequestMemo::CountRejected(10)
            //     + RequestForm::presidentRejectedList(10)->total()
            //     + RequestHR::presidentRejectedList(10)->total()
            //     + Disposal::presidentRejectedList(10)->total()
            //     + DamagedLog::presidentRejectedList(10)->total()
            //     + HRRequest::presidentRejectedList(10)->total()
            //     + SaleAsset::presidentRejectedList(10)->total()
            //     + SendReceive::presidentRejectedList(10)->total()
            //     + ReturnBudget::presidentRejectedList(10)->total()
            //     + Mission::presidentRejectedList(10)->total()
            //     + MissionItem::presidentRejectedList(10)->total()
            //     + Training::presidentRejectedList(10)->total()
            //     + RequestOT::presidentRejectedList(10)->total()
            //     + EmployeePenalty::presidentRejectedList(10)->total()
            //     + CashAdvance::presidentRejectedList(10)->total()
            //     + Resign::presidentRejectedList(10)->total()
            //     + RequestCreateUser::CountRejected(10)
            //     + TransferAsset::CountRejected(10)
            //     + SettingReviewerApprover::CountRejected(10)
            //     + CustomLetter::CountRejected(10)
            //     + Policy::CountRejected(10)
            //     + RequestDisableUser::CountRejected(10)
            //     + @$eachMenuCompany['rejected']['MDN'],

            // 'PTK' => RequestMemo::CountRejected(11)
            //     + RequestForm::presidentRejectedList(11)->total()
            //     + RequestHR::presidentRejectedList(11)->total()
            //     + Disposal::presidentRejectedList(11)->total()
            //     + DamagedLog::presidentRejectedList(11)->total()
            //     + HRRequest::presidentRejectedList(11)->total()
            //     + SaleAsset::presidentRejectedList(11)->total()
            //     + SendReceive::presidentRejectedList(11)->total()
            //     + ReturnBudget::presidentRejectedList(11)->total()
            //     + Mission::presidentRejectedList(11)->total()
            //     + MissionItem::presidentRejectedList(11)->total()
            //     + Training::presidentRejectedList(11)->total()
            //     + RequestOT::presidentRejectedList(11)->total()
            //     + EmployeePenalty::presidentRejectedList(11)->total()
            //     + CashAdvance::presidentRejectedList(11)->total()
            //     + RequestCreateUser::CountRejected(11)
            //     + TransferAsset::CountRejected(11)
            //     + SettingReviewerApprover::CountRejected(11)
            //     + CustomLetter::CountRejected(11)
            //     + Policy::CountRejected(11)
            //     + RequestDisableUser::CountRejected(11)
            //     + @$eachMenuCompany['rejected']['PTK'],

            // 'NIYA' => RequestMemo::CountRejected(12)
            //     + RequestForm::presidentRejectedList(12)->total()
            //     + RequestHR::presidentRejectedList(12)->total()
            //     + Disposal::presidentRejectedList(12)->total()
            //     + DamagedLog::presidentRejectedList(12)->total()
            //     + HRRequest::presidentRejectedList(12)->total()
            //     + SaleAsset::presidentRejectedList(12)->total()
            //     + SendReceive::presidentRejectedList(12)->total()
            //     + ReturnBudget::presidentRejectedList(12)->total()
            //     + Mission::presidentRejectedList(12)->total()
            //     + MissionItem::presidentRejectedList(12)->total()
            //     + Training::presidentRejectedList(12)->total()
            //     + RequestOT::presidentRejectedList(12)->total()
            //     + EmployeePenalty::presidentRejectedList(12)->total()
            //     + CashAdvance::presidentRejectedList(12)->total()
            //     + Resign::presidentRejectedList(12)->total()
            //     + RequestCreateUser::CountRejected(12)
            //     + TransferAsset::CountRejected(12)
            //     + SettingReviewerApprover::CountRejected(12)
            //     + CustomLetter::CountRejected(12)
            //     + Policy::CountRejected(12)
            //     + RequestDisableUser::CountRejected(12)
            //     + @$eachMenuCompany['rejected']['NIYA'],

            // 'DMS' => RequestMemo::CountRejected(13)
            //     + RequestForm::presidentRejectedList(13)->total()
            //     + RequestHR::presidentRejectedList(13)->total()
            //     + Disposal::presidentRejectedList(13)->total()
            //     + DamagedLog::presidentRejectedList(13)->total()
            //     + HRRequest::presidentRejectedList(13)->total()
            //     + SaleAsset::presidentRejectedList(13)->total()
            //     + SendReceive::presidentRejectedList(13)->total()
            //     + ReturnBudget::presidentRejectedList(13)->total()
            //     + Mission::presidentRejectedList(13)->total()
            //     + MissionItem::presidentRejectedList(13)->total()
            //     + Training::presidentRejectedList(13)->total()
            //     + RequestOT::presidentRejectedList(13)->total()
            //     + EmployeePenalty::presidentRejectedList(13)->total()
            //     + CashAdvance::presidentRejectedList(13)->total()
            //     + Resign::presidentRejectedList(13)->total()
            //     + RequestCreateUser::CountRejected(13)
            //     + TransferAsset::CountRejected(13)
            //     + SettingReviewerApprover::CountRejected(13)
            //     + CustomLetter::CountRejected(13)
            //     + Policy::CountRejected(13)
            //     + RequestDisableUser::CountRejected(13)
            //     + @$eachMenuCompany['rejected']['DMS'],

            // 'BRC' => RequestMemo::CountRejected(15)
            //     + RequestForm::presidentRejectedList(15)->total()
            //     + RequestHR::presidentRejectedList(15)->total()
            //     + Disposal::presidentRejectedList(15)->total()
            //     + DamagedLog::presidentRejectedList(15)->total()
            //     + HRRequest::presidentRejectedList(15)->total()
            //     + SaleAsset::presidentRejectedList(15)->total()
            //     + SendReceive::presidentRejectedList(15)->total()
            //     + ReturnBudget::presidentRejectedList(15)->total()
            //     + Training::presidentRejectedList(15)->total()
            //     + RequestOT::presidentRejectedList(15)->total()
            //     + EmployeePenalty::presidentRejectedList(15)->total()
            //     + CashAdvance::presidentRejectedList(15)->total()
            //     + Resign::presidentRejectedList(15)->total()
            //     + RequestCreateUser::CountRejected(15)
            //     + TransferAsset::CountRejected(15)
            //     + SettingReviewerApprover::CountRejected(15)
            //     + CustomLetter::CountRejected(15)
            //     + Policy::CountRejected(15)
            //     + RequestDisableUser::CountRejected(15)
            //     + BorrowingLoan::CountRejected(15)
            //     + @$eachMenuCompany['rejected']['BRC'],
        ];

        $data['disabled'] = [

            // 'STSK' => Resign::presidentDisabledList(1)->total()
            //     + RequestForm::presidentDisabledList(1)->total()
            //     + RequestHR::presidentDisabledList(1)->total()
            //     + HRRequest::presidentDisabledList(1)->total()
            //     + CashAdvance::presidentDisabledList(1)->total()
            //     + EmployeePenalty::presidentDisabledList(1)->total()
            //     + RequestGasoline::CountDisabled(1)
            //     + Mission::presidentDisabledList(1)->total()
            //     + MissionClearance::CountDisabled(1)
            //     + RequestOT::presidentDisabledList(1)->total()
            //     + CustomLetter::CountDisabled(1)
            //     + DamagedLog::presidentDisabledList(1)->total()
            //     + Disposal::presidentDisabledList(1)->total()
            //     + TransferAsset::CountDisabled(1)
            //     + SaleAsset::presidentDisabledList(1)->total()
            //     + SendReceive::presidentDisabledList(1)->total()
            //     + ReturnBudget::presidentDisabledList(1)->total()
            //     + Training::presidentDisabledList(1)->total()
            //     + RequestMemo::CountDisabled(1),

            // 'MFI' => Loan::CountDisabled(2)
            //     + Resign::presidentDisabledList(2)->total()
            //     + RequestForm::presidentDisabledList(2)->total()
            //     + RequestHR::presidentDisabledList(2)->total()
            //     + HRRequest::presidentDisabledList(2)->total()
            //     + CashAdvance::presidentDisabledList(2)->total()
            //     + EmployeePenalty::presidentDisabledList(2)->total()
            //     + RequestGasoline::CountDisabled(2)
            //     + Mission::presidentDisabledList(2)->total()
            //     + MissionClearance::CountDisabled(2)
            //     + GeneralRequest::CountDisabled(2)
            //     + RescheduleLoan::presidentDisabledList(2)->total()
            //     + RequestOT::presidentDisabledList(2)->total()
            //     + Association::CountDisabled(2)
            //     + Penalty::presidentDisabledList(2)->total()
            //     + Penalty::presidentDisabledListInterest(2)->total()
            //     + Penalty::presidentDisabledListAssociation(2)->total()
            //     + CustomLetter::CountDisabled(2)
            //     + DamagedLog::presidentDisabledList(2)->total()
            //     + Disposal::presidentDisabledList(2)->total()
            //     + TransferAsset::CountDisabled(2)
            //     + SaleAsset::presidentDisabledList(2)->total()
            //     + SendReceive::presidentDisabledList(2)->total()
            //     + ReturnBudget::presidentDisabledList(2)->total()
            //     + WithdrawalCollateral::CountDisabled(2)
            //     + VillageLoan::CountDisabled(2)
            //     + Training::presidentDisabledList(2)->total()
            //     + RequestMemo::CountDisabled(2),

            // 'NGO' => Loan::CountDisabled(3)
            //     + Resign::presidentDisabledList(3)->total()
            //     + RequestForm::presidentDisabledList(3)->total()
            //     + RequestHR::presidentDisabledList(3)->total()
            //     + HRRequest::presidentDisabledList(3)->total()
            //     + CashAdvance::presidentDisabledList(3)->total()
            //     + EmployeePenalty::presidentDisabledList(3)->total()
            //     + RequestGasoline::CountDisabled(3)
            //     + Mission::presidentDisabledList(3)->total()
            //     + MissionClearance::CountDisabled(3)
            //     + GeneralRequest::CountDisabled(3)
            //     + RescheduleLoan::presidentDisabledList(3)->total()
            //     + RequestOT::presidentDisabledList(3)->total()
            //     + Association::CountDisabled(3)
            //     + Penalty::presidentDisabledList(3)->total()
            //     + Penalty::presidentDisabledListInterest(3)->total()
            //     + Penalty::presidentDisabledListAssociation(3)->total()
            //     + CustomLetter::CountDisabled(3)
            //     + DamagedLog::presidentDisabledList(3)->total()
            //     + Disposal::presidentDisabledList(3)->total()
            //     + TransferAsset::CountDisabled(3)
            //     + SaleAsset::presidentDisabledList(3)->total()
            //     + SendReceive::presidentDisabledList(3)->total()
            //     + ReturnBudget::presidentDisabledList(3)->total()
            //     + WithdrawalCollateral::CountDisabled(3)
            //     + VillageLoan::CountDisabled(3)
            //     + Training::presidentDisabledList(3)->total()
            //     + RequestMemo::CountDisabled(3),

            // 'PWS' => Loan::CountDisabled(14)
            //     + Resign::presidentDisabledList(14)->total()
            //     + RequestForm::presidentDisabledList(14)->total()
            //     + RequestHR::presidentDisabledList(14)->total()
            //     + HRRequest::presidentDisabledList(14)->total()
            //     + CashAdvance::presidentDisabledList(14)->total()
            //     + EmployeePenalty::presidentDisabledList(14)->total()
            //     + RequestGasoline::CountDisabled(14)
            //     + Mission::presidentDisabledList(14)->total()
            //     + MissionClearance::CountDisabled(14)
            //     + GeneralRequest::CountDisabled(14)
            //     + RescheduleLoan::presidentDisabledList(14)->total()
            //     + RequestOT::presidentDisabledList(14)->total()
            //     + Association::CountDisabled(14)
            //     + Penalty::presidentDisabledList(14)->total()
            //     + Penalty::presidentDisabledListInterest(14)->total()
            //     + Penalty::presidentDisabledListAssociation(14)->total()
            //     + CustomLetter::CountDisabled(14)
            //     + DamagedLog::presidentDisabledList(14)->total()
            //     + Disposal::presidentDisabledList(14)->total()
            //     + TransferAsset::CountDisabled(14)
            //     + SaleAsset::presidentDisabledList(14)->total()
            //     + SendReceive::presidentDisabledList(14)->total()
            //     + ReturnBudget::presidentDisabledList(14)->total()
            //     + WithdrawalCollateral::CountDisabled(14)
            //     + VillageLoan::CountDisabled(14)
            //     + Training::presidentDisabledList(14)->total()
            //     + RequestMemo::CountDisabled(14),

            'ORD' => Resign::presidentDisabledList(4)->total()
                + RequestForm::presidentDisabledList(4)->total()
                + RequestPR::presidentDisabledList(4)->total()
                + RequestPO::presidentDisabledList(4)->total()
                + RequestGRN::presidentDisabledList(4)->total()
                + RequestHR::presidentDisabledList(4)->total()
                + HRRequest::presidentDisabledList(4)->total()
                + CashAdvance::presidentDisabledList(4)->total()
                + RequestOT::presidentDisabledList(4)->total()
                + CustomLetter::CountDisabled(4)
                + DamagedLog::presidentDisabledList(4)->total()
                + Disposal::presidentDisabledList(4)->total()
                + TransferAsset::CountDisabled(4)
                + SaleAsset::presidentDisabledList(4)->total()
                + SendReceive::presidentDisabledList(4)->total()
                + ReturnBudget::presidentDisabledList(4)->total()
                + Training::presidentDisabledList(4)->total()
                + RequestMemo::CountDisabled(4),

            'ORD2' => Resign::presidentDisabledList(16)->total()
                + RequestForm::presidentDisabledList(16)->total()
                + RequestPR::presidentDisabledList(16)->total()
                + RequestPO::presidentDisabledList(16)->total()
                + RequestGRN::presidentDisabledList(16)->total()
                + RequestHR::presidentDisabledList(16)->total()
                + HRRequest::presidentDisabledList(16)->total()
                + CashAdvance::presidentDisabledList(16)->total()
                + RequestOT::presidentDisabledList(16)->total()
                + CustomLetter::CountDisabled(16)
                + DamagedLog::presidentDisabledList(16)->total()
                + Disposal::presidentDisabledList(16)->total()
                + TransferAsset::CountDisabled(16)
                + SaleAsset::presidentDisabledList(16)->total()
                + SendReceive::presidentDisabledList(16)->total()
                + ReturnBudget::presidentDisabledList(16)->total()
                + Training::presidentDisabledList(16)->total()
                + RequestMemo::CountDisabled(16),

            // 'ST' => Resign::presidentDisabledList(5)->total()
            //     + RequestForm::presidentDisabledList(5)->total()
            //     + RequestHR::presidentDisabledList(5)->total()
            //     + HRRequest::presidentDisabledList(5)->total()
            //     + CashAdvance::presidentDisabledList(5)->total()
            //     + RequestOT::presidentDisabledList(5)->total()
            //     + DamagedLog::presidentDisabledList(5)->total()
            //     + Disposal::presidentDisabledList(5)->total()
            //     + TransferAsset::CountDisabled(5)
            //     + SaleAsset::presidentDisabledList(5)->total()
            //     + SendReceive::presidentDisabledList(5)->total()
            //     + ReturnBudget::presidentDisabledList(5)->total()
            //     + Training::presidentDisabledList(5)->total()
            //     + RequestMemo::CountDisabled(5),

            // 'MMI' => Resign::presidentDisabledList(6)->total()
            //     + RequestForm::presidentDisabledList(6)->total()
            //     + RequestHR::presidentDisabledList(6)->total()
            //     + HRRequest::presidentDisabledList(6)->total()
            //     + CashAdvance::presidentDisabledList(6)->total()
            //     + RequestGasoline::CountDisabled(6)
            //     + Mission::presidentDisabledList(6)->total()
            //     + MissionClearance::CountDisabled(6)
            //     + RequestOT::presidentDisabledList(6)->total()
            //     + CustomLetter::CountDisabled(6)
            //     + DamagedLog::presidentDisabledList(6)->total()
            //     + Disposal::presidentDisabledList(6)->total()
            //     + TransferAsset::CountDisabled(6)
            //     + SaleAsset::presidentDisabledList(6)->total()
            //     + SendReceive::presidentDisabledList(6)->total()
            //     + ReturnBudget::presidentDisabledList(6)->total()
            //     + Training::presidentDisabledList(6)->total()
            //     + RequestMemo::CountDisabled(6),

            // 'MHT' => Resign::presidentDisabledList(7)->total()
            //     + RequestForm::presidentDisabledList(7)->total()
            //     + RequestHR::presidentDisabledList(7)->total()
            //     + HRRequest::presidentDisabledList(7)->total()
            //     + CashAdvance::presidentDisabledList(7)->total()
            //     + RequestOT::presidentDisabledList(7)->total()
            //     + CustomLetter::CountDisabled(7)
            //     + DamagedLog::presidentDisabledList(7)->total()
            //     + Disposal::presidentDisabledList(7)->total()
            //     + TransferAsset::CountDisabled(7)
            //     + SaleAsset::presidentDisabledList(7)->total()
            //     + SendReceive::presidentDisabledList(7)->total()
            //     + ReturnBudget::presidentDisabledList(7)->total()
            //     + Training::presidentDisabledList(7)->total()
            //     + RequestMemo::CountDisabled(7),

            // 'TSP' => Resign::presidentDisabledList(8)->total()
            //     + RequestForm::presidentDisabledList(8)->total()
            //     + RequestHR::presidentDisabledList(8)->total()
            //     + HRRequest::presidentDisabledList(8)->total()
            //     + CashAdvance::presidentDisabledList(8)->total()
            //     + RequestOT::presidentDisabledList(8)->total()
            //     + CustomLetter::CountDisabled(8)
            //     + DamagedLog::presidentDisabledList(8)->total()
            //     + Disposal::presidentDisabledList(8)->total()
            //     + TransferAsset::CountDisabled(8)
            //     + SaleAsset::presidentDisabledList(8)->total()
            //     + SendReceive::presidentDisabledList(8)->total()
            //     + ReturnBudget::presidentDisabledList(8)->total()
            //     + Training::presidentDisabledList(8)->total()
            //     + RequestMemo::CountDisabled(8),

            // 'President' => Resign::presidentDisabledList(9)->total()
            //     + RequestForm::presidentDisabledList(9)->total()
            //     + RequestHR::presidentDisabledList(9)->total()
            //     + HRRequest::presidentDisabledList(9)->total()
            //     + CashAdvance::presidentDisabledList(9)->total()
            //     + EmployeePenalty::presidentDisabledList(9)->total()
            //     + RequestGasoline::CountDisabled(9)
            //     + Mission::presidentDisabledList(9)->total()
            //     + MissionClearance::CountDisabled(9)
            //     + RequestOT::presidentDisabledList(9)->total()
            //     + CustomLetter::CountDisabled(9)
            //     + DamagedLog::presidentDisabledList(9)->total()
            //     + Disposal::presidentDisabledList(9)->total()
            //     + TransferAsset::CountDisabled(9)
            //     + SaleAsset::presidentDisabledList(9)->total()
            //     + SendReceive::presidentDisabledList(9)->total()
            //     + ReturnBudget::presidentDisabledList(9)->total()
            //     + Training::presidentDisabledList(9)->total()
            //     + RequestMemo::CountDisabled(9),

            // 'BRC' => Resign::presidentDisabledList(15)->total()
            //     + RequestForm::presidentDisabledList(15)->total()
            //     + RequestHR::presidentDisabledList(15)->total()
            //     + HRRequest::presidentDisabledList(15)->total()
            //     + CashAdvance::presidentDisabledList(15)->total()
            //     + EmployeePenalty::presidentDisabledList(15)->total()
            //     + RequestGasoline::CountDisabled(15)
            //     + Mission::presidentDisabledList(15)->total()
            //     + MissionClearance::CountDisabled(15)
            //     + RequestOT::presidentDisabledList(15)->total()
            //     + CustomLetter::CountDisabled(15)
            //     + DamagedLog::presidentDisabledList(15)->total()
            //     + Disposal::presidentDisabledList(15)->total()
            //     + TransferAsset::CountDisabled(15)
            //     + SaleAsset::presidentDisabledList(15)->total()
            //     + SendReceive::presidentDisabledList(15)->total()
            //     + ReturnBudget::presidentDisabledList(15)->total()
            //     + Training::presidentDisabledList(15)->total()
            //     + RequestMemo::CountDisabled(15)
        ];

        $data['approved'] = [
            // 'STSK' => RequestMemo::presidentApproved(1)->total()
            //     + RequestForm::presidentApproved(1)->total()
            //     + RequestHR::presidentApproved(1)->total()
            //     + Disposal::presidentApproved(1)->total()
            //     + DamagedLog::presidentApproved(1)->total()
            //     + HRRequest::presidentApproved(1)->total()
            //     + SaleAsset::presidentApproved(1)->total()
            //     + SendReceive::presidentApproved(1)->total()
            //     + ReturnBudget::presidentApproved(1)->total()
            //     + Mission::presidentApproved(1)->total()
            //     + MissionItem::presidentApproved(1)->total()
            //     + MissionClearance::CountApproved(1)
            //     + Training::presidentApproved(1)->total()
            //     + RequestOT::presidentApproved(1)->total()
            //     + EmployeePenalty::presidentApproved(1)->total()
            //     + CashAdvance::presidentApproved(1)->total()
            //     + Resign::presidentApproved(1)->total()
            //     + RequestCreateUser::CountApproved(1)
            //     + TransferAsset::CountApproved(1)
            //     + SettingReviewerApprover::CountApproved(1)
            //     + CustomLetter::CountApproved(1)
            //     + Policy::CountApproved(1)
            //     + RequestDisableUser::CountApproved(1)
            //     + RequestGasoline::CountApproved(1)
            //     + @$eachMenuCompany['approved']['STSK'],

            // 'MFI' => RequestMemo::presidentApproved(2)->total()
            //     + RequestForm::presidentApproved(2)->total()
            //     + RequestHR::presidentApproved(2)->total()
            //     + Disposal::presidentApproved(2)->total()
            //     + DamagedLog::presidentApproved(2)->total()
            //     + HRRequest::presidentApproved(2)->total()
            //     + Loan::CountApproved(2)
            //     + SaleAsset::presidentApproved(2)->total()
            //     + SendReceive::presidentApproved(2)->total()
            //     + ReturnBudget::presidentApproved(2)->total()
            //     + RescheduleLoan::presidentApproved(2)->total()
            //     + Mission::presidentApproved(2)->total()
            //     + MissionItem::presidentApproved(2)->total()
            //     + MissionClearance::CountApproved(2)
            //     + Training::presidentApproved(2)->total()
            //     + RequestOT::presidentApproved(2)->total()
            //     + Penalty::presidentApproved(2)->total()
            //     + Penalty::presidentApprovedInterest(2)->total()
            //     + Penalty::presidentApprovedAssociation(2)->total()
            //     + EmployeePenalty::presidentApproved(2)->total()
            //     + CashAdvance::presidentApproved(2)->total()
            //     + Resign::presidentApproved(2)->total()
            //     + GeneralRequest::CountApproved(2)
            //     + RequestCreateUser::CountApproved(2)
            //     + TransferAsset::CountApproved(2)
            //     + SettingReviewerApprover::CountApproved(2)
            //     + Association::CountApproved(2)
            //     + Survey::CountApproved(2)
            //     + CustomLetter::CountApproved(2)
            //     + Policy::CountApproved(2)
            //     + RequestDisableUser::CountApproved(2)
            //     + WithdrawalCollateral::CountApproved(2)
            //     + VillageLoan::CountApproved(2)
            //     + RequestGasoline::CountApproved(2)
            //     + @$eachMenuCompany['approved']['MFI'],

            // 'NGO' => RequestMemo::presidentApproved(3)->total()
            //     + RequestForm::presidentApproved(3)->total()
            //     + RequestHR::presidentApproved(3)->total()
            //     + Disposal::presidentApproved(3)->total()
            //     + DamagedLog::presidentApproved(3)->total()
            //     + HRRequest::presidentApproved(3)->total()
            //     + Loan::CountApproved(3)
            //     + SaleAsset::presidentApproved(3)->total()
            //     + SendReceive::presidentApproved(3)->total()
            //     + ReturnBudget::presidentApproved(3)->total()
            //     + RescheduleLoan::presidentApproved(3)->total()
            //     + Mission::presidentApproved(3)->total()
            //     + MissionItem::presidentApproved(3)->total()
            //     + MissionClearance::CountApproved(3)
            //     + Training::presidentApproved(3)->total()
            //     + RequestOT::presidentApproved(3)->total()
            //     + Penalty::presidentApproved(3)->total()
            //     + Penalty::presidentApprovedInterest(3)->total()
            //     + Penalty::presidentApprovedAssociation(3)->total()
            //     + EmployeePenalty::presidentApproved(3)->total()
            //     + CashAdvance::presidentApproved(3)->total()
            //     + Resign::presidentApproved(3)->total()
            //     + GeneralRequest::CountApproved(3)
            //     + RequestCreateUser::CountApproved(3)
            //     + TransferAsset::CountApproved(3)
            //     + SettingReviewerApprover::CountApproved(3)
            //     + Association::CountApproved(3)
            //     + Survey::CountApproved(3)
            //     + CustomLetter::CountApproved(3)
            //     + Policy::CountApproved(3)
            //     + RequestDisableUser::CountApproved(3)
            //     + WithdrawalCollateral::CountApproved(3)
            //     + VillageLoan::CountApproved(3)
            //     + RequestGasoline::CountApproved(3)
            //     + @$eachMenuCompany['approved']['NGO'],

            // 'PWS' => RequestMemo::presidentApproved(14)->total()
            //     + RequestForm::presidentApproved(14)->total()
            //     + RequestHR::presidentApproved(14)->total()
            //     + Disposal::presidentApproved(14)->total()
            //     + DamagedLog::presidentApproved(14)->total()
            //     + HRRequest::presidentApproved(14)->total()
            //     + Loan::CountApproved(14)
            //     + SaleAsset::presidentApproved(14)->total()
            //     + SendReceive::presidentApproved(14)->total()
            //     + ReturnBudget::presidentApproved(14)->total()
            //     + RescheduleLoan::presidentApproved(14)->total()
            //     + Mission::presidentApproved(14)->total()
            //     + MissionItem::presidentApproved(14)->total()
            //     + MissionClearance::CountApproved(14)
            //     + Training::presidentApproved(14)->total()
            //     + RequestOT::presidentApproved(14)->total()
            //     + Penalty::presidentApproved(14)->total()
            //     + Penalty::presidentApprovedInterest(14)->total()
            //     + Penalty::presidentApprovedAssociation(14)->total()
            //     + EmployeePenalty::presidentApproved(14)->total()
            //     + CashAdvance::presidentApproved(14)->total()
            //     + Resign::presidentApproved(14)->total()
            //     + GeneralRequest::CountApproved(14)
            //     + RequestCreateUser::CountApproved(14)
            //     + TransferAsset::CountApproved(14)
            //     + SettingReviewerApprover::CountApproved(14)
            //     + Association::CountApproved(14)
            //     + Survey::CountApproved(14)
            //     + CustomLetter::CountApproved(14)
            //     + Policy::CountApproved(14)
            //     + RequestDisableUser::CountApproved(14)
            //     + WithdrawalCollateral::CountApproved(14)
            //     + VillageLoan::CountApproved(14)
            //     + RequestGasoline::CountApproved(14)
            //     + @$eachMenuCompany['approved']['PWS'],

            'ORD' => RequestMemo::presidentApproved(4)->total()
                + RequestForm::presidentApproved(4)->total()
                + RequestPR::presidentApproved(4)->total()
                + RequestPO::presidentApproved(4)->total()
                + RequestGRN::presidentApproved(4)->total()
                + RequestHR::presidentApproved(4)->total()
                + Disposal::presidentApproved(4)->total()
                + DamagedLog::presidentApproved(4)->total()
                + HRRequest::presidentApproved(4)->total()
                + SaleAsset::presidentApproved(4)->total()
                + SendReceive::presidentApproved(4)->total()
                + ReturnBudget::presidentApproved(4)->total()
                + Training::presidentApproved(4)->total()
                + RequestOT::presidentApproved(4)->total()
                + EmployeePenalty::presidentApproved(4)->total()
                + CashAdvance::presidentApproved(4)->total()
                + Resign::presidentApproved(4)->total()
                + RequestCreateUser::CountApproved(4)
                + TransferAsset::CountApproved(4)
                + SettingReviewerApprover::CountApproved(4)
                + CustomLetter::CountApproved(4)
                + Policy::CountApproved(4)
                + RequestDisableUser::CountApproved(4)
                + @$eachMenuCompany['approved']['ORD'],

            'ORD2' => RequestMemo::presidentApproved(16)->total()
                + RequestForm::presidentApproved(16)->total()
                + RequestPR::presidentApproved(16)->total()
                + RequestPO::presidentApproved(16)->total()
                + RequestGRN::presidentApproved(16)->total()
                + RequestHR::presidentApproved(16)->total()
                + Disposal::presidentApproved(16)->total()
                + DamagedLog::presidentApproved(16)->total()
                + HRRequest::presidentApproved(16)->total()
                + SaleAsset::presidentApproved(16)->total()
                + SendReceive::presidentApproved(16)->total()
                + ReturnBudget::presidentApproved(16)->total()
                + Training::presidentApproved(16)->total()
                + RequestOT::presidentApproved(16)->total()
                + EmployeePenalty::presidentApproved(16)->total()
                + CashAdvance::presidentApproved(16)->total()
                + Resign::presidentApproved(16)->total()
                + RequestCreateUser::CountApproved(16)
                + TransferAsset::CountApproved(16)
                + SettingReviewerApprover::CountApproved(16)
                + CustomLetter::CountApproved(16)
                + Policy::CountApproved(16)
                + RequestDisableUser::CountApproved(16)
                + @$eachMenuCompany['approved']['ORD2'],

            // 'ST' => RequestMemo::presidentApproved(5)->total()
            //     + RequestForm::presidentApproved(5)->total()
            //     + RequestHR::presidentApproved(5)->total()
            //     + Disposal::presidentApproved(5)->total()
            //     + DamagedLog::presidentApproved(5)->total()
            //     + HRRequest::presidentApproved(5)->total()
            //     + SaleAsset::presidentApproved(5)->total()
            //     + SendReceive::presidentApproved(5)->total()
            //     + ReturnBudget::presidentApproved(5)->total()
            //     + Training::presidentApproved(5)->total()
            //     + RequestOT::presidentApproved(5)->total()
            //     + EmployeePenalty::presidentApproved(5)->total()
            //     + CashAdvance::presidentApproved(5)->total()
            //     + Resign::presidentApproved(5)->total()
            //     + RequestCreateUser::CountApproved(5)
            //     + TransferAsset::CountApproved(5)
            //     + SettingReviewerApprover::CountApproved(5)
            //     + CustomLetter::CountApproved(5)
            //     + Policy::CountApproved(5)
            //     + RequestDisableUser::CountApproved(5)
            //     + @$eachMenuCompany['approved']['ST'],

            // 'MMI' => RequestMemo::presidentApproved(6)->total()
            //     + RequestForm::presidentApproved(6)->total()
            //     + RequestHR::presidentApproved(6)->total()
            //     + Disposal::presidentApproved(6)->total()
            //     + DamagedLog::presidentApproved(6)->total()
            //     + HRRequest::presidentApproved(6)->total()
            //     + SaleAsset::presidentApproved(6)->total()
            //     + SendReceive::presidentApproved(6)->total()
            //     + ReturnBudget::presidentApproved(6)->total()
            //     + Mission::presidentApproved(6)->total()
            //     + MissionItem::presidentApproved(6)->total()
            //     + MissionClearance::CountApproved(6)
            //     + Training::presidentApproved(6)->total()
            //     + RequestOT::presidentApproved(6)->total()
            //     + EmployeePenalty::presidentApproved(6)->total()
            //     + CashAdvance::presidentApproved(6)->total()
            //     + Resign::presidentApproved(6)->total()
            //     + RequestCreateUser::CountApproved(6)
            //     + TransferAsset::CountApproved(6)
            //     + SettingReviewerApprover::CountApproved(6)
            //     + CustomLetter::CountApproved(6)
            //     + Policy::CountApproved(6)
            //     + RequestDisableUser::CountApproved(6)
            //     + RequestGasoline::CountApproved(6)
            //     + @$eachMenuCompany['approved']['MMI'],

            // 'MHT' => RequestMemo::presidentApproved(7)->total()
            //     + RequestForm::presidentApproved(7)->total()
            //     + RequestHR::presidentApproved(7)->total()
            //     + Disposal::presidentApproved(7)->total()
            //     + DamagedLog::presidentApproved(7)->total()
            //     + HRRequest::presidentApproved(7)->total()
            //     + SaleAsset::presidentApproved(7)->total()
            //     + SendReceive::presidentApproved(7)->total()
            //     + ReturnBudget::presidentApproved(7)->total()
            //     + Training::presidentApproved(7)->total()
            //     + RequestOT::presidentApproved(7)->total()
            //     + EmployeePenalty::presidentApproved(7)->total()
            //     + CashAdvance::presidentApproved(7)->total()
            //     + Resign::presidentApproved(7)->total()
            //     + RequestCreateUser::CountApproved(7)
            //     + TransferAsset::CountApproved(7)
            //     + SettingReviewerApprover::CountApproved(7)
            //     + CustomLetter::CountApproved(7)
            //     + Policy::CountApproved(7)
            //     + RequestDisableUser::CountApproved(7)
            //     + @$eachMenuCompany['approved']['MHT'],

            // 'TSP' => RequestMemo::presidentApproved(8)->total()
            //     + RequestForm::presidentApproved(8)->total()
            //     + RequestHR::presidentApproved(8)->total()
            //     + Disposal::presidentApproved(8)->total()
            //     + DamagedLog::presidentApproved(8)->total()
            //     + HRRequest::presidentApproved(8)->total()
            //     + SaleAsset::presidentApproved(8)->total()
            //     + SendReceive::presidentApproved(8)->total()
            //     + ReturnBudget::presidentApproved(8)->total()
            //     + Training::presidentApproved(8)->total()
            //     + RequestOT::presidentApproved(8)->total()
            //     + EmployeePenalty::presidentApproved(8)->total()
            //     + CashAdvance::presidentApproved(8)->total()
            //     + Resign::presidentApproved(8)->total()
            //     + RequestCreateUser::CountApproved(8)
            //     + TransferAsset::CountApproved(8)
            //     + SettingReviewerApprover::CountApproved(8)
            //     + CustomLetter::CountApproved(8)
            //     + Policy::CountApproved(8)
            //     + RequestDisableUser::CountApproved(8)
            //     + @$eachMenuCompany['approved']['TSP'],

            // 'President' => RequestMemo::presidentApproved(9)->total()
            //     + RequestForm::presidentApproved(9)->total()
            //     + RequestHR::presidentApproved(9)->total()
            //     + Disposal::presidentApproved(9)->total()
            //     + DamagedLog::presidentApproved(9)->total()
            //     + HRRequest::presidentApproved(9)->total()
            //     + SaleAsset::presidentApproved(9)->total()
            //     + SendReceive::presidentApproved(9)->total()
            //     + ReturnBudget::presidentApproved(9)->total()
            //     + Training::presidentApproved(9)->total()
            //     + RequestOT::presidentApproved(9)->total()
            //     + EmployeePenalty::presidentApproved(9)->total()
            //     + CashAdvance::presidentApproved(9)->total()
            //     + Resign::presidentApproved(9)->total()
            //     + RequestCreateUser::CountApproved(9)
            //     + TransferAsset::CountApproved(9)
            //     + SettingReviewerApprover::CountApproved(9)
            //     + CustomLetter::CountApproved(9)
            //     + Policy::CountApproved(9)
            //     + RequestDisableUser::CountApproved(9)
            //     + @$eachMenuCompany['approved']['President'],

            // 'MDN' => RequestMemo::CountApproved(10)
            //     + RequestForm::presidentApproved(10)->total()
            //     + RequestHR::presidentApproved(10)->total()
            //     + Disposal::presidentApproved(10)->total()
            //     + DamagedLog::presidentApproved(10)->total()
            //     + HRRequest::presidentApproved(10)->total()
            //     + SaleAsset::presidentApproved(10)->total()
            //     + SendReceive::presidentApproved(10)->total()
            //     + ReturnBudget::presidentApproved(10)->total()
            //     + Mission::presidentApproved(10)->total()
            //     + MissionItem::presidentApproved(10)->total()
            //     + Training::presidentApproved(10)->total()
            //     + RequestOT::presidentApproved(10)->total()
            //     + EmployeePenalty::presidentApproved(10)->total()
            //     + CashAdvance::presidentApproved(10)->total()
            //     + Resign::presidentApproved(10)->total()
            //     + RequestCreateUser::CountApproved(10)
            //     + TransferAsset::CountApproved(10)
            //     + SettingReviewerApprover::CountApproved(10)
            //     + CustomLetter::CountApproved(10)
            //     + Policy::CountApproved(10)
            //     + RequestDisableUser::CountApproved(10)
            //     + @$eachMenuCompany['approved']['MDN'],

            // 'PTK' => RequestMemo::CountApproved(11)
            //     + RequestForm::presidentApproved(11)->total()
            //     + RequestHR::presidentApproved(11)->total()
            //     + Disposal::presidentApproved(11)->total()
            //     + DamagedLog::presidentApproved(11)->total()
            //     + HRRequest::presidentApproved(11)->total()
            //     + SaleAsset::presidentApproved(11)->total()
            //     + SendReceive::presidentApproved(11)->total()
            //     + ReturnBudget::presidentApproved(11)->total()
            //     + Mission::presidentApproved(11)->total()
            //     + MissionItem::presidentApproved(11)->total()
            //     + Training::presidentApproved(11)->total()
            //     + RequestOT::presidentApproved(11)->total()
            //     + EmployeePenalty::presidentApproved(11)->total()
            //     + CashAdvance::presidentApproved(11)->total()
            //     + Resign::presidentApproved(11)->total()
            //     + RequestCreateUser::CountApproved(11)
            //     + TransferAsset::CountApproved(11)
            //     + SettingReviewerApprover::CountApproved(11)
            //     + CustomLetter::CountApproved(11)
            //     + Policy::CountApproved(11)
            //     + RequestDisableUser::CountApproved(11)
            //     + @$eachMenuCompany['approved']['PTK'],

            // 'NIYA' => RequestMemo::CountApproved(12)
            //     + RequestForm::presidentApproved(12)->total()
            //     + RequestHR::presidentApproved(12)->total()
            //     + Disposal::presidentApproved(12)->total()
            //     + DamagedLog::presidentApproved(12)->total()
            //     + HRRequest::presidentApproved(12)->total()
            //     + SaleAsset::presidentApproved(12)->total()
            //     + SendReceive::presidentApproved(12)->total()
            //     + ReturnBudget::presidentApproved(12)->total()
            //     + Mission::presidentApproved(12)->total()
            //     + MissionItem::presidentApproved(12)->total()
            //     + Training::presidentApproved(12)->total()
            //     + RequestOT::presidentApproved(12)->total()
            //     + EmployeePenalty::presidentApproved(12)->total()
            //     + CashAdvance::presidentApproved(12)->total()
            //     + Resign::presidentApproved(12)->total()
            //     + RequestCreateUser::CountApproved(12)
            //     + TransferAsset::CountApproved(12)
            //     + SettingReviewerApprover::CountApproved(12)
            //     + CustomLetter::CountApproved(12)
            //     + Policy::CountApproved(12)
            //     + RequestDisableUser::CountApproved(12)
            //     + @$eachMenuCompany['approved']['NIYA'],

            // 'DMS' => RequestMemo::CountApproved(13)
            //     + RequestForm::presidentApproved(13)->total()
            //     + RequestHR::presidentApproved(13)->total()
            //     + Disposal::presidentApproved(13)->total()
            //     + DamagedLog::presidentApproved(13)->total()
            //     + HRRequest::presidentApproved(13)->total()
            //     + SaleAsset::presidentApproved(13)->total()
            //     + SendReceive::presidentApproved(13)->total()
            //     + ReturnBudget::presidentApproved(13)->total()
            //     + Mission::presidentApproved(13)->total()
            //     + MissionItem::presidentApproved(13)->total()
            //     + Training::presidentApproved(13)->total()
            //     + RequestOT::presidentApproved(13)->total()
            //     + EmployeePenalty::presidentApproved(13)->total()
            //     + CashAdvance::presidentApproved(13)->total()
            //     + Resign::presidentApproved(13)->total()
            //     + RequestCreateUser::CountApproved(13)
            //     + TransferAsset::CountApproved(13)
            //     + SettingReviewerApprover::CountApproved(13)
            //     + CustomLetter::CountApproved(13)
            //     + Policy::CountApproved(13)
            //     + RequestDisableUser::CountApproved(13)
            //     + @$eachMenuCompany['approved']['DMS'],

            // 'BRC' => RequestMemo::CountApproved(15)
            //     + RequestForm::presidentApproved(15)->total()
            //     + RequestHR::presidentApproved(15)->total()
            //     + Disposal::presidentApproved(15)->total()
            //     + DamagedLog::presidentApproved(15)->total()
            //     + HRRequest::presidentApproved(15)->total()
            //     + SaleAsset::presidentApproved(15)->total()
            //     + SendReceive::presidentApproved(15)->total()
            //     + ReturnBudget::presidentApproved(15)->total()
            //     + Training::presidentApproved(15)->total()
            //     + RequestOT::presidentApproved(15)->total()
            //     + EmployeePenalty::presidentApproved(15)->total()
            //     + CashAdvance::presidentApproved(15)->total()
            //     + Resign::presidentApproved(15)->total()
            //     + RequestCreateUser::CountApproved(15)
            //     + TransferAsset::CountApproved(15)
            //     + SettingReviewerApprover::CountApproved(15)
            //     + CustomLetter::CountApproved(15)
            //     + Policy::CountApproved(15)
            //     + RequestDisableUser::CountApproved(15)
            //     + BorrowingLoan::CountApproved(15)
            //     + @$eachMenuCompany['approved']['BRC'],
        ];

//        $data['company_departments'] = @$totalReportEachDepartmentByCompanyPresident;
        $data['tags'] = $groupRequest->presidentGetTagsList();
        $data['to_approve_group_support'] = $groupRequest->countToApproveGroupSupportForPresident();
        $data['request'] = \request()->all();
        return $data;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        ini_set("memory_limit", -1);
        
        $company = \request()->company;
        $type = \request()->type;
        $menu = \request()->menu;
        $data = [
            'company' => $company,
            'type' => $type,
            'menu' => $menu,
        ];
        if(Auth::id() == getCEO()->id) {
            $data = $this->president();
            return response()->json($data);
        }
        else {
            $data['pending'] = $this->pendingManagement();
            $data['to_approve'] = $this->countToApproveByCompanyForManagement();
            $data['rejected'] = $this->rejectedManagement();
            $data['disabled'] = $this->disabledManagement();
            $data['approved'] = $this->approvedManagement();
            return response()->json($data);
        }
    }
    private function management()
    {
        $company = \request()->company;
        $type = \request()->type;
        $data = [
            'company' => $company,
            'type' => $type,
        ];
        $groupRequest = new GroupRequest();
        $eachMenuCompany = $groupRequest->totalEachMenuCompany();

        $data['to_approve'] = [
            // 'STSK' => RequestMemo::presidentApprove(1)->total()
            //     + RequestForm::presidentApprove(1)->total()
            //     + RequestHR::presidentApprove(1)->total()
            //     + Disposal::presidentApprove(1)->total()
            //     + DamagedLog::presidentApprove(1)->total()
            //     + HRRequest::presidentApprove(1)->total()
            //     + Loan::presidentApprove(1)->total()
            //     + SaleAsset::presidentApprove(1)->total()
            //     + ReturnBudget::presidentApprove(1)->total()
            //     + RescheduleLoan::presidentApprove(1)->total()
            //     + Mission::presidentApprove(1)->total()
            //     + MissionItem::presidentApprove(1)->total()
            //     + MissionClearance::CountApproved(1)
            //     + Training::presidentApprove(1)->total()
            //     + RequestGasoline::CountApproved(1)
            //     + RequestOT::presidentApprove(1)->total(),

            // 'MFI' => RequestMemo::presidentApprove(2)->total()
            //     + RequestForm::presidentApprove(2)->total()
            //     + RequestHR::presidentApprove(2)->total()
            //     + Disposal::presidentApprove(2)->total()
            //     + DamagedLog::presidentApprove(2)->total()
            //     + HRRequest::presidentApprove(2)->total()
            //     + SaleAsset::presidentApprove(2)->total()
            //     + ReturnBudget::presidentApprove(2)->total()
            //     + Loan::presidentApprove(2)->total()
            //     + RescheduleLoan::presidentApprove(2)->total()
            //     + Mission::presidentApprove(2)->total()
            //     + MissionItem::presidentApprove(2)->total()
            //     + MissionClearance::CountApproved(2)
            //     + Training::presidentApprove(2)->total()
            //     + RequestGasoline::CountApproved(2)
            //     + RequestOT::presidentApprove(2)->total(),

            // 'NGO' => RequestMemo::presidentApprove(3)->total()
            //     + RequestForm::presidentApprove(3)->total()
            //     + RequestHR::presidentApprove(3)->total()
            //     + Disposal::presidentApprove(3)->total()
            //     + DamagedLog::presidentApprove(3)->total()
            //     + HRRequest::presidentApprove(3)->total()
            //     + SaleAsset::presidentApprove(3)->total()
            //     + ReturnBudget::presidentApprove(3)->total()
            //     + Loan::presidentApprove(3)->total()
            //     + RescheduleLoan::presidentApprove(3)->total()
            //     + Mission::presidentApprove(3)->total()
            //     + MissionItem::presidentApprove(3)->total()
            //     + MissionClearance::CountApproved(3)
            //     + Training::presidentApprove(3)->total()
            //     + RequestGasoline::CountApproved(3)
            //     + RequestOT::presidentApprove(3)->total(),

            'ORD' => RequestMemo::presidentApprove(4)->total()
                + RequestForm::presidentApprove(4)->total()
                + RequestPR::presidentApprove(4)->total()
                + RequestPO::presidentApprove(4)->total()
                + RequestGRN::presidentApprove(4)->total()
                + RequestHR::presidentApprove(4)->total()
                + Disposal::presidentApprove(4)->total()
                + DamagedLog::presidentApprove(4)->total()
                + HRRequest::presidentApprove(4)->total()
                + SaleAsset::presidentApprove(4)->total()
                + ReturnBudget::presidentApprove(4)->total()
                + Loan::presidentApprove(4)->total()
                + RescheduleLoan::presidentApprove(4)->total()
                + Training::presidentApprove(4)->total()
                + RequestOT::presidentApprove(4)->total(),

            // 'ST' => RequestMemo::presidentApprove(5)->total()
            //     + RequestForm::presidentApprove(5)->total()
            //     + RequestHR::presidentApprove(5)->total()
            //     + Disposal::presidentApprove(5)->total()
            //     + DamagedLog::presidentApprove(5)->total()
            //     + HRRequest::presidentApprove(5)->total()
            //     + SaleAsset::presidentApprove(5)->total()
            //     + ReturnBudget::presidentApprove(5)->total()
            //     + Loan::presidentApprove(5)->total()
            //     + RescheduleLoan::presidentApprove(5)->total()
            //     + Training::presidentApprove(5)->total()
            //     + RequestOT::presidentApprove(5)->total(),

            // 'MMI' => RequestMemo::presidentApprove(6)->total()
            //     + RequestForm::presidentApprove(6)->total()
            //     + RequestHR::presidentApprove(6)->total()
            //     + Disposal::presidentApprove(6)->total()
            //     + DamagedLog::presidentApprove(6)->total()
            //     + HRRequest::presidentApprove(6)->total()
            //     + SaleAsset::presidentApprove(6)->total()
            //     + ReturnBudget::presidentApprove(6)->total()
            //     + Loan::presidentApprove(6)->total()
            //     + RescheduleLoan::presidentApprove(6)->total()
            //     + Mission::presidentApprove(6)->total()
            //     + MissionItem::presidentApprove(6)->total()
            //     + MissionClearance::CountApproved(6)
            //     + RequestGasoline::CountApproved(6)
            //     + Training::presidentApprove(6)->total()
            //     + RequestOT::presidentApprove(6)->total(),

            // 'MHT' => RequestMemo::presidentApprove(7)->total()
            //     + RequestForm::presidentApprove(7)->total()
            //     + RequestHR::presidentApprove(7)->total()
            //     + Disposal::presidentApprove(7)->total()
            //     + DamagedLog::presidentApprove(7)->total()
            //     + HRRequest::presidentApprove(7)->total()
            //     + SaleAsset::presidentApprove(7)->total()
            //     + ReturnBudget::presidentApprove(7)->total()
            //     + Loan::presidentApprove(7)->total()
            //     + RescheduleLoan::presidentApprove(7)->total()
            //     + Training::presidentApprove(7)->total()
            //     + RequestOT::presidentApprove(7)->total(),

            // 'TSP' => RequestMemo::presidentApprove(8)->total()
            //     + RequestForm::presidentApprove(8)->total()
            //     + RequestHR::presidentApprove(8)->total()
            //     + Disposal::presidentApprove(8)->total()
            //     + DamagedLog::presidentApprove(8)->total()
            //     + HRRequest::presidentApprove(8)->total()
            //     + SaleAsset::presidentApprove(8)->total()
            //     + ReturnBudget::presidentApprove(8)->total()
            //     + Loan::presidentApprove(8)->total()
            //     + RescheduleLoan::presidentApprove(8)->total()
            //     + Training::presidentApprove(8)->total()
            //     + RequestOT::presidentApprove(8)->total(),
        ];

        $data['rejected'] = [
            // 'STSK' => RequestMemo::presidentRejectedList(1)->total()
            //     + RequestForm::presidentRejectedList(1)->total()
            //     + RequestHR::presidentRejectedList(1)->total()
            //     + Disposal::presidentRejectedList(1)->total()
            //     + DamagedLog::presidentRejectedList(1)->total()
            //     + HRRequest::presidentRejectedList(1)->total()
            //     + SaleAsset::presidentRejectedList(1)->total()
            //     + ReturnBudget::presidentRejectedList(1)->total()
            //     + Loan::presidentRejectedList(1)->total()
            //     + RescheduleLoan::presidentRejectedList(1)->total()
            //     + Mission::presidentRejectedList(1)->total()
            //     + MissionItem::presidentRejectedList(1)->total()
            //     + MissionClearance::CountRejected(1)
            //     + RequestGasoline::CountRejected(1)
            //     + Training::presidentRejectedList(1)->total()
            //     + RequestOT::presidentRejectedList(1)->total()
            //     + @$eachMenuCompany['reject']['STSK'],

            // 'MFI' => RequestMemo::presidentRejectedList(2)->total()
            //     + RequestForm::presidentRejectedList(2)->total()
            //     + RequestHR::presidentRejectedList(2)->total()
            //     + Disposal::presidentRejectedList(2)->total()
            //     + DamagedLog::presidentRejectedList(2)->total()
            //     + HRRequest::presidentRejectedList(2)->total()
            //     + SaleAsset::presidentRejectedList(2)->total()
            //     + ReturnBudget::presidentRejectedList(2)->total()
            //     + Loan::presidentRejectedList(2)->total()
            //     + RescheduleLoan::presidentRejectedList(2)->total()
            //     + Mission::presidentRejectedList(2)->total()
            //     + MissionItem::presidentRejectedList(2)->total()
            //     + MissionClearance::CountRejected(2)
            //     + RequestGasoline::CountRejected(2)
            //     + Training::presidentRejectedList(2)->total()
            //     + RequestOT::presidentRejectedList(2)->total()
            //     + @$eachMenuCompany['reject']['MFI'],

            // 'NGO' => RequestMemo::presidentRejectedList(3)->total()
            //     + RequestForm::presidentRejectedList(3)->total()
            //     + RequestHR::presidentRejectedList(3)->total()
            //     + Disposal::presidentRejectedList(3)->total()
            //     + DamagedLog::presidentRejectedList(3)->total()
            //     + HRRequest::presidentRejectedList(3)->total()
            //     + SaleAsset::presidentRejectedList(3)->total()
            //     + ReturnBudget::presidentRejectedList(3)->total()
            //     + Loan::presidentRejectedList(3)->total()
            //     + RescheduleLoan::presidentRejectedList(3)->total()
            //     + Mission::presidentRejectedList(3)->total()
            //     + MissionItem::presidentRejectedList(3)->total()
            //     + MissionClearance::CountRejected(3)
            //     + RequestGasoline::CountRejected(3)
            //     + Training::presidentRejectedList(3)->total()
            //     + RequestOT::presidentRejectedList(3)->total()
            //     + @$eachMenuCompany['reject']['NGO'],

            'ORD' => RequestMemo::presidentRejectedList(4)->total()
                + RequestForm::presidentRejectedList(4)->total()
                + RequestPR::presidentRejectedList(4)->total()
                + RequestPO::presidentRejectedList(4)->total()
                + RequestGRN::presidentRejectedList(4)->total()
                + RequestHR::presidentRejectedList(4)->total()
                + Disposal::presidentRejectedList(4)->total()
                + DamagedLog::presidentRejectedList(4)->total()
                + HRRequest::presidentRejectedList(4)->total()
                + SaleAsset::presidentRejectedList(4)->total()
                + ReturnBudget::presidentRejectedList(4)->total()
                + Loan::presidentRejectedList(4)->total()
                + RescheduleLoan::presidentRejectedList(4)->total()
                + Training::presidentRejectedList(4)->total()
                + RequestOT::presidentRejectedList(4)->total()
                + @$eachMenuCompany['reject']['ORD'],

            // 'ST' => RequestMemo::presidentRejectedList(5)->total()
            //     + RequestForm::presidentRejectedList(5)->total()
            //     + RequestHR::presidentRejectedList(5)->total()
            //     + Disposal::presidentRejectedList(5)->total()
            //     + DamagedLog::presidentRejectedList(5)->total()
            //     + HRRequest::presidentRejectedList(5)->total()
            //     + SaleAsset::presidentRejectedList(5)->total()
            //     + ReturnBudget::presidentRejectedList(5)->total()
            //     + Loan::presidentRejectedList(5)->total()
            //     + RescheduleLoan::presidentRejectedList(5)->total()
            //     + Training::presidentRejectedList(5)->total()
            //     + RequestOT::presidentRejectedList(5)->total()
            //     + @$eachMenuCompany['reject']['ST'],

            // 'MMI' => RequestMemo::presidentRejectedList(6)->total()
            //     + RequestForm::presidentRejectedList(6)->total()
            //     + RequestHR::presidentRejectedList(6)->total()
            //     + Disposal::presidentRejectedList(6)->total()
            //     + DamagedLog::presidentRejectedList(6)->total()
            //     + HRRequest::presidentRejectedList(6)->total()
            //     + SaleAsset::presidentRejectedList(6)->total()
            //     + ReturnBudget::presidentRejectedList(6)->total()
            //     + Loan::presidentRejectedList(6)->total()
            //     + RescheduleLoan::presidentRejectedList(6)->total()
            //     + Mission::presidentRejectedList(6)->total()
            //     + MissionItem::presidentRejectedList(6)->total()
            //     + MissionClearance::CountRejected(6)
            //     + RequestGasoline::CountRejected(6)
            //     + Training::presidentRejectedList(6)->total()
            //     + RequestOT::presidentRejectedList(6)->total()
            //     + @$eachMenuCompany['reject']['MMI'],

            // 'MHT' => RequestMemo::presidentRejectedList(7)->total()
            //     + RequestForm::presidentRejectedList(7)->total()
            //     + RequestHR::presidentRejectedList(7)->total()
            //     + Disposal::presidentRejectedList(7)->total()
            //     + DamagedLog::presidentRejectedList(7)->total()
            //     + HRRequest::presidentRejectedList(7)->total()
            //     + SaleAsset::presidentRejectedList(7)->total()
            //     + ReturnBudget::presidentRejectedList(7)->total()
            //     + Loan::presidentRejectedList(7)->total()
            //     + RescheduleLoan::presidentRejectedList(7)->total()
            //     + Training::presidentRejectedList(7)->total()
            //     + RequestOT::presidentRejectedList(7)->total()
            //     + @$eachMenuCompany['reject']['MHT'],

            // 'TSP' => RequestMemo::presidentRejectedList(8)->total()
            //     + RequestForm::presidentRejectedList(8)->total()
            //     + RequestHR::presidentRejectedList(8)->total()
            //     + Disposal::presidentRejectedList(8)->total()
            //     + DamagedLog::presidentRejectedList(8)->total()
            //     + HRRequest::presidentRejectedList(8)->total()
            //     + SaleAsset::presidentRejectedList(8)->total()
            //     + ReturnBudget::presidentRejectedList(8)->total()
            //     + Loan::presidentRejectedList(8)->total()
            //     + RescheduleLoan::presidentRejectedList(8)->total()
            //     + Training::presidentRejectedList(8)->total()
            //     + RequestOT::presidentRejectedList(8)->total()
            //     + @$eachMenuCompany['reject']['TSP'],
        ];

        $data['approved'] = [
            // 'STSK' => RequestMemo::presidentApproved(1)->total()
            //     + RequestForm::presidentApproved(1)->total()
            //     + RequestHR::presidentApproved(1)->total()
            //     + Disposal::presidentApproved(1)->total()
            //     + DamagedLog::presidentApproved(1)->total()
            //     + HRRequest::presidentApproved(1)->total()
            //     + SaleAsset::presidentApproved(1)->total()
            //     + ReturnBudget::presidentApproved(1)->total()
            //     + Loan::presidentApproved(1)->total()
            //     + RescheduleLoan::presidentApproved(1)->total()
            //     + Mission::presidentApproved(1)->total()
            //     + MissionItem::presidentApproved(1)->total()
            //     + MissionClearance::CountApproved(1)
            //     + RequestGasoline::CountApproved(1)
            //     + Training::presidentApproved(1)->total()
            //     + RequestOT::presidentApproved(1)->total()
            //     + @$eachMenuCompany['approved']['STSK'],

            // 'MFI' => RequestMemo::presidentApproved(2)->total()
            //     + RequestForm::presidentApproved(2)->total()
            //     + RequestHR::presidentApproved(2)->total()
            //     + Disposal::presidentApproved(2)->total()
            //     + DamagedLog::presidentApproved(2)->total()
            //     + HRRequest::presidentApproved(2)->total()
            //     + Loan::presidentApproved(2)->total()
            //     + SaleAsset::presidentApproved(2)->total()
            //     + ReturnBudget::presidentApproved(2)->total()
            //     + RescheduleLoan::presidentApproved(2)->total()
            //     + Mission::presidentApproved(2)->total()
            //     + MissionItem::presidentApproved(2)->total()
            //     + MissionClearance::CountApproved(2)
            //     + RequestGasoline::CountApproved(2)
            //     + Training::presidentApproved(2)->total()
            //     + RequestOT::presidentApproved(2)->total()
            //     + @$eachMenuCompany['approved']['MFI'],

            // 'NGO' => RequestMemo::presidentApproved(3)->total()
            //     + RequestForm::presidentApproved(3)->total()
            //     + RequestHR::presidentApproved(3)->total()
            //     + Disposal::presidentApproved(3)->total()
            //     + DamagedLog::presidentApproved(3)->total()
            //     + HRRequest::presidentApproved(3)->total()
            //     + Loan::presidentApproved(3)->total()
            //     + SaleAsset::presidentApproved(3)->total()
            //     + ReturnBudget::presidentApproved(3)->total()
            //     + RescheduleLoan::presidentApproved(3)->total()
            //     + Mission::presidentApproved(3)->total()
            //     + MissionItem::presidentApproved(3)->total()
            //     + MissionClearance::CountApproved(3)
            //     + RequestGasoline::CountApproved(3)
            //     + Training::presidentApproved(3)->total()
            //     + RequestOT::presidentApproved(3)->total()
            //     + @$eachMenuCompany['approved']['NGO'],

            'ORD' => RequestMemo::presidentApproved(4)->total()
                + RequestForm::presidentApproved(4)->total()
                + RequestPR::presidentApproved(4)->total()
                + RequestPO::presidentApproved(4)->total()
                + RequestGRN::presidentApproved(4)->total()
                + RequestHR::presidentApproved(4)->total()
                + Disposal::presidentApproved(4)->total()
                + DamagedLog::presidentApproved(4)->total()
                + HRRequest::presidentApproved(4)->total()
                + Loan::presidentApproved(4)->total()
                + SaleAsset::presidentApproved(4)->total()
                + ReturnBudget::presidentApproved(4)->total()
                + RescheduleLoan::presidentApproved(4)->total()
                + Training::presidentApproved(4)->total()
                + RequestOT::presidentApproved(4)->total()
                + @$eachMenuCompany['approved']['ORD'],

            // 'ST' => RequestMemo::presidentApproved(5)->total()
            //     + RequestForm::presidentApproved(5)->total()
            //     + RequestHR::presidentApproved(5)->total()
            //     + Disposal::presidentApproved(5)->total()
            //     + DamagedLog::presidentApproved(5)->total()
            //     + HRRequest::presidentApproved(5)->total()
            //     + Loan::presidentApproved(5)->total()
            //     + SaleAsset::presidentApproved(5)->total()
            //     + ReturnBudget::presidentApproved(5)->total()
            //     + RescheduleLoan::presidentApproved(5)->total()
            //     + Training::presidentApproved(5)->total()
            //     + RequestOT::presidentApproved(5)->total()
            //     + @$eachMenuCompany['approved']['ST'],

            // 'MMI' => RequestMemo::presidentApproved(6)->total()
            //     + RequestForm::presidentApproved(6)->total()
            //     + RequestHR::presidentApproved(6)->total()
            //     + Disposal::presidentApproved(6)->total()
            //     + DamagedLog::presidentApproved(6)->total()
            //     + HRRequest::presidentApproved(6)->total()
            //     + Loan::presidentApproved(6)->total()
            //     + SaleAsset::presidentApproved(6)->total()
            //     + ReturnBudget::presidentApproved(6)->total()
            //     + RescheduleLoan::presidentApproved(6)->total()
            //     + Mission::presidentApproved(6)->total()
            //     + MissionItem::presidentApproved(6)->total()
            //     + MissionClearance::CountApproved(6)
            //     + RequestGasoline::CountApproved(6)
            //     + Training::presidentApproved(6)->total()
            //     + RequestOT::presidentApproved(6)->total()
            //     + @$eachMenuCompany['approved']['MMI'],

            // 'MHT' => RequestMemo::presidentApproved(7)->total()
            //     + RequestForm::presidentApproved(7)->total()
            //     + RequestHR::presidentApproved(7)->total()
            //     + Disposal::presidentApproved(7)->total()
            //     + DamagedLog::presidentApproved(7)->total()
            //     + HRRequest::presidentApproved(7)->total()
            //     + Loan::presidentApproved(7)->total()
            //     + SaleAsset::presidentApproved(7)->total()
            //     + ReturnBudget::presidentApproved(7)->total()
            //     + RescheduleLoan::presidentApproved(7)->total()
            //     + Training::presidentApproved(7)->total()
            //     + RequestOT::presidentApproved(7)->total()
            //     + @$eachMenuCompany['approved']['MHT'],

            // 'TSP' => RequestMemo::presidentApproved(8)->total()
            //     + RequestForm::presidentApproved(8)->total()
            //     + RequestHR::presidentApproved(8)->total()
            //     + Disposal::presidentApproved(8)->total()
            //     + DamagedLog::presidentApproved(8)->total()
            //     + HRRequest::presidentApproved(8)->total()
            //     + Loan::presidentApproved(8)->total()
            //     + SaleAsset::presidentApproved(8)->total()
            //     + ReturnBudget::presidentApproved(8)->total()
            //     + RescheduleLoan::presidentApproved(8)->total()
            //     + Training::presidentApproved(8)->total()
            //     + RequestOT::presidentApproved(8)->total()
            //     + @$eachMenuCompany['approved']['TSP'],
        ];

        $data['pending'] = [
            // 'STSK' => RequestMemo::presidentpendingList(1)->total()
            //     + RequestForm::presidentpendingList(1)->total()
            //     + RequestHR::presidentpendingList(1)->total()
            //     + Disposal::presidentpendingList(1)->total()
            //     + DamagedLog::presidentpendingList(1)->total()
            //     + HRRequest::presidentpendingList(1)->total()
            //     + Loan::presidentpendingList(1)->total()
            //     + SaleAsset::presidentpendingList(1)->total()
            //     + ReturnBudget::presidentpendingList(1)->total()
            //     + RescheduleLoan::presidentpendingList(1)->total()
            //     + Mission::presidentpendingList(1)->total()
            //     + MissionItem::presidentpendingList(1)->total()
            //     + MissionClearance::CountPending(1)
            //     + RequestGasoline::CountPending(1)
            //     + Training::presidentpendingList(1)->total()
            //     + RequestOT::presidentpendingList(1)->total()
            //     + @$eachMenuCompany['pending']['STSK'],

            // 'MFI' => RequestMemo::presidentpendingList(2)->total()
            //     + RequestForm::presidentpendingList(2)->total()
            //     + RequestHR::presidentpendingList(2)->total()
            //     + Disposal::presidentpendingList(2)->total()
            //     + DamagedLog::presidentpendingList(2)->total()
            //     + HRRequest::presidentpendingList(2)->total()
            //     + Loan::presidentpendingList(2)->total()
            //     + SaleAsset::presidentpendingList(2)->total()
            //     + ReturnBudget::presidentpendingList(2)->total()
            //     + RescheduleLoan::presidentpendingList(2)->total()
            //     + Mission::presidentpendingList(2)->total()
            //     + MissionItem::presidentpendingList(2)->total()
            //     + MissionClearance::CountPending(2)
            //     + RequestGasoline::CountPending(2)
            //     + Training::presidentpendingList(2)->total()
            //     + RequestOT::presidentpendingList(2)->total()
            //     + @(integer)$eachMenuCompany['pending']['MFI'],

            // 'NGO' => RequestMemo::presidentpendingList(3)->total()
            //     + RequestForm::presidentpendingList(3)->total()
            //     + RequestHR::presidentpendingList(3)->total()
            //     + Disposal::presidentpendingList(3)->total()
            //     + DamagedLog::presidentpendingList(3)->total()
            //     + HRRequest::presidentpendingList(3)->total()
            //     + Loan::presidentpendingList(3)->total()
            //     + SaleAsset::presidentpendingList(3)->total()
            //     + ReturnBudget::presidentpendingList(3)->total()
            //     + RescheduleLoan::presidentpendingList(3)->total()
            //     + Mission::presidentpendingList(3)->total()
            //     + MissionItem::presidentpendingList(3)->total()
            //     + MissionClearance::CountPending(3)
            //     + RequestGasoline::CountPending(3)
            //     + Training::presidentpendingList(3)->total()
            //     + RequestOT::presidentpendingList(3)->total()
            //     + @(integer)$eachMenuCompany['pending']['NGO'],

            'ORD' => RequestMemo::presidentpendingList(4)->total()
                + RequestForm::presidentpendingList(4)->total()
                + RequestPR::presidentpendingList(4)->total()
                + RequestPO::presidentpendingList(4)->total()
                + RequestGRN::presidentpendingList(4)->total()
                + RequestHR::presidentpendingList(4)->total()
                + Disposal::presidentpendingList(4)->total()
                + DamagedLog::presidentpendingList(4)->total()
                + HRRequest::presidentpendingList(4)->total()
                + Loan::presidentpendingList(4)->total()
                + SaleAsset::presidentpendingList(4)->total()
                + ReturnBudget::presidentpendingList(4)->total()
                + RescheduleLoan::presidentpendingList(4)->total()
                + Training::presidentpendingList(4)->total()
                + RequestOT::presidentpendingList(4)->total()
                + @(integer)$eachMenuCompany['pending']['ORD'],

            // 'ST' => RequestMemo::presidentpendingList(5)->total()
            //     + RequestForm::presidentpendingList(5)->total()
            //     + RequestHR::presidentpendingList(5)->total()
            //     + Disposal::presidentpendingList(5)->total()
            //     + DamagedLog::presidentpendingList(5)->total()
            //     + HRRequest::presidentpendingList(5)->total()
            //     + Loan::presidentpendingList(5)->total()
            //     + SaleAsset::presidentpendingList(5)->total()
            //     + ReturnBudget::presidentpendingList(5)->total()
            //     + RescheduleLoan::presidentpendingList(5)->total()
            //     + Training::presidentpendingList(5)->total()
            //     + RequestOT::presidentpendingList(5)->total()
            //     + @(integer)$eachMenuCompany['pending']['ST'],

            // 'MMI' => RequestMemo::presidentpendingList(6)->total()
            //     + RequestForm::presidentpendingList(6)->total()
            //     + RequestHR::presidentpendingList(6)->total()
            //     + Disposal::presidentpendingList(6)->total()
            //     + DamagedLog::presidentpendingList(6)->total()
            //     + HRRequest::presidentpendingList(6)->total()
            //     + Loan::presidentpendingList(6)->total()
            //     + SaleAsset::presidentpendingList(6)->total()
            //     + ReturnBudget::presidentpendingList(6)->total()
            //     + RescheduleLoan::presidentpendingList(6)->total()
            //     + Mission::presidentpendingList(6)->total()
            //     + MissionItem::presidentpendingList(6)->total()
            //     + MissionClearance::CountPending(6)
            //     + RequestGasoline::CountPending(6)
            //     + Training::presidentpendingList(6)->total()
            //     + RequestOT::presidentpendingList(6)->total()
            //     + @(integer)$eachMenuCompany['pending']['MMI'],

            // 'MHT' => RequestMemo::presidentpendingList(7)->total()
            //     + RequestForm::presidentpendingList(7)->total()
            //     + RequestHR::presidentpendingList(7)->total()
            //     + Disposal::presidentpendingList(7)->total()
            //     + DamagedLog::presidentpendingList(7)->total()
            //     + HRRequest::presidentpendingList(7)->total()
            //     + Loan::presidentpendingList(7)->total()
            //     + SaleAsset::presidentpendingList(7)->total()
            //     + ReturnBudget::presidentpendingList(7)->total()
            //     + RescheduleLoan::presidentpendingList(7)->total()
            //     + Training::presidentpendingList(7)->total()
            //     + RequestOT::presidentpendingList(7)->total()
            //     + @(integer)$eachMenuCompany['pending']['MHT'],

            // 'TSP' => RequestMemo::presidentpendingList(8)->total()
            //     + RequestForm::presidentpendingList(8)->total()
            //     + RequestHR::presidentpendingList(8)->total()
            //     + Disposal::presidentpendingList(8)->total()
            //     + DamagedLog::presidentpendingList(8)->total()
            //     + HRRequest::presidentpendingList(8)->total()
            //     + Loan::presidentpendingList(8)->total()
            //     + SaleAsset::presidentpendingList(8)->total()
            //     + ReturnBudget::presidentpendingList(8)->total()
            //     + RescheduleLoan::presidentpendingList(8)->total()
            //     + Training::presidentpendingList(8)->total()
            //     + RequestOT::presidentpendingList(8)->total()
            //     + @(integer)$eachMenuCompany['pending']['TSP'],
        ];
        return $data;
    }
    public function toApprove(){}
    public function countPendingByCompanyForManagement()
    {
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $tbApprove = 'approve';
        $tbUser = 'users';
        $companies = DB::table('companies')->select(['id', 'short_name_en'])->get();
        $userId = Auth::id();

        $data = [];
        foreach ($companies as $key => $item) {
//            $company = 1;
            $company = $item->id;

            //todo: Memo
            $type = config('app.type_memo');
            $coreTb = 'request_memo';
            // Auth as Reviewer
            $sql = "
            select count('$coreTb.id') as total
            from $coreTb
            join $tbApprove on $coreTb.id = $tbApprove.request_id
            join $tbUser on $coreTb.user_id = $tbUser.id
            where $coreTb.company_id = $company
            and $coreTb.status = $pending
            and $tbApprove.type = $type
            and $tbApprove.reviewer_id = $userId
            and $tbApprove.status = $approved
            and $coreTb.deleted_at is null
            ";
            $memo = DB::select(DB::raw($sql));
            $memo = $memo[0]->total;

            // Auth as Requester
            $sql1 = "
            select count('$coreTb.id') as total
            from $coreTb
            join $tbUser on $coreTb.user_id = $tbUser.id
            where $coreTb.company_id = $company

            and $coreTb.status = $pending
            and $coreTb.user_id = $userId
            and $coreTb.deleted_at is null
            ";
            $memo1 = DB::select(DB::raw($sql1));
            $memo += $memo1[0]->total;

            //todo: Special Expense
            $type = config('app.type_special_expense'); 
            $coreTb = 'requests';

            //PO
            $type = config('app.type_po_request'); 
            $coreTb = 'requests_po';
            
            //GRN
            $type = config('app.type_grn'); 
            $coreTb = 'requests_grn';
            
            $type = config('app.type_pr_request'); 
            $coreTb = 'requests_pr';
            // Auth as Reviewer
            $sql = "
            select count('$coreTb.id') as total
            from $coreTb
            join $tbApprove on $coreTb.id = $tbApprove.request_id
            join $tbUser on $coreTb.user_id = $tbUser.id
            where $coreTb.company_id = $company
            and $coreTb.status = $pending
            and $tbApprove.type = $type
            and $tbApprove.reviewer_id = $userId
            and $tbApprove.status = $approved
            and $coreTb.deleted_at is null
            ";
            $se = DB::select(DB::raw($sql));
            $se = $se[0]->total;

            // Auth as Requester
            $sql1 = "
            select count('$coreTb.id') as total
            from $coreTb
            join $tbUser on $coreTb.user_id = $tbUser.id
            where $coreTb.company_id = $company

            and $coreTb.status = $pending
            and $coreTb.user_id = $userId
            and $coreTb.deleted_at is null
            ";
            $se1 = DB::select(DB::raw($sql1));
            $se += $se1[0]->total;

            //todo: General Expense
            $type = config('app.type_special_expense');
            $coreTb = 'request_hr';
            // Auth as Reviewer
            $sql = "
            select count('$coreTb.id') as total
            from $coreTb
            join $tbApprove on $coreTb.id = $tbApprove.request_id
            join $tbUser on $coreTb.user_id = $tbUser.id
            where $coreTb.company_id = $company
            and $coreTb.status = $pending
            and $tbApprove.type = $type
            and $tbApprove.reviewer_id = $userId
            and $tbApprove.status = $approved
            and $coreTb.deleted_at is null
            ";
            $ge = DB::select(DB::raw($sql));
            $ge = $ge[0]->total;

            // Auth as Requester
            $sql1 = "
            select count('$coreTb.id') as total
            from $coreTb
            join $tbUser on $coreTb.user_id = $tbUser.id
            where $coreTb.company_id = $company

            and $coreTb.status = $pending
            and $coreTb.user_id = $userId
            and $coreTb.deleted_at is null
            ";
            $ge1 = DB::select(DB::raw($sql1));
            $ge += $ge1[0]->total;


            $data[$item->short_name_en] =
                $memo+
                $se+
                $ge
//                $disposal+
//                $damagedLog+
//                $HRRequest+
//                $loan+
//                $sellAsset+
//                $returnBudget+
//                $rescheduleLoan+
//                $mission+
//                $missionItem+
//                $training+
//                $report
            ;
        }
        return $data;
    }

    /**
     * @return array
     */
    public function countToApproveByCompanyForManagement()
    {
        ini_set("memory_limit", -1);

        $pending = config('app.approve_status_draft');
        $tbApprove = 'approve';
        $tbUser = 'users';
        $companies = DB::table('companies')->select(['id', 'short_name_en'])->whereNull('deleted_at')->get();
        $userId = Auth::id();

        $data = [];
        foreach ($companies as $key => $item) {
            $company = $item->id;
            $type = config('app.type_memo');
            $tbMemo = 'request_memo';
            $memo = DB::table($tbMemo)
                ->leftJoin("$tbApprove", "$tbMemo.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbMemo.user_id")
                ->where("$tbMemo.company_id", '=', $company)
                ->where("$tbMemo.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                // ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbMemo.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbMemo.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $memo = $memo->get();
                    $data1 = $memo;

                    $memo = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $memo = array_merge($memo, [$value]);
                       }
                    }
                    $memo = collect($memo);
                }
                else{
                    $memo = $memo->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $memo = $memo->count();

            $type = config('app.type_special_expense');
            $tbSE = 'requests';
            $se = DB::table($tbSE)
                ->leftJoin("$tbApprove", "$tbSE.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbSE.user_id")
                ->where("$tbSE.company_id", '=', $company)
                ->where("$tbSE.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                // ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbSE.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbSE.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $se = $se->get();
                    $data1 = $se;

                    $se = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $se = array_merge($se, [$value]);
                       }
                    }
                    $se = collect($se);
                }
                else{
                    $se = $se->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $se = $se->count();

            // $type = config('app.type_general_expense');
            // $coreTb = 'request_hr';
            // $sql = "
            // select count('$coreTb.id') as total
            // from $coreTb
            // join $tbApprove on $coreTb.id = $tbApprove.request_id
            // join $tbUser on $coreTb.user_id = $tbUser.id
            // where $coreTb.company_id = $company
            // and $tbApprove.type = $type
            // and $coreTb.status = $pending
            // and $tbApprove.reviewer_id = $userId
            // and $tbApprove.status = '$pending'
            // and $coreTb.deleted_at is null
            // ";
            // $ge = DB::select(DB::raw($sql));
            // $ge = $ge[0]->total;

            $type = config('app.type_general_expense');
            $tbGE = 'request_hr';
            $ge = DB::table($tbGE)
                ->leftJoin("$tbApprove", "$tbGE.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbGE.user_id")
                ->where("$tbGE.company_id", '=', $company)
                ->where("$tbGE.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                // ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbGE.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbGE.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $ge = $ge->get();
                    $data1 = $ge;

                    $ge = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $ge = array_merge($ge, [$value]);
                       }
                    }
                    $ge = collect($ge);
                }
                else{
                    $ge = $ge->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $ge = $ge->count();

            $type = config('app.type_disposal');
            $tbDisposal = 'disposals';
            $disposal = DB::table($tbDisposal)
                ->leftJoin("$tbApprove", "$tbDisposal.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbDisposal.user_id")
                ->where("$tbDisposal.company_id", '=', $company)
                ->where("$tbDisposal.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                // ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbDisposal.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbDisposal.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $disposal = $disposal->get();
                    $data1 = $disposal;

                    $disposal = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $disposal = array_merge($disposal, [$value]);
                       }
                    }
                    $disposal = collect($disposal);
                }
                else{
                    $disposal = $disposal->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $disposal = $disposal->count();

            $type = config('app.type_damaged_log');
            $tbDamagedLog = 'damaged_log';
            $damagedLog = DB::table($tbDamagedLog)
                ->leftJoin("$tbApprove", "$tbDamagedLog.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbDamagedLog.user_id")
                ->where("$tbDamagedLog.company_id", '=', $company)
                ->where("$tbDamagedLog.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                // ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbDamagedLog.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbDamagedLog.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $damagedLog = $damagedLog->get();
                    $data1 = $damagedLog;

                    $damagedLog = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $damagedLog = array_merge($damagedLog, [$value]);
                       }
                    }
                    $damagedLog = collect($damagedLog);
                }
                else{
                    $damagedLog = $damagedLog->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $damagedLog = $damagedLog->count();

            $type = config('app.type_hr_request');
            $tbHRRequest = 'hr_requests';
            $HRRequest = DB::table($tbHRRequest)
                ->leftJoin("$tbApprove", "$tbHRRequest.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbHRRequest.user_id")
                ->where("$tbHRRequest.company_id", '=', $company)
                ->where("$tbHRRequest.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                //->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbHRRequest.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbHRRequest.id")
                ->orderBy('id','ASC');

                //check order approver
                if (config('app.is_order_approver') == 1) {
                    $HRRequest = $HRRequest->get();
                    $data1 = $HRRequest;

                    $HRRequest = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $HRRequest = array_merge($HRRequest, [$value]);
                       }
                    }
                    $HRRequest = collect($HRRequest);
                }
                else{
                    $HRRequest = $HRRequest->where("$tbApprove.reviewer_id", Auth::id())->get();
                }
                
            $HRRequest = $HRRequest->count();


            // $type = config('app.type_loans');
            // $tbLoan = 'loans';
            // $loan = DB::table($tbLoan)
            //     ->leftJoin("$tbApprove", "$tbLoan.id", '=', "$tbApprove.request_id")
            //     ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbLoan.user_id")
            //     ->where("$tbLoan.company_id", '=', $company)
            //     ->where("$tbLoan.status", '=', $pending)
            //     ->where("$tbApprove.type", '=', $type)
            //     ->where("$tbApprove.reviewer_id", '=', Auth::id())
            //     ->where("$tbApprove.status", '=', $pending)
            //     ->whereNull("deleted_at")
            //     ->select("$tbLoan.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id")
            //     ->count();
            $loan = Loan::CountToApprove($company);

            $type = config('app.type_sale_asset');
            $tbSellAsset = 'sale_asset';
            $sellAsset = DB::table($tbSellAsset)
                ->leftJoin("$tbApprove", "$tbSellAsset.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbSellAsset.user_id")
                ->where("$tbSellAsset.company_id", '=', $company)
                ->where("$tbSellAsset.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                // ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbSellAsset.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbSellAsset.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $sellAsset = $sellAsset->get();
                    $data1 = $sellAsset;

                    $sellAsset = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $sellAsset = array_merge($sellAsset, [$value]);
                       }
                    }
                    $sellAsset = collect($sellAsset);
                }
                else{
                    $sellAsset = $sellAsset->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $sellAsset = $sellAsset->count();

            $type = config('app.type_send_receive');
            $tbSendReceive = 'send_receive';
            $sendReceive = DB::table($tbSendReceive)
                ->leftJoin("$tbApprove", "$tbSendReceive.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbSendReceive.user_id")
                ->where("$tbSendReceive.company_id", '=', $company)
                ->where("$tbSendReceive.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                // ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbSendReceive.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbSendReceive.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $sendReceive = $sendReceive->get();
                    $data1 = $sendReceive;

                    $sendReceive = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $sendReceive = array_merge($sendReceive, [$value]);
                       }
                    }
                    $sendReceive = collect($sendReceive);
                }
                else{
                    $sendReceive = $sendReceive->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $sendReceive = $sendReceive->count();

            $type = config('app.type_return_budget');
            $tbReturnBudget = 'return_budget';
            $returnBudget = DB::table($tbReturnBudget)
                ->leftJoin("$tbApprove", "$tbReturnBudget.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbReturnBudget.user_id")
                ->where("$tbReturnBudget.company_id", '=', $company)
                ->where("$tbReturnBudget.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                // ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbReturnBudget.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbReturnBudget.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $returnBudget = $returnBudget->get();
                    $data1 = $returnBudget;

                    $returnBudget = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $returnBudget = array_merge($returnBudget, [$value]);
                       }
                    }
                    $returnBudget = collect($returnBudget);
                }
                else{
                    $returnBudget = $returnBudget->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $returnBudget = $returnBudget->count();

            $type = config('app.type_reschedule_loan');
            $tbRescheduleLoan = 'reschedule_loan';
            $rescheduleLoan = DB::table($tbRescheduleLoan)
                ->leftJoin("$tbApprove", "$tbRescheduleLoan.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbRescheduleLoan.user_id")
                ->where("$tbRescheduleLoan.company_id", '=', $company)
                ->where("$tbRescheduleLoan.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbRescheduleLoan.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id")
                ->count();

            $type = config('app.type_mission');
            $tbMission = 'mission';
            $mission = DB::table($tbMission)
                ->leftJoin("$tbApprove", "$tbMission.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbMission.user_id")
                ->where("$tbMission.company_id", '=', $company)
                ->where("$tbMission.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                ->where('approve.position', '!=', 'cc')
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbMission.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbMission.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $mission = $mission->get();
                    $data1 = $mission;

                    $mission = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $mission = array_merge($mission, [$value]);
                       }
                    }
                    $mission = collect($mission);
                }
                else{
                    $mission = $mission->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $mission = $mission->count();

            $type = config('app.type_mission_item');
            $tbMissionItem = 'mission_items';
            $missionItem = DB::table($tbMissionItem)
                ->join("$tbMission", "$tbMission.id", '=', "$tbMissionItem.request_id")
                ->leftJoin("$tbApprove", "$tbMissionItem.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbMission.user_id")
                ->where("$tbMission.company_id", '=', $company)
                ->where("$tbMissionItem.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("$tbMissionItem.deleted_at")
                ->select("$tbMissionItem.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id")
                ->count();

            $type = config('app.type_training');
            $tbTraining = 'training';
            $training = DB::table($tbTraining)
                ->leftJoin("$tbApprove", "$tbTraining.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbTraining.user_id")
                ->where("$tbTraining.company_id", '=', $company)
                ->where("$tbTraining.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                // ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbTraining.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbTraining.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $training = $training->get();
                    $data1 = $training;

                    $training = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $training = array_merge($training, [$value]);
                       }
                    }
                    $training = collect($training);
                }
                else{
                    $training = $training->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $training = $training->count();

            $type = config('app.type_request_ot');
            $tbRequestOT = 'request_ot';
            $request_ot = DB::table($tbRequestOT)
                ->leftJoin("$tbApprove", "$tbRequestOT.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbRequestOT.user_id")
                ->where("$tbRequestOT.company_id", '=', $company)
                ->where("$tbRequestOT.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                // ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbRequestOT.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbRequestOT.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $request_ot = $request_ot->get();
                    $data1 = $request_ot;

                    $request_ot = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $request_ot = array_merge($request_ot, [$value]);
                       }
                    }
                    $request_ot = collect($request_ot);
                }
                else{
                    $request_ot = $request_ot->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $request_ot = $request_ot->count();

            $type = config('app.type_penalty');
            $tbPenalty = 'penalty';
            $penalty = DB::table($tbPenalty)
                ->leftJoin("$tbApprove", "$tbPenalty.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbPenalty.user_id")
                ->where("$tbPenalty.company_id", '=', $company)
                ->where("$tbPenalty.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbPenalty.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id")
                ->count();

            $type = config('app.type_cutting_interest');
            $tbInterest = 'penalty';
            $interest = DB::table($tbInterest)
                ->leftJoin("$tbApprove", "$tbInterest.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbInterest.user_id")
                ->where("$tbInterest.company_id", '=', $company)
                ->where("$tbInterest.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbInterest.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id")
                ->count();

            $type = config('app.type_wave_association');
            $tbAssociation = 'penalty';
            $wave_association = DB::table($tbAssociation)
                ->leftJoin("$tbApprove", "$tbAssociation.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbAssociation.user_id")
                ->where("$tbAssociation.company_id", '=', $company)
                ->where("$tbAssociation.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbAssociation.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id")
                ->count();

            $type = config('app.type_employee_penalty');
            $tbEmployeePenalty = 'employee_penalty';
            $employee_penalty = DB::table($tbEmployeePenalty)
                ->leftJoin("$tbApprove", "$tbEmployeePenalty.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbEmployeePenalty.user_id")
                ->where("$tbEmployeePenalty.company_id", '=', $company)
                ->where("$tbEmployeePenalty.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbEmployeePenalty.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbEmployeePenalty.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $employee_penalty = $employee_penalty->get();
                    $data1 = $employee_penalty;

                    $employee_penalty = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $employee_penalty = array_merge($employee_penalty, [$value]);
                       }
                    }
                    $employee_penalty = collect($employee_penalty);
                }
                else{
                    $employee_penalty = $employee_penalty->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $employee_penalty = $employee_penalty->count();


            $type = config('app.type_cash_advance');
            $tbCashAdvance = 'cash_advance';
            $cash_advance = DB::table($tbCashAdvance)
                ->leftJoin("$tbApprove", "$tbCashAdvance.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbCashAdvance.user_id")
                ->where("$tbCashAdvance.company_id", '=', $company)
                ->where("$tbCashAdvance.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbCashAdvance.user_id", '!=', Auth::id())
                ->where('approve.position', '!=', 'cc')
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbCashAdvance.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbCashAdvance.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $cash_advance = $cash_advance->get();
                    $data1 = $cash_advance;

                    $cash_advance = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $cash_advance = array_merge($cash_advance, [$value]);
                       }
                    }
                    $cash_advance = collect($cash_advance);
                }
                else{
                    $cash_advance = $cash_advance->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $cash_advance = $cash_advance->count();


            $type = config('app.type_resign');
            $tbResign = 'resigns';
            $resign = DB::table($tbResign)
                ->leftJoin("$tbApprove", "$tbResign.id", '=', "$tbApprove.request_id")
                ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbResign.user_id")
                ->where("$tbResign.company_id", '=', $company)
                ->where("$tbResign.status", '=', $pending)
                ->where("$tbApprove.type", '=', $type)
                ->where("$tbApprove.reviewer_id", '=', Auth::id())
                ->where("$tbResign.user_id", '!=', Auth::id())
                ->where("$tbApprove.status", '=', $pending)
                ->whereNull("deleted_at")
                ->select("$tbResign.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
                ->groupBy("$tbResign.id")
                ->orderBy('id','ASC');

            //check order approver
                if (config('app.is_order_approver') == 1) {
                    $resign = $resign->get();
                    $data1 = $resign;

                    $resign = [];
                    foreach ($data1 as $key => $value) {
                       if($value->reviewer_id == Auth::id()){
                            $resign = array_merge($resign, [$value]);
                       }
                    }
                    $resign = collect($resign);
                }
                else{
                    $resign = $resign->where("$tbApprove.reviewer_id", Auth::id())->get();
                }

            $resign = $resign->count();

            // $type = config('app.type_general_request');
            // $tbGR = 'general_request';
            // $gr = DB::table($tbGR)
            //     ->leftJoin("$tbApprove", "$tbGR.id", '=', "$tbApprove.request_id")
            //     ->leftJoin("$tbUser", "$tbUser.id", '=', "$tbGR.user_id")
            //     ->where("$tbGR.company_id", '=', $company)
            //     ->where("$tbGR.status", '=', $pending)
            //     ->where("$tbApprove.type", '=', $type)
            //     ->where("$tbApprove.reviewer_id", '=', Auth::id())
            //     ->where("$tbGR.user_id", '!=', Auth::id())
            //     ->where("$tbApprove.status", '=', $pending)
            //     ->whereNull("deleted_at")
            //     ->select("$tbGR.*", "$tbUser.name as requester_name", "$tbApprove.id as approve_id", "$tbApprove.reviewer_id")
            //     ->groupBy("$tbGR.id")
            //     ->orderBy('id','ASC');

            // //check order approver
            //     if (config('app.is_order_approver') == 1) {
            //         $gr = $gr->get();
            //         $gr = $gr;

            //         $gr = [];
            //         foreach ($data1 as $key => $value) {
            //            if($value->reviewer_id == Auth::id()){
            //                 $gr = array_merge($gr, [$value]);
            //            }
            //         }
            //         $gr = collect($gr);
            //     }
            //     else{
            //         $gr = $gr->where("$tbApprove.reviewer_id", Auth::id())->get();
            //     }

            // $gr = $gr->count();
            $gr = GeneralRequest::CountToApprove($company);

            $request_user = RequestCreateUser::CountToApprove($company);
            $transfer_asset = TransferAsset::CountToApprove($company);
            $setting = SettingReviewerApprover::CountToApprove($company);
            $association = Association::CountToApprove($company);
            $survey_report = Survey::CountToApprove($company);
            $custom_letter = CustomLetter::CountToApprove($company);
            $policy = Policy::CountToApprove($company);
            $request_disable_user = RequestDisableUser::CountToApprove($company);
            $wc = WithdrawalCollateral::CountToApprove($company);
            $vl = VillageLoan::CountToApprove($company);
            $mission_clear = MissionClearance::CountToApprove($company);
            $gl = RequestGasoline::CountToApprove($company);

            /// Report
            $type = config('app.report');
            $userId = Auth::id();
            $sql = "
            select count('g_requests.id') as report_total
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join companies on companies.id = g_requests.company_id
            where g_reviewers.reviewer_id = $userId
            and g_requests.company_id = $company
            and g_requests.type = '$type'
            and g_requests.status = 'pending'
            and g_reviewers.status = 'pending'
            and g_requests.deleted_at is null
            ";
            $report = DB::select(DB::raw($sql));
            $report = $report[0]->report_total;

            // Auth as approve
            $sql1 = "
            select count('g_requests.id') as report_total
            from g_requests
            join g_approvers on g_requests.id = g_approvers.request_id
            join companies on companies.id = g_requests.company_id
            where g_approvers.approver_id = $userId
            and g_requests.company_id = $company
            and g_requests.type = '$type'
            and g_requests.status = 'pending'
            and g_approvers.status = 'pending'
            and g_requests.deleted_at is null
            ";
            $report1 = DB::select(DB::raw($sql1));
            $report1 = $report1[0]->report_total;
            $report += $report1;

            $data[$item->short_name_en] =
                $memo+
                $se+
                $ge+
                $disposal+
                $damagedLog+
                $HRRequest+
                $loan+
                $sellAsset+
                $sendReceive+
                $returnBudget+
                $rescheduleLoan+
                $mission+
                $missionItem+
                $mission_clear+
                $training+
                $request_ot+
                $penalty+
                $interest+
                $wave_association+
                $employee_penalty+
                $cash_advance+
                $resign+
                $gr+
                $request_user+
                $transfer_asset+
                $setting+
                $association+
                $survey_report+
                $custom_letter+
                $policy+
                $request_disable_user+
                $wc+
                $vl+
                $gl+
                $report
            ;
        }
        return $data;
    }

    /**
     * @return array
     */
    public function rejectedManagement()
    {
        ini_set("memory_limit", -1);
        $groupRequest = new GroupRequest();
        $eachMenuCompany['reject'] = $groupRequest->countRejectedListRequestEachCompanyForManagement();
        $presidentRejected = [
            // 'STSK' => RequestMemo::presidentRejectedList(1)->total()
            //         + RequestForm::presidentRejectedList(1)->total()
            //         + RequestHR::presidentRejectedList(1)->total()
            //         + Disposal::presidentRejectedList(1)->total()
            //         + DamagedLog::presidentRejectedList(1)->total()
            //         + HRRequest::presidentRejectedList(1)->total()
            //         + SaleAsset::presidentRejectedList(1)->total()
            //         + SendReceive::presidentRejectedList(1)->total()
            //         + ReturnBudget::presidentRejectedList(1)->total()
            //         + Mission::presidentRejectedList(1)->total()
            //         + MissionItem::presidentRejectedList(1)->total()
            //         + MissionClearance::CountRejected(1)
            //         + Training::presidentRejectedList(1)->total()
            //         + RequestOT::presidentRejectedList(1)->total()
            //         + EmployeePenalty::presidentRejectedList(1)->total()
            //         + CashAdvance::presidentRejectedList(1)->total()
            //         + Resign::presidentRejectedList(1)->total()
            //         + RequestCreateUser::CountRejected(1)
            //         + TransferAsset::CountRejected(1)
            //         + SettingReviewerApprover::CountRejected(1)
            //         + CustomLetter::CountRejected(1)
            //         + Policy::CountRejected(1)
            //         + RequestDisableUser::CountRejected(1)
            //         + RequestGasoline::CountRejected(1)
            //         + @$eachMenuCompany['reject']['STSK'],

            // 'MFI' => RequestMemo::presidentRejectedList(2)->total()
            //         + RequestForm::presidentRejectedList(2)->total()
            //         + RequestHR::presidentRejectedList(2)->total()
            //         + Disposal::presidentRejectedList(2)->total()
            //         + DamagedLog::presidentRejectedList(2)->total()
            //         + HRRequest::presidentRejectedList(2)->total()
            //         + SaleAsset::presidentRejectedList(2)->total()
            //         + SendReceive::presidentRejectedList(2)->total()
            //         + ReturnBudget::presidentRejectedList(2)->total()
            //         + Loan::CountRejected(2)
            //         + RescheduleLoan::presidentRejectedList(2)->total()
            //         + Mission::presidentRejectedList(2)->total()
            //         + MissionItem::presidentRejectedList(2)->total()
            //         + MissionClearance::CountRejected(2)
            //         + Training::presidentRejectedList(2)->total()
            //         + RequestOT::presidentRejectedList(2)->total()
            //         + Penalty::presidentRejectedList(2)->total()
            //         + Penalty::presidentRejectedListInterest(2)->total()
            //         + Penalty::presidentRejectedListAssociation(2)->total()
            //         + EmployeePenalty::presidentRejectedList(2)->total()
            //         + CashAdvance::presidentRejectedList(2)->total()
            //         + Resign::presidentRejectedList(2)->total()
            //         + GeneralRequest::CountRejected(2)
            //         + RequestCreateUser::CountRejected(2)
            //         + TransferAsset::CountRejected(2)
            //         + SettingReviewerApprover::CountRejected(2)
            //         + Association::CountRejected(2)
            //         + Survey::CountRejected(2)
            //         + CustomLetter::CountRejected(2)
            //         + Policy::CountRejected(2)
            //         + RequestDisableUser::CountRejected(2)
            //         + WithdrawalCollateral::CountRejected(2)
            //         + VillageLoan::CountRejected(2)
            //         + RequestGasoline::CountRejected(2)
            //         + @$eachMenuCompany['reject']['MFI'],

            // 'NGO' => RequestMemo::presidentRejectedList(3)->total()
            //         + RequestForm::presidentRejectedList(3)->total()
            //         + RequestHR::presidentRejectedList(3)->total()
            //         + Disposal::presidentRejectedList(3)->total()
            //         + DamagedLog::presidentRejectedList(3)->total()
            //         + HRRequest::presidentRejectedList(3)->total()
            //         + SaleAsset::presidentRejectedList(3)->total()
            //         + SendReceive::presidentRejectedList(3)->total()
            //         + ReturnBudget::presidentRejectedList(3)->total()
            //         + Loan::CountRejected(3)
            //         + RescheduleLoan::presidentRejectedList(3)->total()
            //         + Mission::presidentRejectedList(3)->total()
            //         + MissionItem::presidentRejectedList(3)->total()
            //         + MissionClearance::CountRejected(3)
            //         + Training::presidentRejectedList(3)->total()
            //         + RequestOT::presidentRejectedList(3)->total()
            //         + Penalty::presidentRejectedList(3)->total()
            //         + Penalty::presidentRejectedListInterest(3)->total()
            //         + Penalty::presidentRejectedListAssociation(3)->total()
            //         + EmployeePenalty::presidentRejectedList(3)->total()
            //         + CashAdvance::presidentRejectedList(3)->total()
            //         + Resign::presidentRejectedList(3)->total()
            //         + GeneralRequest::CountRejected(3)
            //         + RequestCreateUser::CountRejected(3)
            //         + TransferAsset::CountRejected(3)
            //         + SettingReviewerApprover::CountRejected(3)
            //         + Association::CountRejected(3)
            //         + Survey::CountRejected(3)
            //         + CustomLetter::CountRejected(3)
            //         + Policy::CountRejected(3)
            //         + RequestDisableUser::CountRejected(3)
            //         + WithdrawalCollateral::CountRejected(3)
            //         + VillageLoan::CountRejected(3)
            //         + RequestGasoline::CountRejected(3)
            //         + @$eachMenuCompany['reject']['NGO'],

            // 'PWS' => RequestMemo::presidentRejectedList(14)->total()
            //         + RequestForm::presidentRejectedList(14)->total()
            //         + RequestHR::presidentRejectedList(14)->total()
            //         + Disposal::presidentRejectedList(14)->total()
            //         + DamagedLog::presidentRejectedList(14)->total()
            //         + HRRequest::presidentRejectedList(14)->total()
            //         + SaleAsset::presidentRejectedList(14)->total()
            //         + SendReceive::presidentRejectedList(14)->total()
            //         + ReturnBudget::presidentRejectedList(14)->total()
            //         + Loan::CountRejected(14)
            //         + RescheduleLoan::presidentRejectedList(14)->total()
            //         + Mission::presidentRejectedList(14)->total()
            //         + MissionItem::presidentRejectedList(14)->total()
            //         + MissionClearance::CountRejected(14)
            //         + Training::presidentRejectedList(14)->total()
            //         + RequestOT::presidentRejectedList(14)->total()
            //         + Penalty::presidentRejectedList(14)->total()
            //         + Penalty::presidentRejectedListInterest(14)->total()
            //         + Penalty::presidentRejectedListAssociation(14)->total()
            //         + EmployeePenalty::presidentRejectedList(14)->total()
            //         + CashAdvance::presidentRejectedList(14)->total()
            //         + Resign::presidentRejectedList(14)->total()
            //         + GeneralRequest::CountRejected(14)
            //         + RequestCreateUser::CountRejected(14)
            //         + TransferAsset::CountRejected(14)
            //         + SettingReviewerApprover::CountRejected(14)
            //         + Association::CountRejected(14)
            //         + Survey::CountRejected(14)
            //         + CustomLetter::CountRejected(14)
            //         + Policy::CountRejected(14)
            //         + RequestDisableUser::CountRejected(14)
            //         + WithdrawalCollateral::CountRejected(14)
            //         + VillageLoan::CountRejected(14)
            //         + RequestGasoline::CountRejected(14)
            //         + @$eachMenuCompany['reject']['PWS'],

            'ORD' => RequestMemo::presidentRejectedList(4)->total()
                    + RequestForm::presidentRejectedList(4)->total()
                    + RequestPR::presidentRejectedList(4)->total()
                    + RequestPO::presidentRejectedList(4)->total()
                    + RequestGRN::presidentRejectedList(4)->total()
                    + RequestHR::presidentRejectedList(4)->total()
                    + Disposal::presidentRejectedList(4)->total()
                    + DamagedLog::presidentRejectedList(4)->total()
                    + HRRequest::presidentRejectedList(4)->total()
                    + SaleAsset::presidentRejectedList(4)->total()
                    + SendReceive::presidentRejectedList(4)->total()
                    + ReturnBudget::presidentRejectedList(4)->total()
                    + Training::presidentRejectedList(4)->total()
                    + RequestOT::presidentRejectedList(4)->total()
                    + EmployeePenalty::presidentRejectedList(4)->total()
                    + CashAdvance::presidentRejectedList(4)->total()
                    + Resign::presidentRejectedList(4)->total()
                    + RequestCreateUser::CountRejected(4)
                    + TransferAsset::CountRejected(4)
                    + SettingReviewerApprover::CountRejected(4)
                    + CustomLetter::CountRejected(4)
                    + Policy::CountRejected(4)
                    + RequestDisableUser::CountRejected(4)
                    + @$eachMenuCompany['reject']['ORD'],

            'ORD2' => RequestMemo::presidentRejectedList(16)->total()
                    + RequestForm::presidentRejectedList(16)->total()
                    + RequestPR::presidentRejectedList(16)->total()
                    + RequestPO::presidentRejectedList(16)->total()
                    + RequestGRN::presidentRejectedList(16)->total()
                    + RequestHR::presidentRejectedList(16)->total()
                    + Disposal::presidentRejectedList(16)->total()
                    + DamagedLog::presidentRejectedList(16)->total()
                    + HRRequest::presidentRejectedList(16)->total()
                    + SaleAsset::presidentRejectedList(16)->total()
                    + SendReceive::presidentRejectedList(16)->total()
                    + ReturnBudget::presidentRejectedList(16)->total()
                    + Training::presidentRejectedList(16)->total()
                    + RequestOT::presidentRejectedList(16)->total()
                    + EmployeePenalty::presidentRejectedList(16)->total()
                    + CashAdvance::presidentRejectedList(16)->total()
                    + Resign::presidentRejectedList(16)->total()
                    + RequestCreateUser::CountRejected(16)
                    + TransferAsset::CountRejected(16)
                    + SettingReviewerApprover::CountRejected(16)
                    + CustomLetter::CountRejected(16)
                    + Policy::CountRejected(16)
                    + RequestDisableUser::CountRejected(16)
                    + @$eachMenuCompany['reject']['ORD2'],

            // 'ST' => RequestMemo::presidentRejectedList(5)->total()
            //         + RequestForm::presidentRejectedList(5)->total()
            //         + RequestHR::presidentRejectedList(5)->total()
            //         + Disposal::presidentRejectedList(5)->total()
            //         + DamagedLog::presidentRejectedList(5)->total()
            //         + HRRequest::presidentRejectedList(5)->total()
            //         + SaleAsset::presidentRejectedList(5)->total()
            //         + SendReceive::presidentRejectedList(5)->total()
            //         + ReturnBudget::presidentRejectedList(5)->total()
            //         + Training::presidentRejectedList(5)->total()
            //         + RequestOT::presidentRejectedList(5)->total()
            //         + EmployeePenalty::presidentRejectedList(5)->total()
            //         + CashAdvance::presidentRejectedList(5)->total()
            //         + Resign::presidentRejectedList(5)->total()
            //         + RequestCreateUser::CountRejected(5)
            //         + TransferAsset::CountRejected(5)
            //         + SettingReviewerApprover::CountRejected(5)
            //         + CustomLetter::CountRejected(5)
            //         + Policy::CountRejected(5)
            //         + RequestDisableUser::CountRejected(5)
            //         + @$eachMenuCompany['reject']['ST'],

            // 'MMI' => RequestMemo::presidentRejectedList(6)->total()
            //         + RequestForm::presidentRejectedList(6)->total()
            //         + RequestHR::presidentRejectedList(6)->total()
            //         + Disposal::presidentRejectedList(6)->total()
            //         + DamagedLog::presidentRejectedList(6)->total()
            //         + HRRequest::presidentRejectedList(6)->total()
            //         + SaleAsset::presidentRejectedList(6)->total()
            //         + SendReceive::presidentRejectedList(6)->total()
            //         + ReturnBudget::presidentRejectedList(6)->total()
            //         + Mission::presidentRejectedList(6)->total()
            //         + MissionItem::presidentRejectedList(6)->total()
            //         + MissionClearance::CountRejected(6)
            //         + Training::presidentRejectedList(6)->total()
            //         + RequestOT::presidentRejectedList(6)->total()
            //         + EmployeePenalty::presidentRejectedList(6)->total()
            //         + CashAdvance::presidentRejectedList(6)->total()
            //         + Resign::presidentRejectedList(6)->total()
            //         + RequestCreateUser::CountRejected(6)
            //         + TransferAsset::CountRejected(6)
            //         + SettingReviewerApprover::CountRejected(6)
            //         + CustomLetter::CountRejected(6)
            //         + Policy::CountRejected(6)
            //         + RequestDisableUser::CountRejected(6)
            //         + RequestGasoline::CountRejected(6)
            //         + @$eachMenuCompany['reject']['MMI'],

            // 'MHT' => RequestMemo::presidentRejectedList(7)->total()
            //         + RequestForm::presidentRejectedList(7)->total()
            //         + RequestHR::presidentRejectedList(7)->total()
            //         + Disposal::presidentRejectedList(7)->total()
            //         + DamagedLog::presidentRejectedList(7)->total()
            //         + HRRequest::presidentRejectedList(7)->total()
            //         + SaleAsset::presidentRejectedList(7)->total()
            //         + SendReceive::presidentRejectedList(1)->total()
            //         + ReturnBudget::presidentRejectedList(7)->total()
            //         + Training::presidentRejectedList(7)->total()
            //         + RequestOT::presidentRejectedList(7)->total()
            //         + EmployeePenalty::presidentRejectedList(7)->total()
            //         + CashAdvance::presidentRejectedList(7)->total()
            //         + Resign::presidentRejectedList(7)->total()
            //         + RequestCreateUser::CountRejected(7)
            //         + TransferAsset::CountRejected(7)
            //         + SettingReviewerApprover::CountRejected(7)
            //         + CustomLetter::CountRejected(7)
            //         + Policy::CountRejected(7)
            //         + RequestDisableUser::CountRejected(7)
            //         + @$eachMenuCompany['reject']['MHT'],

            // 'TSP' => RequestMemo::presidentRejectedList(8)->total()
            //         + RequestForm::presidentRejectedList(8)->total()
            //         + RequestHR::presidentRejectedList(8)->total()
            //         + Disposal::presidentRejectedList(8)->total()
            //         + DamagedLog::presidentRejectedList(8)->total()
            //         + HRRequest::presidentRejectedList(8)->total()
            //         + SaleAsset::presidentRejectedList(8)->total()
            //         + SendReceive::presidentRejectedList(8)->total()
            //         + ReturnBudget::presidentRejectedList(8)->total()
            //         + Training::presidentRejectedList(8)->total()
            //         + RequestOT::presidentRejectedList(8)->total()
            //         + EmployeePenalty::presidentRejectedList(8)->total()
            //         + CashAdvance::presidentRejectedList(8)->total()
            //         + Resign::presidentRejectedList(8)->total()
            //         + RequestCreateUser::CountRejected(8)
            //         + TransferAsset::CountRejected(8)
            //         + SettingReviewerApprover::CountRejected(8)
            //         + CustomLetter::CountRejected(8)
            //         + Policy::CountRejected(8)
            //         + RequestDisableUser::CountRejected(8)
            //         + @$eachMenuCompany['reject']['TSP'],

            // 'President' => RequestMemo::presidentRejectedList(9)->total()
            //         + RequestForm::presidentRejectedList(9)->total()
            //         + RequestHR::presidentRejectedList(9)->total()
            //         + Disposal::presidentRejectedList(9)->total()
            //         + DamagedLog::presidentRejectedList(9)->total()
            //         + HRRequest::presidentRejectedList(9)->total()
            //         + SaleAsset::presidentRejectedList(9)->total()
            //         + SendReceive::presidentRejectedList(9)->total()
            //         + ReturnBudget::presidentRejectedList(9)->total()
            //         + Training::presidentRejectedList(9)->total()
            //         + RequestOT::presidentRejectedList(9)->total()
            //         + EmployeePenalty::presidentRejectedList(9)->total()
            //         + CashAdvance::presidentRejectedList(9)->total()
            //         + Resign::presidentRejectedList(9)->total()
            //         + RequestCreateUser::CountRejected(9)
            //         + TransferAsset::CountRejected(9)
            //         + SettingReviewerApprover::CountRejected(9)
            //         + CustomLetter::CountRejected(9)
            //         + Policy::CountRejected(9)
            //         + RequestDisableUser::CountRejected(9)
            //         + @$eachMenuCompany['reject']['President'],

            // 'MDN' => RequestMemo::CountRejected(10)
            //         + RequestForm::presidentRejectedList(10)->total()
            //         + RequestHR::presidentRejectedList(10)->total()
            //         + Disposal::presidentRejectedList(10)->total()
            //         + DamagedLog::presidentRejectedList(10)->total()
            //         + HRRequest::presidentRejectedList(10)->total()
            //         + SaleAsset::presidentRejectedList(10)->total()
            //         + SendReceive::presidentRejectedList(10)->total()
            //         + ReturnBudget::presidentRejectedList(10)->total()
            //         + Mission::presidentRejectedList(10)->total()
            //         + MissionItem::presidentRejectedList(10)->total()
            //         + Training::presidentRejectedList(10)->total()
            //         + RequestOT::presidentRejectedList(10)->total()
            //         + EmployeePenalty::presidentRejectedList(10)->total()
            //         + CashAdvance::presidentRejectedList(10)->total()
            //         + Resign::presidentRejectedList(10)->total()
            //         + RequestCreateUser::CountRejected(10)
            //         + TransferAsset::CountRejected(10)
            //         + SettingReviewerApprover::CountRejected(10)
            //         + CustomLetter::CountRejected(10)
            //         + Policy::CountRejected(10)
            //         + RequestDisableUser::CountRejected(10)
            //         + @$eachMenuCompany['reject']['MDN'],

            // 'PTK' => RequestMemo::CountRejected(11)
            //         + RequestForm::presidentRejectedList(11)->total()
            //         + RequestHR::presidentRejectedList(11)->total()
            //         + Disposal::presidentRejectedList(11)->total()
            //         + DamagedLog::presidentRejectedList(11)->total()
            //         + HRRequest::presidentRejectedList(11)->total()
            //         + SaleAsset::presidentRejectedList(11)->total()
            //         + SendReceive::presidentRejectedList(11)->total()
            //         + ReturnBudget::presidentRejectedList(11)->total()
            //         + Mission::presidentRejectedList(11)->total()
            //         + MissionItem::presidentRejectedList(11)->total()
            //         + Training::presidentRejectedList(11)->total()
            //         + RequestOT::presidentRejectedList(11)->total()
            //         + EmployeePenalty::presidentRejectedList(11)->total()
            //         + CashAdvance::presidentRejectedList(11)->total()
            //         + Resign::presidentRejectedList(11)->total()
            //         + RequestCreateUser::CountRejected(11)
            //         + TransferAsset::CountRejected(11)
            //         + SettingReviewerApprover::CountRejected(11)
            //         + CustomLetter::CountRejected(11)
            //         + Policy::CountRejected(11)
            //         + RequestDisableUser::CountRejected(11)
            //         + @$eachMenuCompany['reject']['PTK'],

            // 'NIYA' => RequestMemo::CountRejected(12)
            //         + RequestForm::presidentRejectedList(12)->total()
            //         + RequestHR::presidentRejectedList(12)->total()
            //         + Disposal::presidentRejectedList(12)->total()
            //         + DamagedLog::presidentRejectedList(12)->total()
            //         + HRRequest::presidentRejectedList(12)->total()
            //         + SaleAsset::presidentRejectedList(12)->total()
            //         + SendReceive::presidentRejectedList(12)->total()
            //         + ReturnBudget::presidentRejectedList(12)->total()
            //         + Mission::presidentRejectedList(12)->total()
            //         + MissionItem::presidentRejectedList(12)->total()
            //         + Training::presidentRejectedList(12)->total()
            //         + RequestOT::presidentRejectedList(12)->total()
            //         + EmployeePenalty::presidentRejectedList(12)->total()
            //         + CashAdvance::presidentRejectedList(12)->total()
            //         + Resign::presidentRejectedList(12)->total()
            //         + RequestCreateUser::CountRejected(12)
            //         + TransferAsset::CountRejected(12)
            //         + SettingReviewerApprover::CountRejected(12)
            //         + CustomLetter::CountRejected(12)
            //         + Policy::CountRejected(12)
            //         + RequestDisableUser::CountRejected(12)
            //         + @$eachMenuCompany['reject']['NIYA'],

            // 'DMS' => RequestMemo::CountRejected(13)
            //         + RequestForm::presidentRejectedList(13)->total()
            //         + RequestHR::presidentRejectedList(13)->total()
            //         + Disposal::presidentRejectedList(13)->total()
            //         + DamagedLog::presidentRejectedList(13)->total()
            //         + HRRequest::presidentRejectedList(13)->total()
            //         + SaleAsset::presidentRejectedList(13)->total()
            //         + SendReceive::presidentRejectedList(13)->total()
            //         + ReturnBudget::presidentRejectedList(13)->total()
            //         + Mission::presidentRejectedList(13)->total()
            //         + MissionItem::presidentRejectedList(13)->total()
            //         + Training::presidentRejectedList(13)->total()
            //         + RequestOT::presidentRejectedList(13)->total()
            //         + EmployeePenalty::presidentRejectedList(13)->total()
            //         + CashAdvance::presidentRejectedList(13)->total()
            //         + Resign::presidentRejectedList(13)->total()
            //         + RequestCreateUser::CountRejected(13)
            //         + TransferAsset::CountRejected(13)
            //         + SettingReviewerApprover::CountRejected(13)
            //         + CustomLetter::CountRejected(13)
            //         + Policy::CountRejected(13)
            //         + RequestDisableUser::CountRejected(13)
            //         + @$eachMenuCompany['reject']['DMS'],

            // 'BRC' => RequestMemo::CountRejected(15)
            //         + RequestForm::presidentRejectedList(15)->total()
            //         + RequestHR::presidentRejectedList(15)->total()
            //         + Disposal::presidentRejectedList(15)->total()
            //         + DamagedLog::presidentRejectedList(15)->total()
            //         + HRRequest::presidentRejectedList(15)->total()
            //         + SaleAsset::presidentRejectedList(15)->total()
            //         + SendReceive::presidentRejectedList(15)->total()
            //         + ReturnBudget::presidentRejectedList(15)->total()
            //         + Training::presidentRejectedList(15)->total()
            //         + RequestOT::presidentRejectedList(15)->total()
            //         + EmployeePenalty::presidentRejectedList(15)->total()
            //         + CashAdvance::presidentRejectedList(15)->total()
            //         + Resign::presidentRejectedList(15)->total()
            //         + RequestCreateUser::CountRejected(15)
            //         + TransferAsset::CountRejected(15)
            //         + SettingReviewerApprover::CountRejected(15)
            //         + CustomLetter::CountRejected(15)
            //         + Policy::CountRejected(15)
            //         + RequestDisableUser::CountRejected(15)
            //         + BorrowingLoan::CountRejected(15)
            //         + @$eachMenuCompany['reject']['BRC'],
        ];
        return $presidentRejected;
    }

    public function disabledManagement()
    {
        ini_set("memory_limit", -1);
        $presidentDisabled = [
            
            // 'STSK' => Resign::presidentDisabledList(1)->total()
            //     + RequestForm::presidentDisabledList(1)->total()
            //     + RequestHR::presidentDisabledList(1)->total()
            //     + HRRequest::presidentDisabledList(1)->total()
            //     + CashAdvance::presidentDisabledList(1)->total()
            //     + EmployeePenalty::presidentDisabledList(1)->total()
            //     + RequestGasoline::CountDisabled(1)
            //     + Mission::presidentDisabledList(1)->total()
            //     + MissionClearance::CountDisabled(1)
            //     + RequestOT::presidentDisabledList(1)->total()
            //     + CustomLetter::CountDisabled(1)
            //     + DamagedLog::presidentDisabledList(1)->total()
            //     + Disposal::presidentDisabledList(1)->total()
            //     + TransferAsset::CountDisabled(1)
            //     + SaleAsset::presidentDisabledList(1)->total()
            //     + SendReceive::presidentDisabledList(1)->total()
            //     + ReturnBudget::presidentDisabledList(1)->total()
            //     + Training::presidentDisabledList(1)->total()
            //     + RequestMemo::CountDisabled(1),

            // 'MFI' => Loan::CountDisabled(2)
            //     + Resign::presidentDisabledList(2)->total()
            //     + RequestForm::presidentDisabledList(2)->total()
            //     + RequestHR::presidentDisabledList(2)->total()
            //     + HRRequest::presidentDisabledList(2)->total()
            //     + CashAdvance::presidentDisabledList(2)->total()
            //     + EmployeePenalty::presidentDisabledList(2)->total()
            //     + RequestGasoline::CountDisabled(2)
            //     + Mission::presidentDisabledList(2)->total()
            //     + MissionClearance::CountDisabled(2)
            //     + GeneralRequest::CountDisabled(2)
            //     + RescheduleLoan::presidentDisabledList(2)->total()
            //     + RequestOT::presidentDisabledList(2)->total()
            //     + Association::CountDisabled(2)
            //     + Penalty::presidentDisabledList(2)->total()
            //     + Penalty::presidentDisabledListInterest(2)->total()
            //     + Penalty::presidentDisabledListAssociation(2)->total()
            //     + CustomLetter::CountDisabled(2)
            //     + DamagedLog::presidentDisabledList(2)->total()
            //     + Disposal::presidentDisabledList(2)->total()
            //     + TransferAsset::CountDisabled(2)
            //     + SaleAsset::presidentDisabledList(2)->total()
            //     + SendReceive::presidentDisabledList(2)->total()
            //     + ReturnBudget::presidentDisabledList(2)->total()
            //     + WithdrawalCollateral::CountDisabled(2)
            //     + VillageLoan::CountDisabled(2)
            //     + Training::presidentDisabledList(2)->total()
            //     + RequestMemo::CountDisabled(2),

            // 'NGO' => Loan::CountDisabled(3)
            //     + Resign::presidentDisabledList(3)->total()
            //     + RequestForm::presidentDisabledList(3)->total()
            //     + RequestHR::presidentDisabledList(3)->total()
            //     + HRRequest::presidentDisabledList(3)->total()
            //     + CashAdvance::presidentDisabledList(3)->total()
            //     + EmployeePenalty::presidentDisabledList(3)->total()
            //     + RequestGasoline::CountDisabled(3)
            //     + Mission::presidentDisabledList(3)->total()
            //     + MissionClearance::CountDisabled(3)
            //     + GeneralRequest::CountDisabled(3)
            //     + RescheduleLoan::presidentDisabledList(3)->total()
            //     + RequestOT::presidentDisabledList(3)->total()
            //     + Association::CountDisabled(3)
            //     + Penalty::presidentDisabledList(3)->total()
            //     + Penalty::presidentDisabledListInterest(3)->total()
            //     + Penalty::presidentDisabledListAssociation(3)->total()
            //     + CustomLetter::CountDisabled(3)
            //     + DamagedLog::presidentDisabledList(3)->total()
            //     + Disposal::presidentDisabledList(3)->total()
            //     + TransferAsset::CountDisabled(3)
            //     + SaleAsset::presidentDisabledList(3)->total()
            //     + SendReceive::presidentDisabledList(3)->total()
            //     + ReturnBudget::presidentDisabledList(3)->total()
            //     + WithdrawalCollateral::CountDisabled(3)
            //     + VillageLoan::CountDisabled(3)
            //     + Training::presidentDisabledList(3)->total()
            //     + RequestMemo::CountDisabled(3),

            // 'PWS' => Loan::CountDisabled(14)
            //     + Resign::presidentDisabledList(14)->total()
            //     + RequestForm::presidentDisabledList(14)->total()
            //     + RequestHR::presidentDisabledList(14)->total()
            //     + HRRequest::presidentDisabledList(14)->total()
            //     + CashAdvance::presidentDisabledList(14)->total()
            //     + EmployeePenalty::presidentDisabledList(14)->total()
            //     + RequestGasoline::CountDisabled(14)
            //     + Mission::presidentDisabledList(14)->total()
            //     + MissionClearance::CountDisabled(14)
            //     + GeneralRequest::CountDisabled(14)
            //     + RescheduleLoan::presidentDisabledList(14)->total()
            //     + RequestOT::presidentDisabledList(14)->total()
            //     + Association::CountDisabled(14)
            //     + Penalty::presidentDisabledList(14)->total()
            //     + Penalty::presidentDisabledListInterest(14)->total()
            //     + Penalty::presidentDisabledListAssociation(14)->total()
            //     + CustomLetter::CountDisabled(14)
            //     + DamagedLog::presidentDisabledList(14)->total()
            //     + Disposal::presidentDisabledList(14)->total()
            //     + TransferAsset::CountDisabled(14)
            //     + SaleAsset::presidentDisabledList(14)->total()
            //     + SendReceive::presidentDisabledList(14)->total()
            //     + ReturnBudget::presidentDisabledList(14)->total()
            //     + WithdrawalCollateral::CountDisabled(14)
            //     + VillageLoan::CountDisabled(14)
            //     + Training::presidentDisabledList(14)->total()
            //     + RequestMemo::CountDisabled(14),

            'ORD' => Resign::presidentDisabledList(4)->total()
                + RequestForm::presidentDisabledList(4)->total()
                + RequestPR::presidentDisabledList(4)->total()
                + RequestPO::presidentDisabledList(4)->total()
                + RequestGRN::presidentDisabledList(4)->total()
                + RequestHR::presidentDisabledList(4)->total()
                + HRRequest::presidentDisabledList(4)->total()
                + CashAdvance::presidentDisabledList(4)->total()
                + RequestOT::presidentDisabledList(4)->total()
                + CustomLetter::CountDisabled(4)
                + DamagedLog::presidentDisabledList(4)->total()
                + Disposal::presidentDisabledList(4)->total()
                + TransferAsset::CountDisabled(4)
                + SaleAsset::presidentDisabledList(4)->total()
                + SendReceive::presidentDisabledList(4)->total()
                + ReturnBudget::presidentDisabledList(4)->total()
                + Training::presidentDisabledList(4)->total()
                + RequestMemo::CountDisabled(4),

            'ORD2' => Resign::presidentDisabledList(16)->total()
                + RequestForm::presidentDisabledList(16)->total()
                + RequestPR::presidentDisabledList(16)->total()
                + RequestPO::presidentDisabledList(16)->total()
                + RequestGRN::presidentDisabledList(16)->total()
                + RequestHR::presidentDisabledList(16)->total()
                + HRRequest::presidentDisabledList(16)->total()
                + CashAdvance::presidentDisabledList(16)->total()
                + RequestOT::presidentDisabledList(16)->total()
                + CustomLetter::CountDisabled(16)
                + DamagedLog::presidentDisabledList(16)->total()
                + Disposal::presidentDisabledList(16)->total()
                + TransferAsset::CountDisabled(16)
                + SaleAsset::presidentDisabledList(16)->total()
                + SendReceive::presidentDisabledList(16)->total()
                + ReturnBudget::presidentDisabledList(16)->total()
                + Training::presidentDisabledList(16)->total()
                + RequestMemo::CountDisabled(16),

            // 'ST' => Resign::presidentDisabledList(5)->total()
            //     + RequestForm::presidentDisabledList(5)->total()
            //     + RequestHR::presidentDisabledList(5)->total()
            //     + HRRequest::presidentDisabledList(5)->total()
            //     + CashAdvance::presidentDisabledList(5)->total()
            //     + RequestOT::presidentDisabledList(5)->total()
            //     + DamagedLog::presidentDisabledList(5)->total()
            //     + Disposal::presidentDisabledList(5)->total()
            //     + TransferAsset::CountDisabled(5)
            //     + SaleAsset::presidentDisabledList(5)->total()
            //     + SendReceive::presidentDisabledList(5)->total()
            //     + ReturnBudget::presidentDisabledList(5)->total()
            //     + Training::presidentDisabledList(5)->total()
            //     + RequestMemo::CountDisabled(5),

            // 'MMI' => Resign::presidentDisabledList(6)->total()
            //     + RequestForm::presidentDisabledList(6)->total()
            //     + RequestHR::presidentDisabledList(6)->total()
            //     + HRRequest::presidentDisabledList(6)->total()
            //     + CashAdvance::presidentDisabledList(6)->total()
            //     + RequestGasoline::CountDisabled(6)
            //     + Mission::presidentDisabledList(6)->total()
            //     + MissionClearance::CountDisabled(6)
            //     + RequestOT::presidentDisabledList(6)->total()
            //     + CustomLetter::CountDisabled(6)
            //     + DamagedLog::presidentDisabledList(6)->total()
            //     + Disposal::presidentDisabledList(6)->total()
            //     + TransferAsset::CountDisabled(6)
            //     + SaleAsset::presidentDisabledList(6)->total()
            //     + SendReceive::presidentDisabledList(6)->total()
            //     + ReturnBudget::presidentDisabledList(6)->total()
            //     + Training::presidentDisabledList(6)->total()
            //     + RequestMemo::CountDisabled(6),

            // 'MHT' => Resign::presidentDisabledList(7)->total()
            //     + RequestForm::presidentDisabledList(7)->total()
            //     + RequestHR::presidentDisabledList(7)->total()
            //     + HRRequest::presidentDisabledList(7)->total()
            //     + CashAdvance::presidentDisabledList(7)->total()
            //     + RequestOT::presidentDisabledList(7)->total()
            //     + CustomLetter::CountDisabled(7)
            //     + DamagedLog::presidentDisabledList(7)->total()
            //     + Disposal::presidentDisabledList(7)->total()
            //     + TransferAsset::CountDisabled(7)
            //     + SaleAsset::presidentDisabledList(7)->total()
            //     + SendReceive::presidentDisabledList(7)->total()
            //     + ReturnBudget::presidentDisabledList(7)->total()
            //     + Training::presidentDisabledList(7)->total()
            //     + RequestMemo::CountDisabled(7),

            // 'TSP' => Resign::presidentDisabledList(8)->total()
            //     + RequestForm::presidentDisabledList(8)->total()
            //     + RequestHR::presidentDisabledList(8)->total()
            //     + HRRequest::presidentDisabledList(8)->total()
            //     + CashAdvance::presidentDisabledList(8)->total()
            //     + RequestOT::presidentDisabledList(8)->total()
            //     + CustomLetter::CountDisabled(8)
            //     + DamagedLog::presidentDisabledList(8)->total()
            //     + Disposal::presidentDisabledList(8)->total()
            //     + TransferAsset::CountDisabled(8)
            //     + SaleAsset::presidentDisabledList(8)->total()
            //     + SendReceive::presidentDisabledList(8)->total()
            //     + ReturnBudget::presidentDisabledList(8)->total()
            //     + Training::presidentDisabledList(8)->total()
            //     + RequestMemo::CountDisabled(8),

            // 'President' => Resign::presidentDisabledList(9)->total()
            //     + RequestForm::presidentDisabledList(9)->total()
            //     + RequestHR::presidentDisabledList(9)->total()
            //     + HRRequest::presidentDisabledList(9)->total()
            //     + CashAdvance::presidentDisabledList(9)->total()
            //     + EmployeePenalty::presidentDisabledList(9)->total()
            //     + RequestGasoline::CountDisabled(9)
            //     + Mission::presidentDisabledList(9)->total()
            //     + MissionClearance::CountDisabled(9)
            //     + RequestOT::presidentDisabledList(9)->total()
            //     + CustomLetter::CountDisabled(9)
            //     + DamagedLog::presidentDisabledList(9)->total()
            //     + Disposal::presidentDisabledList(9)->total()
            //     + TransferAsset::CountDisabled(9)
            //     + SaleAsset::presidentDisabledList(9)->total()
            //     + SendReceive::presidentDisabledList(9)->total()
            //     + ReturnBudget::presidentDisabledList(9)->total()
            //     + Training::presidentDisabledList(9)->total()
            //     + RequestMemo::CountDisabled(9),

            // 'BRC' => Resign::presidentDisabledList(15)->total()
            //     + RequestForm::presidentDisabledList(15)->total()
            //     + RequestHR::presidentDisabledList(15)->total()
            //     + HRRequest::presidentDisabledList(15)->total()
            //     + CashAdvance::presidentDisabledList(15)->total()
            //     + EmployeePenalty::presidentDisabledList(15)->total()
            //     + RequestGasoline::CountDisabled(15)
            //     + Mission::presidentDisabledList(15)->total()
            //     + MissionClearance::CountDisabled(15)
            //     + RequestOT::presidentDisabledList(15)->total()
            //     + CustomLetter::CountDisabled(15)
            //     + DamagedLog::presidentDisabledList(15)->total()
            //     + Disposal::presidentDisabledList(15)->total()
            //     + TransferAsset::CountDisabled(15)
            //     + SaleAsset::presidentDisabledList(15)->total()
            //     + SendReceive::presidentDisabledList(15)->total()
            //     + ReturnBudget::presidentDisabledList(15)->total()
            //     + Training::presidentDisabledList(15)->total()
            //     + RequestMemo::CountDisabled(15)
        ];
        return $presidentDisabled;
    }

    /**
     * @return array
     */
    public function approvedManagement()
    {
        ini_set("memory_limit", -1);
        $groupRequest = new GroupRequest();
        $eachMenuCompany['approved'] = $groupRequest->countApprovedListRequestEachCompanyForManagement();
        $presidentApproved = [
            // 'STSK' => RequestMemo::presidentApproved(1)->total()
            //         + RequestForm::presidentApproved(1)->total()
            //         + RequestHR::presidentApproved(1)->total()
            //         + Disposal::presidentApproved(1)->total()
            //         + DamagedLog::presidentApproved(1)->total()
            //         + HRRequest::presidentApproved(1)->total()
            //         + SaleAsset::presidentApproved(1)->total()
            //         + SendReceive::presidentApproved(1)->total()
            //         + ReturnBudget::presidentApproved(1)->total()
            //         + Mission::presidentApproved(1)->total()
            //         + MissionItem::presidentApproved(1)->total()
            //         + MissionClearance::CountApproved(1)
            //         + Training::presidentApproved(1)->total()
            //         + RequestOT::presidentApproved(1)->total()
            //         + EmployeePenalty::presidentApproved(1)->total()
            //         + CashAdvance::presidentApproved(1)->total()
            //         + Resign::presidentApproved(1)->total()
            //         + RequestCreateUser::CountApproved(1)
            //         + TransferAsset::CountApproved(1)
            //         + SettingReviewerApprover::CountApproved(1)
            //         + CustomLetter::CountApproved(1)
            //         + Policy::CountApproved(1)
            //         + RequestDisableUser::CountApproved(1)
            //         + RequestGasoline::CountApproved(1)
            //         + @$eachMenuCompany['approved']['STSK'],

            // 'MFI' => RequestMemo::presidentApproved(2)->total()
            //         + RequestForm::presidentApproved(2)->total()
            //         + RequestHR::presidentApproved(2)->total()
            //         + Disposal::presidentApproved(2)->total()
            //         + DamagedLog::presidentApproved(2)->total()
            //         + HRRequest::presidentApproved(2)->total()
            //         + Loan::CountApproved(2)
            //         + SaleAsset::presidentApproved(2)->total()
            //         + SendReceive::presidentApproved(2)->total()
            //         + ReturnBudget::presidentApproved(2)->total()
            //         + RescheduleLoan::presidentApproved(2)->total()
            //         + Mission::presidentApproved(2)->total()
            //         + MissionItem::presidentApproved(2)->total()
            //         + MissionClearance::CountApproved(2)
            //         + Training::presidentApproved(2)->total()
            //         + RequestOT::presidentApproved(2)->total()
            //         + Penalty::presidentApproved(2)->total()
            //         + Penalty::presidentApprovedInterest(2)->total()
            //         + Penalty::presidentApprovedAssociation(2)->total()
            //         + EmployeePenalty::presidentApproved(2)->total()
            //         + CashAdvance::presidentApproved(2)->total()
            //         + Resign::presidentApproved(2)->total()
            //         + GeneralRequest::CountApproved(2)
            //         + RequestCreateUser::CountApproved(2)
            //         + TransferAsset::CountApproved(2)
            //         + SettingReviewerApprover::CountApproved(2)
            //         + Association::CountApproved(2)
            //         + Survey::CountApproved(2)
            //         + CustomLetter::CountApproved(2)
            //         + Policy::CountApproved(2)
            //         + RequestDisableUser::CountApproved(2)
            //         + WithdrawalCollateral::CountApproved(2)
            //         + VillageLoan::CountApproved(2)
            //         + RequestGasoline::CountApproved(2)
            //         + @$eachMenuCompany['approved']['MFI'],

            // 'NGO' => RequestMemo::presidentApproved(3)->total()
            //         + RequestForm::presidentApproved(3)->total()
            //         + RequestHR::presidentApproved(3)->total()
            //         + Disposal::presidentApproved(3)->total()
            //         + DamagedLog::presidentApproved(3)->total()
            //         + HRRequest::presidentApproved(3)->total()
            //         + Loan::CountApproved(3)
            //         + SaleAsset::presidentApproved(3)->total()
            //         + SendReceive::presidentApproved(3)->total()
            //         + ReturnBudget::presidentApproved(3)->total()
            //         + RescheduleLoan::presidentApproved(3)->total()
            //         + Mission::presidentApproved(3)->total()
            //         + MissionItem::presidentApproved(3)->total()
            //         + MissionClearance::CountApproved(3)
            //         + Training::presidentApproved(3)->total()
            //         + RequestOT::presidentApproved(3)->total()
            //         + Penalty::presidentApproved(3)->total()
            //         + Penalty::presidentApprovedInterest(3)->total()
            //         + Penalty::presidentApprovedAssociation(3)->total()
            //         + EmployeePenalty::presidentApproved(3)->total()
            //         + CashAdvance::presidentApproved(3)->total()
            //         + Resign::presidentApproved(3)->total()
            //         + GeneralRequest::CountApproved(3)
            //         + RequestCreateUser::CountApproved(3)
            //         + TransferAsset::CountApproved(3)
            //         + SettingReviewerApprover::CountApproved(3)
            //         + Association::CountApproved(3)
            //         + Survey::CountApproved(3)
            //         + CustomLetter::CountApproved(3)
            //         + Policy::CountApproved(3)
            //         + RequestDisableUser::CountApproved(3)
            //         + WithdrawalCollateral::CountApproved(3)
            //         + VillageLoan::CountApproved(3)
            //         + RequestGasoline::CountApproved(3)
            //         + @$eachMenuCompany['approved']['NGO'],

            // 'PWS' => RequestMemo::presidentApproved(14)->total()
            //         + RequestForm::presidentApproved(14)->total()
            //         + RequestHR::presidentApproved(14)->total()
            //         + Disposal::presidentApproved(14)->total()
            //         + DamagedLog::presidentApproved(14)->total()
            //         + HRRequest::presidentApproved(14)->total()
            //         + Loan::CountApproved(14)
            //         + SaleAsset::presidentApproved(14)->total()
            //         + SendReceive::presidentApproved(14)->total()
            //         + ReturnBudget::presidentApproved(14)->total()
            //         + RescheduleLoan::presidentApproved(14)->total()
            //         + Mission::presidentApproved(14)->total()
            //         + MissionItem::presidentApproved(14)->total()
            //         + MissionClearance::CountApproved(14)
            //         + Training::presidentApproved(14)->total()
            //         + RequestOT::presidentApproved(14)->total()
            //         + Penalty::presidentApproved(14)->total()
            //         + Penalty::presidentApprovedInterest(14)->total()
            //         + Penalty::presidentApprovedAssociation(14)->total()
            //         + EmployeePenalty::presidentApproved(14)->total()
            //         + CashAdvance::presidentApproved(14)->total()
            //         + Resign::presidentApproved(14)->total()
            //         + GeneralRequest::CountApproved(14)
            //         + RequestCreateUser::CountApproved(14)
            //         + TransferAsset::CountApproved(14)
            //         + SettingReviewerApprover::CountApproved(14)
            //         + Association::CountApproved(14)
            //         + Survey::CountApproved(14)
            //         + CustomLetter::CountApproved(14)
            //         + Policy::CountApproved(14)
            //         + RequestDisableUser::CountApproved(14)
            //         + WithdrawalCollateral::CountApproved(14)
            //         + VillageLoan::CountApproved(14)
            //         + RequestGasoline::CountApproved(14)
            //         + @$eachMenuCompany['approved']['PWS'],

            'ORD' => RequestMemo::presidentApproved(4)->total()
                    + RequestForm::presidentApproved(4)->total()
                    + RequestPR::presidentApproved(4)->total()
                    + RequestPO::presidentApproved(4)->total()
                    + RequestGRN::presidentApproved(4)->total()
                    + RequestHR::presidentApproved(4)->total()
                    + Disposal::presidentApproved(4)->total()
                    + DamagedLog::presidentApproved(4)->total()
                    + HRRequest::presidentApproved(4)->total()
                    + SaleAsset::presidentApproved(4)->total()
                    + SendReceive::presidentApproved(4)->total()
                    + ReturnBudget::presidentApproved(4)->total()
                    + Training::presidentApproved(4)->total()
                    + RequestOT::presidentApproved(4)->total()
                    + EmployeePenalty::presidentApproved(4)->total()
                    + CashAdvance::presidentApproved(4)->total()
                    + Resign::presidentApproved(4)->total()
                    + RequestCreateUser::CountApproved(4)
                    + TransferAsset::CountApproved(4)
                    + SettingReviewerApprover::CountApproved(4)
                    + CustomLetter::CountApproved(4)
                    + Policy::CountApproved(4)
                    + RequestDisableUser::CountApproved(4)
                    + @$eachMenuCompany['approved']['ORD'],

            'ORD2' => RequestMemo::presidentApproved(16)->total()
                    + RequestForm::presidentApproved(16)->total()
                    + RequestPR::presidentApproved(16)->total()
                    + RequestPO::presidentApproved(16)->total()
                    + RequestGRN::presidentApproved(16)->total()
                    + RequestHR::presidentApproved(16)->total()
                    + Disposal::presidentApproved(16)->total()
                    + DamagedLog::presidentApproved(16)->total()
                    + HRRequest::presidentApproved(16)->total()
                    + SaleAsset::presidentApproved(16)->total()
                    + SendReceive::presidentApproved(16)->total()
                    + ReturnBudget::presidentApproved(16)->total()
                    + Training::presidentApproved(16)->total()
                    + RequestOT::presidentApproved(16)->total()
                    + EmployeePenalty::presidentApproved(16)->total()
                    + CashAdvance::presidentApproved(16)->total()
                    + Resign::presidentApproved(16)->total()
                    + RequestCreateUser::CountApproved(16)
                    + TransferAsset::CountApproved(16)
                    + SettingReviewerApprover::CountApproved(16)
                    + CustomLetter::CountApproved(16)
                    + Policy::CountApproved(16)
                    + RequestDisableUser::CountApproved(16)
                    + @$eachMenuCompany['approved']['ORD2'],

            // 'ST' => RequestMemo::presidentApproved(5)->total()
            //         + RequestForm::presidentApproved(5)->total()
            //         + RequestHR::presidentApproved(5)->total()
            //         + Disposal::presidentApproved(5)->total()
            //         + DamagedLog::presidentApproved(5)->total()
            //         + HRRequest::presidentApproved(5)->total()
            //         + SaleAsset::presidentApproved(5)->total()
            //         + SendReceive::presidentApproved(5)->total()
            //         + ReturnBudget::presidentApproved(5)->total()
            //         + Training::presidentApproved(5)->total()
            //         + RequestOT::presidentApproved(5)->total()
            //         + EmployeePenalty::presidentApproved(5)->total()
            //         + CashAdvance::presidentApproved(5)->total()
            //         + Resign::presidentApproved(5)->total()
            //         + RequestCreateUser::CountApproved(5)
            //         + TransferAsset::CountApproved(5)
            //         + SettingReviewerApprover::CountApproved(5)
            //         + CustomLetter::CountApproved(5)
            //         + Policy::CountApproved(5)
            //         + RequestDisableUser::CountApproved(5)
            //         + @$eachMenuCompany['approved']['ST'],

            // 'MMI' => RequestMemo::presidentApproved(6)->total()
            //         + RequestForm::presidentApproved(6)->total()
            //         + RequestHR::presidentApproved(6)->total()
            //         + Disposal::presidentApproved(6)->total()
            //         + DamagedLog::presidentApproved(6)->total()
            //         + HRRequest::presidentApproved(6)->total()
            //         + SaleAsset::presidentApproved(6)->total()
            //         + SendReceive::presidentApproved(6)->total()
            //         + ReturnBudget::presidentApproved(6)->total()
            //         + Mission::presidentApproved(6)->total()
            //         + MissionItem::presidentApproved(6)->total()
            //         + MissionClearance::CountApproved(6)
            //         + Training::presidentApproved(6)->total()
            //         + RequestOT::presidentApproved(6)->total()
            //         + EmployeePenalty::presidentApproved(6)->total()
            //         + CashAdvance::presidentApproved(6)->total()
            //         + Resign::presidentApproved(6)->total()
            //         + RequestCreateUser::CountApproved(6)
            //         + TransferAsset::CountApproved(6)
            //         + SettingReviewerApprover::CountApproved(6)
            //         + CustomLetter::CountApproved(6)
            //         + Policy::CountApproved(6)
            //         + RequestDisableUser::CountApproved(6)
            //         + RequestGasoline::CountApproved(6)
            //         + @$eachMenuCompany['approved']['MMI'],

            // 'MHT' => RequestMemo::presidentApproved(7)->total()
            //         + RequestForm::presidentApproved(7)->total()
            //         + RequestHR::presidentApproved(7)->total()
            //         + Disposal::presidentApproved(7)->total()
            //         + DamagedLog::presidentApproved(7)->total()
            //         + HRRequest::presidentApproved(7)->total()
            //         + SaleAsset::presidentApproved(7)->total()
            //         + SendReceive::presidentApproved(7)->total()
            //         + ReturnBudget::presidentApproved(7)->total()
            //         + Training::presidentApproved(7)->total()
            //         + RequestOT::presidentApproved(7)->total()
            //         + EmployeePenalty::presidentApproved(7)->total()
            //         + CashAdvance::presidentApproved(7)->total()
            //         + Resign::presidentApproved(7)->total()
            //         + RequestCreateUser::CountApproved(7)
            //         + TransferAsset::CountApproved(7)
            //         + SettingReviewerApprover::CountApproved(7)
            //         + CustomLetter::CountApproved(7)
            //         + Policy::CountApproved(7)
            //         + RequestDisableUser::CountApproved(7)
            //         + @$eachMenuCompany['approved']['MHT'],

            // 'TSP' => RequestMemo::presidentApproved(8)->total()
            //         + RequestForm::presidentApproved(8)->total()
            //         + RequestHR::presidentApproved(8)->total()
            //         + Disposal::presidentApproved(8)->total()
            //         + DamagedLog::presidentApproved(8)->total()
            //         + HRRequest::presidentApproved(8)->total()
            //         + SaleAsset::presidentApproved(8)->total()
            //         + SendReceive::presidentApproved(8)->total()
            //         + ReturnBudget::presidentApproved(8)->total()
            //         + Training::presidentApproved(8)->total()
            //         + RequestOT::presidentApproved(8)->total()
            //         + EmployeePenalty::presidentApproved(8)->total()
            //         + CashAdvance::presidentApproved(8)->total()
            //         + Resign::presidentApproved(8)->total()
            //         + RequestCreateUser::CountApproved(8)
            //         + TransferAsset::CountApproved(8)
            //         + SettingReviewerApprover::CountApproved(8)
            //         + CustomLetter::CountApproved(8)
            //         + Policy::CountApproved(8)
            //         + RequestDisableUser::CountApproved(8)
            //         + @$eachMenuCompany['approved']['TSP'],

            // 'President' => RequestMemo::presidentApproved(9)->total()
            //         + RequestForm::presidentApproved(9)->total()
            //         + RequestHR::presidentApproved(9)->total()
            //         + Disposal::presidentApproved(9)->total()
            //         + DamagedLog::presidentApproved(9)->total()
            //         + HRRequest::presidentApproved(9)->total()
            //         + SaleAsset::presidentApproved(9)->total()
            //         + SendReceive::presidentApproved(9)->total()
            //         + ReturnBudget::presidentApproved(9)->total()
            //         + Training::presidentApproved(9)->total()
            //         + RequestOT::presidentApproved(9)->total()
            //         + EmployeePenalty::presidentApproved(9)->total()
            //         + CashAdvance::presidentApproved(9)->total()
            //         + Resign::presidentApproved(9)->total()
            //         + RequestCreateUser::CountApproved(9)
            //         + TransferAsset::CountApproved(9)
            //         + SettingReviewerApprover::CountApproved(9)
            //         + CustomLetter::CountApproved(9)
            //         + Policy::CountApproved(9)
            //         + RequestDisableUser::CountApproved(9)
            //         + @$eachMenuCompany['approved']['President'],

            // 'MDN' => RequestMemo::CountApproved(10)
            //         + RequestForm::presidentApproved(10)->total()
            //         + RequestHR::presidentApproved(10)->total()
            //         + Disposal::presidentApproved(10)->total()
            //         + DamagedLog::presidentApproved(10)->total()
            //         + HRRequest::presidentApproved(10)->total()
            //         + SaleAsset::presidentApproved(10)->total()
            //         + SendReceive::presidentApproved(10)->total()
            //         + ReturnBudget::presidentApproved(10)->total()
            //         + Mission::presidentApproved(10)->total()
            //         + MissionItem::presidentApproved(10)->total()
            //         + Training::presidentApproved(10)->total()
            //         + RequestOT::presidentApproved(10)->total()
            //         + EmployeePenalty::presidentApproved(10)->total()
            //         + CashAdvance::presidentApproved(10)->total()
            //         + Resign::presidentApproved(10)->total()
            //         + RequestCreateUser::CountApproved(10)
            //         + TransferAsset::CountApproved(10)
            //         + SettingReviewerApprover::CountApproved(10)
            //         + CustomLetter::CountApproved(10)
            //         + Policy::CountApproved(10)
            //         + RequestDisableUser::CountApproved(10)
            //         + @$eachMenuCompany['approved']['MDN'],

            // 'PTK' => RequestMemo::CountApproved(11)
            //         + RequestForm::presidentApproved(11)->total()
            //         + RequestHR::presidentApproved(11)->total()
            //         + Disposal::presidentApproved(11)->total()
            //         + DamagedLog::presidentApproved(11)->total()
            //         + HRRequest::presidentApproved(11)->total()
            //         + SaleAsset::presidentApproved(11)->total()
            //         + SendReceive::presidentApproved(11)->total()
            //         + ReturnBudget::presidentApproved(11)->total()
            //         + Mission::presidentApproved(11)->total()
            //         + MissionItem::presidentApproved(11)->total()
            //         + Training::presidentApproved(11)->total()
            //         + RequestOT::presidentApproved(11)->total()
            //         + EmployeePenalty::presidentApproved(11)->total()
            //         + CashAdvance::presidentApproved(11)->total()
            //         + Resign::presidentApproved(11)->total()
            //         + RequestCreateUser::CountApproved(11)
            //         + TransferAsset::CountApproved(11)
            //         + SettingReviewerApprover::CountApproved(11)
            //         + CustomLetter::CountApproved(11)
            //         + Policy::CountApproved(11)
            //         + RequestDisableUser::CountApproved(11)
            //         + @$eachMenuCompany['approved']['PTK'],

            // 'NIYA' => RequestMemo::CountApproved(12)
            //         + RequestForm::presidentApproved(12)->total()
            //         + RequestHR::presidentApproved(12)->total()
            //         + Disposal::presidentApproved(12)->total()
            //         + DamagedLog::presidentApproved(12)->total()
            //         + HRRequest::presidentApproved(12)->total()
            //         + SaleAsset::presidentApproved(12)->total()
            //         + SendReceive::presidentApproved(12)->total()
            //         + ReturnBudget::presidentApproved(12)->total()
            //         + Mission::presidentApproved(12)->total()
            //         + MissionItem::presidentApproved(12)->total()
            //         + Training::presidentApproved(12)->total()
            //         + RequestOT::presidentApproved(12)->total()
            //         + EmployeePenalty::presidentApproved(12)->total()
            //         + CashAdvance::presidentApproved(12)->total()
            //         + Resign::presidentApproved(12)->total()
            //         + RequestCreateUser::CountApproved(12)
            //         + TransferAsset::CountApproved(12)
            //         + SettingReviewerApprover::CountApproved(12)
            //         + CustomLetter::CountApproved(12)
            //         + Policy::CountApproved(12)
            //         + RequestDisableUser::CountApproved(12)
            //         + @$eachMenuCompany['approved']['NIYA'],

            // 'DMS' => RequestMemo::CountApproved(13)
            //         + RequestForm::presidentApproved(13)->total()
            //         + RequestHR::presidentApproved(13)->total()
            //         + Disposal::presidentApproved(13)->total()
            //         + DamagedLog::presidentApproved(13)->total()
            //         + HRRequest::presidentApproved(13)->total()
            //         + SaleAsset::presidentApproved(13)->total()
            //         + SendReceive::presidentApproved(13)->total()
            //         + ReturnBudget::presidentApproved(13)->total()
            //         + Mission::presidentApproved(13)->total()
            //         + MissionItem::presidentApproved(13)->total()
            //         + Training::presidentApproved(13)->total()
            //         + RequestOT::presidentApproved(13)->total()
            //         + EmployeePenalty::presidentApproved(13)->total()
            //         + CashAdvance::presidentApproved(13)->total()
            //         + Resign::presidentApproved(13)->total()
            //         + RequestCreateUser::CountApproved(13)
            //         + TransferAsset::CountApproved(13)
            //         + SettingReviewerApprover::CountApproved(13)
            //         + CustomLetter::CountApproved(13)
            //         + Policy::CountApproved(13)
            //         + RequestDisableUser::CountApproved(13)
            //         + @$eachMenuCompany['approved']['DMS'],

            // 'BRC' => RequestMemo::CountApproved(15)
            //         + RequestForm::presidentApproved(15)->total()
            //         + RequestHR::presidentApproved(15)->total()
            //         + Disposal::presidentApproved(15)->total()
            //         + DamagedLog::presidentApproved(15)->total()
            //         + HRRequest::presidentApproved(15)->total()
            //         + SaleAsset::presidentApproved(15)->total()
            //         + SendReceive::presidentApproved(15)->total()
            //         + ReturnBudget::presidentApproved(15)->total()
            //         + Training::presidentApproved(15)->total()
            //         + RequestOT::presidentApproved(15)->total()
            //         + EmployeePenalty::presidentApproved(15)->total()
            //         + CashAdvance::presidentApproved(15)->total()
            //         + Resign::presidentApproved(15)->total()
            //         + RequestCreateUser::CountApproved(15)
            //         + TransferAsset::CountApproved(15)
            //         + SettingReviewerApprover::CountApproved(15)
            //         + CustomLetter::CountApproved(15)
            //         + Policy::CountApproved(15)
            //         + RequestDisableUser::CountApproved(15)
            //         + BorrowingLoan::CountApproved(15)
            //         + @$eachMenuCompany['approved']['BRC'],
        ];
        return $presidentApproved;
    }

    /**
     * @return array
     */
    public function pendingManagement()
    {
        ini_set("memory_limit", -1);
        $groupRequest = new GroupRequest();
        $eachMenuCompany['pending'] = $groupRequest->countPendingListRequestEachCompanyForManagement();
        $presidentPending = [
            // 'STSK' => RequestMemo::presidentpendingList(1)->total()
            //         + RequestForm::presidentpendingList(1)->total()
            //         + RequestHR::presidentpendingList(1)->total()
            //         + Disposal::presidentpendingList(1)->total()
            //         + DamagedLog::presidentpendingList(1)->total()
            //         + HRRequest::presidentpendingList(1)->total()
            //         + SaleAsset::presidentpendingList(1)->total()
            //         + SendReceive::presidentpendingList(1)->total()
            //         + ReturnBudget::presidentpendingList(1)->total()
            //         + Mission::presidentpendingList(1)->total()
            //         + MissionItem::presidentpendingList(1)->total()
            //         + MissionClearance::CountPending(1)
            //         + Training::presidentpendingList(1)->total()
            //         + RequestOT::presidentpendingList(1)->total()
            //         + EmployeePenalty::presidentpendingList(1)->total()
            //         + CashAdvance::presidentpendingList(1)->total()
            //         + Resign::presidentpendingList(1)->total()
            //         + GeneralRequest::presidentpendingList(1)->total()
            //         + RequestCreateUser::CountPending(1)
            //         + TransferAsset::CountPending(1)
            //         + SettingReviewerApprover::CountPending(1)
            //         + CustomLetter::CountPending(1)
            //         + Policy::CountPending(1)
            //         + RequestDisableUser::CountPending(1)
            //         + RequestGasoline::CountPending(1)
            //         + @$eachMenuCompany['pending']['STSK'],

            // 'MFI' => RequestMemo::presidentpendingList(2)->total()
            //         + RequestForm::presidentpendingList(2)->total()
            //         + RequestHR::presidentpendingList(2)->total()
            //         + Disposal::presidentpendingList(2)->total()
            //         + DamagedLog::presidentpendingList(2)->total()
            //         + HRRequest::presidentpendingList(2)->total()
            //         + Loan::CountPending(2)
            //         + SaleAsset::presidentpendingList(2)->total()
            //         + SendReceive::presidentpendingList(2)->total()
            //         + ReturnBudget::presidentpendingList(2)->total()
            //         + RescheduleLoan::presidentpendingList(2)->total()
            //         + Mission::presidentpendingList(2)->total()
            //         + MissionItem::presidentpendingList(2)->total()
            //         + MissionClearance::CountPending(2)
            //         + Training::presidentpendingList(2)->total()
            //         + RequestOT::presidentpendingList(2)->total()
            //         + Penalty::presidentpendingList(2)->total()
            //         + Penalty::presidentpendingListInterest(2)->total()
            //         + Penalty::presidentpendingListAssociation(2)->total()
            //         + EmployeePenalty::presidentpendingList(2)->total()
            //         + CashAdvance::presidentpendingList(2)->total()
            //         + Resign::presidentpendingList(2)->total()
            //         + GeneralRequest::CountPending(2)
            //         + RequestCreateUser::CountPending(2)
            //         + TransferAsset::CountPending(2)
            //         + SettingReviewerApprover::CountPending(2)
            //         + Association::CountPending(2)
            //         + Survey::CountPending(2)
            //         + CustomLetter::CountPending(2)
            //         + Policy::CountPending(2)
            //         + RequestDisableUser::CountPending(2)
            //         + WithdrawalCollateral::CountPending(2)
            //         + VillageLoan::CountPending(2)
            //         + RequestGasoline::CountPending(2)
            //         + @(integer)$eachMenuCompany['pending']['MFI'],

            // 'NGO' => RequestMemo::presidentpendingList(3)->total()
            //         + RequestForm::presidentpendingList(3)->total()
            //         + RequestHR::presidentpendingList(3)->total()
            //         + Disposal::presidentpendingList(3)->total()
            //         + DamagedLog::presidentpendingList(3)->total()
            //         + HRRequest::presidentpendingList(3)->total()
            //         + Loan::CountPending(3)
            //         + SaleAsset::presidentpendingList(3)->total()
            //         + SendReceive::presidentpendingList(3)->total()
            //         + ReturnBudget::presidentpendingList(3)->total()
            //         + RescheduleLoan::presidentpendingList(3)->total()
            //         + Mission::presidentpendingList(3)->total()
            //         + MissionItem::presidentpendingList(3)->total()
            //         + MissionClearance::CountPending(3)
            //         + Training::presidentpendingList(3)->total()
            //         + RequestOT::presidentpendingList(3)->total()
            //         + Penalty::presidentpendingList(3)->total()
            //         + Penalty::presidentpendingListInterest(3)->total()
            //         + Penalty::presidentpendingListAssociation(3)->total()
            //         + EmployeePenalty::presidentpendingList(3)->total()
            //         + CashAdvance::presidentpendingList(3)->total()
            //         + Resign::presidentpendingList(3)->total()
            //         + GeneralRequest::CountPending(3)
            //         + RequestCreateUser::CountPending(3)
            //         + TransferAsset::CountPending(3)
            //         + SettingReviewerApprover::CountPending(3)
            //         + Association::CountPending(3)
            //         + Survey::CountPending(3)
            //         + CustomLetter::CountPending(3)
            //         + Policy::CountPending(3)
            //         + RequestDisableUser::CountPending(3)
            //         + WithdrawalCollateral::CountPending(3)
            //         + VillageLoan::CountPending(3)
            //         + RequestGasoline::CountPending(3)
            //         + @(integer)$eachMenuCompany['pending']['NGO'],

            // 'PWS' => RequestMemo::presidentpendingList(14)->total()
            //         + RequestForm::presidentpendingList(14)->total()
            //         + RequestHR::presidentpendingList(14)->total()
            //         + Disposal::presidentpendingList(14)->total()
            //         + DamagedLog::presidentpendingList(14)->total()
            //         + HRRequest::presidentpendingList(14)->total()
            //         + Loan::CountPending(14)
            //         + SaleAsset::presidentpendingList(14)->total()
            //         + SendReceive::presidentpendingList(14)->total()
            //         + ReturnBudget::presidentpendingList(14)->total()
            //         + RescheduleLoan::presidentpendingList(14)->total()
            //         + Mission::presidentpendingList(14)->total()
            //         + MissionItem::presidentpendingList(14)->total()
            //         + MissionClearance::CountPending(14)
            //         + Training::presidentpendingList(14)->total()
            //         + RequestOT::presidentpendingList(14)->total()
            //         + Penalty::presidentpendingList(14)->total()
            //         + Penalty::presidentpendingListInterest(14)->total()
            //         + Penalty::presidentpendingListAssociation(14)->total()
            //         + EmployeePenalty::presidentpendingList(14)->total()
            //         + CashAdvance::presidentpendingList(14)->total()
            //         + Resign::presidentpendingList(14)->total()
            //         + GeneralRequest::CountPending(14)
            //         + RequestCreateUser::CountPending(14)
            //         + TransferAsset::CountPending(14)
            //         + SettingReviewerApprover::CountPending(14)
            //         + Association::CountPending(14)
            //         + Survey::CountPending(14)
            //         + CustomLetter::CountPending(14)
            //         + Policy::CountPending(14)
            //         + RequestDisableUser::CountPending(14)
            //         + WithdrawalCollateral::CountPending(14)
            //         + VillageLoan::CountPending(14)
            //         + RequestGasoline::CountPending(14)
            //         + @(integer)$eachMenuCompany['pending']['PWS'],

            'ORD' => RequestMemo::presidentpendingList(4)->total()
                    + RequestForm::presidentpendingList(4)->total()
                    + RequestPR::presidentpendingList(4)->total()
                    + RequestPO::presidentpendingList(4)->total()
                    + RequestGRN::presidentpendingList(4)->total()
                    + RequestHR::presidentpendingList(4)->total()
                    + Disposal::presidentpendingList(4)->total()
                    + DamagedLog::presidentpendingList(4)->total()
                    + HRRequest::presidentpendingList(4)->total()
                    + SaleAsset::presidentpendingList(4)->total()
                    + SendReceive::presidentpendingList(4)->total()
                    + ReturnBudget::presidentpendingList(4)->total()
                    + Training::presidentpendingList(4)->total()
                    + RequestOT::presidentpendingList(4)->total()
                    + EmployeePenalty::presidentpendingList(4)->total()
                    + CashAdvance::presidentpendingList(4)->total()
                    + Resign::presidentpendingList(4)->total()
                    + RequestCreateUser::CountPending(4)
                    + TransferAsset::CountPending(4)
                    + SettingReviewerApprover::CountPending(4)
                    + CustomLetter::CountPending(4)
                    + Policy::CountPending(4)
                    + RequestDisableUser::CountPending(4)
                    + @(integer)$eachMenuCompany['pending']['ORD'],

            'ORD2' => RequestMemo::presidentpendingList(16)->total()
                    + RequestForm::presidentpendingList(16)->total()
                    + RequestPR::presidentpendingList(16)->total()
                    + RequestPO::presidentpendingList(16)->total()
                    + RequestGRN::presidentpendingList(16)->total()
                    + RequestHR::presidentpendingList(16)->total()
                    + Disposal::presidentpendingList(16)->total()
                    + DamagedLog::presidentpendingList(16)->total()
                    + HRRequest::presidentpendingList(16)->total()
                    + SaleAsset::presidentpendingList(16)->total()
                    + SendReceive::presidentpendingList(16)->total()
                    + ReturnBudget::presidentpendingList(16)->total()
                    + Training::presidentpendingList(16)->total()
                    + RequestOT::presidentpendingList(16)->total()
                    + EmployeePenalty::presidentpendingList(16)->total()
                    + CashAdvance::presidentpendingList(16)->total()
                    + Resign::presidentpendingList(16)->total()
                    + RequestCreateUser::CountPending(16)
                    + TransferAsset::CountPending(16)
                    + SettingReviewerApprover::CountPending(16)
                    + CustomLetter::CountPending(16)
                    + Policy::CountPending(16)
                    + RequestDisableUser::CountPending(16)
                    + @(integer)$eachMenuCompany['pending']['ORD2'],

            // 'ST' => RequestMemo::presidentpendingList(5)->total()
            //         + RequestForm::presidentpendingList(5)->total()
            //         + RequestHR::presidentpendingList(5)->total()
            //         + Disposal::presidentpendingList(5)->total()
            //         + DamagedLog::presidentpendingList(5)->total()
            //         + HRRequest::presidentpendingList(5)->total()
            //         + SaleAsset::presidentpendingList(5)->total()
            //         + SendReceive::presidentpendingList(5)->total()
            //         + ReturnBudget::presidentpendingList(5)->total()
            //         + Training::presidentpendingList(5)->total()
            //         + RequestOT::presidentpendingList(5)->total()
            //         + EmployeePenalty::presidentpendingList(5)->total()
            //         + CashAdvance::presidentpendingList(5)->total()
            //         + Resign::presidentpendingList(5)->total()
            //         + RequestCreateUser::CountPending(5)
            //         + TransferAsset::CountPending(5)
            //         + SettingReviewerApprover::CountPending(5)
            //         + CustomLetter::CountPending(5)
            //         + Policy::CountPending(5)
            //         + RequestDisableUser::CountPending(5)
            //         + @(integer)$eachMenuCompany['pending']['ST'],

            // 'MMI' => RequestMemo::presidentpendingList(6)->total()
            //         + RequestForm::presidentpendingList(6)->total()
            //         + RequestHR::presidentpendingList(6)->total()
            //         + Disposal::presidentpendingList(6)->total()
            //         + DamagedLog::presidentpendingList(6)->total()
            //         + HRRequest::presidentpendingList(6)->total()
            //         + SaleAsset::presidentpendingList(6)->total()
            //         + SendReceive::presidentpendingList(6)->total()
            //         + ReturnBudget::presidentpendingList(6)->total()
            //         + Mission::presidentpendingList(6)->total()
            //         + MissionItem::presidentpendingList(6)->total()
            //         + MissionClearance::CountPending(6)
            //         + Training::presidentpendingList(6)->total()
            //         + RequestOT::presidentpendingList(6)->total()
            //         + EmployeePenalty::presidentpendingList(6)->total()
            //         + CashAdvance::presidentpendingList(6)->total()
            //         + Resign::presidentpendingList(6)->total()
            //         + RequestCreateUser::CountPending(6)
            //         + TransferAsset::CountPending(6)
            //         + SettingReviewerApprover::CountPending(6)
            //         + CustomLetter::CountPending(6)
            //         + Policy::CountPending(6)
            //         + RequestDisableUser::CountPending(6)
            //         + RequestGasoline::CountPending(6)
            //         + @(integer)$eachMenuCompany['pending']['MMI'],

            // 'MHT' => RequestMemo::presidentpendingList(7)->total()
            //         + RequestForm::presidentpendingList(7)->total()
            //         + RequestHR::presidentpendingList(7)->total()
            //         + Disposal::presidentpendingList(7)->total()
            //         + DamagedLog::presidentpendingList(7)->total()
            //         + HRRequest::presidentpendingList(7)->total()
            //         + SaleAsset::presidentpendingList(7)->total()
            //         + SendReceive::presidentpendingList(7)->total()
            //         + ReturnBudget::presidentpendingList(7)->total()
            //         + Training::presidentpendingList(7)->total()
            //         + RequestOT::presidentpendingList(7)->total()
            //         + EmployeePenalty::presidentpendingList(7)->total()
            //         + CashAdvance::presidentpendingList(7)->total()
            //         + Resign::presidentpendingList(7)->total()
            //         + RequestCreateUser::CountPending(7)
            //         + TransferAsset::CountPending(7)
            //         + SettingReviewerApprover::CountPending(7)
            //         + CustomLetter::CountPending(7)
            //         + Policy::CountPending(7)
            //         + RequestDisableUser::CountPending(7)
            //         + @(integer)$eachMenuCompany['pending']['MHT'],

            // 'TSP' => RequestMemo::presidentpendingList(8)->total()
            //         + RequestForm::presidentpendingList(8)->total()
            //         + RequestHR::presidentpendingList(8)->total()
            //         + Disposal::presidentpendingList(8)->total()
            //         + DamagedLog::presidentpendingList(8)->total()
            //         + HRRequest::presidentpendingList(8)->total()
            //         + SaleAsset::presidentpendingList(8)->total()
            //         + SendReceive::presidentpendingList(8)->total()
            //         + ReturnBudget::presidentpendingList(8)->total()
            //         + Training::presidentpendingList(8)->total()
            //         + RequestOT::presidentpendingList(8)->total()
            //         + EmployeePenalty::presidentpendingList(8)->total()
            //         + CashAdvance::presidentpendingList(8)->total()
            //         + Resign::presidentpendingList(8)->total()
            //         + RequestCreateUser::CountPending(8)
            //         + TransferAsset::CountPending(8)
            //         + SettingReviewerApprover::CountPending(8)
            //         + CustomLetter::CountPending(8)
            //         + Policy::CountPending(8)
            //         + RequestDisableUser::CountPending(8)
            //         + @(integer)$eachMenuCompany['pending']['TSP'],

            // 'President' => RequestMemo::presidentpendingList(9)->total()
            //         + RequestForm::presidentpendingList(9)->total()
            //         + RequestHR::presidentpendingList(9)->total()
            //         + Disposal::presidentpendingList(9)->total()
            //         + DamagedLog::presidentpendingList(9)->total()
            //         + HRRequest::presidentpendingList(9)->total()
            //         + SaleAsset::presidentpendingList(9)->total()
            //         + SendReceive::presidentpendingList(9)->total()
            //         + ReturnBudget::presidentpendingList(9)->total()
            //         + Training::presidentpendingList(9)->total()
            //         + RequestOT::presidentpendingList(9)->total()
            //         + EmployeePenalty::presidentpendingList(9)->total()
            //         + CashAdvance::presidentpendingList(9)->total()
            //         + Resign::presidentpendingList(9)->total()
            //         + RequestCreateUser::CountPending(9)
            //         + TransferAsset::CountPending(9)
            //         + SettingReviewerApprover::CountPending(9)
            //         + CustomLetter::CountPending(9)
            //         + Policy::CountPending(9)
            //         + RequestDisableUser::CountPending(9)
            //         + @(integer)$eachMenuCompany['pending']['President'],

            // 'MDN' => RequestMemo::CountPending(10)
            //         + RequestForm::presidentpendingList(10)->total()
            //         + RequestHR::presidentpendingList(10)->total()
            //         + Disposal::presidentpendingList(10)->total()
            //         + DamagedLog::presidentpendingList(10)->total()
            //         + HRRequest::presidentpendingList(10)->total()
            //         + SaleAsset::presidentpendingList(10)->total()
            //         + SendReceive::presidentpendingList(10)->total()
            //         + ReturnBudget::presidentpendingList(10)->total()
            //         + Mission::presidentpendingList(10)->total()
            //         + MissionItem::presidentpendingList(10)->total()
            //         + Training::presidentpendingList(10)->total()
            //         + RequestOT::presidentpendingList(10)->total()
            //         + EmployeePenalty::presidentpendingList(10)->total()
            //         + CashAdvance::presidentpendingList(10)->total()
            //         + Resign::presidentpendingList(10)->total()
            //         + RequestCreateUser::CountPending(10)
            //         + TransferAsset::CountPending(10)
            //         + SettingReviewerApprover::CountPending(10)
            //         + CustomLetter::CountPending(10)
            //         + Policy::CountPending(10)
            //         + RequestDisableUser::CountPending(10)
            //         + @(integer)$eachMenuCompany['pending']['MDN'],

            // 'PTK' => RequestMemo::CountPending(11)
            //         + RequestForm::presidentpendingList(11)->total()
            //         + RequestHR::presidentpendingList(11)->total()
            //         + Disposal::presidentpendingList(11)->total()
            //         + DamagedLog::presidentpendingList(11)->total()
            //         + HRRequest::presidentpendingList(11)->total()
            //         + SaleAsset::presidentpendingList(11)->total()
            //         + SendReceive::presidentpendingList(11)->total()
            //         + ReturnBudget::presidentpendingList(11)->total()
            //         + Mission::presidentpendingList(11)->total()
            //         + MissionItem::presidentpendingList(11)->total()
            //         + Training::presidentpendingList(11)->total()
            //         + RequestOT::presidentpendingList(11)->total()
            //         + EmployeePenalty::presidentpendingList(11)->total()
            //         + CashAdvance::presidentpendingList(11)->total()
            //         + Resign::presidentpendingList(11)->total()
            //         + RequestCreateUser::CountPending(11)
            //         + TransferAsset::CountPending(11)
            //         + SettingReviewerApprover::CountPending(11)
            //         + CustomLetter::CountPending(11)
            //         + Policy::CountPending(11)
            //         + RequestDisableUser::CountPending(11)
            //         + @(integer)$eachMenuCompany['pending']['PTK'],

            // 'NIYA' => RequestMemo::CountPending(12)
            //         + RequestForm::presidentpendingList(12)->total()
            //         + RequestHR::presidentpendingList(12)->total()
            //         + Disposal::presidentpendingList(12)->total()
            //         + DamagedLog::presidentpendingList(12)->total()
            //         + HRRequest::presidentpendingList(12)->total()
            //         + SaleAsset::presidentpendingList(12)->total()
            //         + SendReceive::presidentpendingList(12)->total()
            //         + ReturnBudget::presidentpendingList(12)->total()
            //         + Mission::presidentpendingList(12)->total()
            //         + MissionItem::presidentpendingList(12)->total()
            //         + Training::presidentpendingList(12)->total()
            //         + RequestOT::presidentpendingList(12)->total()
            //         + EmployeePenalty::presidentpendingList(12)->total()
            //         + CashAdvance::presidentpendingList(12)->total()
            //         + Resign::presidentpendingList(12)->total()
            //         + RequestCreateUser::CountPending(12)
            //         + TransferAsset::CountPending(12)
            //         + SettingReviewerApprover::CountPending(12)
            //         + CustomLetter::CountPending(12)
            //         + Policy::CountPending(12)
            //         + RequestDisableUser::CountPending(12)
            //         + @(integer)$eachMenuCompany['pending']['NIYA'],

            // 'DMS' => RequestMemo::CountPending(13)
            //         + RequestForm::presidentpendingList(13)->total()
            //         + RequestHR::presidentpendingList(13)->total()
            //         + Disposal::presidentpendingList(13)->total()
            //         + DamagedLog::presidentpendingList(13)->total()
            //         + HRRequest::presidentpendingList(13)->total()
            //         + SaleAsset::presidentpendingList(13)->total()
            //         + SendReceive::presidentpendingList(13)->total()
            //         + ReturnBudget::presidentpendingList(13)->total()
            //         + Mission::presidentpendingList(13)->total()
            //         + MissionItem::presidentpendingList(13)->total()
            //         + Training::presidentpendingList(13)->total()
            //         + RequestOT::presidentpendingList(13)->total()
            //         + EmployeePenalty::presidentpendingList(13)->total()
            //         + CashAdvance::presidentpendingList(13)->total()
            //         + Resign::presidentpendingList(13)->total()
            //         + RequestCreateUser::CountPending(13)
            //         + TransferAsset::CountPending(13)
            //         + SettingReviewerApprover::CountPending(13)
            //         + CustomLetter::CountPending(13)
            //         + Policy::CountPending(13)
            //         + RequestDisableUser::CountPending(13)
            //         + @(integer)$eachMenuCompany['pending']['DMS'],

            // 'BRC' => RequestMemo::CountPending(15)
            //         + RequestForm::presidentpendingList(15)->total()
            //         + RequestHR::presidentpendingList(15)->total()
            //         + Disposal::presidentpendingList(15)->total()
            //         + DamagedLog::presidentpendingList(15)->total()
            //         + HRRequest::presidentpendingList(15)->total()
            //         + SaleAsset::presidentpendingList(15)->total()
            //         + SendReceive::presidentpendingList(15)->total()
            //         + ReturnBudget::presidentpendingList(15)->total()
            //         + Training::presidentpendingList(15)->total()
            //         + RequestOT::presidentpendingList(15)->total()
            //         + EmployeePenalty::presidentpendingList(15)->total()
            //         + CashAdvance::presidentpendingList(15)->total()
            //         + Resign::presidentpendingList(15)->total()
            //         + RequestCreateUser::CountPending(15)
            //         + TransferAsset::CountPending(15)
            //         + SettingReviewerApprover::CountPending(15)
            //         + CustomLetter::CountPending(15)
            //         + Policy::CountPending(15)
            //         + RequestDisableUser::CountPending(15)
            //         + BorrowingLoan::CountPending(15)
            //         + @(integer)$eachMenuCompany['pending']['BRC'],
        ];
        return $presidentPending;
    }


    //////////////////////////
    public function requestType()
    {
        ini_set("memory_limit", -1);
        
        $groupRequest = new GroupRequest();
        $request = \request();

        $tags = \request()->tags;
        // Param
        $type = \request()->type;
        $menu = $request->menu;
        $companyShortName = $request->company;
        $company = Company::select('id')->where('short_name_en', $companyShortName)->first();
        $company = @$company->id;

        if ($menu == 'toapprove' || $menu == 'to_approve_report' || $menu == 'to_approve_group_support') {

            $data['requestType']['memo'] = RequestMemo::presidentApprove($company)->total();
            $data['requestType']['special'] = RequestForm::presidentApprove($company)->total();
            $data['requestType']['pr_request'] = RequestPR::presidentApprove($company)->total();
            $data['requestType']['po_request'] = RequestPO::presidentApprove($company)->total();
            $data['requestType']['grn'] = RequestGRN::presidentApprove($company)->total();
            $data['requestType']['general'] = RequestHR::presidentApprove($company)->total();
            $data['requestType']['disposal'] = Disposal::presidentApprove($company)->total();
            $data['requestType']['damagedlog'] = DamagedLog::presidentApprove($company)->total();
            $data['requestType']['hr_request'] = HRRequest::presidentApprove($company)->total();
            $data['requestType']['loan'] = Loan::CountToApprove($company);
            $data['requestType']['sale_asset'] = SaleAsset::presidentApprove($company)->total();
            $data['requestType']['return_budget'] = ReturnBudget::presidentApprove($company)->total();
            $data['requestType']['send_receive'] = SendReceive::presidentApprove($company)->total();
            $data['requestType']['reschedule_loan'] = RescheduleLoan::presidentApprove($company)->total();
            $data['requestType']['mission'] = Mission::presidentApprove($company)->total();
            $data['requestType']['v_mission'] = MissionItem::presidentApprove($company)->total();
            $data['requestType']['training'] = Training::presidentApprove($company)->total();
            $data['requestType']['request_ot'] = RequestOT::presidentApprove($company)->total();
            $data['requestType']['penalty'] = Penalty::presidentApprove($company)->total();
            $data['requestType']['interest'] = Penalty::presidentApproveInterest($company)->total();
            $data['requestType']['wave_association'] = Penalty::presidentApproveAssociation($company)->total();
            $data['requestType']['employee_penalty'] = EmployeePenalty::presidentApprove($company)->total();
            $data['requestType']['cash_advance'] = CashAdvance::presidentApprove($company)->total();
            $data['requestType']['resign'] = Resign::presidentApproveResign($company)->total();
            $data['requestType']['resign_last_day'] = Resign::presidentApproveRequestLastDay($company)->total();
            $data['requestType']['request_user'] = RequestCreateUser::CountToApprove($company);
            $data['requestType']['transfer_asset'] = TransferAsset::CountToApprove($company);
            $data['requestType']['setting'] = SettingReviewerApprover::CountToApprove($company);
            $data['requestType']['general_request'] = GeneralRequest::CountToApprove($company);
            $data['requestType']['association'] = Association::CountToApprove($company);
            $data['requestType']['survey_report'] = Survey::CountToApprove($company);
            $data['requestType']['custom_letter'] = CustomLetter::CountToApprove($company);
            $data['requestType']['policy'] = Policy::CountToApprove($company);
            $data['requestType']['request_disable_user'] = RequestDisableUser::CountToApprove($company);
            $data['requestType']['borrowing_loan'] = BorrowingLoan::CountToApprove($company);
            $data['requestType']['withdrawal'] = WithdrawalCollateral::CountToApprove($company);
            $data['requestType']['village_loan'] = VillageLoan::CountToApprove($company);
            $data['requestType']['mission_clearance'] = MissionClearance::CountToApprove($company);
            $data['requestType']['request_gasoline'] = RequestGasoline::CountToApprove($company);
            if (Auth::id() == getCEO()->id) {
                $data['requestType']['report'] = $groupRequest->countToApproveListOfReportByCompanyOfPresident();
                $data['company_departments'] = @$groupRequest->getToApproveListOfReportEachDepartmentByCompanyPresident();
            } else {
                $data['requestType']['report'] = $groupRequest->countToApproveByCompany($company);
            }

        } elseif ($menu == 'reject') {

            $data['requestType']['memo'] = RequestMemo::presidentRejectedList($company)->total();
            $data['requestType']['special'] = RequestForm::presidentRejectedList($company)->total();
            $data['requestType']['pr_request'] = RequestPR::presidentRejectedList($company)->total();
            $data['requestType']['po_request'] = RequestPO::presidentRejectedList($company)->total();
            $data['requestType']['grn'] = RequestGRN::presidentRejectedList($company)->total();
            $data['requestType']['general'] = RequestHR::presidentRejectedList($company)->total();
            $data['requestType']['disposal'] = Disposal::presidentRejectedList($company)->total();
            $data['requestType']['damagedlog'] = DamagedLog::presidentRejectedList($company)->total();
            $data['requestType']['hr_request'] = HRRequest::presidentRejectedList($company)->total();
            $data['requestType']['loan'] = Loan::CountRejected($company);
            $data['requestType']['sale_asset'] = SaleAsset::presidentRejectedList($company)->total();
            $data['requestType']['return_budget'] = ReturnBudget::presidentRejectedList($company)->total();
            $data['requestType']['send_receive'] = SendReceive::presidentRejectedList($company)->total();
            $data['requestType']['reschedule_loan'] = RescheduleLoan::presidentRejectedList($company)->total();
            $data['requestType']['mission'] = Mission::presidentRejectedList($company)->total();
            $data['requestType']['v_mission'] = MissionItem::presidentRejectedList($company)->total();
            $data['requestType']['training'] = Training::presidentRejectedList($company)->total();
            $data['requestType']['request_ot'] = RequestOT::presidentRejectedList($company)->total();
            $data['requestType']['penalty'] = Penalty::presidentRejectedList($company)->total();
            $data['requestType']['interest'] = Penalty::presidentRejectedListInterest($company)->total();
            $data['requestType']['wave_association'] = Penalty::presidentRejectedListAssociation($company)->total();
            $data['requestType']['employee_penalty'] = EmployeePenalty::presidentRejectedList($company)->total();
            $data['requestType']['cash_advance'] = CashAdvance::presidentRejectedList($company)->total();
            $data['requestType']['resign'] = Resign::presidentRejectedListResign($company)->total();
            $data['requestType']['resign_last_day'] = Resign::presidentRejectedListRequestLastDay($company)->total();
            $data['requestType']['general_request'] = GeneralRequest::CountRejected($company);
            $data['requestType']['request_user'] = RequestCreateUser::CountRejected($company);
            $data['requestType']['transfer_asset'] = TransferAsset::CountRejected($company);
            $data['requestType']['setting'] = SettingReviewerApprover::CountRejected($company);
            $data['requestType']['association'] = Association::CountRejected($company);
            $data['requestType']['survey_report'] = Survey::CountRejected($company);
            $data['requestType']['custom_letter'] = CustomLetter::CountRejected($company);
            $data['requestType']['policy'] = Policy::CountRejected($company);
            $data['requestType']['request_disable_user'] = RequestDisableUser::CountRejected($company);
            $data['requestType']['borrowing_loan'] = BorrowingLoan::CountRejected($company);
            $data['requestType']['withdrawal'] = WithdrawalCollateral::CountRejected($company);
            $data['requestType']['village_loan'] = VillageLoan::CountRejected($company);
            $data['requestType']['mission_clearance'] = MissionClearance::CountRejected($company);
            $data['requestType']['request_gasoline'] = RequestGasoline::CountRejected($company);
            if (Auth::id() == getCEO()->id) {
                $data['requestType']['report'] = $groupRequest->countRejectedListOfReportByCompanyOfPresident();
                $data['company_departments'] = @$groupRequest->getRejectedListOfReportEachDepartmentByCompanyPresident();

            } else {
                $data['requestType']['report'] = $groupRequest->countRejectedByCompany($company);
            }

        }elseif ($menu == 'disable') {

            $data['requestType']['memo'] = RequestMemo::CountDisabled($company);
            $data['requestType']['special'] = RequestForm::presidentDisabledList($company)->total();
            $data['requestType']['pr_request'] = RequestPR::presidentDisabledList($company)->total();
            $data['requestType']['po_request'] = RequestPO::presidentDisabledList($company)->total();
            $data['requestType']['grn'] = RequestGRN::presidentDisabledList($company)->total();
            $data['requestType']['general'] = RequestHR::presidentDisabledList($company)->total();
            $data['requestType']['disposal'] = Disposal::presidentDisabledList($company)->total();
            $data['requestType']['damagedlog'] = DamagedLog::presidentDisabledList($company)->total();
            $data['requestType']['hr_request'] = HRRequest::presidentDisabledList($company)->total();
            $data['requestType']['loan'] = Loan::CountDisabled($company);
            $data['requestType']['sale_asset'] = SaleAsset::presidentDisabledList($company)->total();
            $data['requestType']['return_budget'] = ReturnBudget::presidentDisabledList($company)->total();
            $data['requestType']['send_receive'] = SendReceive::presidentDisabledList($company)->total();
            $data['requestType']['reschedule_loan'] = RescheduleLoan::presidentDisabledList($company)->total();
            $data['requestType']['mission'] = Mission::presidentDisabledList($company)->total();
            $data['requestType']['v_mission'] = 0;
            $data['requestType']['training'] = Training::presidentDisabledList($company)->total();
            $data['requestType']['request_ot'] = RequestOT::presidentDisabledList($company)->total();
            $data['requestType']['penalty'] = Penalty::presidentDisabledList($company)->total();
            $data['requestType']['interest'] = Penalty::presidentDisabledListInterest($company)->total();
            $data['requestType']['wave_association'] = Penalty::presidentDisabledListAssociation($company)->total();
            $data['requestType']['employee_penalty'] = EmployeePenalty::presidentDisabledList($company)->total();
            $data['requestType']['cash_advance'] =  CashAdvance::presidentDisabledList($company)->total();
            $data['requestType']['resign'] = Resign::presidentDisabledListResign($company)->total();
            $data['requestType']['resign_last_day'] = Resign::presidentDisabledListRequestLastDay($company)->total();
            $data['requestType']['general_request'] = GeneralRequest::CountDisabled($company);
            $data['requestType']['request_user'] = 0;
            $data['requestType']['transfer_asset'] = TransferAsset::CountDisabled($company);
            $data['requestType']['setting'] = 0;
            $data['requestType']['association'] = Association::CountDisabled($company);
            $data['requestType']['survey_report'] = 0;
            $data['requestType']['custom_letter'] = CustomLetter::CountDisabled($company);
            $data['requestType']['policy'] = 0;
            $data['requestType']['request_disable_user'] = 0;
            $data['requestType']['borrowing_loan'] = 0;
            $data['requestType']['withdrawal'] = WithdrawalCollateral::CountDisabled($company);
            $data['requestType']['village_loan'] = VillageLoan::CountDisabled($company);
            $data['requestType']['mission_clearance'] = MissionClearance::CountDisabled($company);
            $data['requestType']['request_gasoline'] = RequestGasoline::CountDisabled($company);
            if (Auth::id() == getCEO()->id) {
                $data['requestType']['report'] = 0;
                $data['company_departments'] = 0;

            } else {
                $data['requestType']['report'] = 0;
            }

        } elseif ($menu == 'approved') {

            $data['requestType']['memo'] = RequestMemo::presidentApproved($company)->total();
            $data['requestType']['special'] = RequestForm::presidentApproved($company)->total();
            $data['requestType']['pr_request'] = RequestPR::presidentApproved($company)->total();
            $data['requestType']['po_request'] = RequestPO::presidentApproved($company)->total();
            $data['requestType']['grn'] = RequestGRN::presidentApproved($company)->total();
            $data['requestType']['general'] = RequestHR::presidentApproved($company)->total();
            $data['requestType']['disposal'] = Disposal::presidentApproved($company)->total();
            $data['requestType']['damagedlog'] = DamagedLog::presidentApproved($company)->total();
            $data['requestType']['hr_request'] = HRRequest::presidentApproved($company)->total();
            $data['requestType']['loan'] = Loan::CountApproved($company);
            $data['requestType']['sale_asset'] = SaleAsset::presidentApproved($company)->total();
            $data['requestType']['return_budget'] = ReturnBudget::presidentApproved($company)->total();
            $data['requestType']['send_receive'] = SendReceive::presidentApproved($company)->total();
            $data['requestType']['reschedule_loan'] = RescheduleLoan::presidentApproved($company)->total();
            $data['requestType']['mission'] = Mission::presidentApproved($company)->total();
            $data['requestType']['v_mission'] = MissionItem::presidentApproved($company)->total();
            $data['requestType']['training'] = Training::presidentApproved($company)->total();
            $data['requestType']['request_ot'] = RequestOT::presidentApproved($company)->total();
            $data['requestType']['penalty'] = Penalty::presidentApproved($company)->total();
            $data['requestType']['interest'] = Penalty::presidentApprovedInterest($company)->total();
            $data['requestType']['wave_association'] = Penalty::presidentApprovedAssociation($company)->total();
            $data['requestType']['employee_penalty'] = EmployeePenalty::presidentApproved($company)->total();
            $data['requestType']['cash_advance'] = CashAdvance::presidentApproved($company)->total();
            $data['requestType']['resign'] = Resign::presidentApprovedResign($company)->total();
            $data['requestType']['resign_last_day'] = Resign::presidentApprovedRequestLastDay($company)->total();
            $data['requestType']['general_request'] = GeneralRequest::CountApproved($company);
            $data['requestType']['request_user'] = RequestCreateUser::CountApproved($company);
            $data['requestType']['transfer_asset'] = TransferAsset::CountApproved($company);
            $data['requestType']['setting'] = SettingReviewerApprover::CountApproved($company);
            $data['requestType']['association'] = Association::CountApproved($company);
            $data['requestType']['survey_report'] = Survey::CountApproved($company);
            $data['requestType']['custom_letter'] = CustomLetter::CountApproved($company);
            $data['requestType']['policy'] = Policy::CountApproved($company);
            $data['requestType']['request_disable_user'] = RequestDisableUser::CountApproved($company);
            $data['requestType']['borrowing_loan'] = BorrowingLoan::CountApproved($company);
            $data['requestType']['withdrawal'] = WithdrawalCollateral::CountApproved($company);
            $data['requestType']['village_loan'] = VillageLoan::CountApproved($company);
            $data['requestType']['mission_clearance'] = MissionClearance::CountApproved($company);
            $data['requestType']['request_gasoline'] = RequestGasoline::CountApproved($company);
            if (Auth::id() == getCEO()->id) {
                $data['requestType']['report'] = $groupRequest->countApprovedOfReportByCompanyOfPresident();
                $data['company_departments'] = @$groupRequest->getApprovedListOfReportEachDepartmentByCompanyPresident();
            } else {
                $data['requestType']['report'] = $groupRequest->countApprovedByCompany($company);
            }

        } elseif ($menu == 'pending') {

            $data['requestType']['memo'] = RequestMemo::presidentpendingList($company)->total();
            $data['requestType']['special'] = RequestForm::presidentpendingList($company)->total();
            $data['requestType']['pr_request'] = RequestPR::presidentpendingList($company)->total();
            $data['requestType']['po_request'] = RequestPO::presidentpendingList($company)->total();
            $data['requestType']['grn'] = RequestGRN::presidentpendingList($company)->total();
            $data['requestType']['general'] = RequestHR::presidentpendingList($company)->total();
            $data['requestType']['disposal'] = Disposal::presidentpendingList($company)->total();
            $data['requestType']['damagedlog'] = DamagedLog::presidentpendingList($company)->total();
            $data['requestType']['hr_request'] = HRRequest::presidentpendingList($company)->total();
            $data['requestType']['loan'] = Loan::CountPending($company);
            $data['requestType']['sale_asset'] = SaleAsset::presidentpendingList($company)->total();
            $data['requestType']['return_budget'] = ReturnBudget::presidentpendingList($company)->total();
            $data['requestType']['send_receive'] = SendReceive::presidentpendingList($company)->total();
            $data['requestType']['reschedule_loan'] = RescheduleLoan::presidentpendingList($company)->total();
            $data['requestType']['mission'] = Mission::presidentpendingList($company)->total();
            $data['requestType']['v_mission'] = MissionItem::presidentpendingList($company)->total();
            $data['requestType']['training'] = Training::presidentpendingList($company)->total();
            $data['requestType']['request_ot'] = RequestOT::presidentpendingList($company)->total();
            $data['requestType']['penalty'] = Penalty::presidentpendingList($company)->total();
            $data['requestType']['interest'] = Penalty::presidentpendingListInterest($company)->total();
            $data['requestType']['wave_association'] = Penalty::presidentpendingListAssociation($company)->total();
            $data['requestType']['employee_penalty'] = EmployeePenalty::presidentpendingList($company)->total();
            $data['requestType']['cash_advance'] = CashAdvance::presidentpendingList($company)->total();
            $data['requestType']['resign'] = Resign::presidentpendingListResign($company)->total();
            $data['requestType']['resign_last_day'] = Resign::presidentpendingListRequestLastDay($company)->total();
            $data['requestType']['general_request'] = GeneralRequest::CountPending($company);
            $data['requestType']['request_user'] = RequestCreateUser::CountPending($company);
            $data['requestType']['transfer_asset'] = TransferAsset::CountPending($company);
            $data['requestType']['setting'] = SettingReviewerApprover::CountPending($company);
            $data['requestType']['association'] = Association::CountPending($company);
            $data['requestType']['survey_report'] = Survey::CountPending($company);
            $data['requestType']['custom_letter'] = CustomLetter::CountPending($company);
            $data['requestType']['policy'] = Policy::CountPending($company);
            $data['requestType']['request_disable_user'] = RequestDisableUser::CountPending($company);
            $data['requestType']['borrowing_loan'] = BorrowingLoan::CountPending($company);
            $data['requestType']['withdrawal'] = WithdrawalCollateral::CountPending($company);
            $data['requestType']['village_loan'] = VillageLoan::CountPending($company);
            $data['requestType']['mission_clearance'] = MissionClearance::CountPending($company);
            $data['requestType']['request_gasoline'] = RequestGasoline::CountPending($company);
            $data['requestType']['report'] = $groupRequest->countPendingByCompany($company);
        }

//        $totalReportEachTagsByCompanyPresident = $groupRequest->tagsListOfReportForManagement();

//        $data['menu'] = $menu;
//        $data['type'] = $type;
//        $data['company'] = $company;

        if($type == config('app.report')){
            if (Auth::id() == getCEO()->id) {
                $data['tags'] = $groupRequest->presidentGetTagsList();
            } else {
                $data['company_departments'] = @$groupRequest->departmentLisOfReportMenuForManagement();
                $data['tags'] = $groupRequest->tagsListOfReportForManagement();
            }

            // if(!($tags == null || $tags == '' || $tags == 'null') && (Auth::id() == getCEO()->id)){
            //     $data['groups'] = $groupRequest->groupListOfReport();
            // }
            
        }

            
        $data['param'] = \request()->all();

        return response()->json($data);
    }


    //////////////////////////
    public function requestGroup()
    {
        $groupRequest = new GroupRequest();
        $request = \request();

        $tags = \request()->tags;
        // Param
        $type = \request()->type;
        $data = [];

        if ($type == config('app.report')) {
            if (!($tags == null || $tags == '' || $tags == 'null')) {
                if (Auth::id() == getCEO()->id) {
                    $data['groups'] = $groupRequest->groupListOfReport();
                }

                else {
                    $data['groups'] = $groupRequest->groupListOfReportForManagerment();
                }
            }
        }

        return response()->json($data);
    }

    //////////////////////////
    public function groupSupport()
    {
        ini_set("memory_limit", -1);
        
        $groupRequest = new GroupRequest();
        $request = \request();

        $tags = \request()->tags;

        if (Auth::id() == getCEO()->id) {
            $data['company_departments'] = $groupRequest->getGroupSupportDepartmentList();
            $data['tags'] = $groupRequest->getGroupSupportTagList();
            if (!($tags == null || $tags == '' || $tags == 'null')) {
                $data['groups'] = $groupRequest->getGroupSupportGroupList();
            }
        } 
        // else {
        //     $data['company_departments'] = @$groupRequest->departmentLisOfReportMenuForManagement();
        //     $data['tags'] = $groupRequest->tagsListOfReportForManagement();
        // }
        // group request

        $data['param'] = \request()->all();

        return response()->json($data);
    }


    //////////////////////////
    public function requestDepartment()
    {
        ini_set("memory_limit", -1);

        $request = \request();

        // Param
        $type = \request()->type;
        $menu = $request->menu; 

        $department = $request->department;

        $departments = Department::whereNull('deleted_at')->get();

        // add other to department 
        $other = (object)[
                            "id" => -1,
                            "name_en" => "Other",
                            "name_km" => "ផ្សេងៗ"
                        ];
        // merge collection
        $departments = $departments->push($other);

        $companyShortName = $request->company;
        $company = @Company::where('short_name_en', $companyShortName)->first()->id;

        if (!($type == config('app.report')) && $menu == 'approved') {

            if($type == 'Memo'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = RequestMemo::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'Special'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = RequestForm::presidentApproved($company, $depart_id)->total();
                }

            }
            
            elseif($type == 'Pr_Request'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = RequestPR::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'Po_Request'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = RequestPO::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'GRN'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = RequestGRN::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'General'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = RequestHR::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'Disposal'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = Disposal::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'DamagedLog'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = DamagedLog::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'Letter'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = HRRequest::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'SaleAsset'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = SaleAsset::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'ReturnBudget'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = ReturnBudget::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'SendReceive'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = SendReceive::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'Mission'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = Mission::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'VerifyMission'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = MissionItem::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'MissionClearance'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = MissionClearance::CountApproved($company, $depart_id);
                }

            }

            elseif($type == 'Training'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = Training::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'RequestOT'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = RequestOT::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'EmployeePenalty'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = EmployeePenalty::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'CashAdvance'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = CashAdvance::presidentApproved($company, $depart_id)->total();
                }

            }

            elseif($type == 'Resign'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = Resign::presidentApprovedResign($company, $depart_id)->total();
                }

            }

            elseif($type == 'RequestLastDay'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = Resign::presidentApprovedRequestLastDay($company, $depart_id)->total();
                }

            }

            elseif($type == 'RequestUser'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = RequestCreateUser::CountApproved($company, $depart_id);
                }

            }

            elseif($type == 'TransferAsset'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = TransferAsset::CountApproved($company, $depart_id);
                }

            }

            elseif($type == 'Setting'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = SettingReviewerApprover::CountApproved($company, $depart_id);
                }

            }

            elseif($type == 'CustomLetter'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = CustomLetter::CountApproved($company, $depart_id);
                }

            }

            elseif($type == 'Policy'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = Policy::CountApproved($company, $depart_id);
                }

            }

            elseif($type == 'RequestDisableUser'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = RequestDisableUser::CountApproved($company, $depart_id);
                }

            }

            elseif($type == 'Withdrawal'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = WithdrawalCollateral::CountApproved($company, $depart_id);
                }

            }

            elseif($type == 'Village'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;
                    
                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = VillageLoan::CountApproved($company, $depart_id);
                }

            }

            elseif($type == 'RequestGasoline'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = RequestGasoline::CountApproved($company, $depart_id);
                }

            }

            $data['departments'] = $departments;
            //$data['param'] = \request()->all();

            return response()->json($data);

        }

        elseif (!($type == config('app.report')) && $menu == 'pending') {

            if($type == 'Policy'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = Policy::CountPending($company, $depart_id);
                }

            }

            $data['departments'] = $departments;
            //$data['param'] = \request()->all();
            return response()->json($data);

        }

        elseif (!($type == config('app.report')) && $menu == 'toapprove') {

            if($type == 'Policy'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = Policy::CountToApprove($company, $depart_id);
                }

            }

            $data['departments'] = $departments;
            //$data['param'] = \request()->all();

            return response()->json($data);

        }

        elseif (!($type == config('app.report')) && $menu == 'reject') {

            if($type == 'Policy'){

                foreach ($departments as $key => $item) {
                    $depart_id = $item->id;

                    $departments[$key]->active = ($depart_id == $department) ? 1 : 0;
                    $departments[$key]->link = URL::to("/$menu?company=$companyShortName&type=$type&department=$depart_id");
                    $departments[$key]->total = Policy::CountRejected($company, $depart_id);
                }

            }

            $data['departments'] = $departments;
            //$data['param'] = \request()->all();

            return response()->json($data);

        }

        return response()->json(null);
    }

}
