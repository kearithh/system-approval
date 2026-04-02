<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::get('admin', function () {
    // dd(app()->version());
    dd(\Illuminate\Support\Facades\Hash::make('123456'));
    return view('app');
});

Route::get('login', function () {
    return view('auth.login');
});

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/dashboard', 'DashboardController@index');
Route::get('/checking_login', 'Auth\LoginController@check_login');

Auth::routes();

Route::get('pdf', 'RequestFormController@pdf');

Route::group(['middleware'=>'auth'], function(){

    // Route::get('/home', 'RequestFormController@index')->name('home');
    Route::get('/home', 'DashboardController@index')->name('home');
    Route::get('/', function () {
        return redirect('/dashboard');
    });
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('none_request', 'DashboardController@noneRequest')->name('none_request');

    /**
     * Pending List
     */
    Route::group(['prefix'=>'pending'], function(){
        Route::get('/', 'PendingController@Pending')->name('pending');
        Route::get('memo', 'PendingController@memo')->name('pending.memo');
        Route::get('special-expense', 'PendingController@specialExpense')->name('pending.specialExpense');
        Route::get('pr-request', 'PendingController@prRequest')->name('pending.prRequest');
        Route::get('withdrawal-collateral', 'PendingController@withdrawalCollateral')->name('pending.withdrawalCollateral');
        Route::get('general-expense', 'PendingController@generalExpense')->name('pending.generalExpense');
        Route::get('disposal', 'PendingController@disposal')->name('pending.disposal');
        Route::get('damagedlog', 'PendingController@damagedlog')->name('pending.damagedlog');
        Route::get('hr_request', 'PendingController@hr_request')->name('pending.hr_request');
    });

    /**
     * Approval List
     */
    Route::group(['prefix'=>'approval'], function(){
        Route::get('memo', 'ApprovalController@memo')->name('approval.memo');
        Route::get('special-expense', 'ApprovalController@specialExpense')->name('approval.specialExpense');
        Route::get('pr-request', 'ApprovalController@prRequest')->name('approval.prRequest');
        Route::get('general-expense', 'ApprovalController@generalExpense')->name('approval.generalExpense');
        Route::get('withdrawal-collateral', 'ApprovalController@withdrawalCollateral')->name('approval.withdrawalCollateral');
        Route::get('disposal', 'ApprovalController@disposal')->name('approval.disposal');
        Route::get('damagedlog', 'ApprovalController@damagedlog')->name('approval.damagedlog');
        Route::get('hr_request', 'ApprovalController@hr_request')->name('approval.hr_request');
    });

    /**
     * To Approve List
     */
    Route::group(['prefix'=>'toapprove'], function(){
        Route::get('/', 'ToApproveController@ToApprove')->name('toapprove.ToApprove');
    });
    Route::group(['prefix'=>'to_approve_report'], function(){
        Route::get('/', 'ToApproveController@ToApprove')->name('toapprove.ToApprove');
    });
    Route::group(['prefix'=>'to_approve_group_support'], function(){
        Route::get('/', 'ToApproveGroupSupportController@toApproveGroupSupport')->name('toApproveGroupSupport');
    });
    Route::group(['prefix'=>'approved'], function(){
        Route::get('/', 'ApprovedController@Approved')->name('approved');
    });

    /**
     * Reject List
     */
    Route::group(['prefix'=>'reject'], function(){
        Route::get('/', 'RejectController@ListReject')->name('reject.reject');
        Route::get('memo', 'RejectController@memo')->name('reject.memo');
        Route::get('special-expense', 'RejectController@specialExpense')->name('reject.specialExpense');
        Route::get('pr-request', 'RejectController@prRequest')->name('reject.prRequest');
        Route::get('po-request', 'RejectController@poRequest')->name('reject.poRequest');
        Route::get('withdrawal-collateral', 'RejectController@withdrawalCollateral')->name('reject.withdrawalCollateral');
        Route::get('general-expense', 'RejectController@generalExpense')->name('reject.generalExpense');
        Route::get('disposal', 'RejectController@disposal')->name('reject.disposal');
        Route::get('damagedlog', 'RejectController@damagedlog')->name('reject.damagedlog');
        Route::get('hr_request', 'RejectController@hr_request')->name('reject.hr_request');
    });

    /**
     * Disabled List
     */
    Route::group(['prefix'=>'disable'], function(){
        Route::get('/', 'DisableController@ListDisable')->name('disable.disable');
    });


    Route::group(['prefix'=>'summary_report'], function(){
        Route::get('special-expense', 'SummaryReportController@specialExpense')->name('summary.specialExpense');
        Route::get('pr-request', 'SummaryReportController@prRequest')->name('summary.prRequest');
        Route::get('po-request', 'SummaryReportController@poRequest')->name('summary.poRequest');
        Route::get('grn', 'SummaryReportController@gRn')->name('summary.gRn');
        Route::get('withdrawal-collateral', 'SummaryReportController@withdrawalCollateral')->name('summary.withdrawalCollateral');
        Route::get('special-expense/export', 'SummaryReportController@specialExpenseExport')->name('summary.specialExpenseExport');
        Route::get('pr-request/export', 'SummaryReportController@prRequestExport')->name('summary.prRequestExport');
        Route::get('po-request/export', 'SummaryReportController@poRequestExport')->name('summary.poRequestExport');
        Route::get('grn/export', 'SummaryReportController@gRnExport')->name('summary.gRnExport');

        Route::get('general-expense', 'SummaryReportController@generalExpense')->name('summary.generalExpense');
        Route::get('general-expense/export', 'SummaryReportController@generalExpenseExport')->name('summary.generalExpenseExport');

        Route::get('memo', 'SummaryReportController@memo')->name('summary.memo');
        Route::get('memo/export', 'SummaryReportController@memoExport')->name('summary.memoExport');

        Route::get('loan', 'SummaryReportController@loan')->name('summary.loan');
        Route::get('loan/export', 'SummaryReportController@loanExport')->name('summary.loanExport');

        Route::get('penalty', 'SummaryReportController@penalty')->name('summary.penalty');
        Route::get('penalty/export', 'SummaryReportController@penaltyExport')->name('summary.penaltyExport');

        Route::get('wave_association', 'SummaryReportController@waveAssociation')->name('summary.wave_association');
        Route::get('wave_association/export', 'SummaryReportController@waveAssociationExport')->name('summary.waveAssociationExport');

        Route::get('cuttingInterest', 'SummaryReportController@cuttingInterest')->name('summary.cuttingInterest');
        Route::get('cuttingInterest/export', 'SummaryReportController@cuttingInterestExport')->name('summary.cuttingInterestExport');

        Route::get('ot-report', 'SummaryReportController@OTReport')->name('summary.OTReport');
        Route::get('ot-report/export', 'SummaryReportController@OTExport')->name('summary.OTExport');

        Route::get('report', 'SummaryReportController@Report')->name('summary.Report');
        Route::get('report/export', 'SummaryReportController@ReportExport')->name('summary.ReportExport');

        Route::get('mission', 'SummaryReportController@mission')->name('summary.mission');
        Route::get('mission/export', 'SummaryReportController@missionExport')->name('summary.misiionExport');

        Route::get('resign_letter', 'SummaryReportController@resignLetter')->name('summary.resign_letter');
        Route::get('resign_letter/export', 'SummaryReportController@resignLetterExport')->name('summary.resignLetterExport');
    });


    /**
     * The route of expense hr request
     * Expense HR
     */
    Route::get('request_hr', 'RequestHRController@index')->name('request_hr.index');
    Route::post('request_hr', 'RequestHRController@store')->name('request_hr.store');
    Route::get('request_hr/create', 'RequestHRController@create')->name('request_hr.create');
    Route::get('request_hr/{id}/show', 'RequestHRController@show')->name('request_hr.show');
    Route::get('request_hr/{id}/edit', 'RequestHRController@edit')->name('request_hr.edit');
    Route::post('request_hr/{id}/update', 'RequestHRController@update')->name('request_hr.update');
    Route::post('request_hr/{id}/delete', 'RequestHRController@destroy')->name('request_hr.destroy');
    Route::post('request_hr/{id}/approve', 'RequestHRController@approve')->name('request_hr.approve');
    Route::post('request_hr/{id}/reject', 'RequestHRController@reject')->name('request_hr.reject');
    Route::post('request_hr/{id}/disable', 'RequestHRController@disable')->name('request_hr.disable');
    Route::post('request_hr/import', 'RequestHRController@import')->name('request_hr.import');

    /**
     * The route of Cash Advance
     * Cash Advance
     */
    Route::post('cash_advance', 'CashAdvanceController@store')->name('cash_advance.store');
    Route::get('cash_advance/create', 'CashAdvanceController@create')->name('cash_advance.create');
    Route::get('cash_advance/{id}/show', 'CashAdvanceController@show')->name('cash_advance.show');
    Route::get('cash_advance/{id}/edit', 'CashAdvanceController@edit')->name('cash_advance.edit');
    Route::post('cash_advance/{id}/update', 'CashAdvanceController@update')->name('cash_advance.update');
    Route::post('cash_advance/{id}/delete', 'CashAdvanceController@destroy')->name('cash_advance.destroy');
    Route::post('cash_advance/{id}/approve', 'CashAdvanceController@approve')->name('cash_advance.approve');
    Route::post('cash_advance/{id}/reject', 'CashAdvanceController@reject')->name('cash_advance.reject');
    Route::post('cash_advance/{id}/disable', 'CashAdvanceController@disable')->name('cash_advance.disable');
    Route::post('cash_advance/{id}/clear', 'CashAdvanceController@clear')->name('cash_advance.clear');

    /**
     * The route of expense request
     * Expense
     */
    Route::get('request', 'RequestFormController@index')->name('request.index');
    Route::post('request', 'RequestFormController@store')->name('request.store');
    Route::get('request/create', 'RequestFormController@create')->name('request.create');
    Route::get('request/{id}/show', 'RequestFormController@show')->name('request.show');
    Route::get('request/{id}/edit', 'RequestFormController@edit')->name('request.edit');
    Route::post('request/{id}/update', 'RequestFormController@update')->name('request.update');
    Route::post('request/{id}/delete', 'RequestFormController@destroy')->name('request.destroy');
    Route::post('request/{id}/approve', 'RequestFormController@approve')->name('request.approve');
    Route::post('request/{id}/reject', 'RequestFormController@reject')->name('request.reject');
    Route::post('request/{id}/disable', 'RequestFormController@disable')->name('request.disable');
    Route::get('request/find_review', 'RequestFormController@findReview')->name('request.find_review');

    /*** Good Received Note */
    Route::get('request_grn', 'RequestGRNController@index')->name('request_grn.index');
    Route::post('request_grn', 'RequestGRNController@store')->name('request_grn.store');
    Route::get('request_grn/create', 'RequestGRNController@create')->name('request_grn.create');
    Route::get('request_grn/{id}/show', 'RequestGRNController@show')->name('request_grn.show');
    Route::get('request_grn/{id}/edit', 'RequestGRNController@edit')->name('request_grn.edit');
    Route::post('request_grn/{id}/update', 'RequestGRNController@update')->name('request_grn.update');
    Route::post('request_grn/{id}/delete', 'RequestGRNController@destroy')->name('request_grn.destroy');
    Route::post('request_grn/{id}/approve', 'RequestGRNController@approve')->name('request_grn.approve');
    Route::post('request_grn/{id}/reject', 'RequestGRNController@reject')->name('request_grn.reject');
    Route::post('request_grn/{id}/disable', 'RequestGRNController@disable')->name('request_grn.disable');
    Route::get('request_grn/find_review', 'RequestGRNController@findReview')->name('request_grn.find_review');
    // Route::get('request_grn/search', 'RequestGRNController@search')->name('search');

    //PO Request

    Route::get('request_po', 'RequestPOController@index')->name('request_po.index');
    Route::post('request_po', 'RequestPOController@store')->name('request_po.store');
    Route::get('request_po/create', 'RequestPOController@create')->name('request_po.create');
    Route::get('request_po/{id}/show', 'RequestPOController@show')->name('request_po.show');
    Route::get('request_po/{id}/edit', 'RequestPOController@edit')->name('request_po.edit');
    Route::post('request_po/{id}/update', 'RequestPOController@update')->name('request_po.update');
    Route::post('request_po/{id}/delete', 'RequestPOController@destroy')->name('request_po.destroy');
    Route::post('request_po/{id}/approve', 'RequestPOController@approve')->name('request_po.approve');
    Route::post('request_po/{id}/reject', 'RequestPOController@reject')->name('request_po.reject');
    Route::post('request_po/{id}/disable', 'RequestPOController@disable')->name('request_po.disable');
    Route::get('request_po/find_review', 'RequestPOController@findReview')->name('request_po.find_review');
    //PR request
    Route::get('request_pr', 'RequestPRController@index')->name('request_pr.index');
    Route::post('request_pr', 'RequestPRController@store')->name('request_pr.store');
    Route::get('request_pr/create', 'RequestPRController@create')->name('request_pr.create');
    Route::get('request_pr/{id}/show', 'RequestPRController@show')->name('request_pr.show');
    Route::get('request_pr/{id}/edit', 'RequestPRController@edit')->name('request_pr.edit');
    Route::post('request_pr/{id}/update', 'RequestPRController@update')->name('request_pr.update');
    Route::post('request_pr/{id}/delete', 'RequestPRController@destroy')->name('request_pr.destroy');
    Route::post('request_pr/{id}/approve', 'RequestPRController@approve')->name('request_pr.approve');
    Route::post('request_pr/{id}/reject', 'RequestPRController@reject')->name('request_pr.reject');
    Route::post('request_pr/{id}/disable', 'RequestPRController@disable')->name('request_pr.disable');
    Route::get('request_pr/find_review', 'RequestPRController@findReview')->name('request_pr.find_review');

    // Loan Village
    Route::get('village_loan', 'VillageLoanController@index')->name('village_loan.index');
    Route::post('village_loan', 'VillageLoanController@store')->name('village_loan.store');
    Route::get('village_loan/create', 'VillageLoanController@create')->name('village_loan.create');
    Route::get('village_loan/{id}/show', 'VillageLoanController@show')->name('village_loan.show');
    Route::get('village_loan/{id}/edit', 'VillageLoanController@edit')->name('village_loan.edit');
    Route::post('village_loan/{id}/update', 'VillageLoanController@update')->name('village_loan.update');
    Route::post('village_loan/{id}/delete', 'VillageLoanController@destroy')->name('village_loan.destroy');
    Route::post('village_loan/{id}/approve', 'VillageLoanController@approve')->name('village_loan.approve');
    Route::post('village_loan/{id}/reject', 'VillageLoanController@reject')->name('village_loan.reject');
    Route::post('village_loan/{id}/disable', 'VillageLoanController@disable')->name('village_loan.disable');
    Route::get('village_loan/find_review', 'VillageLoanController@findReview')->name('village_loan.find_review');

    //withdrawal Collateral

    Route::get('withdrawal_collateral', 'WithdrawalCollateralController@index')->name('withdrawal_collateral.index');
    Route::post('withdrawal_collateral', 'WithdrawalCollateralController@store')->name('withdrawal_collateral.store');
    Route::get('withdrawal_collateral/create', 'WithdrawalCollateralController@create')->name('withdrawal_collateral.create');
    Route::get('withdrawal_collateral/{id}/show', 'WithdrawalCollateralController@show')->name('withdrawal_collateral.show');
    Route::get('withdrawal_collateral/{id}/edit', 'WithdrawalCollateralController@edit')->name('withdrawal_collateral.edit');
    Route::post('withdrawal_collateral/{id}/update', 'WithdrawalCollateralController@update')->name('withdrawal_collateral.update');
    Route::post('withdrawal_collateral/{id}/delete', 'WithdrawalCollateralController@destroy')->name('withdrawal_collateral.destroy');
    Route::post('withdrawal_collateral/{id}/approve', 'WithdrawalCollateralController@approve')->name('withdrawal_collateral.approve');
    Route::post('withdrawal_collateral/{id}/reject', 'WithdrawalCollateralController@reject')->name('withdrawal_collateral.reject');
    Route::post('withdrawal_collateral/{id}/disable', 'WithdrawalCollateralController@disable')->name('withdrawal_collateral.disable');
    Route::get('withdrawal_collateral/find_review', 'WithdrawalCollateralController@findReview')->name('withdrawal_collateral.find_review');


    /**
     * The route of memo request
     * Memo
     */
    Route::get('request_memo', 'RequestMemoController@index')->name('request_memo.index');
    Route::post('request_memo', 'RequestMemoController@store')->name('request_memo.store');
    Route::get('request_memo/create', 'RequestMemoController@create')->name('request_memo.create');
    Route::get('request_memo/{id}/show', 'RequestMemoController@show')->name('request_memo.show');
    Route::get('request_memo/{id}/edit', 'RequestMemoController@edit');
    Route::post('request_memo/{id}/update', 'RequestMemoController@update')->name('request_memo.update');
    Route::post('request_memo/{id}/delete', 'RequestMemoController@destroy')->name('request_memo.destroy');
    Route::post('request_memo/{id}/approve', 'RequestMemoController@approve')->name('request_memo.approve');
    Route::post('request_memo/{id}/reject', 'RequestMemoController@reject')->name('request_memo.reject');
    Route::post('request_memo/{id}/disable', 'RequestMemoController@disable')->name('request_memo.disable');
    Route::get('request_memo/find_approver', 'RequestMemoController@findApprover')->name('request_memo.find_approver');
    Route::get('public_memo', 'RequestMemoController@publicMemo')->name('public_memo');
    Route::post('request_memo/{id}/abrogation', 'RequestMemoController@abrogation')->name('request_memo.abrogation');

    /**
     * The route of dispose request
     * Dispose
     */
    Route::get('request_dispose', 'RequestDisposeController@index')->name('request_dispose.index');
    Route::post('request_dispose', 'RequestDisposeController@store')->name('request_dispose.store');
    Route::get('request_dispose/create', 'RequestDisposeController@create')->name('request_dispose.create');
    Route::get('request_dispose/{id}/show', 'RequestDisposeController@show')->name('request_dispose.show');
    Route::get('request_dispose/{id}/edit', 'RequestDisposeController@edit')->name('request_dispose.edit');
    Route::post('request_dispose/{id}/update', 'RequestDisposeController@update')->name('request_dispose.update');
    Route::post('request_dispose/{id}/delete', 'RequestDisposeController@destroy')->name('request_dispose.destroy');
    Route::post('request_dispose/{id}/approve', 'RequestDisposeController@approve')->name('request_dispose.approve');
    Route::post('request_dispose/{id}/reject', 'RequestDisposeController@reject')->name('request_dispose.reject');

    /**
     * The route of dispose request
     * Dispose
     */
    Route::get('disposal', 'DisposalController@index')->name('disposal.index');
    Route::post('disposal', 'DisposalController@store')->name('disposal.store');
    Route::get('disposal/create', 'DisposalController@create')->name('disposal.create');
    Route::get('disposal/{id}/show', 'DisposalController@show')->name('disposal.show');
    Route::get('disposal/{id}/edit', 'DisposalController@edit')->name('disposal.edit');
    Route::post('disposal/{id}/update', 'DisposalController@update')->name('disposal.update');
    Route::post('disposal/{id}/delete', 'DisposalController@destroy')->name('disposal.destroy');
    Route::post('disposal/{id}/approve', 'DisposalController@approve')->name('disposal.approve');
    Route::post('disposal/{id}/reject', 'DisposalController@reject')->name('disposal.reject');
    Route::post('disposal/{id}/disable', 'DisposalController@disable')->name('disposal.disable');


    /**
     * The route of damagedlog request
     * Damaged Log
     */
    Route::get('damagedlog', 'DamagedLogController@index')->name('damagedlog.index');
    Route::post('damagedlog', 'DamagedLogController@store')->name('damagedlog.store');
    Route::get('damagedlog/create', 'DamagedLogController@create')->name('damagedlog.create');
    Route::get('damagedlog/{id}/show', 'DamagedLogController@show')->name('damagedlog.show');
    Route::get('damagedlog/{id}/edit', 'DamagedLogController@edit')->name('damagedlog.edit');
    Route::post('damagedlog/{id}/update', 'DamagedLogController@update')->name('damagedlog.update');
    Route::post('damagedlog/{id}/delete', 'DamagedLogController@destroy')->name('damagedlog.destroy');
    Route::post('damagedlog/{id}/approve', 'DamagedLogController@approve')->name('damagedlog.approve');
    Route::post('damagedlog/{id}/reject', 'DamagedLogController@reject')->name('damagedlog.reject');
    Route::post('damagedlog/{id}/disable', 'DamagedLogController@disable')->name('damagedlog.disable');


    /**
     * The route of HR request
     * HR
     */
    Route::get('hr_request', 'HRRequestController@index')->name('hr_request.index');
    Route::post('hr_request', 'HRRequestController@store')->name('hr_request.store');
    Route::get('hr_request/create', 'HRRequestController@create')->name('hr_request.create');
    Route::get('hr_request/{id}/show', 'HRRequestController@show')->name('hr_request.show');
    Route::get('hr_request/{id}/edit', 'HRRequestController@edit')->name('hr_request.edit');
    Route::post('hr_request/{id}/update', 'HRRequestController@update')->name('hr_request.update');
    Route::post('hr_request/{id}/delete', 'HRRequestController@destroy')->name('hr_request.destroy');
    Route::post('hr_request/{id}/approve', 'HRRequestController@approve')->name('hr_request.approve');
    Route::post('hr_request/{id}/reject', 'HRRequestController@reject')->name('hr_request.reject');
    Route::post('hr_request/{id}/disable', 'HRRequestController@disable')->name('hr_request.disable');


    /**
     * The route of Resign
     * Resign
     */
    Route::get('resign', 'ResignController@index')->name('resign.index');
    Route::post('resign', 'ResignController@store')->name('resign.store');
    Route::get('resign/create', 'ResignController@create')->name('resign.create');
    Route::get('resign/{id}/show', 'ResignController@show')->name('resign.show');
    Route::get('resign/{id}/edit', 'ResignController@edit')->name('resign.edit');
    Route::post('resign/{id}/update', 'ResignController@update')->name('resign.update');
    Route::post('resign/{id}/delete', 'ResignController@destroy')->name('resign.destroy');
    Route::post('resign/{id}/approve', 'ResignController@approve')->name('resign.approve');
    Route::post('resign/{id}/reject', 'ResignController@reject')->name('resign.reject');
    Route::post('resign/{id}/disable', 'ResignController@disable')->name('resign.disable');


    /**
     * The route of HR request
     * HR
     */
    Route::post('loan', 'LoanController@store')->name('loan.store');
    Route::get('loan/create', 'LoanController@create')->name('loan.create');
    Route::get('loan/{id}/show', 'LoanController@show')->name('loan.show');
    Route::get('loan/{id}/edit', 'LoanController@edit')->name('loan.edit');
    Route::post('loan/{id}/update', 'LoanController@update')->name('loan.update');
    Route::post('loan/{id}/delete', 'LoanController@destroy')->name('loan.destroy');
    Route::post('loan/{id}/approve', 'LoanController@approve')->name('loan.approve');
    Route::post('loan/{id}/reject', 'LoanController@reject')->name('loan.reject');
    Route::post('loan/{id}/disable', 'LoanController@disable')->name('loan.disable');
    Route::post('loan/view_reference', 'LoanController@viewReference')->name('loan.view.reference');


    /**
     * The route of sale_asset request
     * Sale Asset
     */
    Route::post('sale_asset', 'SaleAssetController@store')->name('sale_asset.store');
    Route::get('sale_asset/create', 'SaleAssetController@create')->name('sale_asset.create');
    Route::get('sale_asset/{id}/show', 'SaleAssetController@show')->name('sale_asset.show');
    Route::get('sale_asset/{id}/edit', 'SaleAssetController@edit')->name('sale_asset.edit');
    Route::post('sale_asset/{id}/update', 'SaleAssetController@update')->name('sale_asset.update');
    Route::post('sale_asset/{id}/delete', 'SaleAssetController@destroy')->name('sale_asset.destroy');
    Route::post('sale_asset/{id}/approve', 'SaleAssetController@approve')->name('sale_asset.approve');
    Route::post('sale_asset/{id}/reject', 'SaleAssetController@reject')->name('sale_asset.reject');
    Route::post('sale_asset/{id}/disable', 'SaleAssetController@disable')->name('sale_asset.disable');


    /**
     * The route of return_budget request
     * Sale Asset
     */
    Route::post('return_budget', 'ReturnBudgetController@store')->name('return_budget.store');
    Route::get('return_budget/create', 'ReturnBudgetController@create')->name('return_budget.create');
    Route::get('return_budget/{id}/show', 'ReturnBudgetController@show')->name('return_budget.show');
    Route::get('return_budget/{id}/edit', 'ReturnBudgetController@edit')->name('return_budget.edit');
    Route::post('return_budget/{id}/update', 'ReturnBudgetController@update')->name('return_budget.update');
    Route::post('return_budget/{id}/delete', 'ReturnBudgetController@destroy')->name('return_budget.destroy');
    Route::post('return_budget/{id}/approve', 'ReturnBudgetController@approve')->name('return_budget.approve');
    Route::post('return_budget/{id}/reject', 'ReturnBudgetController@reject')->name('return_budget.reject');
    Route::post('return_budget/{id}/disable', 'ReturnBudgetController@disable')->name('return_budget.disable');


    /**
     * The route of send_receive request
     * Sale Asset
     */
    Route::post('send_receive', 'SendReceiveController@store')->name('send_receive.store');
    Route::get('send_receive/create', 'SendReceiveController@create')->name('send_receive.create');
    Route::get('send_receive/{id}/show', 'SendReceiveController@show')->name('send_receive.show');
    Route::get('send_receive/{id}/edit', 'SendReceiveController@edit')->name('send_receive.edit');
    Route::post('send_receive/{id}/update', 'SendReceiveController@update')->name('send_receive.update');
    Route::post('send_receive/{id}/delete', 'SendReceiveController@destroy')->name('send_receive.destroy');
    Route::post('send_receive/{id}/approve', 'SendReceiveController@approve')->name('send_receive.approve');
    Route::post('send_receive/{id}/reject', 'SendReceiveController@reject')->name('send_receive.reject');
    Route::post('send_receive/{id}/disable', 'SendReceiveController@disable')->name('send_receive.disable');


    /**
     * The route of send_receive request
     * Sale Asset
     */
    Route::get('transfer_asset', 'TransferAssetController@index')->name('transfer_asset.index');
    Route::post('transfer_asset', 'TransferAssetController@store')->name('transfer_asset.store');
    Route::get('transfer_asset/create', 'TransferAssetController@create')->name('transfer_asset.create');
    Route::get('transfer_asset/{id}/show', 'TransferAssetController@show')->name('transfer_asset.show');
    Route::get('transfer_asset/{id}/edit', 'TransferAssetController@edit')->name('transfer_asset.edit');
    Route::post('transfer_asset/{id}/update', 'TransferAssetController@update')->name('transfer_asset.update');
    Route::post('transfer_asset/{id}/delete', 'TransferAssetController@destroy')->name('transfer_asset.destroy');
    Route::post('transfer_asset/{id}/approve', 'TransferAssetController@approve')->name('transfer_asset.approve');
    Route::post('transfer_asset/{id}/reject', 'TransferAssetController@reject')->name('transfer_asset.reject');
    Route::post('transfer_asset/{id}/disable', 'TransferAssetController@disable')->name('transfer_asset.disable');



    /**
     * The route of reschedule_loan request
     * reschedule_loan
     */
    Route::post('reschedule_loan', 'RescheduleLoanController@store')->name('reschedule_loan.store');
    Route::get('reschedule_loan/create', 'RescheduleLoanController@create')->name('reschedule_loan.create');
    Route::get('reschedule_loan/{id}/show', 'RescheduleLoanController@show')->name('reschedule_loan.show');
    Route::get('reschedule_loan/{id}/edit', 'RescheduleLoanController@edit')->name('reschedule_loan.edit');
    Route::post('reschedule_loan/{id}/update', 'RescheduleLoanController@update')->name('reschedule_loan.update');
    Route::post('reschedule_loan/{id}/delete', 'RescheduleLoanController@destroy')->name('reschedule_loan.destroy');
    Route::post('reschedule_loan/{id}/approve', 'RescheduleLoanController@approve')->name('reschedule_loan.approve');
    Route::post('reschedule_loan/{id}/reject', 'RescheduleLoanController@reject')->name('reschedule_loan.reject');
    Route::post('reschedule_loan/{id}/disable', 'RescheduleLoanController@disable')->name('reschedule_loan.disable');



    /**
     * The route of mission request
     * mission
     */
    Route::post('mission', 'MissionController@store')->name('mission.store');
    Route::get('mission/create', 'MissionController@create')->name('mission.create');
    Route::get('mission/{id}/show', 'MissionController@show')->name('mission.show');
    Route::get('mission/{id}/edit', 'MissionController@edit')->name('mission.edit');
    Route::post('mission/{id}/update', 'MissionController@update')->name('mission.update');
    Route::post('mission/{id}/delete', 'MissionController@destroy')->name('mission.destroy');
    Route::post('mission/{id}/approve', 'MissionController@approve')->name('mission.approve');
    Route::post('mission/{id}/reject', 'MissionController@reject')->name('mission.reject');
    Route::post('mission/{id}/disable', 'MissionController@disable')->name('mission.disable');
    Route::post('mission/{id}/verify', 'MissionController@verify')->name('mission.verify');

    /**
     * The route of mission clrearance
     * mission clrearance
     */
    Route::post('mission_clearance', 'MissionClearanceController@store')->name('mission_clearance.store');
    Route::get('mission_clearance/create', 'MissionClearanceController@create')->name('mission_clearance.create');
    Route::get('mission_clearance/{id}/show', 'MissionClearanceController@show')->name('mission_clearance.show');
    Route::get('mission_clearance/{id}/edit', 'MissionClearanceController@edit')->name('mission_clearance.edit');
    Route::post('mission_clearance/{id}/update', 'MissionClearanceController@update')->name('mission_clearance.update');
    Route::post('mission_clearance/{id}/delete', 'MissionClearanceController@destroy')->name('mission_clearance.destroy');
    Route::post('mission_clearance/{id}/approve', 'MissionClearanceController@approve')->name('mission_clearance.approve');
    Route::post('mission_clearance/{id}/reject', 'MissionClearanceController@reject')->name('mission_clearance.reject');
    Route::post('mission_clearance/{id}/disable', 'MissionClearanceController@disable')->name('mission_clearance.disable');


    /**
     * The route of OT request
     * OT
     */
    Route::post('request_ot', 'RequestOTController@store')->name('request_ot.store');
    Route::get('request_ot/create', 'RequestOTController@create')->name('request_ot.create');
    Route::get('request_ot/{id}/show', 'RequestOTController@show')->name('request_ot.show');
    Route::get('request_ot/{id}/edit', 'RequestOTController@edit')->name('request_ot.edit');
    Route::post('request_ot/{id}/update', 'RequestOTController@update')->name('request_ot.update');
    Route::post('request_ot/{id}/delete', 'RequestOTController@destroy')->name('request_ot.destroy');
    Route::post('request_ot/{id}/approve', 'RequestOTController@approve')->name('request_ot.approve');
    Route::post('request_ot/{id}/reject', 'RequestOTController@reject')->name('request_ot.reject');
    Route::post('request_ot/{id}/disable', 'RequestOTController@disable')->name('request_ot.disable');
    Route::get('request_ot/check-staff', 'RequestOTController@checkStaff')->name('request_ot.check-staff');


    /**
     * The route of mission request
     * mission
     */
    Route::post('training', 'TrainingController@store')->name('training.store');
    Route::get('training/create', 'TrainingController@create')->name('training.create');
    Route::get('training/{id}/show', 'TrainingController@show')->name('training.show');
    Route::get('training/{id}/edit', 'TrainingController@edit')->name('training.edit');
    Route::post('training/{id}/update', 'TrainingController@update')->name('training.update');
    Route::post('training/{id}/delete', 'TrainingController@destroy')->name('training.destroy');
    Route::post('training/{id}/approve', 'TrainingController@approve')->name('training.approve');
    Route::post('training/{id}/reject', 'TrainingController@reject')->name('training.reject');
    Route::post('training/{id}/disable', 'TrainingController@disable')->name('training.disable');


    /**
     * The route of penalty request
     *
     */
    Route::get('penalty', 'PenaltyController@index')->name('penalty.index');
    Route::post('penalty', 'PenaltyController@store')->name('penalty.store');
    Route::get('penalty/create', 'PenaltyController@create')->name('penalty.create');
    Route::get('cutting_interest/create', 'PenaltyController@cutting_interest_create')->name('cutting_interest.create');
    Route::get('wave_association/create', 'PenaltyController@wave_association_create')->name('wave_association.create');
    Route::get('penalty/{id}/show', 'PenaltyController@show')->name('penalty.show');
    Route::get('penalty/{id}/edit', 'PenaltyController@edit')->name('penalty.edit');
    Route::post('penalty/{id}/update', 'PenaltyController@update')->name('penalty.update');
    Route::post('penalty/{id}/delete', 'PenaltyController@destroy')->name('penalty.destroy');
    Route::post('penalty/{id}/approve', 'PenaltyController@approve')->name('penalty.approve');
    Route::post('penalty/{id}/reject', 'PenaltyController@reject')->name('penalty.reject');
    Route::post('penalty/{id}/disable', 'PenaltyController@disable')->name('penalty.disable');


    /**
     * The route of employee_penalty request
     *
     */
    Route::get('employee_penalty', 'EmployeePenaltyController@index')->name('employee_penalty.index');
    Route::post('employee_penalty', 'EmployeePenaltyController@store')->name('employee_penalty.store');
    Route::get('employee_penalty/create', 'EmployeePenaltyController@create')->name('employee_penalty.create');
    Route::get('employee_penalty/{id}/show', 'EmployeePenaltyController@show')->name('employee_penalty.show');
    Route::get('employee_penalty/{id}/edit', 'EmployeePenaltyController@edit')->name('employee_penalty.edit');
    Route::post('employee_penalty/{id}/update', 'EmployeePenaltyController@update')->name('employee_penalty.update');
    Route::post('employee_penalty/{id}/delete', 'EmployeePenaltyController@destroy')->name('employee_penalty.destroy');
    Route::post('employee_penalty/{id}/approve', 'EmployeePenaltyController@approve')->name('employee_penalty.approve');
    Route::post('employee_penalty/{id}/reject', 'EmployeePenaltyController@reject')->name('employee_penalty.reject');
    Route::post('employee_penalty/{id}/disable', 'EmployeePenaltyController@disable')->name('employee_penalty.disable');


    /**
     * The route of survey
     * survey
     */
    Route::post('survey_report', 'SurveyReportController@store')->name('survey_report.store');
    Route::get('survey_report/create', 'SurveyReportController@create')->name('survey_report.create');
    Route::get('survey_report/{id}/show', 'SurveyReportController@show')->name('survey_report.show');
    Route::get('survey_report/{id}/edit', 'SurveyReportController@edit')->name('survey_report.edit');
    Route::post('survey_report/{id}/update', 'SurveyReportController@update')->name('survey_report.update');
    Route::post('survey_report/{id}/delete', 'SurveyReportController@destroy')->name('survey_report.destroy');
    Route::post('survey_report/{id}/approve', 'SurveyReportController@approve')->name('survey_report.approve');
    Route::post('survey_report/{id}/reject', 'SurveyReportController@reject')->name('survey_report.reject');


    /**
     * The route of general request
     * General Request
     */
    Route::post('general_request', 'GeneralRequestController@store')->name('general_request.store');
    Route::post('general_request_keep_money', 'GeneralRequestController@storeKeepMoney')->name('general_request.store_keep_money');
    Route::post('general_request_daily_expense', 'GeneralRequestController@storeDailyExpense')->name('general_request.store_daily_expense');
    Route::post('general_request_exchange_money', 'GeneralRequestController@storeExchangeMoney')->name('general_request.store_exchange_money');
    Route::get('general_request/create', 'GeneralRequestController@create')->name('general_request.create');
    Route::get('general_request/{id}/show', 'GeneralRequestController@show')->name('general_request.show');
    Route::get('general_request/{id}/edit', 'GeneralRequestController@edit')->name('general_request.edit');
    Route::post('general_request/{id}/update', 'GeneralRequestController@update')->name('general_request.update');
    Route::post('general_request_keep_money/{id}/update', 'GeneralRequestController@updateKeepMoney')->name('general_request.update_keep_money');
    Route::post('general_request_daily_expense/{id}/update', 'GeneralRequestController@updateDailyExpense')->name('general_request.update_daily_expense');
    Route::post('general_request_exchange_money/{id}/update', 'GeneralRequestController@updateExchangeMoney')->name('general_request.update_exchange_money');
    Route::post('general_request/{id}/delete', 'GeneralRequestController@destroy')->name('general_request.destroy');
    Route::post('general_request/{id}/approve', 'GeneralRequestController@approve')->name('general_request.approve');
    Route::post('general_request/{id}/reject', 'GeneralRequestController@reject')->name('general_request.reject');
    Route::post('general_request/{id}/disable', 'GeneralRequestController@disable')->name('general_request.disable');
    Route::get('get-general-request', 'GeneralRequestController@getRequestItem')->name('get-general-request');
    Route::get('get-edit-general-request', 'GeneralRequestController@getEditRequestItem')->name('get-edit-general-request');


    /**
     * The route of request_create_user request
     * mission
     */
    Route::post('request_create_user', 'RequestCreateUserController@store')->name('request_create_user.store');
    Route::get('request_create_user/create', 'RequestCreateUserController@create')->name('request_create_user.create');
    Route::get('request_create_user/{id}/show', 'RequestCreateUserController@show')->name('request_create_user.show');
    Route::get('request_create_user/{id}/edit', 'RequestCreateUserController@edit')->name('request_create_user.edit');
    Route::post('request_create_user/{id}/update', 'RequestCreateUserController@update')->name('request_create_user.update');
    Route::post('request_create_user/{id}/delete', 'RequestCreateUserController@destroy')->name('request_create_user.destroy');
    Route::post('request_create_user/{id}/approve', 'RequestCreateUserController@approve')->name('request_create_user.approve');
    Route::post('request_create_user/{id}/reject', 'RequestCreateUserController@reject')->name('request_create_user.reject');


    /**
     * The route of association request
     * Sale Asset
     */
    Route::post('association', 'AssociationController@store')->name('association.store');
    Route::get('association/create', 'AssociationController@create')->name('association.create');
    Route::get('association/{id}/show', 'AssociationController@show')->name('association.show');
    Route::get('association/{id}/edit', 'AssociationController@edit')->name('association.edit');
    Route::post('association/{id}/update', 'AssociationController@update')->name('association.update');
    Route::post('association/{id}/delete', 'AssociationController@destroy')->name('association.destroy');
    Route::post('association/{id}/approve', 'AssociationController@approve')->name('association.approve');
    Route::post('association/{id}/reject', 'AssociationController@reject')->name('association.reject');
    Route::post('association/{id}/disable', 'AssociationController@disable')->name('association.disable');

    /**
     * The route of custom letter request
     * Sale Asset
     */
    Route::post('custom_letter', 'CustomLetterController@store')->name('custom_letter.store');
    Route::get('custom_letter/create', 'CustomLetterController@create')->name('custom_letter.create');
    Route::get('custom_letter/{id}/show', 'CustomLetterController@show')->name('custom_letter.show');
    Route::get('custom_letter/{id}/edit', 'CustomLetterController@edit')->name('custom_letter.edit');
    Route::post('custom_letter/{id}/update', 'CustomLetterController@update')->name('custom_letter.update');
    Route::post('custom_letter/{id}/delete', 'CustomLetterController@destroy')->name('custom_letter.destroy');
    Route::post('custom_letter/{id}/approve', 'CustomLetterController@approve')->name('custom_letter.approve');
    Route::post('custom_letter/{id}/reject', 'CustomLetterController@reject')->name('custom_letter.reject');
    Route::post('custom_letter/{id}/disable', 'CustomLetterController@disable')->name('custom_letter.disable');

    /**
     * The route of request_disable_user request
     * mission
     */
    Route::post('request_disable_user', 'RequestDisableUserController@store')->name('request_disable_user.store');
    Route::get('request_disable_user/create', 'RequestDisableUserController@create')->name('request_disable_user.create');
    Route::get('request_disable_user/{id}/show', 'RequestDisableUserController@show')->name('request_disable_user.show');
    Route::get('request_disable_user/{id}/edit', 'RequestDisableUserController@edit')->name('request_disable_user.edit');
    Route::post('request_disable_user/{id}/update', 'RequestDisableUserController@update')->name('request_disable_user.update');
    Route::post('request_disable_user/{id}/delete', 'RequestDisableUserController@destroy')->name('request_disable_user.destroy');
    Route::post('request_disable_user/{id}/approve', 'RequestDisableUserController@approve')->name('request_disable_user.approve');
    Route::post('request_disable_user/{id}/reject', 'RequestDisableUserController@reject')->name('request_disable_user.reject');


    /**
     * The route of policy request
     * Policy
     */
    Route::post('policy', 'PolicyController@store')->name('policy.store');
    Route::get('policy/create', 'PolicyController@create')->name('policy.create');
    Route::get('policy/{id}/show', 'PolicyController@show')->name('policy.show');
    Route::get('policy/{id}/edit', 'PolicyController@edit')->name('policy.edit');
    Route::post('policy/{id}/update', 'PolicyController@update')->name('policy.update');
    Route::post('policy/{id}/delete', 'PolicyController@destroy')->name('policy.destroy');
    Route::post('policy/{id}/approve', 'PolicyController@approve')->name('policy.approve');
    Route::post('policy/{id}/reject', 'PolicyController@reject')->name('policy.reject');
    Route::get('public_policy', 'PolicyController@publicPolicy')->name('public_policy');

    /**
     * The route of lesson request
     * Lesson
     */
    Route::post('lesson/store', 'LessonController@store')->name('lesson.store');
    Route::get('lesson/create', 'LessonController@create')->name('lesson.create');
    Route::get('lesson/{id}/show', 'LessonController@show')->name('lesson.show');
    Route::get('lesson/{id}/edit', 'LessonController@edit')->name('lesson.edit');
    Route::post('lesson/{id}/update', 'LessonController@update')->name('lesson.update');
    Route::post('lesson/{id}/delete', 'LessonController@destroy')->name('lesson.destroy');
    Route::get('public_lesson', 'LessonController@publicLesson')->name('public_lesson');

    /**
     * The route of borrowing loan
     * Borrowing
     */
    Route::post('borrowing_loan', 'BorrowingLoanController@store')->name('borrowing_loan.store');
    Route::get('borrowing_loan/create', 'BorrowingLoanController@create')->name('borrowing_loan.create');
    Route::get('borrowing_loan/{id}/show', 'BorrowingLoanController@show')->name('borrowing_loan.show');
    Route::get('borrowing_loan/{id}/edit', 'BorrowingLoanController@edit')->name('borrowing_loan.edit');
    Route::post('borrowing_loan/{id}/update', 'BorrowingLoanController@update')->name('borrowing_loan.update');
    Route::post('borrowing_loan/{id}/delete', 'BorrowingLoanController@destroy')->name('borrowing_loan.destroy');
    Route::post('borrowing_loan/{id}/approve', 'BorrowingLoanController@approve')->name('borrowing_loan.approve');
    Route::post('borrowing_loan/{id}/reject', 'BorrowingLoanController@reject')->name('borrowing_loan.reject');
    Route::post('borrowing_loan/{id}/disable', 'BorrowingLoanController@disable')->name('borrowing_loan.disable');
    Route::post('borrowing_loan/view_reference', 'BorrowingLoanController@viewReference')->name('borrowing_loan.view.reference');


    /**
     * The route of request gasoline
     * request gasoline
     */
    Route::post('request_gasoline', 'RequestGasolineController@store')->name('request_gasoline.store');
    Route::get('request_gasoline/create', 'RequestGasolineController@create')->name('request_gasoline.create');
    Route::get('request_gasoline/{id}/show', 'RequestGasolineController@show')->name('request_gasoline.show');
    Route::get('request_gasoline/{id}/edit', 'RequestGasolineController@edit')->name('request_gasoline.edit');
    Route::post('request_gasoline/{id}/update', 'RequestGasolineController@update')->name('request_gasoline.update');
    Route::post('request_gasoline/{id}/delete', 'RequestGasolineController@destroy')->name('request_gasoline.destroy');
    Route::post('request_gasoline/{id}/approve', 'RequestGasolineController@approve')->name('request_gasoline.approve');
    Route::post('request_gasoline/{id}/reject', 'RequestGasolineController@reject')->name('request_gasoline.reject');
    Route::post('request_gasoline/{id}/disable', 'RequestGasolineController@disable')->name('request_gasoline.disable');


    /**
     * The route of branch request
     * Branch
     */
    Route::get('branch', 'BranchController@index')->name('branch.index');
    Route::get('branch/create', 'BranchController@create')->name('branch.create');
    Route::post('branch/store', 'BranchController@store')->name('branch.store');
    Route::get('branch/edit/{id}', 'BranchController@edit')->name('branch.edit');
    Route::post('branch/update/{id}', 'BranchController@update')->name('branch.update');
    Route::get('branch/destroy/{id}', 'BranchController@destroy')->name('branch.destroy');


    /**
     * The route of position
     * Position
     */
    Route::get('position', 'PositionController@index')->name('position.index');
    Route::get('position/create', 'PositionController@create')->name('position.create');
    Route::post('position/store', 'PositionController@store')->name('position.store');
    Route::get('position/edit/{id}', 'PositionController@edit')->name('position.edit');
    Route::post('position/update/{id}', 'PositionController@update')->name('position.update');
    Route::get('position/destroy/{id}', 'PositionController@destroy')->name('position.destroy');


    /**
     * The route of setting memo
     * Setting
     */
    Route::get('setting_memo', 'SettingMemoController@index')->name('setting_memo.index');
    Route::get('setting_memo/create', 'SettingMemoController@create')->name('setting_memo.create');
    Route::post('setting_memo/store', 'SettingMemoController@store')->name('setting_memo.store');
    Route::get('setting_memo/edit/{id}', 'SettingMemoController@edit')->name('setting_memo.edit');
    Route::post('setting_memo/update/{id}', 'SettingMemoController@update')->name('setting_memo.update');
    Route::get('setting_memo/destroy/{id}', 'SettingMemoController@destroy')->name('setting_memo.destroy');


    /**
     * The route of setting benefit_ot
     * Setting
     */
    Route::get('benefit_ot', 'SettingBenefitController@index')->name('benefit_ot.index');
    Route::get('benefit_ot/create', 'SettingBenefitController@create')->name('benefit_ot.create');
    Route::post('benefit_ot/store', 'SettingBenefitController@store')->name('benefit_ot.store');
    Route::get('benefit_ot/edit/{id}', 'SettingBenefitController@edit')->name('benefit_ot.edit');
    Route::post('benefit_ot/update/{id}', 'SettingBenefitController@update')->name('benefit_ot.update');
    Route::get('benefit_ot/destroy/{id}', 'SettingBenefitController@destroy')->name('benefit_ot.destroy');

    /**
     * The route of setting memo
     * Setting
     */
    Route::get('setting_group_support', 'SettingGroupSupportController@index')->name('setting_group_support.index');
    Route::get('setting_group_support/edit/{id}', 'SettingGroupSupportController@edit')->name('setting_group_support.edit');
    Route::post('setting_group_support/update/{id}', 'SettingGroupSupportController@update')->name('setting_group_support.update');

    /**
     * The route of auto approve
     * Auto
     */
    Route::get('approve_report', 'AutoApproveController@approveReport')->name('approve_report.approve_report');
    Route::post('approve_report/store', 'AutoApproveController@storeApproveReport')->name('approve_report.store');
    Route::get('approve_request', 'AutoApproveController@approveRequest')->name('approve_report.approve_request');
    Route::post('approve_request/store', 'AutoApproveController@storeApproveRequest')->name('approve_request.store');


    // manage setting reviwer and approver
    Route::get('setting-reviewer-approver', 'SettingController@index')->name('setting-reviewer-approver.index');
    Route::get('setting-reviewer-approver/create', 'SettingController@create')->name('setting-reviewer-approver.create');
    Route::post('setting-reviewer-approver/store', 'SettingController@store')->name('setting-reviewer-approver.store');
    Route::get('setting-reviewer-approver/{id}/edit', 'SettingController@edit')->name('setting-reviewer-approver.edit');
    Route::post('setting-reviewer-approver/{id}/update', 'SettingController@update')->name('setting-reviewer-approver.update');
    Route::post('setting-reviewer-approver/{id}/delete', 'SettingController@destroy')->name('setting-reviewer-approver.destroy');
    Route::get('setting-reviewer-approver/{id}/show', 'SettingController@show')->name('setting-reviewer-approver.show');
    Route::post('setting-reviewer-approver/{id}/approve', 'SettingController@approve')->name('setting-reviewer-approver.approve');
    Route::post('setting-reviewer-approver/{id}/reject', 'SettingController@reject')->name('setting-reviewer-approver.reject');
    Route::get('setting-reviewer-approver/find', 'SettingController@find')->name('setting-reviewer-approver.find');

     /**
     * The route of setting approver report
     * Setting
     */
    Route::get('setting-approver-report', 'SettingApproverReportController@index')->name('setting-approver-report.index');
    Route::post('setting-approver-report/update', 'SettingApproverReportController@update')->name('setting-approver-report.update');


    Route::get('department', 'DepartmentController@index')->name('department.index');
    //Route::get('department/create', 'DepartmentController@create')->name('department.create');
    Route::post('department/store', 'DepartmentController@store')->name('department.store');


    Route::get('company', 'CompanyController@index')->name('company.index');
    //Route::get('company/create', 'CompanyController@create')->name('company.create');
    Route::post('company/store', 'CompanyController@store')->name('company.store');

    Route::get('reviewer', 'ReviewerController@index')->name('reviewer.index');
    Route::get('reviewer/create', 'ReviewerController@create')->name('reviewer.create');
    Route::post('reviewer/store', 'ReviewerController@store')->name('reviewer.store');
    Route::get('reviewer/edit/{id}', 'ReviewerController@edit')->name('reviewer.edit');
    Route::post('reviewer/update/{id}', 'ReviewerController@update')->name('reviewer.update');
    Route::get('reviewer/destroy/{id}', 'ReviewerController@destroy')->name('reviewer.destroy');


    Route::resource('user', 'UserController', ['except' => ['show']]);
    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
    Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
    Route::get('user/edit_password', 'UserController@passEdit')->name('password.edit');
    Route::post('user/update_password', 'UserController@passUpdate')->name('password.update');
    Route::get('user/destroy/{id}', 'UserController@user_destroy')->name('user_destroy');
    Route::get('user/export', 'SummaryReportController@userExport')->name('summary.userExport');


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Pending List
     */
    Route::group(['prefix'=>'stsk'], function(){
        Route::get('', 'STSKRequest\\STSKRequestController@index')->name('stsk.index');
        Route::post('', 'STSKRequest\\STSKRequestController@store')->name('stsk.store');
        Route::get('create', 'STSKRequest\\STSKRequestController@create')->name('stsk.create');
        Route::get('{id}/show', 'STSKRequest\\STSKRequestController@show')->name('stsk.show');
        Route::get('{id}/edit', 'STSKRequest\\STSKRequestController@edit')->name('stsk.edit');
        Route::post('{id}/update', 'STSKRequest\\STSKRequestController@update')->name('stsk.update');
        Route::post('{id}/delete', 'STSKRequest\\STSKRequestController@destroy')->name('stsk.destroy');
        Route::post('{id}/approve', 'STSKRequest\\STSKRequestController@approve')->name('stsk.approve');
        Route::post('{id}/reject', 'STSKRequest\\STSKRequestController@reject')->name('stsk.reject');
    });
    // Contract Management
    Route::get('properties-owner', 'ContractMagement\\PropertiesOwnerController@index')->name('properties-owner');
    Route::post('properties-owner', 'ContractMagement\\PropertiesOwnerController@store')->name('properties-owner-store');
    Route::get('properties-owner/destroy/{id}', 'ContractMagement\\PropertiesOwnerController@destroy')->name('properties-owner.destroy');

    Route::get('properties', 'ContractMagement\\PropertiesController@index')->name('properties');
    Route::post('properties', 'ContractMagement\\PropertiesController@store')->name('properties-store');
    Route::get('properties/destroy/{id}', 'ContractMagement\\PropertiesController@destroy')->name('properties.destroy');

    Route::get('contract', 'ContractMagement\\ContractController@index')->name('contract');
    Route::post('contract', 'ContractMagement\\ContractController@store')->name('contract-store');
    Route::get('contract/destroy/{id}', 'ContractMagement\\ContractController@destroy')->name('contract.destroy');
    Route::post('contract/payment', 'ContractMagement\\ContractController@paymentContract')->name('contract-payment');
    Route::get('contract/payment/show', 'ContractMagement\\ContractController@showPayment')->name('contract.show');
    Route::get('contract/history/show', 'ContractMagement\\ContractController@showContractHistory')->name('contract.history.show');
    // tasks dateline tracking
    Route::get('task-dateline-tracking', 'ContractMagement\\TaskDatelineTrackingController@index')->name('task-dateline-tracking');
    Route::post('task-dateline-tracking-import', 'ContractMagement\\TaskDatelineTrackingController@import')->name('task-dateline-tracking-import');
    Route::post('task-dateline-tracking', 'ContractMagement\\TaskDatelineTrackingController@store')->name('task-dateline-tracking-store');
    Route::get('task-dateline-tracking/destroy/{id}', 'ContractMagement\\TaskDatelineTrackingController@destroy')->name('task-dateline-tracking-destroy');


});
Route::get('user/import', function (){
    return view('welcome');
});
Route::post('user/import', 'UserController@import')->name('user.import');
Route::get('user/notification', 'UserController@sendNotification')->name('user.send.notification');


