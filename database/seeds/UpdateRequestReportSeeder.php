<?php

use App\Model\GroupRequest;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use setasign\Fpdi\PdfReader\PageBoundaries;
use \setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\File;
use App\REDepartment;
use Carbon\Carbon;

class UpdateRequestReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $data = GroupRequest::all();
        // foreach ($data as $item) {
        //     $reviewerStatus = DB::table('g_reviewers')
        //         ->where('g_reviewers.request_id', $item->id)
        //         ->whereIn('g_reviewers.status', [config('app.pending'), config('app.rejected')])
        //         ->count();

        //     if (!$reviewerStatus) {
        //         DB::table('g_requests')
        //             ->where('g_requests.id', $item->id)
        //             ->update([
        //                 'review_status' => 1,
        //             ])
        //         ;
        //     }
        // }

        // set to approved for president in report < 2020-02-25
        $data = GroupRequest::where('review_status', 1)->where('company_id', 4)->where('status', config('app.pending'))->where('end_date', '<', '2021-07-05 00:00:00')->whereNull('deleted_at')->get();

        //$user = User::find(getCEO()->id); 
        $user = User::find(28); // kuonlinkim

        foreach ($data as $item) {

            $approverRecord = DB::table('g_approvers')
                    ->where('g_approvers.request_id', $item->id)
                    ->where('g_approvers.approver_id', $user->id)
                    ->update([
                       'status' => config('app.approved'),
                       'approved_at' => Carbon::now(),
                    ]);

            if ($approverRecord) {

                ini_set("memory_limit", -1);

                $item->status = config('app.approved');
                $item->approvable = null;
                $item->rejectable = null;
                $item->editable = null;
                $item->deletable = null;

                $shortSignPath = str_replace('storage/', 'app/', $user->short_signature);
                $shortSignPath = storage_path($shortSignPath);
                $signPath = str_replace('storage/', 'app/', $user->signature);
                $signPath = storage_path($signPath);
                $attach = $item->attachments;
                $pdfPath = public_path($attach[0]['src']);

                // check have signature and part file in new 
                $partString = $attach[0]['src'];
                $partCheck = 'new/attachment';
                if (strpos($partString, $partCheck) !== false) {
                    // dd($partCheck);
                }
                else {
                    $newPdfPath = public_path('new/'.$attach[0]['src']);

                    shell_exec( "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$newPdfPath $pdfPath");

                    $pdf = new FPDI();
                    $pageCount = $pdf->setSourceFile($newPdfPath);
                    $i = 1;
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $templateId = $pdf->importPage($pageNo);
                        $pdf->AddPage();
                        $pdf->useTemplate($templateId, ['adjustPageSize' => true]);
                        $pdf->SetFont('Helvetica');
                        $pdf->SetTopMargin(1);

                        if ($i == $pageCount) {
                            $x = $pdf->GetPageWidth()-40;
                            $y = $pdf->GetPageHeight()-20;
                            $pdf->Image($signPath, $x, $y, 30);
                        } else {
                            $x = $pdf->GetPageWidth()-20;
                            $y = $pdf->GetPageHeight()-15;
                            $pdf->Image($shortSignPath, $x, $y, 10);
                        }
                        $i++;
                    }
                
                    $oldFile = @$attach[0]['src'];

                    $pdf->Output('F', $newPdfPath);

                    $attach[0]['src'] = 'new/'.$attach[0]['src'];
                    $item->attachments = $attach;

                    $item->save();

                    // delete old file
                    File::delete(@$oldFile);
                }

            }

        }


        // // set to back approved for president in report > 2020-12-14 00:00:00 && < 2020-12-15 00:00:00
        // $data = GroupRequest::where('review_status', 1)
        //                     ->where('company_id', 2)
        //                     ->where('status', config('app.approved'))
        //                     ->whereBetween('end_date',['2020-12-14 00:00:00', '2020-12-15 00:00:00'])
        //                     ->whereNull('deleted_at')
        //                     ->get();

        // $user = User::find(getCEO()->id); 

        // foreach ($data as $item) {

        //     $approverRecord = DB::table('g_approvers')
        //             ->where('g_approvers.request_id', $item->id)
        //             ->where('g_approvers.approver_id', $user->id)
        //             ->update([
        //                'status' => config('app.pending'),
        //                'approved_at' => null,
        //             ]);

        //     if ($approverRecord) {

        //         $item->status = config('app.pending');
        //         $item->approvable = [getCEO()->id];
        //         $item->rejectable = [getCEO()->id];
        //         $item->editable = null;
        //         $item->deletable = null;

        //         $item->save();

        //     }
        // }

    }
}
