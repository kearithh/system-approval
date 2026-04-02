<?php

use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

if (!function_exists('post_request')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function post_request(array $data)
    {
        try {

            $url = 'https://onesignal.com/api/v1/notifications';

            $postData = array_merge(
                $data,
                ['app_id' => '77f1823c-5dca-4f8f-b66f-aaa816b126ba']
            );
            $param['body'] = json_encode($postData);

            $http_client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $response = $http_client->post($url, $param);

            $responseBody = json_decode($response->getBody()->getContents());

            return $responseBody;

        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            return $e->getMessage();
        }
    }
}

if (!function_exists('prifixGender')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function prifixGender($data)
    {
        $gender = '';
        if($data == 'M')
        {
            $gender = 'ខ្ញុំបាទ';
        }
        elseif($data == 'F')
        {
            $gender = 'នាងខ្ញុំ';
        }
        else
        {
            $gender = 'ខ្ញុំបាទ/នាងខ្ញុំ';
        }
        echo $gender;
    }
}

if (!function_exists('prifixGenderStaff')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function prifixGenderStaff($data)
    {
        $gender = '';
        if($data == 'M')
        {
            $gender = 'លោក';
        }
        elseif($data == 'F')
        {
            $gender = 'លោកស្រី';
        }
        else
        {
            $gender = 'លោក/លោកស្រី';
        }
        echo $gender;
    }
}

if (!function_exists('genderKhmer')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function genderKhmer($data)
    {
        $gender = '';
        if($data == 'M')
        {
            $gender = 'ប្រុស';
        }
        elseif($data == 'F')
        {
            $gender = 'ស្រី';
        }
        echo $gender;
    }
}

if (!function_exists('request_status')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function request_status($data)
    {
        $html = '';
        if ($data->status == config('app.approve_status_reject')) {
            $html .= '<button class="btn btn-xs bg-red" title="Request was commented" type="button">';
            $html .= 'Commented';
            $html .= '</button>';
        }
        elseif ($data->status == config('app.approve_status_draft')) {
            $html .= '<button class="btn btn-xs bg-orange" title="Request is pending" type="button">';
            $html .= 'Pending';
            $html .= '</button>';
        }
        elseif ($data->status == config('app.approve_status_disable')) {
            $html .= '<button class="btn btn-xs bg-secondary" title="Request was rejected" type="button">';
            $html .= 'Rejected';
            $html .= '</button>';
        }
        else {
            $html .= '<button class="btn btn-xs bg-success" title="The request was approved" type="button">';
            $html .= 'Approved';
            $html .= '</button>';
        }
        echo $html;
    }
}

if (!function_exists('mission_status')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function mission_status($status)
    {
        $html = '';
        if (@$status == config('app.approve_status_reject')) {
            $html .= '<button class="btn btn-xs bg-red" title="Request was commented" type="button">';
            $html .= 'Commented';
            $html .= '</button>';
        }
        elseif (@$status == config('app.approve_status_draft')) {
            $html .= '<button class="btn btn-xs bg-orange" title="Request is pending" type="button">';
            $html .= 'Pending';
            $html .= '</button>';
        }
        elseif (@$status == config('app.approve_status_disable')) {
            $html .= '<button class="btn btn-xs bg-dark" title="Request was rejected" type="button">';
            $html .= 'Rejected';
            $html .= '</button>';
        }
        else {
            $html .= '<button class="btn btn-xs bg-success" title="The request was approved" type="button">';
            $html .= 'Approved';
            $html .= '</button>';
        }
        echo $html;
    }
}

if (!function_exists('memo_status')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function memo_status($data)
    {
        $html = '';
        if ($data->status == 3) { // -1
            $html .= '<button class="btn btn-xs bg-red" title="Request was commented" type="button">';
            $html .= 'Commented';
            $html .= '</button>';
        }
        elseif ($data->status == 1) {
            $html .= '<button class="btn btn-xs bg-orange" title="Request is pending" type="button">';
            $html .= 'Pending';
            $html .= '</button>';
        }
        else {
            $html .= '<button class="btn btn-xs bg-success" title="The request was approved by CEO" type="button">';
            $html .= 'Approved';
            $html .= '</button>';
        }
        echo $html;
    }
}

