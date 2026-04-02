<?php

namespace App\Http\Controllers;

use App\Approve;
use App\User;
use App\Company;
use App\Model\Setting;
use App\Model\GroupRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use setasign\Fpdi\PdfReader\PageBoundaries;
use \setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\REDepartment;
use Carbon\Carbon;
use Redirect;
class AutoApproveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function approveReport()
    {
        $company = Company::select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();
        $reportSetting = Setting::where('name', config('app.approver_setting_report'))->first();
        $staff = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.user_status', config('app.user_active'))
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                )->get();

        return view('auto_approve.report', compact('company', 'staff'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeApproveReport(Request $request)
    {
        $start_date = strtotime($request->start_date);
        $start_date = Carbon::createFromTimestamp($start_date)->format('Y-m-d H:i:s');

        $end_date = strtotime($request->end_date);
        $end_date = Carbon::createFromTimestamp($end_date)->format('Y-m-d H:i:s');

        $data = GroupRequest::where('status', config('app.pending'));
        if (@$request->company_id != 'all') {
            $data = $data->where('company_id', @$request->company_id);
        }
        $data = $data->whereBetween('end_date', [@$start_date, @$end_date])
                ->whereNull('deleted_at')
                ->get();

        $user = User::find(@$request->user_id);

        if (@$data && @$user) {
            foreach ($data as $item) {
                $approver = DB::table('g_requests')
                    ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                    ->where('g_requests.id', @$item->id)
                    ->where('g_approvers.approver_id', @$user->id)
                    ->where('g_requests.review_status', 1)
                    ->where('g_approvers.status', config('app.pending'))
                    ->select([
                        'g_approvers.approver_id'
                    ])
                    ->first();
                // check user is approver
                if (@$user->id == @$approver->approver_id) {
                    $approverRecord = DB::table('g_approvers')
                        ->where('g_approvers.request_id', @$item->id)
                        ->where('g_approvers.approver_id', @$user->id)
                        ->update([
                           'status' => config('app.approved'),
                           'approved_at' => Carbon::now(),
                        ]);
                    // set approve to approver table
                    if (@$approverRecord) {

                        ini_set("memory_limit", -1);

                        $item->status = config('app.approved');
                        $item->approvable = null;
                        $item->rejectable = null;
                        $item->editable = null;
                        $item->deletable = null;

                        // $shortSignPath = str_replace('storage/', 'app/', $user->short_signature);
                        // $shortSignPath = storage_path($shortSignPath);
                        // $signPath = str_replace('storage/', 'app/', $user->signature);
                        // $signPath = storage_path($signPath);
                        // $attach = $item->attachments;
                        // $pdfPath = public_path($attach[0]['src']);

                        // // check have signature and part file in new 
                        // $partString = $attach[0]['src'];
                        // $partCheck = 'new/attachment';
                        // $extension = @File::extension($partString);

                        // close goshscript
                        // if (strpos($partString, $partCheck) !== false || $extension != 'pdf') {
                        //     // dd($partCheck);
                        // }
                        // if (1 == 2) {
                        //     $newPdfPath = public_path('new/'.$attach[0]['src']);

                        //     shell_exec( "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$newPdfPath $pdfPath");

                        //     $pdf = new FPDI();
                        //     $pageCount = $pdf->setSourceFile($newPdfPath);
                        //     $i = 1;
                        //     for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        //         $templateId = $pdf->importPage($pageNo);
                        //         $pdf->AddPage();
                        //         $pdf->useTemplate($templateId, ['adjustPageSize' => true]);
                        //         $pdf->SetFont('Helvetica');
                        //         $pdf->SetTopMargin(1);

                        //         if ($i == $pageCount) {
                        //             $x = $pdf->GetPageWidth()-40;
                        //             $y = $pdf->GetPageHeight()-20;
                        //             $pdf->Image($signPath, $x, $y, 30);
                        //         } else {
                        //             $x = $pdf->GetPageWidth()-20;
                        //             $y = $pdf->GetPageHeight()-15;
                        //             $pdf->Image($shortSignPath, $x, $y, 10);
                        //         }
                        //         $i++;
                        //     }
                        
                        //     $oldFile = @$attach[0]['src'];

                        //     $pdf->Output('F', $newPdfPath);

                        //     $attach[0]['src'] = 'new/'.$attach[0]['src'];
                        //     $item->attachments = $attach;

                        //     $item->save();

                        //     // delete old file
                        //     File::delete(@$oldFile);
                        // }
                        $item->save();
                    }
                }
                else {
                    $reviewer = DB::table('g_reviewers')
                        ->where('g_reviewers.request_id', @$item->id)
                        ->where('g_reviewers.reviewer_id', @$user->id)
                        ->where('g_reviewers.status', config('app.pending'))
                        ->select([
                            'g_reviewers.reviewer_id',
                        ])
                        ->first();
                    // check is revierwe and first approve
                    if(@$user->id == @$reviewer->reviewer_id && @$user->id == @$item->approvable[0]) {
                        $reviewerRecord = DB::table('g_reviewers')
                            ->where('g_reviewers.request_id', @$item->id)
                            ->where('g_reviewers.reviewer_id', @$user->id)
                            ->update([
                                'status' => config('app.approved'),
                                'approved_at' => Carbon::now(),
                            ]);
                        // remove reviewer on array field
                        if ($reviewerRecord) {
                            @$item->approvable = @remove_matching_value_in_array(@$user->id, @$item->approvable);
                            @$item->rejectable = @remove_matching_value_in_array(@$user->id, @$item->rejectable);
     
                            // review status
                            $reviewerStatus = DB::table('g_reviewers')
                                ->where('g_reviewers.request_id', @$item->id)
                                ->whereIn('g_reviewers.status', [config('app.pending'), config('app.rejected')])
                                ->count();
                            // add is reviwe 1
                            if (!$reviewerStatus) {
                                $item->review_status = 1;
                            }
                            $item->save();
                        }
                    }
                }

            }

            return back()->with(['status' => 1]);
            
        }

        return back()->with(['status' => 4]);
    } 

    public function approveRequest()
    {
        $company = Company::select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();
        $request_type = config('app.request_types');
        $staff = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.user_status', config('app.user_active'))
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                )->get();

        return view('auto_approve.request', compact('company', 'request_type', 'staff'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeApproveRequest(Request $request)
    {
        $start_date = strtotime($request->start_date);
        $start_date = Carbon::createFromTimestamp($start_date)->format('Y-m-d H:i:s');

        $end_date = strtotime($request->end_date);
        $end_date = Carbon::createFromTimestamp($end_date)->format('Y-m-d H:i:s');

        $company = @$request->company_id;
        $type = @$request->request_type;
        $approver = @$request->user_id;
        $table = 'request_ot';
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');
        $ap = config('app.approve_status_approve');

        // all request to reviewer and approver
        $data = DB::table($table)
            ->leftJoin('approve', "$table.id", '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', "$table.user_id")
            ->select(
                "$table.id",
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->where("$table.company_id", '=', @$company)
            ->where('approve.type', '=', @$type)
            ->whereIn('approve.status', [@$pending, @$ap])
            ->where("$table.status", '!=', @$reject)
            ->whereBetween("$table.start_date", [@$start_date, @$end_date])
            ->whereNull('deleted_at')
            ->groupBy("$table.id")
            ->orderBy('id','ASC');

            //check order reviewer and approver
            if (config('app.is_order_approver') == 1) {
                $data = $data->get();
                $data1 = $data;

                $data = [];
                foreach ($data1 as $key => $value) {
                   if($value->reviewer_id == @$approver){
                        $data = array_merge($data, [$value]) ;
                   }
                }
                $data = collect($data);
            }
            else{
                $data = $data->where('approve.reviewer_id', '=', @$approver)->get();
            }
        $i = 0;
        $j = 0;
        $l = 0;
        if (@$data) {
            foreach ($data as $item) {
                $id = @$item->id;

                // select first approver and reviewer
                $can_approve = Approve::where('request_id', @$id)
                    ->where('type', @$type)
                    ->where('status', @$pending)
                    ->orderBy('id', 'ASC')
                    ->first();
                
                // check have $can_approve
                if (@$can_approve) {
                    // check can approve 
                    if (@$approver == @$can_approve->reviewer_id) {
                        $i++;
                        // set approved to atble approve 
                        $approve = Approve::where('id', @$item->approve_id)
                            ->where('type', @$type)
                            ->first();
                        $approve->status = @$ap;
                        $approve->approved_at = Carbon::now();
                        $approve->save();

                        // select last approver and reviewer (approver) 
                        $last_approve = Approve::where('request_id', @$id)
                            ->where('type', @$type)
                            ->where('status', @$ap)
                            ->where('position', 'approver')
                            ->orderBy('id', 'DESC')
                            ->first();
                        if (@$approver == @$last_approve->reviewer_id) {
                            // set approved to request
                            $codeGenerate = generateCode(@$table, $company, @$id, 'OT');
                            $the_request = DB::table($table)
                                ->where('id', @$id)
                                ->update([
                                    'status' =>  @$ap,
                                    'code_increase' => $codeGenerate['increase'],
                                    'code' => $codeGenerate['newCode']
                                ]);
                        }
                    }
                    else {
                        $l++;
                    } 
                } 

                // 'else' request is approved
                else {
                    $j++;
                    // select last approver and reviewer (approver) 
                    $check_approved = Approve::where('request_id', @$id)
                        ->join("$table", "$table.id", '=', 'approve.request_id')
                        ->select(
                            'approve.*'
                        )
                        ->where("$table.status", '=', @$pending)
                        ->where('approve.type', @$type)
                        ->where('approve.status', @$ap)
                        ->where('approve.position', 'approver')
                        ->orderBy('approve.id', 'DESC')
                        ->first();
                    if (@$approver == @$check_approved->reviewer_id) {
                        // set approved to request
                        $codeGenerate = generateCode(@$table, $company, @$id, 'OT');
                        $the_request = DB::table($table)
                            ->where('id', @$id)
                            ->update([
                                'status' =>  @$ap,
                                'code_increase' => $codeGenerate['increase'],
                                'code' => $codeGenerate['newCode']
                            ]);
                    }
                }
            }
            // dd($data, $i, $j, $l);
            return back()->with(['status' => 1]);
        }
        return back()->with(['status' => 4]);
    }

}