// GroupRequest
Route::group(['middleware'=>'auth', 'prefix'=>'group_request'], function(){
    Route::get('', 'GroupRequestController@index');
    Route::get('department/create', 'DepartmentController@create')->name('re.department.create');
    Route::get('get-edit-template-form', 'GroupRequestController@getEditTemplateForm')->name('re.get-edit-template-form');
    Route::post('template/{id}/update', 'GroupRequestController@updateTemplate')->name('re.template.update');
    Route::post('template/{id}/delete', 'GroupRequestController@deleteTemplate')->name('re.template.delete');

    Route::get('create', 'GroupRequestController@createTemplate')->name('re.item.create');
    Route::get('item/{groupRequestId}/show', 'GroupRequestController@show')->name('re.item.show');
    Route::post('item/store', 'GroupRequestController@storeGroupRequestTemplate')->name('re.item.store');
    Route::post('item/{id}/delete', 'GroupRequestController@destroy')->name('re.item.destroy');

    Route::post('item-upload', 'GroupRequestController@upload')->name('re.upload');
    Route::post('item-approve/{id}', 'GroupRequestController@approve')->name('re.item-approve');
    Route::post('item-reject/{id}', 'GroupRequestController@reject')->name('re.item-reject');
    Route::post('group-request-store', 'GroupRequestController@storeGroupRequest')->name('re.group-request-store');
    Route::post('group-request-update', 'GroupRequestController@updateGroupRequest')->name('re.group-request-update');


    Route::get('{company_short_name}/index', 'GroupRequestController@index')->name('re.index');

    Route::get('get-company-by-department', 'GroupRequestController@getDepartmentByCompany')->name('re.get-company-by-department');
    Route::get('get-approver-by-company', 'GroupRequestController@getApproverByCompany')->name('re.get-approver-by-company');

    Route::get('get-request-form', 'GroupRequestController@getNewRequestForm')->name('re.get-request-form');
    Route::get('get-edit-request-form', 'GroupRequestController@getEditRequestForm')->name('re.get-edit-request-form');
});

Route::get('user-guide', 'UserController@userGuide')->name('user.guide');
Route::get('get-number-notification', 'NotificationController@index')->name('number.notification');
Route::get('get-number-request-type', 'NotificationController@requestType')->name('number.request.type');
Route::get('get-number-department-type', 'NotificationController@requestDepartment')->name('number.department.type');
Route::get('get-number-group', 'NotificationController@requestGroup')->name('number.groupe');
Route::get('get-number-group-support', 'NotificationController@groupSupport')->name('number.groupe.support');
//Get AML
Route::get('xml', 'XmlController@index')->name('xml.index');
Route::post('get-xml', 'XmlController@update')->name('xml.update');