if (!function_exists('can')) {
    function can($data)
    {
        $reviewer = $data->approvals()->where('approve_reviewer_id', Auth::id())->first();
        $reviewerStatus = isset($reviewer->status) ? $reviewer->approve_status : NULL;
        if(
            $data->user_id == Auth::id()
        || $data->status == config('app.approve_status_approve')
        || $data->status == config('app.approve_status_reject')
        || $reviewerStatus == config('app.approve_status_approve')
        )
        {
            return true;
        } else {
            return false;
        }
    }
}
if (!function_exists('can_action')) {
    function can_action($data, $type = null)
    {
        $pending = config('app.approve_status_draft');
        $rejected = config('app.approve_status_reject');
        $requester = (@$data->user_id == Auth::id() ?: @$data->created_by == Auth::id());

        if ($type == config('app.type_general_expense'))
        {
            $isReviewerApprove = \App\RequestHR::isReviewing($data->id);

        } elseif ($type == config('app.type_memo')) {
            $isReviewerApprove = \App\RequestMemo::isReviewing($data->id);

        } elseif ($type == config('app.type_special_expense')) {
            $isReviewerApprove = \App\RequestForm::isReviewing($data->id);
        } elseif ($type == config('app.type_pr_request')) {
            $isReviewerApprove = \App\RequestPR::isReviewing($data->id);
        } elseif ($type == config('app.type_po_request')) {
            $isReviewerApprove = \App\RequestPO::isReviewing($data->id);
        } elseif ($type == config('app.type_grn')) {
            $isReviewerApprove = \App\RequestGRN::isReviewing($data->id);

        } elseif ($type == config('app.type_disposal')) {
            $isReviewerApprove = \App\Disposal::isReviewing($data->id);

        } elseif ($type == config('app.type_damaged_log')) {
            $isReviewerApprove = \App\DamagedLog::isReviewing($data->id);

        }

        if ($requester) {
            // Able to action only, pending and reviewer not yet approved, rejected
            $isPending = ($data->status == $pending);
            $isRejected = ($data->status == $rejected);
            if (($isPending) || $isRejected ) {
                return true;
            }
        } elseif (in_array($type, (array)Auth::user()->edit_pending_request)) {
            $isPending = ($data->status == $pending);
            $isRejected = ($data->status == $rejected);
            if (($isPending) || $isRejected ) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('can_approve_reject')) {
    function can_approve_reject($data, $type)
    {
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $approver = (Auth::user()->position->level == config('app.position_level_president'));

        if ($type == config('app.type_general_expense'))
        {
//            dump($type);
            $isReviewed = \App\RequestHR::isReviewed($data->id);

        } elseif ($type == config('app.type_memo')) {
            //$approver = (Auth::id() == $data->approver()->id);

            $isReviewed = \App\RequestMemo::isReviewed($data->id);

        } elseif ($type == config('app.type_special_expense')) { 
            $isReviewed = \App\RequestForm::isReviewed($data->id);
        } elseif ($type == config('app.type_pr_request')) { 
            $isReviewed = \App\RequestPR::isReviewed($data->id);
        } elseif ($type == config('app.type_po_request')) { 
            $isReviewed = \App\RequestPO::isReviewed($data->id);
        } elseif ($type == config('app.type_grn')) { 
            $isReviewed = \App\RequestGRN::isReviewed($data->id);

        } elseif ($type == config('app.type_disposal')) {
            $isReviewed = \App\Disposal::isReviewed($data->id);

        } elseif ($type == config('app.type_damaged_log')) {
            $isReviewed = \App\DamagedLog::isReviewed($data->id);

        } elseif ($type == config('app.type_hr_request')) {
            $isReviewed = \App\HRRequest::isReviewed($data->id);

        } elseif ($type == config('app.report')) {
            $isReviewed = $data->isReviewed();
        }
//        dd($isReviewed);
        if ($approver) {
            // Able to action only, pending and reviewer not yet approved, rejected
            if ($data->status == config('app.approve_status_draft')) {
//                dd(3);
                return true;
            }
        }

        // close
        // check approve
        // $reviewer = $data->approvals()->where('approve_reviewer_id', Auth::id())->first();

        // check review first and can approve for request pennding
        $reviewer = $data->approvals()
                    ->where('status', config('app.approve_status_draft'))
                    ->where('approve_status', config('app.approve_status_draft'))
                    ->first();
        //dd($reviewer);
        if ($reviewer) {

            //close
            // $isPendingOnAuth = ($reviewer->approve_status == config('app.approve_status_draft'));
            // if ($isPendingOnAuth) {
            //     return true;
            // }

            $isPendingOnAuth = ($reviewer->approve_reviewer_id == Auth::id());
            if ($isPendingOnAuth) {
                return true;
            }

        }
        return false;
    }
}

if (!function_exists('can_reject')) {
    function can_reject($data, $type)
    {
        // check review first and can reject for request
        $reviewer = $data->approvals()
                    ->whereIn('status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->where('approve_status', config('app.approve_status_draft'))
                    ->first();

        if ($reviewer) {

            $isPendingOnAuth = ($reviewer->approve_reviewer_id == Auth::id());
            if ($isPendingOnAuth) {
                return true;
            }

        }
        return false;
    }
}

if (!function_exists('can_approve_or_reject')) {
    function can_approve_or_reject($data, $type = null)
    {
        $isPendingOnAuth = 1;//$data->isPendingOnAuth();
        if ($isPendingOnAuth) {
                return true;
        }
        return false;
    }
}


if (!function_exists('can_disposal')) {
    function can_disposal($data)
    {
        $reviewer = $data->reviewers()->where('id', Auth::id())->first();
        $reviewerStatus = isset($reviewer->status) ? $reviewer->status : NULL;
        if(
            $data->user_id == Auth::id()
            || $data->status == config('app.approve_status_approve')
            || $data->status == config('app.approve_status_reject')
            || $reviewerStatus == config('app.approve_status_approve')
        )
        {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('is_penalty')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function is_penalty($data)
    {
        $html = '';
        if ($data == 1) { // -1
            $html .= '<button class="btn btn-xs bg-success" title="" type="button">';
            $html .= 'Yes';
            $html .= '</button>';
        }
        else {
            $html .= '<button class="btn btn-xs bg-red" title="" type="button">';
            $html .= 'No';
            $html .= '</button>';
        }
        echo $html;
    }
}

if (!function_exists('reviewer_position')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function reviewer_position($data)
    {
        $html = '';
        foreach ($data as $item) {
            if ($item->approve_status == config('app.approve_status_approve')) {
                $html .= '<button class="btn btn-xs btn-primary" style="margin-right: 2px; margin-bottom: 2px" title="Approved" type="button">';
                $html .= '<i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;';
                $html .= @$item->reviewer_name;
                $html .= '</button><br>';
            }
            else if ($item->approve_status == config('app.approve_status_reject')) {
                $html .= '<button class="btn btn-xs btn-primary" style="margin-right: 2px; margin-bottom: 2px" title="Commented" type="button">';
                $html .= '<i class="fa fa-times"></i>&nbsp;&nbsp;&nbsp;';
                $html .= @$item->reviewer_name;
                $html .= '</button><br>';
            }
            else if ($item->approve_status == config('app.approve_status_draft')) {
                $html .= '<button class="btn btn-xs btn-primary" style=" display: table-cell;margin-right: 2px; margin-bottom: 2px" title="Pending" type="button">';
                if (strpos(@$item->approve_position, 'short') !== false) {
                    $html .= '&nbsp;<i class="fa fa-exclamation"></i>';
                }
                $html .= '&nbsp;<i class="fa fa-exclamation"></i>&nbsp;&nbsp;&nbsp;&nbsp;';
                $html .= @$item->reviewer_name;
                $html .= '</button><br>';
            }
            else if ($item->approve_status == config('app.approve_status_disable')) {
                $html .= '<button class="btn btn-xs btn-primary" style="margin-right: 2px; margin-bottom: 2px" title="Rejected" type="button">';
                $html .= '<i class="fa fa-ban"></i>&nbsp;&nbsp;&nbsp;';
                $html .= @$item->reviewer_name;
                $html .= '</button><br>';
            }
        }
        echo $html;
    }
}

if (!function_exists('reviewers_list')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function reviewers_list($data)
    {
        $html = '';
        foreach ($data as $item) {
            if ($item->review_status == config('app.approved')) {
                $html .= '<button class="btn btn-xs btn-primary" style="margin-right: 2px; margin-bottom: 2px" title="Approved" type="button">';
                $html .= '<i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;';
                $html .= @$item->name;
                $html .= '</button><br>';
            }
            else if ($item->review_status == config('app.rejected')) {
                $html .= '<button class="btn btn-xs btn-primary" style="margin-right: 2px; margin-bottom: 2px" title="Commented" type="button">';
                $html .= '<i class="fa fa-times"></i>&nbsp;&nbsp;&nbsp;';
                $html .= @$item->name;
                $html .= '</button><br>';
            }
            else if ($item->review_status == config('app.pending')) {
                $html .= '<button class="btn btn-xs btn-primary" style=" display: table-cell;margin-right: 2px; margin-bottom: 2px" title="Pending" type="button">';
                if (strpos(@$item->name, 'short') !== false) {
                    $html .= '&nbsp;<i class="fa fa-exclamation"></i>';
                }
                $html .= '&nbsp;<i class="fa fa-exclamation"></i>&nbsp;&nbsp;&nbsp;&nbsp;';
                $html .= @$item->name;
                $html .= '</button><br>';
            }
            else {
                $html .= '<button class="btn btn-xs btn-info" style="margin-right: 2px; margin-bottom: 2px" title="Commented" type="button">';
                $html .= @$item->name;
                $html .= '</button><br>';
            }

        }
        echo $html;
    }
}

if (!function_exists('type')) {
    /**
     * @param $item
     */
    function type($item)
    {
        $html = '';
        if ($item == 'daily') {
            $html .= '<button class="btn btn-xs btn-warning" style="line-height: 1; padding: 1px; font-size: 11px;" title="{{ $item }}" type="button">';
            $html .= @$item;
            $html .= '</button><br>';
        }
        if ($item == 'weekly') {
            $html .= '<button class="btn btn-xs btn-warning" style="line-height: 1; padding: 1px; font-size: 11px;" title="{{ $item }}" type="button">';
            $html .= @$item;
            $html .= '</button><br>';
        }
        if ($item == 'monthly') {
            $html .= '<button class="btn btn-xs btn-warning" style="line-height: 1; padding: 1px; font-size: 11px;" title="{{ $item }}" type="button">';
            $html .= @$item;
            $html .= '</button><br>';
        }
        echo $html;
    }
}

if (!function_exists('approver_position')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function approver_position($data)
    {
        $html = '';
        foreach ($data as $item) {
            if ($item->approve_status == config('app.approve_status_approve')) {
                $html .= '<button class="btn btn-xs btn-info" style="margin-right: 2px; margin-bottom: 2px" title="Approved" type="button">';
                $html .= '<i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;';
                $html .= @$item->reviewer_name;
                $html .= '</button>';
            }
            else if ($item->approve_status == config('app.approve_status_reject')) {
                $html .= '<button class="btn btn-xs btn-info" style="margin-right: 2px; margin-bottom: 2px" title="Commented" type="button">';
                $html .= '<i class="fa fa-times"></i>&nbsp;&nbsp;&nbsp;';
                $html .= @$item->reviewer_name;
                $html .= '</button>';
            }
            else if ($item->approve_status == config('app.approve_status_draft')) {
                $html .= '<button class="btn btn-xs btn-info" style=" display: table-cell;margin-right: 2px; margin-bottom: 2px" title="Pending" type="button">';
                $html .= '&nbsp;<i class="fa fa-exclamation"></i>&nbsp;&nbsp;&nbsp;&nbsp;';
                $html .= @$item->reviewer_name;
                $html .= '</button>';
            }
            else if ($item->approve_status == config('app.approve_status_disable')) {
                $html .= '<button class="btn btn-xs btn-info" style="margin-right: 2px; margin-bottom: 2px" title="Rejected" type="button">';
                $html .= '&nbsp;<i class="fa fa-ban"></i>&nbsp;&nbsp;&nbsp;&nbsp;';
                $html .= @$item->reviewer_name;
                $html .= '</button>';
            }
        }
        echo $html;
    }
}


if (!function_exists('items')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function items($data)
    {
        $html = '<ul>';
        foreach ($data as $item) {
            $html .= '<li>'. @$item->name .'</li>';
        }
        $html .= '</ul>';
        echo $html;
    }
}


if (!function_exists('items_desc')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function items_desc($data)
    {
        $html = '<ul>';
        foreach ($data as $item) {
            $html .= '<li>'. @$item->name .
                    ' ('. @$item->qty . '*' . @$item->unit_price .
                    ' = '. @$item->qty * @$item->unit_price .
                    @$item->currency .
                    ') </li>';
        }
        $html .= '</ul>';
        echo $html;
    }
}


if (!function_exists('created_at')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function created_at($data)
    {
        $html = \Carbon\Carbon::createFromTimestamp(strtotime($data));
        $data = $html->format('d-m-Y').' | '.$html->format('h:i A');
        echo $data;
    }
}

if (!function_exists('start_date')) {
    /**
     * @param array $data
     * @return mixed|string
     */
    function start_date($data)
    {
        $html = \Carbon\Carbon::createFromTimestamp(strtotime($data));
        $data = $html->format('d-m-Y');
        echo $data;
    }
}

if (!function_exists('khmer_number')) {
    /**
     * @param mixed $data
     * @return mixed|string
     */
    function khmer_number($data)
    {
        if ($data) {
            $data = str_split($data);
            $kh_number = [
                '០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩',
            ];
            $value = '';
            foreach ($data as $item) {
                $value .= $kh_number[$item];
            }
            echo $value;
        }
        else {
            echo  '........';
        }
    }
}

if (!function_exists('khmer_month')) {
    /**
     * @param mixed $data
     * @return mixed|string
     */
    function khmer_month($int)
    {

        $month = [
            '',
            'មករា',
            'កុម្ភៈ',
            'មីនា',
            'មេសា',
            'ឧសភា',
            'មិថុនា',
            'កក្កដា',
            'សីហា',
            'កញ្ញា',
            'តុលា',
            'វិច្ឆិកា',
            'ធ្នូ'
        ];
        echo $month[(integer)$int];
    }
}


if (!function_exists('typePositionCEO')) {
    /**
     * @param mixed $data
     * @return mixed|string
     */
    function typePositionCEO($inttype)
    {

        $typeCEO = [
            'ប្រធាននាយកប្រតិបត្តិ',
            'ប្រធាននាយិកាប្រតិបត្តិ', //1=MFI
            'ប្រធាននាយិកាប្រតិបត្តិ', //2=NGO
            'ប្រធានក្រុមប្រឹក្សាភិបាល', //3=STSK &Clinic
            'ម្ចាស់ស្ថានីយ', //4=Tela
            'អគ្គនាយិការ' //5=ST
        ];
        echo $typeCEO[(integer)$inttype];
    }
}


if (!function_exists('getCEO')) {
    /**
     * @return mixed
     */
    function getCEO()
    {
        $ceo = User
            ::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('positions.level', '=', config('app.position_level_president'))
            ->where('users.user_status', config('app.user_active'))
            ->select([
                'users.*',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->first()
        ;
        return $ceo;
    }
}
if (!function_exists('getCEOAndPresident')) {
    /**
     * @return mixed
     */
    function getCEOAndPresident()
    {
        $data = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereIn('positions.level', [
                config('app.position_level_president'),
                config('app.position_level_ceo')
            ])
            ->select([
                'users.*',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get()
        ;
        return $data;
    }
}

if (!function_exists('upload')) {
    /**
     * @param UploadedFile $file
     * @param null $path
     * @return object
     */
    function upload(UploadedFile $file, $path = 'user')
    {
        $fullPath = storage_path().'/'.$path;
        if(!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0777, TRUE);
        }
        $defaultPath = 'storage/';
        $src = Storage::disk('local')->put($path, $file);
        return (object)[
            'src' => $defaultPath.$src,
            'original_name' => $file->getClientOriginalName()
        ];
    }
}

if (!function_exists('stringToDay')) {
    /**
     * @param $stringDate
     * @return string|null
     */
    function stringToDay($stringDate)
    {
        $day = \Carbon\Carbon::createFromTimestamp(strtotime($stringDate));
        if ($day) {
            return $day->format('d');
        }
        else {
            return null;
        }
    }
}

if (!function_exists('stringToMonth')) {
    /**
     * @param $stringDate
     * @return string|null
     */
    function stringToMonth($stringDate)
    {
        $day = \Carbon\Carbon::createFromTimestamp(strtotime($stringDate));
        if ($day) {
            return $day->format('m');
        }
        else {
            return null;
        }
    }
}

if (!function_exists('stringToYear')) {
    /**
     * @param $stringDate
     * @return string|null
     */
    function stringToYear($stringDate)
    {
        $day = \Carbon\Carbon::createFromTimestamp(strtotime($stringDate));
        if ($day) {
            return $day->format('Y');
        }
        else {
            return null;
        }
    }
}

if (!function_exists('defaultTabApproval')) {
    function defaultTabApproval($request) {
        if (Auth::user()->position->level == config('app.position_level_president')) {
            $request->type = $request->type ? $request->type : 3;
            $request->status = $request->status ? $request->status : 0;
        }
    }
}

/**
 * @param $complete_char
 * @param bool $enableThousand
 * @return string
 */
function num2khtext($complete_char, $enableThousand = false){
//function for split uft8 character
    function mb_str_split( $string ) {
//Split at all position not after the start: ^
//and not before the end: $
        return preg_split('/(?<!^)(?!$)/u', $string );
    }
//remove left zeros
    $cleanStr = ltrim($complete_char, '0');
//split number/string to array
    $num_arr = mb_str_split($cleanStr);
    $translated=''; $addThousand=false;
//string array
    $khNUMTxt = array('', 'មួយ', 'ពីរ', 'បី', 'បួន', 'ប្រាំ', 'ប្រាំមួយ', 'ប្រាំពីរ', 'ប្រាំបី', 'ប្រាំបួន');
    $twoLetter = array('', 'ដប់', 'ម្ភៃ', 'សាមសិប', 'សែសិប', 'ហាសិប', 'ហុកសិប', 'ចិតសិប', 'ប៉ែតសិប', 'កៅសិប');
    $khNUMLev = array('', '', '', 'រយ', 'ពាន់', 'ម៊ឺន', 'សែន', 'លាន');
    $khnum = array('០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩');
//loop to check each number character
    foreach($num_arr as $key=>$value){
//convert khmer number to latin number if found
        if(in_array($value,$khnum)){$value = array_search($value,$khnum);}
//allow only number
        if(!is_numeric($value)){return '';}
//check what pos the charactor in
        $pos = count($num_arr) - ($key);
        if($pos>count($khNUMLev)-1){$pos=($pos % count($khNUMLev))+2;}
//enable or diable read in thousand
        if($enableThousand and ($pos == 5 or $pos == 6)){$pos = $pos-3;}
//concatenate number as text
        if($pos==2){
            $translated .= $twoLetter[$value];
        }else{
            if($value>5){$translated .=  $khNUMTxt[5].$khNUMTxt[$value - 5];}else{$translated .= $khNUMTxt[$value];}
        }
//work for thousand
        if($pos==2 or $pos == 3 or $pos == 4){
            if($value>0){$addThousand=true;}
        }
//concatenate number level
        if($value>0 or ($pos==4 and $addThousand and $enableThousand) or $pos==7){
            $translated .= $khNUMLev[$pos];
        }
//make addthousand to default value (false)
        if($pos==4){$addThousand=false;}
    }
//return the complete number in text
    return $translated;
}

if (!function_exists('appendQueryString')) {
    function appendQueryString($key, $type) {
        $queryString = request()->getQueryString();
        parse_str($queryString, $param);
        @$param[$key] = $type;
        return http_build_query($param);
    }
}

if (!function_exists('string_to_time')) {
    function string_to_time($param) {
        if ($param) {
            return Carbon::createFromTimestamp(strtotime($param));
        }
        return null;
    }
}

if (!function_exists('store_file_as_json')) {
    function store_file_as_json($param, $dir = 'attachment/report') {
        if ($param) {
            $orgName = $param->getClientOriginalName();
            $src = Storage::disk('local')->put($dir, $param);
            $data  = [
                [
                    'org_name' => $orgName,
                    'src' => 'storage/'.$src,
                    'uploaded_at' => (string)\Carbon\Carbon::now()->format('d/m/Y')
                ],
            ];
            return $data;
        }
        return null;
    }
}

if (!function_exists('remove_matching_value_in_array')) {
    function remove_matching_value_in_array($value, $array) {
        if ($value) {

            // old
            // $pos = array_search($value, $array);
            // unset($array[$pos]);

            // checking int not string
            // foreach (array_keys($array, $value, true) as $key) {
            //     unset($array[$key]);
            // }

            foreach (array_keys($array, $value) as $key) {
                unset($array[$key]);
            }
            $array = array_values($array);
        }
        return (array)$array;
    }
}

function nextBtn($data = []) {
    $menu = @$_GET['menu'];
    $type = @$_GET['type'];
    $key = $menu.'_'.$type.'_next_pre';
    $request = request();

    $prefix = '';
    $currentId = $request->segment(2);
    $showSegment = $request->segment(3);
    if (@$_GET['type'] == 'report') {
        $prefix = '/item';
        $currentId = $request->segment(3);
        $showSegment = $request->segment(4);
    }

    $data = $data ? $data : session($key);
    $nextIndex = array_search((integer)$currentId, $data) + 1;
    $nextId = @$data[$nextIndex];

    if ($nextId) {
        $nextUrl = '/'.$request->segment(1).$prefix.'/'.$nextId.'/'.$showSegment.'?menu='.@$_GET['menu'].'&type='.@$_GET['type'];
        return $nextUrl;
    }
    return null;
}

/**
 * which menu list? which request type?
 *
 * @param null $data
 * @return string|null
 */
function preBtn($data = []) {
    $menu = @$_GET['menu'];
    $type = @$_GET['type'];
    $key = $menu.'_'.$type.'_next_pre';
    $request = request();

    $prefix = '';
    $currentId = $request->segment(2);
    $showSegment = $request->segment(3);
    if (@$_GET['type'] == 'report') {
        $prefix = '/item';
        $currentId = $request->segment(3);
        $showSegment = $request->segment(4);
    }

    $data = $data ? $data : session($key);
    $nextIndex = array_search((integer)$currentId, @$data) - 1;
    $nextId = @$data[$nextIndex];
    if ($nextId) {
        $nextUrl = '/'.$request->segment(1).$prefix.'/'.$nextId.'/'.$showSegment.'?menu='.@$_GET['menu'].'&type='.@$_GET['type'];
        return $nextUrl;
    }
    return null;
}


//funtion generate #1
function generateCode($table, $companyId, $requestId, $prefix) {

    $data = [];
    $last = DB::table($table)->where('company_id', $companyId)
        ->where('id', '!=', $requestId)
        ->whereNotNull('code')
        ->whereYear('updated_at', date('Y'))
        //->orderBy('id', 'desc')
        ->orderBy('code_increase', 'desc')
        ->first();
    if($last){
        //function helper generate new code
        $arr = converToArrayInsert($last->code_increase, $last->code);
        //return $arr['newCode'];
    }

    else{
        //function helper generate new code
        $company = DB::table('companies')->where('id', $companyId)->first();
        $code = $company->short_name_en.'-'.$prefix.'-0000-'.date('y');
        $arr = converToArrayInsert(0, $code);
    }

    $data = $arr;

    return $data;
}


//function helper generate new code #2
if (!function_exists('converToArrayInsert')) {
    /**
     * @return mixed
     */
    function converToArrayInsert($increase, $code)
    {
        $myArray = explode('-', $code);

        if($myArray[3] < date("y")){
            $code_increase = sprintf('%04d', 1);
            $increase = 1;
        }
        else{
            $code_increase = sprintf('%04d', $increase + 1);
            $increase = $increase + 1;
        }

        $newCode = $myArray[0].'-'.$myArray[1].'-'.$code_increase.'-'.date("y");

        return compact('increase','newCode');

    }
}


if (!function_exists('userObject')) {
    /**
     * @return mixed
     */
    function userObject($users_id) {

        $data = DB::table('users')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
            ->where('users.id', $users_id)
            ->select(
                'users.id',
                'users.name',
                'users.name_en',
                'users.gender',
                'users.email',
                'users.signature',
                'users.short_signature',
                'users.position_id',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'users.branch_id',
                'branches.name_km as branch_name',
                'branches.branch as is_branch',
                'users.department_id',
                'departments.name_km as department_name',
                'users.company_id',
                'companies.long_name as company_name',
                'companies.code as company_code'
            )->first();
        return $data;
    }
}

if (!function_exists('userPosition')) {
    /**
     * @return mixed
     */
    function userPosition($users_id) {
        $data = DB::table('users')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.id', $users_id)
            ->select(
                'users.id',
                'users.name',
                'users.position_id',
                'positions.name_km as position_name'
            )->first();
        return $data;
    }
}


if (!function_exists('getMailUser')) {
    /**
     * @return mixed
     */
    function getMailUser($id, $requester, $type, $handler = null) {

        $users = DB::table('users')
                    ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                    ->where('approve.request_id', $id)
                    ->where('approve.type', $type)
                    ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
                    ->whereNotNull('email')
                    ->select(
                        'users.email'
                    )
                    ->get();

        $emails = [];
        foreach ($users as $key => $value) {
            $emails[] = $value->email;
        }

        if ($handler == config('app.send_to_requester')) {
            $creater = DB::table('users')
                            ->where('users.id', $requester)
                            ->whereNotNull('email')
                            ->first();
            if ($creater) {
                array_push($emails, $creater->email);
            }
        }

        return $emails;
    }
}



//function helper show code
if (!function_exists('showArrayCode')) {
    /**
     * @return mixed
     */
    function showArrayCode($code)
    {
        $myArray = explode('-', $code);

        $viewCode = $myArray[1].'-'.$myArray[2].'-'.$myArray[3];

        echo $viewCode;

    }
}


class Form
{
    /**
     * @param array $data
     * @return array|string
     * @throws Throwable
     */
    public static function text(array $data)
    {
       return self::inputGenerator('text', $data);
    }

    /**
     * @param array $data
     * @return array|string
     * @throws Throwable
     */
    public static function checkbox(array $data)
    {
        $class = @$data['class'];
        $label = @$data['label'];
        $value = @$data['value'];
        unset($data['class']);
        unset($data['label']);
        return  view('form.checkbox', compact('data', 'class', 'label', 'value'))->render();
    }

    /**
     * @param array $data
     * @return array|string
     * @throws Throwable
     */
    public static function select(array $data)
    {
        $option = @$data['option'];
        $class = @$data['class'];
        $label = @$data['label'];
        $value = @$data['value'];
        $selected = @$data['selected'];
        unset($data['option']);
        unset($data['class']);
        unset($data['label']);
        return  view('form.select', compact('data', 'option', 'class', 'label', 'value', 'selected'))->render();
    }



    /**
     * @param array $data
     * @return array|string
     * @throws Throwable
     */
    public static function radio(array $data)
    {
        $class = @$data['class'];
        $label = @$data['label'];
        $value = @$data['value'];
        unset($data['class']);
        unset($data['label']);
        return  view('form.radio', compact('data', 'class', 'label', 'value'))->render();
    }

    /**
     * @param array $data
     * @return array|string
     * @throws Throwable
     */
    public static function hidden(array $data)
    {
        return self::inputGenerator('hidden', $data);
    }

    /**
     * @param array $data
     * @return array|string
     * @throws Throwable
     */
    public static function number(array $data)
    {
        return self::inputGenerator('number', $data);
    }

    /**
     * @param array $data
     * @return array|string
     * @throws Throwable
     */
    public static function textarea(array $data)
    {
        $class = @$data['class'];
        $label = @$data['label'];
        $value = @$data['value'];
        unset($data['class']);
        unset($data['label']);
        $data = (object)$data;
        return  view('form.textarea', compact('data', 'class', 'label', 'value'))->render();
    }

    /**
     * @param string $type
     * @param array $data
     * @return array|string
     * @throws Throwable
     */
    private static function inputGenerator(string $type, array $data)
    {
        $class = @$data['class'];
        $label = @$data['label'];
        unset($data['class']);
        unset($data['label']);
        $viewPath = 'form.'.$type;
        return  view($viewPath, compact('data', 'class', 'label'))->render();
    }
}

class CollectionHelper
{
    public static function paginate(Collection $results, $total, $pageSize, $option = null)
    {
        $page = Paginator::resolveCurrentPage('page');

        if ($option) {
            $option = array_merge($option, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
        } else {
            $option = [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ];
        }
        return self::paginator($results->forPage($page, $pageSize), $total, $pageSize, $page, $option);

    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected static function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }
}

if (!function_exists('store_file_as_jsons')) {
    function store_file_as_jsons($params, $dir = 'attachment/request') {
        if ($params) {
            $data = [];
            foreach ($params as $param) {

                $size = $param->getSize();
                //dd($size);
                if ($size <= 15500000) { // 15M
                    $orgName = $param->getClientOriginalName();
                    $src = Storage::disk('local')->put($dir, $param);
                    $data[]  =
                        [
                            'org_name' => $orgName,
                            'src' => 'storage/'.$src,
                            'uploaded_at' => (string)\Carbon\Carbon::now()->format('d/m/Y')
                        ];
                }
            }
            return $data;
        }
        return null;
    }
}

if (!function_exists('store_file_mapping_as_jsons')) {
    function store_file_mapping_as_jsons($params, $dir = 'request') {
        if ($params) {
            $data = [];
            foreach ($params as $param) {

                $size = $param->getSize();
                dd($size, Storage::disk('mnt'), base_path());
                if ($size <= 15500000) { // 15M
                    $orgName = $param->getClientOriginalName();
                    $src = Storage::disk('mnt')->put($dir, $param);
                    $data[]  =
                        [
                            'org_name' => $orgName,
                            'src' => $dir.'/'.$src,
                            'uploaded_at' => (string)\Carbon\Carbon::now()->format('d/m/Y')
                        ];
                }
            }
            return $data;
        }
        return null;
    }
}

if (!function_exists('store_lesson_file')) {
    function store_lesson_file($param, $dir = 'attachment/lesson') {
        if ($param) {
            $size = $param->getSize();
            if ($size <= 15500000) { // 15M
                $orgName = $param->getClientOriginalName();
                $src = Storage::disk('local')->put($dir, $param);
                $data =
                    [
                        'org_name' => $orgName,
                        'src' => 'storage/'.$src,
                        'uploaded_at' => (string)\Carbon\Carbon::now()->format('d/m/Y')
                    ];
            }
            return $data;
        }
        return null;
    }
}

if (!function_exists('num_format')) {
    function num_format($numVal,$afterPoint=2,$minAfterPoint=0,$thousandSep=",",$decPoint="."){
        // Same as number_format() but without unnecessary zeros.
        $ret = number_format($numVal,$afterPoint,$decPoint,$thousandSep);
        if($afterPoint!=$minAfterPoint){
            while(($afterPoint>$minAfterPoint) && (substr($ret,-1) =="0") ){
                // $minAfterPoint!=$minAfterPoint and number ends with a '0'
                // Remove '0' from end of string and set $afterPoint=$afterPoint-1
                $ret = substr($ret,0,-1);
                $afterPoint = $afterPoint-1;
            }
        }
        if(substr($ret,-1)==$decPoint) {
            $ret = substr($ret,0,-1);
        }
        return $ret;
    }
}

if (!function_exists('admin_action')) {
    function admin_action()
    {
        if (
            @Auth::user()->role == config('app.system_admin_role') ||
            @Auth::user()->role == config('app.system_sub_admin_role') ||
            @Auth::user()->name == 'admin'
        ) {
            return true;
        }
        return false;
    }
}

if (!function_exists('hr_action')) {
    function hr_action()
    {
        if (
            @Auth::user()->department_id == 3 || // department hr
            @Auth::user()->department_id == 29 || // department hr&admin
            @Auth::user()->department_id == 32 // department recruitment 
        ) {
            return true;
        }
        return false;
    }
}


if (!function_exists('check_nickname')) {
    function check_nickname($position_level, $date)
    {
        // $date = strtotime(@$date->format('Y-m-d'));
        $date = Carbon::createFromTimestamp(strtotime($date))->format('Y-m-d');
        $date = strtotime($date);
        $default = strtotime('2022-08-01'); // effective date
        // set for only president and effective 2022-08-01
        if($position_level == config('app.position_level_president') && $date >= $default)
        {
            // return 'ឧកញ៉ា';
            return 'Neak Oknha';
        }
        return null;
    }
}
if (!function_exists('auto_generate_invoice')) {
    function auto_generate_invoice($branchId,$type)
    {
        $setting = DB::table('setting_auto_increments')->where('branch_id', $branchId)->where('type',$type)->where('inv_year', date('Y'))->lockForUpdate()->first();
        $inv_id = $setting->inv_id + 1;
        $invoiceNumber = $setting->inv_prefix.'-'.date('y').'-'.date('m').'-'.str_pad($inv_id, 5, "0", STR_PAD_LEFT);
        DB::table('setting_auto_increments')->whereId($setting->id)->update(['inv_id' => $inv_id]);
        //dd($invoiceNumber);
        return $invoiceNumber;
    }
}


class Constants{

    /** Contract management */
    public const
        SOFTWARE_LICENSE_RENEWAL = 1,
        VENDOR_AGREEMENT         = 2,
        PROPERTY_RENTAL          = 3,
        STAFF_CONTRACT           = 4;

    public const PROPERTY_TYPE = [
        self::SOFTWARE_LICENSE_RENEWAL => 'Software License Renewal',
        self::VENDOR_AGREEMENT         => 'Vendor Agreement',
        self::PROPERTY_RENTAL          => 'Property Rental',
        self::STAFF_CONTRACT           => 'Staff Contract',
    ];

    public const CHANNEL = [
        'telegram' => 1,
        'email'    => 2,
    ];

    public const
        CONTRACT_MANAGEMEMNT_IT    = 1,
        CONTRACT_MANAGEMEMNT_ADMIN = 2,
        CONTRACT_MANAGEMEMNT_HR    = 3,
        CONTRACT_MANAGEMEMNT_ALL   = 4;
    public const CONTRACT_MANAGEMEMNT_ROLES = [
        self::CONTRACT_MANAGEMEMNT_IT    => 'IT',
        self::CONTRACT_MANAGEMEMNT_ADMIN => 'Admin',
        self::CONTRACT_MANAGEMEMNT_HR    => 'HR',
        self::CONTRACT_MANAGEMEMNT_ALL   => 'All',
    ];

    /** Count as day, alert before expire N days */
    public const CONTRACT_TYPE = [
        'SOFTWARE_LICENSE_RENEWAL' => 30,
        'VENDOR_AGREEMENT'         => 30,
        'PROPERTY_RENTAL'          => 30,
        'STAFF_CONTRACT'           => 30,
    ];
    public const
            HOUSE       = 1,
            SOFTWARE    = 2,
            PROFILE     = 3;
    public const PROPERTIES_TYPE = [
        self::HOUSE     => 'House',
        self::SOFTWARE  => 'Software',
        self::PROFILE   => 'Staff Profile',
    ];

    // task dateline tracking
    public const TASKDATELINE = 2;



}