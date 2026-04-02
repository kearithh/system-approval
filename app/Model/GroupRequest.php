<?php

namespace App\Model;

use App\Traits\CRUDable;
use CollectionHelper;
use Carbon\Carbon;
use App\Model\GroupRequestTemplate;
use App\SettingGroupSupport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupRequest extends Model
{
    // All request each company                                         by menu list
    // All request each company + type                                  by menu list
    // All request each company + type + Departments                    by menu list
    // All request each company + type + Departments + Tags             by menu list

    use SoftDeletes;

    use CRUDable;

    protected $table = 'g_requests';

    protected $fillable = [
//        'id',
        'type',
        'user_id',
        'user_name',
        'company_id',
        'company_name',
        'department_id',
        'department_name',
        'branch_id',
        'branch_name',
        'template_id',
        'name',
        'status',
        'review_status',

        'tags',
        'properties',
        'attachments',

//        'deadline',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',

        'created_at',
        'updated_at',
        'deleted_at',

        // Permission for each record: to store user id
        'cc',
        'approvable',
        'rejectable',
        'viewable',
        'editable',
        'deletable',
    ];

    protected $dates = ['start_date', 'end_date'];

    /**
     * @var string[]
     */
    protected $casts = [
        'properties' => 'object',
        'attachments' => 'array',

        'cc' => 'array',

        'approvable' => 'array',
        'rejectable' => 'array',
        'viewable' => 'array',
        'editable' => 'array',
        'deletable' => 'array',

    ];

    protected $listColumn = "
            g_requests.id,
            g_requests.type,
            g_requests.user_id,
            g_requests.user_name,
            g_requests.company_id,
            g_requests.company_name,
            g_requests.department_id,
            g_requests.department_name,
            g_requests.branch_id,
            g_requests.branch_name,
            g_requests.name,
            g_requests.status,
            g_requests.review_status,
            g_requests.tags,
            g_requests.properties,
            g_requests.attachments,
            g_requests.start_date,
            g_requests.end_date,
            g_requests.created_by,
            g_requests.updated_by,
            g_requests.created_at,
            g_requests.updated_at,
            g_requests.deleted_at,

            g_reviewers.reviewer_id,
            g_reviewers.status as review_status,

            g_approvers.approver_id,
            g_approvers.status as approve_status,

            approvable,
            rejectable,
            viewable,
            editable,
            deletable
        ";

    protected $totalListColumn = "
            g_requests.id,
            g_requests.type,
            g_requests.user_id,
            g_requests.user_name,
            g_requests.company_id,
            g_requests.company_name,
            g_requests.department_id,
            g_requests.department_name,
            g_requests.branch_id,
            g_requests.branch_name,
            g_requests.name,
            g_requests.status,
            g_requests.review_status,
            g_requests.tags,
            g_requests.properties,
            g_requests.attachments,
            g_requests.start_date,
            g_requests.end_date,
            g_requests.created_by,
            g_requests.updated_by,
            g_requests.created_at,
            g_requests.updated_at,
            g_requests.deleted_at,

            g_reviewers.reviewer_id,
            g_reviewers.status as review_status,

            g_approvers.approver_id,
            g_approvers.status as approve_status,

            companies.short_name_en as company_short_name,

            approvable,
            rejectable,
            viewable,
            editable,
            deletable,
            count('*') as total_record
        ";

    /**
     * @param null $requestId
     * @return Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getApproverByRequestId($requestId = null)
    {
        $requestId = $requestId ? $requestId : $this->id;
        $data = DB::table('users')
            ->join('positions', 'positions.id', '=', 'users.position_id')
            ->join('g_approvers', 'g_approvers.approver_id', '=', 'users.id')
            ->join('g_requests', 'g_requests.id', '=', 'g_approvers.request_id')
            ->where('g_requests.id', '=', $requestId)
            ->select([
                'users.id',
                'users.name',
                'users.username',
                'positions.name_km as position_name',
                'g_approvers.status as review_status',
                'g_approvers.status as approve_status',
                'g_approvers.approved_at',
                'g_approvers.rejected_at',
                'g_approvers.comment',
                'g_approvers.attachments',
            ])
            ->first()
        ;
        if ($data) {
            $data->attachments = json_decode($data->attachments);
            $data->attachments = @$data->attachments[0];
        }
        return $data;
    }

    /**
     * @param null $requestId
     * @return \Illuminate\Support\Collection
     */
    public function getReviewersByRequestId($requestId = null)
    {
        $requestId = $requestId ? $requestId : $this->id;
        $data = DB::table('users')
            ->join('positions', 'positions.id', '=', 'users.position_id')
            ->join('g_reviewers', 'g_reviewers.reviewer_id', '=', 'users.id')
            ->join('g_requests', 'g_requests.id', '=', 'g_reviewers.request_id')

            ->where('g_requests.id', '=', $requestId)
            ->select([
                'users.id',
                'users.name',
                'users.username',
                'positions.name_km as position_name',
                'g_reviewers.status as review_status',
                'g_reviewers.status as approve_status',
                'g_reviewers.approved_at',
                'g_reviewers.rejected_at',
                'g_reviewers.comment',
                'g_reviewers.attachments',
            ])
            ->orderBy('g_reviewers.id', 'ASC')
            ->get()
            ;
        if ($data) {
            foreach ($data as $key => $item) {
                $data[$key]->attachments = json_decode($data[$key]->attachments);
                $data[$key]->attachments = @$data[$key]->attachments[0];
            }
        }
        return $data;
    }

    /**
     * @param null $requestId
     * @return \Illuminate\Support\Collection
     */
    public function getCCByRequestId($requestId = null)
    {
        $requestId = $requestId ? $requestId : $this->id;
        $data = DB::table('users')
            ->join('positions', 'positions.id', '=', 'users.position_id')
            ->join('g_ccs', 'g_ccs.reviewer_id', '=', 'users.id')
            ->join('g_requests', 'g_requests.id', '=', 'g_ccs.request_id')

            ->where('g_requests.id', '=', $requestId)
            ->select([
                'users.id',
                'users.name',
                'users.username',
                'positions.name_km as position_name',
                'g_ccs.status as review_status',
                'g_ccs.status as approve_status',
                'g_ccs.approved_at',
                'g_ccs.rejected_at',
                'g_ccs.comment',
                'g_ccs.attachments',
            ])
            ->get()
            ;
        if ($data) {
            foreach ($data as $key => $item) {
                $data[$key]->attachments = json_decode($data[$key]->attachments);
                $data[$key]->attachments = @$data[$key]->attachments[0];
            }
        }
        return $data;
    }

    /**
     * @param $type
     * @param null $companyId
     * @param null $departmentId
     * @param null $tags
     * @param null $status
     * @param null $userId
     * @param null $reviewerOrApproverStatus
     * @param null $excludeOwner
     * @return array
     */
    public function getRelatedRequestByUser
    (
        $type,
        $companyId = null,
        $departmentId = null,
        $tags = null,
        $status = null,
        $date_from = null,
        $date_to = null,
        $userId = null,
        $reviewerOrApproverStatus = null,
        $excludeOwner = null
    ) {
        $data = $this->getRelatedRequestByUserQuery(
            $type,
            $companyId,
            $departmentId,
            $tags,
            $status,
            $date_from,
            $date_to,
            $userId,
            $reviewerOrApproverStatus,
            $excludeOwner
        );

        // Append reviewers and approver
        foreach($data as $key => $item) {
            $item->reviewers = $this->getReviewersByRequestId($item->id);
            $item->approver = $this->getApproverByRequestId($item->id);
            $item->ccs = $this->getCCByRequestId($item->id);
            $item->attachments = json_decode($item->attachments);
        }
        return $data;
    }

    /**
     * @param $type
     * @param null $companyId
     * @param null $departmentId
     * @param null $tags
     * @param null $status
     * @param null $userId
     * @param null $reviewerOrApproverStatus
     * @param null $requestId
     * @param null $excludeOwner
     * @return array
     */
    public function getRelatedRequestByUserQuery(
        $type,
        $companyId = null,
        $departmentId = null,
        $tags = null,
        $status = null,
        $date_from = null,
        $date_to = null,
        $userId = null,
        $reviewerOrApproverStatus = null,
        $requestId = null,
        $excludeOwner = null
    )
    {
//        $status = null;
//        $companyId = null;
//        $reviewerOrApproverStatus = null;
        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }
        $userId = $userId ? $userId : Auth::id();
        $cols = $this->listColumn;
        $tagsSetting = collect(config('app.tags'))->pluck('slug')->toArray();
        $sql =
            "
            select $cols
            from g_requests
            left join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            where g_requests.user_id = $userId
            and g_requests.type = '$type'
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sql .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql .= " and g_requests.department_id = $departmentId";
        }
        // if ($tags) {
        //     $sql .= " and g_requests.tags = '$tags'";
        // }
        if (in_array($tags, $tagsSetting)) {
            $sql .= " and g_requests.tags = '$tags'";
        }
        if ($status) {
            $sql .= " and g_requests.status = '$status'";
        }
        if ($reviewerOrApproverStatus) {
            $sql .= " and g_reviewers.status = '$reviewerOrApproverStatus'";
        }
        if ($requestId) {
            $sql .= " and g_requests.id = $requestId";
        }
        if ($excludeOwner) {
            $sql .= " and g_requests.user_id <> $userId";
        }
        if ($date_from || $date_to) {
            $sql .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sql .= " group by g_requests.id";
        $sql .= " union";

        $sql1 = "
            select $cols
            from g_reviewers
            join g_requests on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            where g_reviewers.reviewer_id = $userId
            and g_requests.type = '$type'
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sql1 .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql1 .= " and g_requests.department_id = $departmentId";
        }
        // if ($tags) {
        //     $sql1 .= " and g_requests.tags = '$tags'";
        // }
        if (in_array($tags, $tagsSetting)) {
            $sql1 .= " and g_requests.tags = '$tags'";
        }
        if ($status) {
            $sql1 .= " and g_requests.status = '$status'";
        }
        if ($reviewerOrApproverStatus) {
            $sql1 .= " and g_reviewers.status = '$reviewerOrApproverStatus'";
        }
        if ($requestId) {
            $sql1 .= " and g_requests.id = $requestId";
        }
        if ($excludeOwner) {
            $sql1 .= " and g_requests.user_id <> $userId";
        }
        if ($date_from || $date_to) {
            $sql1 .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sql1 .= " group by g_requests.id";
        $sql1 .= " union";

        $sql2 = "
            select $cols
            from g_approvers
            join g_requests on g_requests.id = g_approvers.request_id
            left join g_reviewers on g_requests.id = g_reviewers.reviewer_id
            where g_approvers.approver_id = $userId
            and g_requests.type = '$type'
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sql2 .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql2 .= " and g_requests.department_id = $departmentId";
        }
        if ($tags) {
            $sql2 .= " and g_requests.tags = '$tags'";
        }
        if ($status) {
            $sql2 .= " and g_requests.status = '$status'";
        }
        if ($reviewerOrApproverStatus) {
            $sql2 .= " and g_reviewers.status = '$reviewerOrApproverStatus'";
        }
        if ($requestId) {
            $sql2 .= " and g_requests.id = $requestId";
        }
        if ($excludeOwner) {
            $sql2 .= " and g_requests.user_id <> $userId";
        }
        if ($date_from || $date_to) {
            $sql2 .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sql2 .= " group by g_requests.id";

        $data = DB::select(DB::raw($sql.' '.$sql1.' '. $sql2));

        $data = CollectionHelper::paginate(collect($data)->sortByDesc('id'), count($data), 20);

//      dd($data);
        return $data;
    }

    /**
     * @param $type
     * @param null $companyId
     * @param null $departmentId
     * @param null $tags
     * @param null $status
     * @param null $userId
     * @param null $reviewerOrApproverStatus
     * @param $excludeOwner
     * @return int
     */
    public function getTotalRelatedRequestByUser
    (
        $type,
        $companyId = null,
        $departmentId = null,
        $tags = null,
        $status = null,
        $userId = null,
        $reviewerOrApproverStatus = null,
        $excludeOwner = null
    ) {
        $data = $this->getRelatedRequestByUserQuery(
            $type,
            $companyId,
            $departmentId,
            $tags,
            $status,
            $userId,
            $reviewerOrApproverStatus,
            $excludeOwner
        );

        $data = count($data);

        return $data;
    }


    /**
     * @param $requestId
     * @param $reviewerIds
     * @return bool
     */
    public function storeReviewers($reviewerIds, $requestId = null)
    {
        $data = null;
        $requestId = $requestId ? $requestId : $this->id;

        // $reviewers = DB::table('users')
        //     ->join('positions', 'users.position_id', '=', 'positions.id')
        //     ->whereIn('users.id', $reviewerIds)
        //     ->select(['users.*', 'positions.name_km as position_name'])
        //     ->get();

        //store reviewers
        foreach ($reviewerIds as $key => $item) {

            $reviewers = DB::table('users')
                ->join('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.id', $item)
                ->select(['users.*', 'positions.name_km as position_name'])
                ->first();

            $reviewerParam[$key] = [
                'request_id' => $requestId,
                'reviewer_id' => $reviewers->id,
                'reviewer_name' => $reviewers->name,
                'reviewer_position' => $reviewers->position_name,
                'status' => config('app.pending'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        if (@$reviewerParam) {
            $data = DB::table('g_reviewers')->insert(@$reviewerParam);
        }
        return $data;
    }


    /**
     * @param $requestId
     * @param $reviewerIds
     * @return bool
     */
    public function storeCC($ccIds = null, $requestId = null)
    {
        $requestId = $requestId ? $requestId : $this->id;

        $data = null;
        if(@$ccIds){
            foreach ($ccIds as $key => $item) {

                $reviewers = DB::table('users')
                    ->join('positions', 'users.position_id', '=', 'positions.id')
                    ->where('users.id', $item)
                    ->select(['users.*', 'positions.name_km as position_name'])
                    ->first();

                $ccParam[$key] = [
                    'request_id' => $requestId,
                    'reviewer_id' => $reviewers->id,
                    'reviewer_name' => $reviewers->name,
                    'reviewer_position' => $reviewers->position_name,
                    'status' => 'cc',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            $data = DB::table('g_ccs')->insert(@$ccParam);
        }
            
        return $data;
    }

    /**
     * @param $requestId
     * @param $approverId
     * @return bool
     */
    public function storeApprover($approverId, $requestId = null)
    {
        $requestId = $requestId ? $requestId : $this->id;
        $approver = DB::table('users')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.id', (array)$approverId)
            ->select(['users.*', 'positions.name_km as position_name'])
            ->first();
        $approverParam = [
            'request_id' => $requestId,
            'approver_id' => $approver->id,
            'approver_name' => $approver->name,
            'approver_position' => $approver->position_name,
            'status' => config('app.pending'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        $data = DB::table('g_approvers')->insert(@$approverParam);
        return $data;
    }


    /**
     * @param $requestId
     * @param $reviewerIds
     * @return bool
     */
    public function updateReviewers($reviewerIds, $requestId = null)
    {
        $requestId = $requestId ? $requestId : $this->id;
        $data = null;
        // $reviewers = DB::table('users')
        //     ->join('positions', 'users.position_id', '=', 'positions.id')
        //     ->whereIn('users.id', $reviewerIds)
        //     ->select(['users.*', 'positions.name_km as position_name'])
        //     ->get();

        //delete reviewers
        $item = DB::table('g_reviewers')->where('request_id', $requestId)->delete();

        //store reviewers
        foreach ($reviewerIds as $key => $item) {

            $reviewers = DB::table('users')
                ->join('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.id', $item)
                ->select(['users.*', 'positions.name_km as position_name'])
                ->first();

            $reviewerParam[$key] = [
                'request_id' => $requestId,
                'reviewer_id' => $reviewers->id,
                'reviewer_name' => $reviewers->name,
                'reviewer_position' => $reviewers->position_name,
                'status' => config('app.pending'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        if(@$reviewerParam){
            $data = DB::table('g_reviewers')->insert(@$reviewerParam);
        }
        return $data;
    }


    /**
     * @param $requestId
     * @param $reviewerIds
     * @return bool
     */
    public function updateCC($ccIds = null, $requestId = null)
    {
        $requestId = $requestId ? $requestId : $this->id;
        $data = null;

        //delete ccs
        $item = DB::table('g_ccs')->where('request_id', $requestId)->delete();

        if(@$ccIds){
            //store ccs
            foreach ($ccIds as $key => $item) {

                $reviewers = DB::table('users')
                    ->join('positions', 'users.position_id', '=', 'positions.id')
                    ->where('users.id', $item)
                    ->select(['users.*', 'positions.name_km as position_name'])
                    ->first();

                $ccParam[$key] = [
                    'request_id' => $requestId,
                    'reviewer_id' => $reviewers->id,
                    'reviewer_name' => $reviewers->name,
                    'reviewer_position' => $reviewers->position_name,
                    'status' => 'cc',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
            if(@$ccParam){
                $data = DB::table('g_ccs')->insert(@$ccParam);
            }
        }
            
        return $data;
    }

    /**
     * @param $requestId
     * @param $approverId
     * @return bool
     */
    public function updateApprover($approverId, $requestId = null)
    {
        $requestId = $requestId ? $requestId : $this->id;

        $approver = DB::table('users')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.id', (array)$approverId)
            ->select(['users.*', 'positions.name_km as position_name'])
            ->first();

        //delete approver
        $item = DB::table('g_approvers')->where('request_id', $requestId)->delete();

        //store approver
        $approverParam = [
            'request_id' => $requestId,
            'approver_id' => $approver->id,
            'approver_name' => $approver->name,
            'approver_position' => $approver->position_name,
            'status' => config('app.pending'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        $data = DB::table('g_approvers')->insert(@$approverParam);
        return $data;
    }

    /**
     * @param $type
     * @param $companyId
     * @param null $status
     * @param null $userId
     */
    public function getTotalRequestEachDepartment(
        $type,
        $companyId,
        $status = null,
        $userId = null
    )
    {
        $userId = $userId ? $userId : Auth::id();
        $data = DB::table('g_requests');
        $data = $data->join('users', 'g_requests.user_id', '=', 'users.id');
        $data = $data->where('g_requests.type', '=', $type);
        $data = $data->where('g_requests.user_id', '=', $userId);
        $data = $data->whereNull('g_requests.deleted_at');
        if ($companyId) {
            $data = $data->where('g_requests.company_id', '=', $companyId);
        }
        if ($status) {
            $data = $data->where('g_requests.status', '=', $status);
        }
        $data = $data->select([
            'g_requests.company_id',
            'g_requests.company_name',
            'g_requests.department_id',
            'g_requests.department_name',
            DB::raw('count(g_requests.department_id) as total')
        ])
        ->groupBy('g_requests.department_id')
        ;

        $relatedData = DB::table('g_requests');
        $relatedData = $relatedData->join('users', 'g_requests.user_id', '=', 'users.id');
        $relatedData = $relatedData->join('g_reviewers', 'g_reviewers.reviewer_id', '=', 'users.id');
        $relatedData = $relatedData->where('g_requests.type', '=', $type);
        $relatedData = $relatedData->where('g_reviewers.reviewer_id', '=', $userId);
        $relatedData = $relatedData->whereNull('g_requests.deleted_at');
        if ($companyId) {
            $relatedData = $relatedData->where('g_requests.company_id', '=', $companyId);
        }
        if ($status) {
            $relatedData = $relatedData->where('g_requests.status', '=', $status);
        }
        $relatedData = $relatedData->select([
            'g_requests.company_id',
            'g_requests.company_name',
            'g_requests.department_id',
            'g_requests.department_name',
            DB::raw('count(g_requests.department_id) as total')
        ])
            ->unionAll($data)
            ->groupBy('g_requests.department_id')
            ->get()
        ;
        return $relatedData;
    }

    /**
     * @param $type
     * @param $tags
     * @param null $companyId
     * @param null $departmentId
     * @param null $status
     * @param null $userId
     * @return \Illuminate\Support\Collection
     */
    public function getTotalRequestEachTags(
        $type,
        $companyId = null,
        $departmentId = null,
        $status = null,
        $userId = null
    )
    {
        $uriSegment = request()->segment(1);
        $userId = $userId ? $userId : Auth::id();
        $data = DB::table('g_requests');
        $data = $data->join('users', 'g_requests.user_id', '=', 'users.id');
        $data = $data->where('g_requests.type', '=', $type);
        $data = $data->whereNull('g_requests.deleted_at');
        if ($uriSegment == 'toapprove') {
            $data = $data->where('g_requests.user_id', '!=', $userId);
        } else {
            $data = $data->where('g_requests.user_id', '=', $userId);
        }
        if ($companyId) {
            $data = $data->where('g_requests.company_id', '=', $companyId);
        }
        if ($departmentId) {
            $data = $data->where('g_requests.department_id', '=', $departmentId);
        }
        if ($status) {
            $data = $data->where('g_requests.status', '=', $status);
        }
        $data = $data->select([
            'g_requests.id',
            'g_requests.user_id',
            'g_requests.company_id',
            'g_requests.company_name',
            'g_requests.department_id',
            'g_requests.department_name',
            'g_requests.tags',
            DB::raw('count(g_requests.tags) as total')
        ])
            ->groupBy('g_requests.tags')
        ;

        $relatedData = DB::table('g_requests');
        $relatedData = $relatedData->join('users', 'g_requests.user_id', '=', 'users.id');
        $relatedData = $relatedData->join('g_reviewers', 'g_reviewers.reviewer_id', '=', 'users.id');
        $relatedData = $relatedData->where('g_requests.type', '=', $type);
        $relatedData = $relatedData->where('g_reviewers.reviewer_id', '=', $userId);
        $relatedData = $relatedData->whereNull('g_requests.deleted_at');
        if ($companyId) {
            $relatedData = $relatedData->where('g_requests.company_id', '=', $companyId);
        }
        if ($departmentId) {
            $data = $data->where('g_requests.department_id', '=', $departmentId);
        }
        if ($status) {
            $relatedData = $relatedData->where('g_requests.status', '=', $status);
            if ($uriSegment == 'toapprove') {
                $relatedData = $relatedData->where('g_reviewers.status', '=', $status);
                $relatedData = $relatedData->where('g_requests.user_id', '!=', $userId);
            }
        }
        $relatedData = $relatedData->select([
            'g_requests.id',
            'g_requests.user_id',
            'g_requests.company_id',
            'g_requests.company_name',
            'g_requests.department_id',
            'g_requests.department_name',
            'g_requests.tags',
            DB::raw('count(g_requests.tags) as total')
        ])
            ->unionAll($data)
            ->groupBy('g_requests.tags')
            ->get()
        ;
        return $relatedData;
    }

    /**
     * @return mixed
     */
    public function footerSection()
    {
        $created_at = strtotime($this->created_at);
        $data = DB::table('companies')->find($this->company_id);
        $sections = json_decode(@$data->letterhead);
        if ($sections) {
            foreach($sections as $key => $section) {
                $start = strtotime(@$section->start_effective);
                $end = strtotime(@$section->end_effective);
                if ($start <= $created_at && $end >= $created_at) {
                    return @$section->footer_section;
                }
            }
        } else {
            return @$data->footer_section;
        }
        return @$data->footer_section;
    }

    /**
     * @param $type
     * @param null $companyId
     * @param null $departmentId
     * @param null $tags
     * @param null $status
     * @param null $userId
     * @param null $reviewerOrApproverStatus
     * @param null $requestId
     * @param null $excludeOwner
     * @return array
     */
    public function getToApprovedRecord(
        $type,
        $companyId = null,
        $departmentId = null,
        $tags = null,
        $groups = null,
        $status = null,
        $userId = null,
        $reviewerOrApproverStatus = null,
        $date_from,
        $date_to,
        $requestId = null,
        $excludeOwner = null
    )
    {
        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }

        $status = config('app.pending');
//        $companyId = null;
        $reviewerOrApproverStatus = config('app.pending');
        $userId = $userId ? $userId : Auth::id();
        $tagsSetting = collect(config('app.tags'))->pluck('slug')->toArray();
        $cols = $this->listColumn;
        $sql =
            "
            select $cols
            from g_requests
            left join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join g_request_templates on g_request_templates.id = g_requests.template_id
            and g_requests.type = '$type'
            and g_reviewers.reviewer_id = $userId
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sql .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql .= " and g_requests.department_id = $departmentId";
        }
        if (in_array($tags, $tagsSetting)) {
            $sql .= " and g_requests.tags = '$tags'";
        }
        if ($status) {
            $sql .= " and g_requests.status = '$status'";
        }
        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sql .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sql .= " and g_requests.template_id = '$groups'";
            }
        }
        if ($reviewerOrApproverStatus) {
            $sql .= " and g_reviewers.status = '$reviewerOrApproverStatus'";
        }
//        if (Auth::user()->position->level == config('app.position_level_president')) {
//            $sql .= " and g_requests.review_status = 1";
//        }
        if ($date_from || $date_to) {
            $sql .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sql .= " group by g_requests.id";

        $sql1 =
            "
            select $cols
            from g_requests
            left join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join g_request_templates on g_request_templates.id = g_requests.template_id
            and g_requests.type = '$type'
            and g_approvers.approver_id = $userId
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sql1 .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql1 .= " and g_requests.department_id = $departmentId";
        }
        if (in_array($tags, $tagsSetting)) {
            $sql1 .= " and g_requests.tags = '$tags'";
        }
        if ($status) {
            $sql1 .= " and g_requests.status = '$status'";
        }
        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sql1 .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sql1 .= " and g_requests.template_id = '$groups'";
            }
        }

        // if (Auth::user()->position->level == config('app.position_level_president')) {
        //     $sql1 .= " and g_requests.review_status = 1";
        // } else {
        //     if ($reviewerOrApproverStatus) {
        //         $sql1 .= " and g_requests.status = '$reviewerOrApproverStatus'";
        //     }
        // }

        $sql1 .= " and g_approvers.approver_id = " . Auth::id();

        // check reviewers done
        // $sql1 .= " and g_requests.review_status = 1";

        if ($date_from || $date_to) {
            $sql1 .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }

        $sql1 .= " group by g_requests.id";

        $data = DB::select(DB::raw($sql.' union '.$sql1));

        $data = CollectionHelper::paginate(collect($data)->sortByDesc('id'), count($data), 20);

        // Append reviewers and approver
        foreach($data as $key => $item) {
            $item->reviewers = $this->getReviewersByRequestId($item->id);
            $item->approver = $this->getApproverByRequestId($item->id);
            $item->ccs = $this->getCCByRequestId($item->id);
            $item->attachments = json_decode($item->attachments);
        }
        return $data;
    }

    /**
     * Pending Record = Auth Request Pending + Reviewer Approved
     *
     * @param $type
     * @param null $companyId
     * @param null $departmentId
     * @param null $tags
     * @param null $status
     * @param null $userId
     * @param null $reviewerOrApproverStatus
     * @param null $requestId
     * @param null $excludeOwner
     * @return array
     */
    public function getPendingRecord(
        $type,
        $companyId = null,
        $departmentId = null,
        $tags = NULL,
        $groups = NULL,
        $status = null,
        $userId = null,
        $date_from = null,
        $date_to = null,
        $reviewerOrApproverStatus = null,
        $requestId = null,
        $excludeOwner = null
    )
    {
        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }

        $status = config('app.pending');
//        $companyId = null;
        $reviewerOrApproverStatus = config('app.approved');
        $userId = $userId ? $userId : Auth::id();
        $cols = $this->listColumn;
        $tagsSetting = collect(config('app.tags'))->pluck('slug')->toArray();

        //Auth Request Pending
        $sql =
            "
            select $cols
            from g_requests
            left join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join g_request_templates on g_request_templates.id = g_requests.template_id
            and g_requests.type = '$type'
            and g_requests.user_id = $userId
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sql .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql .= " and g_requests.department_id = $departmentId";
        }
        if (in_array($tags, $tagsSetting)) {
            $sql .= " and g_requests.tags = '$tags'";
        }
        if ($status) {
            $sql .= " and g_requests.status = '$status'";
        }
        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sql .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sql .= " and g_requests.template_id = '$groups'";
            }
        }
//        if ($reviewerOrApproverStatus) {
//            $sql .= " and g_reviewers.status = '$reviewerOrApproverStatus'";
//        }
        if ($date_from || $date_to) {
            $sql .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sql .= " group by g_requests.id";

        $sql .= " union";

        // Reviewer Approved

        $sql1 = "
            select $cols
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join g_request_templates on g_request_templates.id = g_requests.template_id
            and g_requests.type = '$type'
            and g_reviewers.reviewer_id = $userId
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sql1 .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql1 .= " and g_requests.department_id = $departmentId";
        }
        if (in_array($tags, $tagsSetting)) {
            $sql1 .= " and g_requests.tags = '$tags'";
        }
        if ($status) {
            $sql1 .= " and g_requests.status = '$status'";
        }
        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sql1 .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sql1 .= " and g_requests.template_id = '$groups'";
            }
        }
        if ($reviewerOrApproverStatus) {
            $sql1 .= " and g_reviewers.status = '$reviewerOrApproverStatus'";
        }
//        if ($requestId) {
//            $sql1 .= " and g_requests.id = $requestId";
//        }
//        if ($excludeOwner) {
//            $sql1 .= " and g_requests.user_id <> $userId";
//        }
        if ($date_from || $date_to) {
            $sql1 .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sql1 .= " group by g_requests.id";


        $data = DB::select(DB::raw($sql.' '.$sql1));

        $data = CollectionHelper::paginate(collect($data)->sortByDesc('id'), count($data), 20);

//        dd($sql1,$data);

        // Append reviewers and approver
        foreach($data as $key => $item) {
            $item->reviewers = $this->getReviewersByRequestId($item->id);
            $item->ccs = $this->getCCByRequestId($item->id);
            $item->approver = $this->getApproverByRequestId($item->id);
            $item->attachments = json_decode($item->attachments);
        }
//        dd($data);
        return $data;
    }

//    public function totalRequest()
//    {
//
//        // All request each company                                         by menu list
//        // All request each company + type                                  by menu list
//        // All request each company + type + Departments                    by menu list
//        // All request each company + type + Departments + Tags             by menu list
//
//        $request = request();
//
//
////        $groupRequest = new GroupRequest();
////        $type = config('app.report');
////        $status = null;
////        $reviewerOrApproverStatus = null;
////        $userId = Auth::id();
////        $labelType = '';
//        $company = DB::table('companies')->where('short_name_en', $request->company)->first();
//        $department = DB::table('company_departments')->where('company_id', @$company->id)->where('short_name', $request->department)->first();
//        $companyDepartment = DB::table('company_departments')->where('company_id', @$company->id)->get();
//
//        // define variable
//        $labelType = '';
//        $totalData = [];
//
//        // param
//        $companyId                      = @$company->id;
//        $type                           = @$request->type;
//        $departmentId                   = @$department->id;
//        $tags                           = @strtolower($request->tags);
//        $status                         = @null;
//        $reviewerOrApproverStatus       = @null;
//        $userId                         = @Auth::id();
//
//
//
//        $uriSegment = request()->segment(1);
//        if ($uriSegment == 'pending')
//        {
//            $status = config('app.pending');
//            $reviewerOrApproverStatus = config('app.approved');
//            $labelType = 'badge-warning';
//
//            $totalData['company'] = $this->totalPending($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'company_id');
//            $totalData['form_type'] = $this->totalPending($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'type');
//            $totalData['department'] = $this->totalPending($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'department_id');
//            $totalData['tags'] = $this->totalPending($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'tags');
//
//
////            dump($totalData);
//        }
//        elseif ($uriSegment == 'toapprove')
//        {
//            $status = config('app.pending');
//            $reviewerOrApproverStatus = config('app.pending');
//            $labelType = 'badge-info';
//
//            $totalData['company'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'company_id');
//            $totalData['form_type'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'type');
//            $totalData['department'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'department_id');
//            $totalData['tags'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'tags');
//
//
////            dump($totalData);
//        }
//        elseif ($uriSegment == 'reject')
//        {
//            $status = config('app.rejected');
//            $reviewerOrApproverStatus = config('app.rejected');
//            $labelType = 'badge-danger';
//
//            $totalData['company'] = $this->totalRejected($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'company_id');
//            $totalData['form_type'] = $this->totalRejected($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'type');
//            $totalData['department'] = $this->totalRejected($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'department_id');
//            $totalData['tags'] = $this->totalRejected($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'tags');
//
//
////            dump($totalData);
//        }
//        elseif ($uriSegment == 'approved')
//        {
//            $status = config('app.approved');
//            $labelType = 'badge-success';
//
//            $totalData['company'] = $this->totalApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'company_id');
//            $totalData['form_type'] = $this->totalApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'type');
//            $totalData['department'] = $this->totalApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'department_id');
//            $totalData['tags'] = $this->totalApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'tags');
//
//
////            dump($totalData);
//        }
////        else {
//            $totalData['pending_company'] = $this->totalPending($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'company_id');
//            $totalData['toapprove_company'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'company_id');
//            $totalData['reject_company'] = $this->totalRejected($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'company_id');
//            $totalData['approved_company'] = $this->totalApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'company_id');
////            $totalData['form_type'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'type');
////            $totalData['department'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'department_id');
////            $totalData['tags'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'tags');
//
//
////            dump($totalData);
////        }
//        return $totalData;
//
//        // All request each company                                         by menu list
//        // All request each company + type                                  by menu list
//        // All request each company + type + Departments                    by menu list
//        // All request each company + type + Departments + Tags             by menu list
//    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param $companyId
     * @param $type
     * @param $departmentId
     * @param $tags
     * @param $status
     * @param $reviewerOrApproverStatus
     * @param $userId
     * @param $groupBy
     * @return array|\Illuminate\Support\Collection
     */
    private function totalPending($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, $groupBy)
    {
        $cols = $this->totalListColumn;
        $sql =
            "
            select $cols
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join companies on companies.id = g_requests.company_id
            where g_requests.user_id = $userId
            and g_requests.deleted_at is null
            ";

        // Pending List /////////////////////////////////////////////////
        if ($status) {
            $sql .= " and g_requests.status = '$status'";
        }////////////////////////////////////////////////////////////////////

        switch ($groupBy) {
            case 'company_id':
                break;
            case 'type':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                break;
            case 'department_id':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                if ($type) {
                    $sql .= " and g_requests.type = '$type'";
                }
                break;
            case 'tags':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                if ($departmentId) {
                    $sql .= " and g_requests.department_id = $departmentId";
                }
                if ($type) {
                    $sql .= " and g_requests.type = '$type'";
                }
                break;
            default:
                break;
        }

//        $data = DB::select(DB::raw($sql));
//        return $data;
        $sql .= " group by g_requests.$groupBy";

        $cols = $this->totalListColumn;
        $sql1 =
            "
            select $cols
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join companies on companies.id = g_requests.company_id
            where g_reviewers.reviewer_id = $userId
            and g_requests.deleted_at is null
            ";

        // Pending List /////////////////////////////////////////////////
        if ($status) {
            $sql1 .= " and g_requests.status = '$status'";
        }
        if ($reviewerOrApproverStatus) {
            $sql1 .= " and g_reviewers.status = '$reviewerOrApproverStatus'";
        }////////////////////////////////////////////////////////////////////

        switch ($groupBy) {
            case 'company_id':
                break;
            case 'type':
                if ($companyId) {
                    $sql1 .= " and g_requests.company_id = $companyId";
                }
                break;
            case 'department_id':
                if ($companyId) {
                    $sql1 .= " and g_requests.company_id = $companyId";
                }
                if ($type) {
                    $sql1 .= " and g_requests.type = '$type'";
                }
                break;
            case 'tags':
                if ($companyId) {
                    $sql1 .= " and g_requests.company_id = $companyId";
                }
                if ($departmentId) {
                    $sql1 .= " and g_requests.department_id = $departmentId";
                }
                if ($type) {
                    $sql1 .= " and g_requests.type = '$type'";
                }
                break;
            default:
                break;
        }
        $sql1 .= " group by g_requests.$groupBy";
        $data = DB::select(DB::raw($sql.' union '.$sql1));
        if ($groupBy == 'company_id') {
            $totalData = [];
            foreach($data as $item) {
                $add = $item->company_short_name;
                $totalData[$add] = $item->total_record;
            }
            return $totalData;
        }
        if ($groupBy == 'department_id') {
            $departmentCompany = DB::table('company_departments')
                                ->whereNull('deleted_at')
                                ->where('company_id', $companyId)
                                ->get();
            foreach($data as $value) {
                foreach ($departmentCompany as $key => $item) {
                    if ($item->id == $value->department_id) {
                        $departmentCompany[$key]->total = $value->total_record;
                    } else {
                    }
                }
            }
            return $departmentCompany;
        }
        else {
            return $data;
        }
    }

    /**
     * @param $companyId
     * @param $type
     * @param $departmentId
     * @param $tags
     * @param $status
     * @param $reviewerOrApproverStatus
     * @param $userId
     * @param $groupBy
     * @return array|\Illuminate\Support\Collection
     */
    private function totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, $groupBy)
    {
        // All request each company                                         by menu list
        // All request each company + type                                  by menu list
        // All request each company + type + Departments                    by menu list
        // All request each company + type + Departments + Tags             by menu list
        $cols = $this->totalListColumn;
        $sql =
            "
            select $cols
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join companies on companies.id = g_requests.company_id
            where g_reviewers.reviewer_id = $userId
            and g_requests.deleted_at is null
            ";

        // To Approved List /////////////////////////////////////////////////
        if ($status) {
            $sql .= " and g_requests.status = '$status'";
        }
        if ($reviewerOrApproverStatus) {
            $sql .= " and g_reviewers.status = '$reviewerOrApproverStatus'";
        }////////////////////////////////////////////////////////////////////

        switch ($groupBy) {
            case 'company_id':
                $sql .= " group by g_requests.$groupBy";
                $data = DB::select(DB::raw($sql));
                $totalData = [];
                foreach($data as $item) {
                    $add = $item->company_short_name;
                    $totalData[$add] = $item->total_record;
                }
                return $totalData;
                break;
            case 'type':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                $sql .= " group by g_requests.$groupBy";
                break;
            case 'department_id':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                if ($type) {
                    $sql .= " and g_requests.type = '$type'";
                }
                $sql .= " group by g_requests.$groupBy";
                break;
            case 'tags':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                if ($departmentId) {
                    $sql .= " and g_requests.department_id = $departmentId";
                }
                if ($type) {
                    $sql .= " and g_requests.type = '$type'";
                }
                $sql .= " group by g_requests.$groupBy";
                break;
            default:
                break;
        }
        $data = DB::select(DB::raw($sql));
        if ($groupBy == 'company_id') {
            $totalData = [];
            foreach($data as $item) {
                $add = $item->company_short_name;
                $totalData[$add] = $item->total_record;
            }
            return $totalData;
        }
        if ($groupBy == 'department_id') {
            $departmentCompany = DB::table('company_departments')
                                ->where('company_id', $companyId)
                                ->whereNull('deleted_at')
                                ->get();
            foreach ($departmentCompany as $item) {
                foreach($data as $value) {
                    if ($item->id == $value->department_id) {
                        $item->total = $value->total_record;
                    } else {
                        $item->total = 0;
                    }
                }
            }
            return $departmentCompany;
        }
        else {
            return $data;
        }

    }

    /**
     * @param $companyId
     * @param $type
     * @param $departmentId
     * @param $tags
     * @param $status
     * @param $reviewerOrApproverStatus
     * @param $userId
     * @param $groupBy // company, form_type, department, tags
     * @return array
     */
    private function totalRejected($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, $groupBy)
    {
        // All request each company                                         by menu list
        // All request each company + type                                  by menu list
        // All request each company + type + Departments                    by menu list
        // All request each company + type + Departments + Tags             by menu list
        $cols = $this->totalListColumn;
        $sql =
            "
            select $cols
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join companies on companies.id = g_requests.company_id
            where g_requests.user_id = $userId
            and g_requests.deleted_at is null
            ";

        // Pending List /////////////////////////////////////////////////
        if ($status) {
            $sql .= " and g_requests.status = '$status'";
        }////////////////////////////////////////////////////////////////////

        switch ($groupBy) {
            case 'company_id':
                break;
            case 'type':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                break;
            case 'department_id':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                if ($type) {
                    $sql .= " and g_requests.type = '$type'";
                }
                break;
            case 'tags':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                if ($departmentId) {
                    $sql .= " and g_requests.department_id = $departmentId";
                }
                if ($type) {
                    $sql .= " and g_requests.type = '$type'";
                }
                break;
            default:
                break;
        }

//        $data = DB::select(DB::raw($sql));
//        return $data;
        $sql .= " group by g_requests.$groupBy";

        $cols = $this->totalListColumn;
        $sql1 =
            "
            select $cols
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join companies on companies.id = g_requests.company_id
            where g_reviewers.reviewer_id = $userId
            and g_requests.deleted_at is null
            ";

        // Pending List /////////////////////////////////////////////////
        if ($status) {
            $sql1 .= " and g_requests.status = '$status'";
        }
        if ($reviewerOrApproverStatus) {
            $sql1 .= " and g_reviewers.status = '$reviewerOrApproverStatus'";
        }////////////////////////////////////////////////////////////////////

        switch ($groupBy) {
            case 'company_id':
                break;
            case 'type':
                if ($companyId) {
                    $sql1 .= " and g_requests.company_id = $companyId";
                }
                break;
            case 'department_id':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                if ($type) {
                    $sql1 .= " and g_requests.type = '$type'";
                }
                break;
            case 'tags':
                if ($companyId) {
                    $sql1 .= " and g_requests.company_id = $companyId";
                }
                if ($departmentId) {
                    $sql1 .= " and g_requests.department_id = $departmentId";
                }
                if ($type) {
                    $sql1 .= " and g_requests.type = '$type'";
                }
                break;
            default:
                break;
        }
//        dd($sql.' union '.$sql1);
        $sql1 .= " group by g_requests.$groupBy";
        $data = DB::select(DB::raw($sql.' union '.$sql1));
        if ($groupBy == 'company_id') {
            $totalData = [];
            foreach($data as $item) {
                $add = $item->company_short_name;
                $totalData[$add] = $item->total_record;
            }
            return $totalData;
        }
        if ($groupBy == 'department_id') {
            $departmentCompany = DB::table('company_departments')
                                ->where('company_id', $companyId)
                                ->whereNull('deleted_at')
                                ->get();
            foreach ($departmentCompany as $item) {
                foreach($data as $value) {
                    if ($item->id == $value->department_id) {
                        $item->total = $value->total_record;
                    } else {
                        $item->total = 0;
                    }
                }
            }
            return $departmentCompany;
        }
        else {
            return $data;
        }
    }

    /**
     * @param $companyId
     * @param $type
     * @param $departmentId
     * @param $tags
     * @param $status
     * @param $reviewerOrApproverStatus
     * @param $userId
     * @param $groupBy // company, form_type, department, tags
     * @return array
     */
    private function totalApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, $groupBy)
    {
        // All request each company                                         by menu list
        // All request each company + type                                  by menu list
        // All request each company + type + Departments                    by menu list
        // All request each company + type + Departments + Tags             by menu list
        $cols = $this->totalListColumn;
        $sql =
            "
            select $cols
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join companies on companies.id = g_requests.company_id
            where g_requests.user_id = $userId
            and g_requests.deleted_at is null
            ";

        // Pending List /////////////////////////////////////////////////
        if ($status) {
            $sql .= " and g_requests.status = '$status'";
        }////////////////////////////////////////////////////////////////////

        switch ($groupBy) {
            case 'company_id':
                break;
            case 'type':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                break;
            case 'department_id':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                if ($type) {
                    $sql .= " and g_requests.type = '$type'";
                }
                break;
            case 'tags':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                if ($departmentId) {
                    $sql .= " and g_requests.department_id = $departmentId";
                }
                if ($type) {
                    $sql .= " and g_requests.type = '$type'";
                }
                break;
            default:
                break;
        }

//        $data = DB::select(DB::raw($sql));
//        return $data;
        $sql .= " group by g_requests.$groupBy";

        $cols = $this->totalListColumn;
        $sql1 =
            "
            select $cols
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join companies on companies.id = g_requests.company_id
            where g_reviewers.reviewer_id = $userId
            and g_requests.deleted_at is null
            ";

        // Pending List /////////////////////////////////////////////////
        if ($status) {
            $sql1 .= " and g_requests.status = '$status'";
        }

        switch ($groupBy) {
            case 'company_id':
                break;
            case 'type':
                if ($companyId) {
                    $sql1 .= " and g_requests.company_id = $companyId";
                }
                break;
            case 'department_id':
                if ($companyId) {
                    $sql .= " and g_requests.company_id = $companyId";
                }
                if ($type) {
                    $sql1 .= " and g_requests.type = '$type'";
                }
                break;
            case 'tags':
                if ($companyId) {
                    $sql1 .= " and g_requests.company_id = $companyId";
                }
                if ($departmentId) {
                    $sql1 .= " and g_requests.department_id = $departmentId";
                }
                if ($type) {
                    $sql1 .= " and g_requests.type = '$type'";
                }
                break;
            default:
                break;
        }
//        dd($sql.' union '.$sql1);
        $sql1 .= " group by g_requests.$groupBy";
        $data = DB::select(DB::raw($sql.' union '.$sql1));
        if ($groupBy == 'company_id') {
            $totalData = [];
            foreach($data as $item) {
                $add = $item->company_short_name;
                $totalData[$add] = $item->total_record;
            }
            return $totalData;
        }
        if ($groupBy == 'department_id') {
            $departmentCompany = DB::table('company_departments')
                                ->where('company_id', $companyId)
                                ->whereNull('deleted_at')
                                ->get();
            foreach ($departmentCompany as $item) {
                foreach($data as $value) {
                    if ($item->id == $value->department_id) {
                        $item->total = $value->total_record;
                    } else {
                        $item->total = 0;
                    }
                }
            }
            return $departmentCompany;
        }
        else {
            return $data;
        }
    }

    public function totalEachMenuCompany()
    {
        $request = request();
        $company = DB::table('companies')->where('short_name_en', $request->company)->first();
        $department = DB::table('company_departments')->where('company_id', @$company->id)->where('short_name', $request->department)->first();

        // param
        $companyId                      = @$company->id;
        $type                           = @$request->type;
        $departmentId                   = @$department->id;
        $tags                           = @strtolower($request->tags);
        $userId                         = @Auth::id();
        $totalData['pending'] = $this->totalPending($companyId, $type, $departmentId, $tags, config('app.pending'), config('app.approved'), $userId, 'company_id');
        $totalData['toapprove'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, config('app.pending'), config('app.pending'), $userId, 'company_id');
        $totalData['reject'] = $this->totalRejected($companyId, $type, $departmentId, $tags, config('app.rejected'), config('app.rejected'), $userId, 'company_id');
        $totalData['approved'] = $this->totalApproved($companyId, $type, $departmentId, $tags, config('app.approved'), config('app.approved'), $userId, 'company_id');


//        dump($totalData);
        return $totalData;


    }
    public function totalEachMenuCompanyType()
    {
        $request = request();
        $company = DB::table('companies')->where('short_name_en', $request->company)->first();
        $department = DB::table('company_departments')->where('company_id', @$company->id)->where('short_name', $request->department)->first();

        // param
        $companyId                      = @$company->id;
        $type                           = @$request->type;
        $departmentId                   = @$department->id;
        $tags                           = @strtolower($request->tags);
        $userId                         = @Auth::id();
        $reviewerOrApproverStatus = config('app.approved');
        $totalData = [];
        $uriSegment = request()->segment(1);
        if ($uriSegment == 'pending')
        {
            $status = config('app.pending');
            $reviewerOrApproverStatus = config('app.approved');
            $labelType = 'badge-warning';
            $totalData['pending'] = $this->totalPending($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'type');
        }
        elseif ($uriSegment == 'toapprove')
        {
            $status = config('app.pending');
            $reviewerOrApproverStatus = config('app.pending');
            $labelType = 'badge-info';
            $totalData['toapprove'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'type');
        }
        elseif ($uriSegment == 'reject')
        {
            $status = config('app.rejected');
            $reviewerOrApproverStatus = config('app.rejected');
            $labelType = 'badge-danger';
            $totalData['reject'] = $this->totalRejected($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'type');
        }
        elseif ($uriSegment == 'approved')
        {
            $status = config('app.approved');
            $labelType = 'badge-success';
            $totalData['approved'] = $this->totalApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'type');
        }

        $data = [];
        if ($totalData) {
            foreach($totalData[$uriSegment] as $item) {
                $data['label'] = $labelType;
                $data['report_each_company'] = $item->total_record;
            }
        }
        return $data;
    }

    /**
     * @return array
     */
    public function totalEachMenuCompanyTypeDepartment()
    {
        $request = request();
        $company = DB::table('companies')->where('short_name_en', $request->company)->first();
        $departmentCompany = DB::table('company_departments')
                            ->where('company_id', @$company->id)
                            ->whereNull('deleted_at')
                            ->get();
//        dd($departmentCompany);
        $department = DB::table('company_departments')->where('company_id', @$company->id)->where('short_name', $request->department)->first();

        // param
        $companyId                      = @$company->id;
        $type                           = @$request->type;
        $departmentId                   = @$department->id;
        $tags                           = @strtolower($request->tags);
        $userId                         = @Auth::id();
        $reviewerOrApproverStatus = config('app.approved');
        $totalData = [];
        $uriSegment = request()->segment(1);
        if ($uriSegment == 'pending')
        {
            $status = config('app.pending');
            $reviewerOrApproverStatus = config('app.approved');
            $labelType = 'badge-warning';
            $totalData['pending'] = $this->totalPending($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'department_id');
        }
        elseif ($uriSegment == 'toapprove')
        {
            $status = config('app.pending');
            $reviewerOrApproverStatus = config('app.pending');
            $labelType = 'badge-info';
            $totalData['toapprove'] = $this->totalToApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'department_id');
        }
        elseif ($uriSegment == 'reject')
        {
            $status = config('app.rejected');
            $reviewerOrApproverStatus = config('app.rejected');
            $labelType = 'badge-danger';
            $totalData['reject'] = $this->totalRejected($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'department_id');
        }
        elseif ($uriSegment == 'approved')
        {
            $status = config('app.approved');
            $labelType = 'badge-success';
            $totalData['approved'] = $this->totalApproved($companyId, $type, $departmentId, $tags, $status, $reviewerOrApproverStatus, $userId, 'department_id');
        }

//        $data = [];
//        if ($totalData) {
//            foreach($totalData[$uriSegment] as $item) {
//                $data['label'] = $labelType;
//                $data['report_each_company'] = $item->total_record;
//            }
//        }
//        dd($totalData);
        return @$totalData[$uriSegment];
    }

    public function getReviewerId()
    {
        $data = DB::table('g_reviewers')
                ->where('request_id', '=', $this->id)
                ->select(
                    'id',
                    'request_id',
                    'reviewer_id',
                    'reviewer_name'
                )
                ->orderBy('g_reviewers.id', 'asc')
                ->get();
                //dd($data);
        return @$data;
    }

    public function getApproverId()
    {
        $data = DB::table('g_approvers')->where('request_id', '=', $this->id)->pluck('approver_id')->first();
        return $data;
    }


    /////////////////////////////////////////////////////////////////////////////////////////
    /// Count total for president
    private function presidents(){}

    public function presidentGetToApproveList()
    {
        $cols = $this->listColumn;
        $type = config('app.report');
        $userId = Auth::id();
        $companyId = DB::table('companies')
            ->where('short_name_en', '=', @$_GET['company'])
            ->first();
        $companyId = @$companyId->id;

        $department = DB::table('company_departments')
            ->where('company_id', '=', $companyId)
            ->where('short_name', @$_GET['department'])->first();
        $departmentId = @$department->id;

        $tags = @strtolower($_GET['tags']);
        $groups = @strtolower($_GET['groups']);

        $date_from = @strtolower($_GET['date_from']);
        $date_to = @strtolower($_GET['date_to']);
        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }

        $tagsSetting = collect(config('app.tags'))->pluck('slug')->toArray();
        $user_group = @SettingGroupSupport::where('name', 'user_group')->first()->value;
        $status = config('app.pending');

        $sql2 = "
            select $cols
            from g_requests
            join g_approvers on g_requests.id = g_approvers.request_id
            left join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_request_templates on g_request_templates.id = g_requests.template_id
            where g_approvers.approver_id = $userId
            and g_requests.type = '$type'
            and g_requests.status = '$status'
            and g_approvers.status = '$status'
            and g_requests.review_status = 1
            and g_requests.deleted_at is null
            ";
        if(config('app.is_use_group_support') == 1) {
            $sql2 .= " and g_requests.user_id NOT IN (".implode(', ', $user_group).")";
        }
        if ($companyId) {
            $sql2 .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql2 .= " and g_requests.department_id = $departmentId";
        }
        // if ($tags && $tags != 'null') {
        //     $sql2 .= " and g_requests.tags = '$tags'";
        // }
        if (in_array($tags, $tagsSetting)) {
            $sql2 .= " and g_requests.tags = '$tags'";
        }

        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sql2 .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sql2 .= " and g_requests.template_id = '$groups'";
            }
        }

        if ($date_from || $date_to) {
            $sql2 .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }

        $sql2 .= " group by g_requests.id desc";
        $data = DB::select(DB::raw($sql2));

        $data = CollectionHelper::paginate(collect($data), count($data), 20);

        // Append reviewers and approver
        foreach($data as $key => $item) {
            $item->reviewers = $this->getReviewersByRequestId($item->id);
            $item->approver = $this->getApproverByRequestId($item->id);
            $item->ccs = $this->getCCByRequestId($item->id);
            $item->attachments = json_decode($item->attachments);
        }
        return $data;
    }

    public function presidentGetToApproveGroupSupportList()
    {
        $cols = $this->listColumn;
        $type = config('app.report');
        $userId = Auth::id();

        $tags = @strtolower($_GET['tags']);
        $groups = @strtolower($_GET['groups']);

        $date_from = @strtolower($_GET['date_from']);
        $date_to = @strtolower($_GET['date_to']);

        $department = @strtolower($_GET['department']);
        $group_support = @SettingGroupSupport::where('name', 'user_group')->first();
        $companyDepartment = DB::table('company_departments')
            ->select([
                'id',
                'name_en',
                'short_name'
            ])
            ->whereNull('deleted_at')
            ->where('short_name', $department)
            ->get();

        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }

        $tagsSetting = collect(config('app.tags'))->pluck('slug')->toArray();
        $user_group = @SettingGroupSupport::where('name', 'user_group')->first()->value;
        $status = config('app.pending');

        $sql = "
            select $cols
            from g_requests
            join g_approvers on g_requests.id = g_approvers.request_id
            left join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_request_templates on g_request_templates.id = g_requests.template_id
            where g_approvers.approver_id = $userId
            and g_requests.type = '$type'
            and g_requests.status = '$status'
            and g_approvers.status = '$status'
            and g_requests.review_status = 1
            and g_requests.user_id IN (".implode(', ', $user_group).")
            and g_requests.deleted_at is null
            ";

        if (in_array($tags, $tagsSetting)) {
            $sql .= " and g_requests.tags = '$tags'";
        }

        if ($companyDepartment->count() > 0) {
            $com_dep = [];
            foreach ($companyDepartment as $val) {
                $com_dep[] = $val->id;
            }
            $sql .= " and g_requests.department_id IN (".implode(', ', $com_dep).")";
        }

        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sql .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sql .= " and g_requests.template_id = '$groups'";
            }
        }

        if ($date_from || $date_to) {
            $sql .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }

        $sql .= " group by g_requests.id desc";
        $data = DB::select(DB::raw($sql));

        $data = CollectionHelper::paginate(collect($data), count($data), 20);

        // Append reviewers and approver
        foreach($data as $key => $item) {
            $item->reviewers = $this->getReviewersByRequestId($item->id);
            $item->approver = $this->getApproverByRequestId($item->id);
            $item->ccs = $this->getCCByRequestId($item->id);
            $item->attachments = json_decode($item->attachments);
        }
        return $data;
    }

    public function presidentGetRejectedList()
    {
        $cols = $this->listColumn;
        $type = config('app.report');
        $userId = Auth::id();
        $companyId = DB::table('companies')
            ->where('short_name_en', '=', @$_GET['company'])
            ->first();
        $companyId = @$companyId->id;

        $department = DB::table('company_departments')
            ->where('company_id', '=', $companyId)
            ->where('short_name', @$_GET['department'])->first();
        $departmentId = @$department->id;

        $tags = @strtolower($_GET['tags']);

        $date_from = @strtolower($_GET['date_from']);
        $date_to = @strtolower($_GET['date_to']);
        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }

        $status = config('app.rejected');

        $sql2 = "
            select $cols
            from g_requests
            join g_approvers on g_requests.id = g_approvers.request_id
            left join g_reviewers on g_requests.id = g_reviewers.request_id
            where g_approvers.approver_id = $userId
            and g_requests.type = '$type'
            and g_requests.status = '$status'
            and g_approvers.status = '$status'
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sql2 .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql2 .= " and g_requests.department_id = $departmentId";
        }
        if ($tags && $tags != 'null') {
            $sql2 .= " and g_requests.tags = '$tags'";
        }
        if ($date_from || $date_to) {
            $sql2 .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sql2 .= " group by g_requests.id";
        $data = DB::select(DB::raw($sql2));

        $data = CollectionHelper::paginate(collect($data)->sortByDesc('id'), count($data), 20);

        // Append reviewers and approver
        foreach($data as $key => $item) {
            $item->reviewers = $this->getReviewersByRequestId($item->id);
            $item->approver = $this->getApproverByRequestId($item->id);
            $item->ccs = $this->getCCByRequestId($item->id);
            $item->attachments = json_decode($item->attachments);
        }
        return $data;
    }

    public function presidentGetApprovedList()
    {
        $cols = $this->listColumn;
        $type = config('app.report');
        $userId = Auth::id();
        $companyId = DB::table('companies')
            ->where('short_name_en', '=', @$_GET['company'])
            ->first();
        $companyId = @$companyId->id;

        $department = DB::table('company_departments')
            ->where('company_id', '=', $companyId)
            ->where('short_name', @$_GET['department'])->first();
        $departmentId = @$department->id;

        $tags = @strtolower($_GET['tags']);

        $groups = @strtolower($_GET['groups']);

        $status = config('app.approved');

        $date_from = @strtolower($_GET['date_from']);
        $date_to = @strtolower($_GET['date_to']);
        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }

        // Auth as CC
        $sql1 = "
            select $cols
            from g_requests
            join g_approvers on g_requests.id = g_approvers.request_id 
            join g_ccs on g_requests.id = g_ccs.request_id
            left join g_reviewers on g_requests.id = g_reviewers.request_id and g_reviewers.reviewer_id = $userId
            left join g_request_templates on g_request_templates.id = g_requests.template_id
            where g_ccs.reviewer_id = $userId
            and g_requests.type = '$type'
            and g_requests.status = '$status'
            and g_requests.review_status = 1
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sql1 .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql1 .= " and g_requests.department_id = $departmentId";
        }
        if ($tags && $tags != 'null') {
            $sql1 .= " and g_requests.tags = '$tags'";
        }
        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sql1 .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sql1 .= " and g_requests.template_id = '$groups'";
            }
        }
        if ($date_from || $date_to) {
            $sql1 .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sql1 .= " group by g_requests.id";
        $sql1 .= " union";

        // Auth as Approver
        $sql2 = "
            select $cols
            from g_requests
            join g_approvers on g_requests.id = g_approvers.request_id
            left join g_reviewers on g_requests.id = g_reviewers.request_id
            left join g_request_templates on g_request_templates.id = g_requests.template_id
            where g_approvers.approver_id = $userId
            and g_requests.type = '$type'
            and g_requests.status = '$status'
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sql2 .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sql2 .= " and g_requests.department_id = $departmentId";
        }
        if ($tags && $tags != 'null') {
            $sql2 .= " and g_requests.tags = '$tags'";
        }
        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sql2 .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sql2 .= " and g_requests.template_id = '$groups'";
            }
        }

        if ($date_from || $date_to) {
            $sql2 .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }

        // $pageNumber = request()->page ? request()->page : 1;
        // $page = $pageNumber * 15;

        // $sql2 .= " group by g_requests.id desc limit 45 offset $page";

        $sql2 .= " group by g_requests.id desc";
        $data = DB::select(DB::raw($sql1.' '.$sql2));
        // $data = DB::select(DB::raw($sql2));
        // dd($data);

        $data = CollectionHelper::paginate(collect($data), count($data), 20);

        // Append reviewers and approver
        foreach($data as $key => $item) {
            $item->reviewers = $this->getReviewersByRequestId($item->id);
            $item->approver = $this->getApproverByRequestId($item->id);
            $item->ccs = $this->getCCByRequestId($item->id);
            $item->attachments = json_decode($item->attachments);
        }
        return $data;
    }

    /**
     * Count to approve list request each company for president
     * countToApproveListRequestEachCompanyForPresident
     * @return array
     */
    public function countToApproveGroupSupportForPresident()
    {
        $user_group = @SettingGroupSupport::where('name', 'user_group')->first()->value;
        $toApproveList = DB::table('g_requests')
                ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                ->where('g_requests.review_status', '=', 1)
                ->where('g_requests.status', '=', config('app.pending'))
                ->whereIn('g_requests.user_id', $user_group)
                ->whereNull('g_requests.deleted_at')
                ->where('g_approvers.approver_id', '=', Auth::id())
                ->count();
        return $toApproveList;
    }


    /**
     * Count to approve list request each company for president
     * countToApproveListRequestEachCompanyForPresident
     * @return array
     */
    public function countToApproveListRequestEachCompanyForPresident()
    {
        $toApproveList = [];
        $companies = DB::table('companies')->get();
        foreach ($companies as $item) {
            $toApproveList[$item->short_name_en] = DB::table('g_requests')
                ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                ->select('g_requests.id')
                ->where('g_requests.review_status', '=', 1)
                ->where('g_requests.status', '=', config('app.pending'))
                ->whereNull('g_requests.deleted_at');
            if(config('app.is_use_group_support') == 1) {
                $user_group = @SettingGroupSupport::where('name', 'user_group')->first()->value;
                $toApproveList[$item->short_name_en] = $toApproveList[$item->short_name_en]     
                    ->whereNotIn('g_requests.user_id', $user_group);
            }
            $toApproveList[$item->short_name_en] = $toApproveList[$item->short_name_en]     
                ->where('g_requests.company_id', '=', $item->id)
                ->where('g_approvers.approver_id', '=', Auth::id())
                ->count();
        }
        return $toApproveList;
    }

    /**
     * Count rejected request each company for president
     * countRejectedRequestEachCompanyForPresident
     * @return array
     */
    public function countRejectedRequestEachCompanyForPresident()
    {
        $requestedList = [];
        $companies = DB::table('companies')->get();
        foreach ($companies as $item) {
            $requestedList[$item->short_name_en] = DB
                ::table('g_requests')
                ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                ->where('g_requests.review_status', '=', 1)
                ->where('g_requests.company_id', '=', $item->id)
                ->where('g_approvers.approver_id', '=', Auth::id())
                ->where('g_approvers.status', '=', config('app.rejected'))
                ->whereNull('g_requests.deleted_at')
                ->count();
        }
        return $requestedList;
    }

    /**
     * Count approved request each company for president
     * countApprovedRequestEachCompanyForPresident
     * @return array
     */
    public function countApprovedRequestEachCompanyForPresident()
    {
        $approvedList = [];
        $companies = DB::table('companies')->select('id', 'short_name_en')->get();
        foreach ($companies as $item) {
            $data = DB
                ::table('g_requests')
                ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                ->where('g_requests.review_status', '=', 1)
                ->where('g_requests.company_id', '=', $item->id)
                ->where('g_approvers.approver_id', '=', Auth::id())
                ->where('g_approvers.status', '=', config('app.approved'))
                ->where('g_requests.status', '=', config('app.approved'))
                ->whereNull('g_requests.deleted_at')
                ->count();

            $data += DB
                ::table('g_requests')
                ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                ->where('g_requests.review_status', '=', 1)
                ->where('g_requests.company_id', '=', $item->id)
                ->where('g_ccs.reviewer_id', '=', Auth::id())
                ->where('g_requests.status', '=', config('app.approved'))
                ->whereNull('g_requests.deleted_at')
                ->count();

            $approvedList[$item->short_name_en] = $data;
        }
        return $approvedList;
    }

    public function countToApproveListOfReportByCompanyOfPresident()
    {
        $companyShortNameEn = @$_GET['company'];
        if ($companyShortNameEn) {
            $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
            $approvedList = DB
                ::table('g_requests')
                ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                ->where('g_requests.type', '=', config('app.report'))
                ->where('g_requests.review_status', '=', 1)
                ->where('g_requests.company_id', '=', $company->id)
                ->where('g_approvers.approver_id', '=', Auth::id())
                ->where('g_approvers.status', '=', config('app.pending'))
                ->where('g_requests.status', '=', config('app.pending'))
                ->whereNull('g_requests.deleted_at')
                ->count();
            return $approvedList;
        }
        return 0;
    }

    public function countRejectedListOfReportByCompanyOfPresident()
    {
        $companyShortNameEn = @$_GET['company'];
        if ($companyShortNameEn) {
            $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
            $data = DB
                ::table('g_requests')
                ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                ->where('g_requests.type', '=', config('app.report'))
                ->where('g_requests.review_status', '=', 1)
                ->where('g_requests.company_id', '=', $company->id)
                ->where('g_approvers.approver_id', '=', Auth::id())
                ->where('g_approvers.status', '=', config('app.rejected'))
                ->where('g_requests.status', '=', config('app.rejected'))
                ->whereNull('g_requests.deleted_at')
                ->count();
//                ->get('g_requests.*');

//            dd($data);
            return $data;
        }
        return 0;
    }

    public function countApprovedOfReportByCompanyOfPresident()
    {
        $companyShortNameEn = @$_GET['company'];
        if ($companyShortNameEn) {
            $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
            // Auth as approver
            $data = DB
                ::table('g_requests')
                ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                ->where('g_requests.type', '=', config('app.report'))
                ->where('g_requests.review_status', '=', 1)
                ->where('g_requests.company_id', '=', $company->id)
                ->where('g_approvers.approver_id', '=', Auth::id())
                ->where('g_approvers.status', '=', config('app.approved'))
                ->where('g_requests.status', '=', config('app.approved'))
                ->whereNull('g_requests.deleted_at')
                ->count();

            // Auth as cc
            $data +=
                DB::table('g_requests')
                ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                ->where('g_requests.status', '=', config('app.approved'))
                ->where('g_requests.company_id', '=', $company->id)
                ->where('g_ccs.reviewer_id', '=', Auth::id())
                ->whereNull('g_requests.deleted_at')
                ->count();
            return $data;
        }
        return 0;
    }

    public function getToApproveListOfReportEachDepartmentByCompanyPresident($company = null, $type = null)
    {
        $request = \request();
        $menu = $request->menu;
        $companyShortNameEn = $request->company;
        $requestType = $request->type;
        $department = $request->department;
        $tags = '';//$request->tags;

        if ($companyShortNameEn && $requestType == config('app.report')) {
            $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
            $companyDepartment = DB::table('company_departments')
                                ->where('company_id', '=', $company->id)
                                ->whereNull('deleted_at')
                                ->get();
            foreach ($companyDepartment as $key => $item) {

                $companyDepartment[$key]->active = ($item->short_name == $department) ? 1 : 0;
                $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name&tags=$tags");

                $companyDepartment[$key]->total = DB::table('g_requests')
                    ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                    ->where('g_requests.type', '=', config('app.report'))
                    ->where('g_requests.review_status', '=', 1)
                    ->where('g_requests.status', '=', config('app.pending'))
                    ->where('g_requests.company_id', '=', $company->id)
                    ->where('g_requests.department_id', '=', $item->id);
                if(config('app.is_use_group_support') == 1) {
                    $user_group = @SettingGroupSupport::where('name', 'user_group')->first()->value;
                    $companyDepartment[$key]->total = $companyDepartment[$key]->total     
                        ->whereNotIn('g_requests.user_id', $user_group);
                }
                $companyDepartment[$key]->total = $companyDepartment[$key]->total 
                    ->where('g_approvers.approver_id', '=', Auth::id())
                    ->where('g_approvers.status', '=', config('app.pending'))
                    ->whereNull('g_requests.deleted_at')
                    ->count();
            }
            return $companyDepartment;
        }
        return 0;
    }

    public function getRejectedListOfReportEachDepartmentByCompanyPresident()
    {
        $request = \request();
        $menu = $request->menu;
        $companyShortNameEn = $request->company;
        $requestType = $request->type;
        $department = $request->department;
        $tags = '';//$request->tags;

        if ($companyShortNameEn && $requestType == config('app.report')) {
            $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
            $companyDepartment = DB::table('company_departments')
                                ->where('company_id', '=', $company->id)
                                ->whereNull('deleted_at')
                                ->get();
            foreach ($companyDepartment as $key => $item) {

                $companyDepartment[$key]->active = ($item->short_name == $department) ? 1 : 0;
                $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name&tags=$tags");


                $companyDepartment[$key]->total = DB
                    ::table('g_requests')
                    ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                    ->where('g_requests.type', '=', config('app.report'))
                    ->where('g_requests.review_status', '=', 1)
                    ->where('g_requests.status', '=', config('app.rejected'))
                    ->where('g_requests.company_id', '=', $company->id)
                    ->where('g_requests.department_id', '=', $item->id)

                    ->where('g_approvers.approver_id', '=', Auth::id())
                    ->where('g_approvers.status', '=', config('app.rejected'))
                    ->whereNull('g_requests.deleted_at')
                    ->count();
            }
            return $companyDepartment;
        }
        return 0;
    }

    public function getApprovedListOfReportEachDepartmentByCompanyPresident()
    {
        $request = \request();
        $menu = $request->menu;
        $companyShortNameEn = $request->company;
        $requestType = $request->type;
        $department = $request->department;
        $tags = '';//$request->tags;

        if ($companyShortNameEn && $requestType == config('app.report')) {
            $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
            $companyDepartment = DB::table('company_departments')
                                ->where('company_id', '=', $company->id)
                                ->whereNull('deleted_at')
                                ->get();
            foreach ($companyDepartment as $key => $item) {

                $companyDepartment[$key]->active = ($item->short_name == $department) ? 1 : 0;
                $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name&tags=$tags");
                // Auth as approver
                $data = DB
                    ::table('g_requests')
                    ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                    ->where('g_requests.type', '=', config('app.report'))
                    ->where('g_requests.review_status', '=', 1)
                    ->where('g_requests.status', '=', config('app.approved'))
                    ->where('g_requests.company_id', '=', $company->id)
                    ->where('g_requests.department_id', '=', $item->id)
                    ->where('g_approvers.approver_id', '=', Auth::id())
                    ->where('g_approvers.status', '=', config('app.approved'))
                    ->whereNull('g_requests.deleted_at')
                    ->count();

                // Auth as cc
                $data +=
                    DB::table('g_requests')
                    ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                    ->where('g_requests.status', '=', config('app.approved'))
                    ->where('g_requests.company_id', '=', $company->id)
                    ->where('g_requests.department_id', '=', $item->id)
                    ->where('g_ccs.reviewer_id', '=', Auth::id())
                    ->whereNull('g_requests.deleted_at')
                    ->count();

                $companyDepartment[$key]->total = $data;
            }
            return $companyDepartment;
        }
        return 0;
    }

//    public function getToApproveListOfReportEachTagsByCompanyPresident()
//    {
//        $companyShortNameEn = @$_GET['company'];
//        $requestType = @$_GET['type'];
//
//        if ($companyShortNameEn && $requestType == config('app.report')) {
//            $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
//            $settingTags = config('app.tags');
//            if (@$_GET['department']) {
//                $companyDepartmentShortName = @$_GET['department'];
//                $companyDepartment = DB
//                    ::table('company_departments')
//                    ->where('company_id', '=', $company->id)
//                    ->where('short_name', '=', $companyDepartmentShortName)
//                    ->first();
//            }
//            foreach ($settingTags as $key => $item) {
//                $data = DB
//                    ::table('g_requests')
//                    ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
//                    ->where('g_requests.type', '=', config('app.report'))
//                    ->where('g_requests.review_status', '=', 1)
//                    ->where('g_requests.status', '=', config('app.pending'))
//                    ->where('g_requests.company_id', '=', $company->id);
//                if (@$companyDepartment) {
//                    $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
//                }
//                $data = $data
//                    ->where('g_requests.tags', '=', $item->slug)
//
//                    ->where('g_approvers.approver_id', '=', Auth::id())
//                    ->where('g_approvers.status', '=', config('app.pending'))
//                    ->count();
//
//
//                $settingTags[$key]->total = $data;
//            }
//            return $settingTags;
//        }
//        return 0;
//    }
    public function presidentGetTagsList($menu = null)
    {
        $request = \request();
        $menu = $menu ? $menu : $request->menu;
        $companyShortNameEn = $request->company;
        $requestType = $request->type;
        $department = $request->department;
        $tags = $request->tags;

        if ($companyShortNameEn && $requestType == config('app.report')) {
            $settingTags = config('app.tags');

            //todo: to approve list
            if ($menu == 'toapprove' || $menu == 'to_approve_report') {

                $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                    ->where('company_id', '=', $company->id)
                    ->where('short_name', '=', $department)->first();
                foreach ($settingTags as $key => $item) {
                    $settingTags[$key]->active = ($item->slug == $tags) ? 1 : 0;
                    $settingTags[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$item->slug");

                    $data = DB::table('g_requests')
                        ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.review_status', '=', 1);
                    if(config('app.is_use_group_support') == 1) {
                        $user_group = @SettingGroupSupport::where('name', 'user_group')->first()->value;
                        $data = $data->whereNotIn('g_requests.user_id', $user_group);
                    }
                    $data = $data
                        ->where('g_approvers.approver_id', '=', Auth::id())
                        ->where('g_approvers.status', '=', config('app.pending'))
                        ->whereNull('g_requests.deleted_at')
                        ->where('g_requests.tags', '=', $item->slug);
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total = $data;
                }
                return $settingTags;
            }

            //todo: Reject list
            if ($menu == 'reject') {
                $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                    ->where('company_id', '=', $company->id)
                    ->where('short_name', '=', $department)->first();
                foreach ($settingTags as $key => $item) {
                    $settingTags[$key]->active = ($item->slug == $tags) ? 1 : 0;
                    $settingTags[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$item->slug");

                    // Auth as Approve
                    $data = DB
                        ::table('g_requests')
                        ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                        ->where('g_approvers.status', '=', config('app.rejected'))
                        ->where('g_requests.type', '=', config('app.report'))
//                        ->where('g_requests.user_id', '=', Auth::id())
                        ->where('g_requests.status', '=', config('app.rejected'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.tags', '=', $item->slug)
                        ->where('g_approvers.approver_id', '=', Auth::id())
                        ->whereNull('g_requests.deleted_at')

                    ;
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total = $data;

                }
                return $settingTags;
            }

            //todo: Approved list
            if ($menu == 'approved') {
                $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                    ->where('company_id', '=', $company->id)
                    ->where('short_name', '=', $department)->first();
                foreach ($settingTags as $key => $item) {
                    $settingTags[$key]->active = ($item->slug == $tags) ? 1 : 0;
                    $settingTags[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$item->slug");

                    // Auth as Approver
                    $data = DB
                        ::table('g_requests')
                        ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_approvers.approver_id', '=', Auth::id())
                        ->where('g_requests.tags', '=', $item->slug)
                        ->whereNull('g_requests.deleted_at')
                    ;
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total = $data;

                    // Auth as CC
                    $data1 = DB
                        ::table('g_requests')
                        ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_ccs.reviewer_id', '=', Auth::id())
                        ->where('g_requests.tags', '=', $item->slug)
                        ->whereNull('g_requests.deleted_at')
                    ;
                    if ($companyDepartment) {
                        $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data1 = $data1->count();
                    $settingTags[$key]->total += $data1;
                }
                return $settingTags;
            }
        }
        return 0;
    }

    public function getGroupSupportDepartmentList($menu = null)
    {
        $request = \request();
        $menu = $menu ? $menu : $request->menu;
        $department = $request->department;
        $group_support = @SettingGroupSupport::where('name', 'user_group')->first();
        $companyDepartment = DB::table('departments')
            ->select([
                'id',
                'name_en',
                'short_name'
            ])
            ->whereNull('deleted_at')
            ->whereIn('id', $group_support->department)
            ->get();

        foreach ($companyDepartment as $key => $val) {
            $companyDepartment[$key]->active = ($val->short_name == $department) ? 1 : 0;
            $companyDepartment[$key]->link = URL::to("/$menu?department=$val->short_name");
            $user_group = $group_support->value;

            $comDepart = DB::table('company_departments')
            ->select([
                'id',
                'short_name'
            ])
            ->where('department_id', $val->id)
            ->whereNull('deleted_at')
            ->get();
            if ($comDepart) {
                $i = 0;
                foreach ($comDepart as $item) {
                    $sql = DB::table('g_requests')
                        ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.review_status', '=', 1)
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.department_id', $item->id)
                        ->whereIn('g_requests.user_id', $user_group)
                        ->where('g_approvers.approver_id', '=', Auth::id())
                        ->where('g_approvers.status', '=', config('app.pending'))
                        ->whereNull('g_requests.deleted_at')
                        ->count();
                    $i = $i + $sql;
                }
                $companyDepartment[$key]->total = $i;
            }
        }
        return $companyDepartment;
    }

    public function getGroupSupportTagList($menu = null)
    {
        $request = \request();
        $menu = $menu ? $menu : $request->menu;
        $tags = $request->tags;
        $settingTags = config('app.tags');
        $department = $request->department;
        $group_support = @SettingGroupSupport::where('name', 'user_group')->first();
        $companyDepartment = DB::table('company_departments')
            ->select([
                'id',
                'name_en',
                'short_name'
            ])
            ->whereNull('deleted_at')
            ->where('short_name', $department)
            ->get();
        if ($menu == 'to_approve_group_support') {
            foreach ($settingTags as $key => $item) {
                $settingTags[$key]->active = ($item->slug == $tags) ? 1 : 0;
                $settingTags[$key]->link = URL::to("/$menu?department=$department&tags=$item->slug");

                $data = DB::table('g_requests')
                    ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                    ->select('g_requests->id')
                    ->whereIn('g_requests.user_id', $group_support->value)
                    ->where('g_requests.type', '=', config('app.report'))
                    ->where('g_requests.status', '=', config('app.pending'))
                    ->where('g_requests.review_status', '=', 1)
                    ->where('g_approvers.approver_id', '=', Auth::id())
                    ->where('g_approvers.status', '=', config('app.pending'))
                    ->whereNull('g_requests.deleted_at');
                    $data = $data->where('g_requests.tags', '=', $item->slug);
                    if ($companyDepartment->count() > 0) {
                        $com_dep = [];
                        foreach ($companyDepartment as $val) {
                            $com_dep[] = $val->id;
                        }
                        $data = $data->whereIn('g_requests.department_id', $com_dep);
                    }
                $data = $data->count();
                $settingTags[$key]->total = $data;
            }
            //dd(@$settingTags);
            return $settingTags;
        }

        return 0;
    }

    private function management(){}
    public function departmentLisOfReportMenuForManagement()
    {
        $request = \request();
        $menu = $request->menu;
        $companyShortNameEn = $request->company;
        $requestType = $request->type;
        $department = $request->department;
        $tags = $request->tags;

        if ($companyShortNameEn && $requestType == config('app.report')) {

            //todo: pending list
            if ($menu == 'pending') {
                $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                                    ->where('company_id', '=', $company->id)
                                    ->whereNull('deleted_at')
                                    ->get();
                foreach ($companyDepartment as $key => $item) {
                    $companyDepartment[$key]->active = ($item->short_name == $department) ? 1 : 0;
                    // $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name&tags=$tags");
                    $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name");

                    // Auth as Requester
                    $companyDepartment[$key]->total = DB
                        ::table('g_requests')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.user_id', '=', Auth::id())
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)
                        ->whereNull('g_requests.deleted_at')
                        ->count();

                    // Auth as Reviewer
                    $companyDepartment[$key]->total += DB
                        ::table('g_requests')
                        ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)

                        ->where('g_reviewers.reviewer_id', '=', Auth::id())
                        ->where('g_reviewers.status', '=', config('app.approved'))
                        ->whereNull('g_requests.deleted_at')
                        ->count();
                }
                return $companyDepartment;
            }

            //todo: to approve list
            if ($menu == 'toapprove') {
                $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                                    ->where('company_id', '=', $company->id)
                                    ->whereNull('deleted_at')
                                    ->get();
                foreach ($companyDepartment as $key => $item) {
                    $companyDepartment[$key]->active = ($item->short_name == $department) ? 1 : 0;
                    // $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name&tags=$tags");
                    $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name");

                    $companyDepartment[$key]->total = DB
                        ::table('g_requests')
                        ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        // ->where('g_requests.review_status', '=', 0)
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)

                        ->where('g_reviewers.reviewer_id', '=', Auth::id())
                        ->where('g_reviewers.status', '=', config('app.pending'))
                        ->whereNull('g_requests.deleted_at')
                        ->count();

                    $companyDepartment[$key]->total += DB
                        ::table('g_requests')
                        ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)

                        ->where('g_approvers.approver_id', '=', Auth::id())
                        ->where('g_approvers.status', '=', config('app.pending'))
                        // ->where('g_requests.review_status', '=', 1)
                        ->whereNull('g_requests.deleted_at')
                        ->count();
                }
                return $companyDepartment;
            }

            //todo: Reject list
            if ($menu == 'reject') {
                $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                                    ->where('company_id', '=', $company->id)
                                    ->whereNull('deleted_at')
                                    ->get();
                foreach ($companyDepartment as $key => $item) {
                    $companyDepartment[$key]->active = ($item->short_name == $department) ? 1 : 0;
                    // $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name&tags=$tags");
                    $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name");

                    // Auth as Requester
                    $companyDepartment[$key]->total = 0 ;
                    $companyDepartment[$key]->total += DB
                        ::table('g_requests')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.user_id', '=', Auth::id())
                        ->where('g_requests.status', '=', config('app.rejected'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)
                        ->whereNull('g_requests.deleted_at')
                        ->count();

                    // Auth as Reviewer
                    $companyDepartment[$key]->total += DB
                        ::table('g_requests')
                        ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.rejected'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)

                        ->where('g_reviewers.reviewer_id', '=', Auth::id())
                        ->whereNull('g_requests.deleted_at')
                        ->count();

                    // Auth as Approver
                    $companyDepartment[$key]->total += DB
                        ::table('g_requests')
                        ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.rejected'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)

                        ->where('g_approvers.approver_id', '=', Auth::id())
                        ->where('g_approvers.status', '=', config('app.rejected'))
                        ->where('g_requests.review_status', '=', 1)
                        ->whereNull('g_requests.deleted_at')
                        ->count();
                }
                return $companyDepartment;
            }

            //todo: Approved list
            if ($menu == 'approved') {
                $company = DB::table('companies')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                                    ->where('company_id', '=', $company->id)
                                    ->whereNull('deleted_at')
                                    ->get();
                foreach ($companyDepartment as $key => $item) {
                    $companyDepartment[$key]->active = ($item->short_name == $department) ? 1 : 0;
                    // $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name&tags=$tags");
                    $companyDepartment[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$item->short_name");

                    // Auth as Requester
                    $companyDepartment[$key]->total = DB
                        ::table('g_requests')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.user_id', '=', Auth::id())
                        ->where('g_requests.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)
                        ->whereNull('g_requests.deleted_at')
                        ->count();

                    // Auth as Reviewer
                    $companyDepartment[$key]->total += DB
                        ::table('g_requests')
                        ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)

                        ->where('g_reviewers.reviewer_id', '=', Auth::id())
                        ->whereNull('g_requests.deleted_at')
                        ->count();

                    // Auth as approver
                    $companyDepartment[$key]->total += DB
                        ::table('g_requests')
                        ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)

                        ->where('g_approvers.approver_id', '=', Auth::id())
                        ->whereNull('g_requests.deleted_at')
                        ->count();

                    // Auth as cc
                    $companyDepartment[$key]->total += DB
                        ::table('g_requests')
                        ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.department_id', '=', $item->id)

                        ->where('g_ccs.reviewer_id', '=', Auth::id())
                        ->whereNull('g_requests.deleted_at')
                        ->count();
                }
                return $companyDepartment;
            }
        }
        return 0;
    }
    public function tagsListOfReportForManagement()
    {
        $request = \request();
        $menu = $request->menu;
        $companyShortNameEn = $request->company;
        $requestType = $request->type;
        $department = $request->department;
        $tags = $request->tags;

        if ($companyShortNameEn && $requestType == config('app.report')) {
            $settingTags = config('app.tags');

            //todo: pending list
            if ($menu == 'pending') {
                $company = DB::table('companies')->select('id')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                    ->select('id')
                    ->where('company_id', '=', $company->id)
                    ->where('short_name', '=', $department)->first();
                foreach ($settingTags as $key => $item) {
                    $settingTags[$key]->active = ($item->slug == $tags) ? 1 : 0;
                    $settingTags[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$item->slug");

                    // Auth as Requester
                    $data = DB
                        ::table('g_requests')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.user_id', '=', Auth::id())
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->whereNull('g_requests.deleted_at')
                        ->where('g_requests.tags', '=', $item->slug);
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total = $data;

                    // Auth as Reviewer
                    $data = DB
                        ::table('g_requests')
                        ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.company_id', '=', $company->id)

                        ->where('g_reviewers.reviewer_id', '=', Auth::id())
                        ->where('g_reviewers.status', '=', config('app.approved'))
                        ->whereNull('g_requests.deleted_at')
                        ->where('g_requests.tags', '=', $item->slug);

                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total += $data;
                }
                return $settingTags;
            }

            //todo: to approve list
            if ($menu == 'toapprove') {
                $company = DB::table('companies')->select('id')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                    ->select('id')
                    ->where('company_id', '=', $company->id)
                    ->where('short_name', '=', $department)->first();
                foreach ($settingTags as $key => $item) {
                    $settingTags[$key]->active = ($item->slug == $tags) ? 1 : 0;
                    $settingTags[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$item->slug");

                    $data = DB
                        ::table('g_requests')
                        ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.company_id', '=', $company->id)

                        ->where('g_reviewers.reviewer_id', '=', Auth::id())
                        ->where('g_reviewers.status', '=', config('app.pending'))
                        ->whereNull('g_requests.deleted_at')
                        ->where('g_requests.tags', '=', $item->slug);
                    ;
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total = $data;

                    $data1 = DB
                        ::table('g_requests')
                        ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.company_id', '=', $company->id)

                        ->where('g_approvers.approver_id', '=', Auth::id())
                        ->where('g_approvers.status', '=', config('app.pending'))
                        ->where('g_requests.tags', '=', $item->slug)
                        ->whereNull('g_requests.deleted_at')
                        // ->where('g_requests.review_status', '=', 1)
                    ;
                    if ($companyDepartment) {
                        $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data1 = $data1->count();
                    $settingTags[$key]->total += $data1;
                }
                return $settingTags;
            }

            //todo: Reject list
            if ($menu == 'reject') {
                $company = DB::table('companies')->select('id')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                    ->select('id')
                    ->where('company_id', '=', $company->id)
                    ->where('short_name', '=', $department)->first();
                foreach ($settingTags as $key => $item) {
                    $settingTags[$key]->active = ($item->slug == $tags) ? 1 : 0;
                    $settingTags[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$item->slug");

                    // Auth as Requester
                    $data = DB
                        ::table('g_requests')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.user_id', '=', Auth::id())
                        ->where('g_requests.status', '=', config('app.rejected'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.tags', '=', $item->slug)
                        ->whereNull('g_requests.deleted_at')
                    ;
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total = $data;

                    // Auth as Reviewer
                    $data = DB
                        ::table('g_requests')
                        ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.rejected'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.tags', '=', $item->slug)
                        ->where('g_reviewers.reviewer_id', '=', Auth::id())
                        ->whereNull('g_requests.deleted_at')
                        ;
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total += $data;
                }
                return $settingTags;
            }

            //todo: Approved list
            if ($menu == 'approved') {
                $company = DB::table('companies')->select('id')->where('short_name_en', '=', $companyShortNameEn)->first();
                $companyDepartment = DB::table('company_departments')
                    ->select('id')
                    ->where('company_id', '=', $company->id)
                    ->where('short_name', '=', $department)->first();
                foreach ($settingTags as $key => $item) {
                    $settingTags[$key]->active = ($item->slug == $tags) ? 1 : 0;
                    $settingTags[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$item->slug");

                    // Auth as Requester
                    $data = DB
                        ::table('g_requests')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.user_id', '=', Auth::id())
                        ->where('g_requests.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.tags', '=', $item->slug)
                        ->whereNull('g_requests.deleted_at')
                    ;
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total = $data;

                    // Auth as Reviewer
                    $data = DB
                        ::table('g_requests')
                        ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_reviewers.reviewer_id', '=', Auth::id())
                        ->where('g_requests.tags', '=', $item->slug)
                        ->whereNull('g_requests.deleted_at')
                    ;
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total += $data;

                    // Auth as Approver
                    $data = DB
                        ::table('g_requests')
                        ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_approvers.approver_id', '=', Auth::id())
                        ->where('g_requests.tags', '=', $item->slug)
                        ->whereNull('g_requests.deleted_at')
                    ;
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total += $data;

                    // Auth as CC
                    $data = DB
                        ::table('g_requests')
                        ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                        ->select('g_requests.id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_ccs.reviewer_id', '=', Auth::id())
                        ->where('g_requests.tags', '=', $item->slug)
                        ->whereNull('g_requests.deleted_at')
                    ;
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingTags[$key]->total += $data;
                }
                return $settingTags;
            }
        }
        return 0;
    }


    public function groupListOfReport()
    {
        $request = \request();
        $menu = $request->menu;
        $companyShortNameEn = $request->company;
        $requestType = $request->type;
        $department = $request->department;
        $tags = $request->tags;
        $groups = $request->groups;

            if ($companyShortNameEn && $requestType == config('app.report')) {

                //todo: Approved list
                if ($menu == 'approved') {
                    $company = DB::table('companies')->select('id')->where('short_name_en', '=', $companyShortNameEn)->first();
                    $settingGroup = GroupRequestTemplate::select('id', 'name')
                        ->where('company_id', '=', $company->id)
                        ->whereNull('deleted_at')
                        ->get();
                    $companyDepartment = DB::table('company_departments')
                        ->select('id')
                        ->where('company_id', '=', $company->id)
                        ->where('short_name', '=', $department)->first();
                    foreach ($settingGroup as $key => $item) {
                        $settingGroup[$key]->active = ($item->id == $groups) ? 1 : 0;
                        $settingGroup[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$tags&groups=$item->id");

                        // Auth as Approver
                        $data = DB::table('g_requests')
                            ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_approvers.approver_id', '=', Auth::id())
                            ->where('g_requests.template_id', '=', $item->id)
                            ->whereNull('g_requests.deleted_at');
                        if ($tags) {
                            $data = $data->where('g_requests.tags', '=', $tags);
                        }
                        if ($companyDepartment) {
                            $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                        }
                        $data = $data->count();
                        $settingGroup[$key]->total += $data;

                        // Auth as CC
                        $data1 = DB::table('g_requests')
                            ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_ccs.reviewer_id', '=', Auth::id())
                            ->where('g_requests.template_id', '=', $item->id)
                            ->whereNull('g_requests.deleted_at')
                        ;
                        if ($tags) {
                            $data1 = $data1->where('g_requests.tags', '=', $tags);
                        }
                        if ($companyDepartment) {
                            $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                        }
                        $data1 = $data1->count();
                        $settingGroup[$key]->total += $data1;
                    }

                    // start calculate other
                    // Auth as Approver
                    $data1 = DB::table('g_request_templates')
                            ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                            ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_approvers.approver_id', '=', Auth::id())
                            ->whereNotNull('g_request_templates.deleted_at')
                            ->whereNull('g_requests.deleted_at');
                    if ($tags) {
                        $data1 = $data1->where('g_requests.tags', '=', $tags);
                    }
                    if ($companyDepartment) {
                        $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $total = $data1->count();

                    // end calculate other 

                    $settingGroup2 = collect([
                        [
                            "id"        => 0,
                            "name"      => "Other",
                            "active"    => ($groups == "Other") ? 1 : 0,
                            "link"      => URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$tags&groups=Other"),
                            "total"     => $total
                        ]
                    ]);

                    // to do show other
                    $settingGroup = $settingGroup->concat($settingGroup2);

                    return $settingGroup;
                }

                //todo: to approve list
                if ($menu == 'to_approve_report') {
                    $company = DB::table('companies')->select('id')->where('short_name_en', '=', $companyShortNameEn)->first();
                    $settingGroup = GroupRequestTemplate::select('id', 'name')
                        ->where('company_id', '=', $company->id)
                        ->whereNull('deleted_at')
                        ->get();
                    $companyDepartment = DB::table('company_departments')
                        ->select('id')
                        ->where('company_id', '=', $company->id)
                        ->where('short_name', '=', $department)->first();
                    foreach ($settingGroup as $key => $item) {
                        $settingGroup[$key]->active = ($item->id == $groups) ? 1 : 0;
                        $settingGroup[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$tags&groups=$item->id");

                        // Auth as Approver
                        $data = DB::table('g_requests')
                            ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.pending'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_requests.review_status', '=', 1)
                            ->where('g_approvers.approver_id', '=', Auth::id())
                            ->where('g_approvers.status', '=', config('app.pending'))
                            ->where('g_requests.template_id', '=', $item->id)
                            ->whereNull('g_requests.deleted_at');
                        if(config('app.is_use_group_support') == 1) {
                            $user_group = @SettingGroupSupport::where('name', 'user_group')->first()->value;
                            $data = $data->whereNotIn('g_requests.user_id', $user_group);
                        }
                        if ($tags) {
                            $data = $data->where('g_requests.tags', '=', $tags);
                        }
                        if ($companyDepartment) {
                            $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                        }
                        $data = $data->count();
                        $settingGroup[$key]->total = $data;
                    }

                    // start calculate other
                    // Auth as Approver
                    $data1 = DB::table('g_request_templates')
                        ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                        ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                        ->select('id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_requests.review_status', '=', 1)
                        ->where('g_approvers.approver_id', '=', Auth::id())
                        ->where('g_approvers.status', '=', config('app.pending'))
                        ->whereNotNull('g_request_templates.deleted_at')
                        ->whereNull('g_requests.deleted_at');
                    if(config('app.is_use_group_support') == 1) {
                        $user_group = @SettingGroupSupport::where('name', 'user_group')->first()->value;
                        $data1 = $data1->whereNotIn('g_requests.user_id', $user_group);
                    }
                    if ($tags) {
                        $data1 = $data1->where('g_requests.tags', '=', $tags);
                    }
                    if ($companyDepartment) {
                        $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                    }

                    $total = $data1->count();
                    // end calculate other 

                    $settingGroup2 = collect([
                        [
                            "id"        => 0,
                            "name"      => "Other",
                            "active"    => ($groups == "Other") ? 1 : 0,
                            "link"      => URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$tags&groups=Other"),
                            "total"     => $total
                        ]
                    ]);

                    // to do show other
                    $settingGroup = $settingGroup->concat($settingGroup2);

                    return $settingGroup;
                }
            }
            
        return 0;
    }

    public function getGroupSupportGroupList()
    {
        $request = \request();
        $menu = $request->menu;
        $tags = $request->tags;
        $groups = $request->groups;
        $department = $request->department;
        $group_support = @SettingGroupSupport::where('name', 'user_group')->first();
        $companyDepartment = DB::table('company_departments')
            ->select([
                'id',
                'name_en',
                'short_name'
            ])
            ->whereNull('deleted_at')
            ->where('short_name', $department)
            ->get();
        if ($menu == 'to_approve_group_support') {
            $settingGroup = GroupRequestTemplate::select('id', 'name')
                            ->whereNull('deleted_at')
                            ->get();
            foreach ($settingGroup as $key => $item) {
                $settingGroup[$key]->active = ($item->id == $groups) ? 1 : 0;
                $settingGroup[$key]->link = URL::to("/$menu?department=$department&tags=$tags&groups=$item->id");

                // Auth as Approver
                $data = DB::table('g_requests')
                    ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                    ->select('id')
                    ->whereIn('g_requests.user_id', $group_support->value)
                    ->where('g_requests.type', '=', config('app.report'))
                    ->where('g_requests.status', '=', config('app.pending'))
                    ->where('g_requests.review_status', '=', 1)
                    ->where('g_approvers.approver_id', '=', Auth::id())
                    ->where('g_approvers.status', '=', config('app.pending'))
                    ->where('g_requests.template_id', '=', $item->id)
                    ->whereNull('g_requests.deleted_at');
                if ($tags) {
                    $data = $data->where('g_requests.tags', '=', $tags);
                }
                if ($companyDepartment->count() > 0) {
                        $com_dep = [];
                        foreach ($companyDepartment as $val) {
                            $com_dep[] = $val->id;
                        }
                        $data = $data->whereIn('g_requests.department_id', $com_dep);
                    }
                $data = $data->count();
                $settingGroup[$key]->total = $data;
            }

            // start calculate other
            // Auth as Approver
            $data1 = DB::table('g_request_templates')
                    ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                    ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                    ->select('id')
                    ->whereIn('g_requests.user_id', $group_support->value)
                    ->where('g_requests.type', '=', config('app.report'))
                    ->where('g_requests.status', '=', config('app.pending'))
                    ->where('g_requests.review_status', '=', 1)
                    ->where('g_approvers.approver_id', '=', Auth::id())
                    ->where('g_approvers.status', '=', config('app.pending'))
                    ->whereNotNull('g_request_templates.deleted_at')
                    ->whereNull('g_requests.deleted_at');
            if ($tags) {
                $data1 = $data1->where('g_requests.tags', '=', $tags);
            }
            if ($companyDepartment->count() > 0) {
                $com_dep = [];
                foreach ($companyDepartment as $val) {
                    $com_dep[] = $val->id;
                }
                $data1 = $data1->whereIn('g_requests.department_id', $com_dep);
            }
            $total = $data1->count();
            // end calculate other 

            $settingGroup2 = collect([
                [
                    "id"        => 0,
                    "name"      => "Other",
                    "active"    => ($groups == "Other") ? 1 : 0,
                    "link"      => URL::to("/$menu?department=$department&tags=$tags&groups=Other"),
                    "total"     => $total
                ]
            ]);

            // to do show other
            $settingGroup = $settingGroup->concat($settingGroup2);

            return $settingGroup;
        }
            
        return 0;
    }


    public function groupListOfReportForManagerment()
    {
        $request = \request();
        $menu = $request->menu;
        $companyShortNameEn = $request->company;
        $requestType = $request->type;
        $department = $request->department;
        $tags = $request->tags;
        $groups = $request->groups;

            if ($companyShortNameEn && $requestType == config('app.report')) {

                //todo: Approved list
                if ($menu == 'approved') {
                    $company = DB::table('companies')->select('id')->where('short_name_en', '=', $companyShortNameEn)->first();
                    $settingGroup = GroupRequestTemplate::select('id', 'name')
                                    ->where('company_id', '=', $company->id)
                                    ->whereNull('deleted_at')
                                    ->get();
                    $companyDepartment = DB::table('company_departments')
                        ->select('id')
                        ->where('company_id', '=', $company->id)
                        ->where('short_name', '=', $department)->first();
                    foreach ($settingGroup as $key => $item) {
                        $settingGroup[$key]->active = ($item->id == $groups) ? 1 : 0;
                        $settingGroup[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$tags&groups=$item->id");

                        // Auth as Requester
                        $data = DB
                            ::table('g_requests')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.user_id', '=', Auth::id())
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_requests.template_id', '=', $item->id)
                            ->whereNull('g_requests.deleted_at')
                        ;
                        if ($tags) {
                            $data = $data->where('g_requests.tags', '=', $tags);
                        }
                        if ($companyDepartment) {
                            $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                        }
                        $data = $data->count();
                        $settingGroup[$key]->total = $data;

                        // Auth as Reviewer
                        $data1 = DB
                            ::table('g_requests')
                            ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_reviewers.reviewer_id', '=', Auth::id())
                            ->where('g_requests.template_id', '=', $item->id)
                            ->whereNull('g_requests.deleted_at')
                        ;
                        if ($tags) {
                            $data1 = $data1->where('g_requests.tags', '=', $tags);
                        }
                        if ($companyDepartment) {
                            $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                        }
                        $data1 = $data1->count();
                        $settingGroup[$key]->total += $data1;

                        // Auth as Approver
                        $data2 = DB
                            ::table('g_requests')
                            ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_approvers.approver_id', '=', Auth::id())
                            ->where('g_requests.template_id', '=', $item->id)
                            ->whereNull('g_requests.deleted_at')
                        ;
                        if ($tags) {
                            $data2 = $data2->where('g_requests.tags', '=', $tags);
                        }
                        if ($companyDepartment) {
                            $data2 = $data2->where('g_requests.department_id', '=', $companyDepartment->id);
                        }
                        $data2 = $data2->count();
                        $settingGroup[$key]->total += $data2;

                        // Auth as CC
                        $data3 = DB
                            ::table('g_requests')
                            ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_ccs.reviewer_id', '=', Auth::id())
                            ->where('g_requests.template_id', '=', $item->id)
                            ->whereNull('g_requests.deleted_at')
                        ;
                        if ($tags) {
                            $data3 = $data3->where('g_requests.tags', '=', $tags);
                        }
                        if ($companyDepartment) {
                            $data3 = $data3->where('g_requests.department_id', '=', $companyDepartment->id);
                        }
                        $data3 = $data3->count();
                        $settingGroup[$key]->total += $data3;
                    }

                    // start calculate other 
                    // Auth as Requester
                    $data = DB::table('g_request_templates')
                            ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.user_id', '=', Auth::id())
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->whereNotNull('g_request_templates.deleted_at')
                            ->whereNull('g_requests.deleted_at')
                    ;

                    if ($tags) {
                        $data = $data->where('g_requests.tags', '=', $tags);
                    }
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();

                    // Auth as Reviewer
                    $data1 = DB::table('g_request_templates')
                            ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                            ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_reviewers.reviewer_id', '=', Auth::id())
                            ->whereNotNull('g_request_templates.deleted_at')
                            ->whereNull('g_requests.deleted_at')
                    ;
                    if ($tags) {
                        $data1 = $data1->where('g_requests.tags', '=', $tags);
                    }
                    if ($companyDepartment) {
                        $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data1 = $data1->count();
                    

                    // Auth as Approver
                    $data2 = DB::table('g_request_templates')
                            ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                            ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_approvers.approver_id', '=', Auth::id())
                            ->whereNotNull('g_request_templates.deleted_at')
                            ->whereNull('g_requests.deleted_at')
                    ;
                    if ($tags) {
                        $data2 = $data2->where('g_requests.tags', '=', $tags);
                    }
                    if ($companyDepartment) {
                        $data2 = $data2->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data2 = $data2->count();


                    // Auth as CC
                    $data3 = DB::table('g_request_templates')
                            ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                            ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.approved'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_ccs.reviewer_id', '=', Auth::id())
                            ->whereNotNull('g_request_templates.deleted_at')
                            ->whereNull('g_requests.deleted_at')
                    ;
                    if ($tags) {
                        $data3 = $data3->where('g_requests.tags', '=', $tags);
                    }
                    if ($companyDepartment) {
                        $data3 = $data3->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data3 = $data3->count();


                    $total = $data + $data1 + $data2 + $data3;
                    // end calculate other 

                    $settingGroup2 = collect([
                        [
                            "id"        => 0,
                            "name"      => "Other",
                            "active"    => ($groups == "Other") ? 1 : 0,
                            "link"      => URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$tags&groups=Other"),
                            "total"     => $total
                        ]
                    ]);

                    // to do show other
                    $settingGroup = $settingGroup->concat($settingGroup2);

                    return $settingGroup;
                }

                //todo: to approve list
                if ($menu == 'to_approve_report' || $menu == 'toapprove') {
                    $company = DB::table('companies')->select('id')->where('short_name_en', '=', $companyShortNameEn)->first();
                    $settingGroup = GroupRequestTemplate::select('id', 'name')
                                    ->where('company_id', '=', $company->id)
                                    ->whereNull('deleted_at')
                                    ->get();
                    $companyDepartment = DB::table('company_departments')
                        ->select('id')
                        ->where('company_id', '=', $company->id)
                        ->where('short_name', '=', $department)->first();
                    foreach ($settingGroup as $key => $item) {
                        $settingGroup[$key]->active = ($item->id == $groups) ? 1 : 0;
                        $settingGroup[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$tags&groups=$item->id");

                        // Auth as Approver
                        $data = DB
                            ::table('g_requests')
                            ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.pending'))
                            ->where('g_requests.company_id', '=', $company->id)
                            // ->where('g_requests.review_status', '=', 1)
                            ->where('g_approvers.approver_id', '=', Auth::id())
                            ->where('g_approvers.status', '=', config('app.pending'))
                            ->where('g_requests.template_id', '=', $item->id)
                            ->whereNull('g_requests.deleted_at')
                        ;
                        if ($tags) {
                            $data = $data->where('g_requests.tags', '=', $tags);
                        }
                        if ($companyDepartment) {
                            $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                        }
                        $data = $data->count();
                        $settingGroup[$key]->total = $data;

                        // Auth as Reviewer
                        $data1 = DB
                            ::table('g_requests')
                            ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.pending'))
                            ->where('g_reviewers.status', '=', config('app.pending'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_reviewers.reviewer_id', '=', Auth::id())
                            ->where('g_requests.template_id', '=', $item->id)
                            ->whereNull('g_requests.deleted_at')
                        ;
                        if ($tags) {
                            $data1 = $data1->where('g_requests.tags', '=', $tags);
                        }
                        if ($companyDepartment) {
                            $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                        }
                        $data1 = $data1->count();
                        $settingGroup[$key]->total += $data1;

                    }

                    // start calculate other 
                    // Auth as Approver
                    $data = DB::table('g_request_templates')
                            ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                            ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.pending'))
                            ->where('g_requests.company_id', '=', $company->id)
                            // ->where('g_requests.review_status', '=', 1)
                            ->where('g_approvers.approver_id', '=', Auth::id())
                            ->where('g_approvers.status', '=', config('app.pending'))
                            ->whereNotNull('g_request_templates.deleted_at')
                            ->whereNull('g_requests.deleted_at')
                        ;
                    if ($tags) {
                        $data = $data->where('g_requests.tags', '=', $tags);
                    }
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();

                    // Auth as Reviewer
                    $data1 = DB::table('g_request_templates')
                            ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                            ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                            ->select('id')
                            ->where('g_requests.type', '=', config('app.report'))
                            ->where('g_requests.status', '=', config('app.pending'))
                            ->where('g_reviewers.status', '=', config('app.pending'))
                            ->where('g_requests.company_id', '=', $company->id)
                            ->where('g_reviewers.reviewer_id', '=', Auth::id())
                            ->whereNotNull('g_request_templates.deleted_at')
                            ->whereNull('g_requests.deleted_at')
                    ;
                    if ($tags) {
                        $data1 = $data1->where('g_requests.tags', '=', $tags);
                    }
                    if ($companyDepartment) {
                        $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data1 = $data1->count();
                    // dd($data1);
                    $total = $data + $data1;
                    // end calculate other 

                    $settingGroup2 = collect([
                        [
                            "id"        => 0,
                            "name"      => "Other",
                            "active"    => ($groups == "Other") ? 1 : 0,
                            "link"      => URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$tags&groups=Other"),
                            "total"     => $total
                        ]
                    ]);

                    // to do show other
                    $settingGroup = $settingGroup->concat($settingGroup2);

                    return $settingGroup;
                }

                //todo: pending list 
            if ($menu == 'pending') {
                $company = DB::table('companies')->select('id')->where('short_name_en', '=', $companyShortNameEn)->first();
                $settingGroup = GroupRequestTemplate::select('id', 'name')
                                ->where('company_id', '=', $company->id)
                                ->whereNull('deleted_at')
                                ->get();
                $companyDepartment = DB::table('company_departments')
                    ->select('id')
                    ->where('company_id', '=', $company->id)
                    ->where('short_name', '=', $department)->first();
                foreach ($settingGroup as $key => $item) {
                    $settingGroup[$key]->active = ($item->id == $groups) ? 1 : 0;
                    $settingGroup[$key]->link = URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$tags&groups=$item->id");

                    // Auth as Requester
                    $data = DB
                        ::table('g_requests')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.user_id', '=', Auth::id())
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_requests.template_id', '=', $item->id)
                        ->where('g_requests.company_id', '=', $company->id)
                        ->whereNull('g_requests.deleted_at')
                    ;
                    if ($tags) {
                        $data = $data->where('g_requests.tags', '=', $tags);
                    }
                    if ($companyDepartment) {
                        $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data = $data->count();
                    $settingGroup[$key]->total = $data;

                    // Auth as Reviewer
                    $data1 = DB
                        ::table('g_requests')
                        ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                        ->select('id')
                        ->where('g_requests.type', '=', config('app.report'))
                        ->where('g_requests.status', '=', config('app.pending'))
                        ->where('g_reviewers.status', '=', config('app.approved'))
                        ->where('g_requests.company_id', '=', $company->id)
                        ->where('g_reviewers.reviewer_id', '=', Auth::id())
                        ->where('g_requests.template_id', '=', $item->id)
                        ->whereNull('g_requests.deleted_at')
                    ;
                    if ($tags) {
                        $data1 = $data1->where('g_requests.tags', '=', $tags);
                    }
                    if ($companyDepartment) {
                        $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                    }
                    $data1 = $data1->count();
                    $settingGroup[$key]->total += $data1;

                }

                // start calculate other 
                // Auth as Requester
                $data = DB::table('g_request_templates')
                    ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                    ->where('g_requests.type', '=', config('app.report'))
                    ->where('g_requests.user_id', '=', Auth::id())
                    ->where('g_requests.status', '=', config('app.pending'))
                    ->where('g_requests.company_id', '=', $company->id)
                    ->whereNotNull('g_request_templates.deleted_at')
                    ->whereNull('g_requests.deleted_at')
                ;
                if ($tags) {
                    $data = $data->where('g_requests.tags', '=', $tags);
                }
                if ($companyDepartment) {
                    $data = $data->where('g_requests.department_id', '=', $companyDepartment->id);
                }
                $data = $data->count();

                // Auth as Reviewer
                $data1 = DB::table('g_request_templates')
                    ->join('g_requests', 'g_requests.template_id', '=', 'g_request_templates.id')
                    ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                    ->select('id')
                    ->where('g_requests.type', '=', config('app.report'))
                    ->where('g_requests.status', '=', config('app.pending'))
                    ->where('g_reviewers.status', '=', config('app.approved'))
                    ->where('g_reviewers.reviewer_id', '=', Auth::id())
                    ->where('g_requests.company_id', '=', $company->id)
                    ->whereNotNull('g_request_templates.deleted_at')
                    ->whereNull('g_requests.deleted_at')
                ;
                if ($tags) {
                    $data1 = $data1->where('g_requests.tags', '=', $tags);
                }
                if ($companyDepartment) {
                    $data1 = $data1->where('g_requests.department_id', '=', $companyDepartment->id);
                }
                $data1 = $data1->count();
                // dd($data1);
                $total = $data + $data1;
                // end calculate other 

                $settingGroup2 = collect([
                    [
                        "id"        => 0,
                        "name"      => "Other",
                        "active"    => ($groups == "Other") ? 1 : 0,
                        "link"      => URL::to("/$menu?company=$companyShortNameEn&type=$requestType&department=$department&tags=$tags&groups=Other"),
                        "total"     => $total
                    ]
                ]);

                // to do show other
                $settingGroup = $settingGroup->concat($settingGroup2);

                return $settingGroup;
            }

        }
            
        return 0;
    }


    public function getPendingReportByDepartment()
    {
        $request = \request();
        $company = $request->company;
        $department = $request->department;
        $tags = $request->tags;

        $company = DB::table('companies')->where('short_name_en', '=', $company)->first();
        $companyDepartment = DB::table('company_departments')
                            ->where('company_id', '=', $company->id)
                            ->whereNull('deleted_at')
                            ->get();

        // Query Param
        $userId = Auth::id();
        $pending = config('app.pending');
        $approved = config('app.approved');
        $companyId = $company->id;
        $cols = $this->listColumn;

        // Auth as Requester Query
        $sql =
            "
            select $cols
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join companies on companies.id = g_requests.company_id
            where g_requests.user_id = $userId

            and g_requests.status = '$pending'
            and g_requests.company_id = $companyId
            and g_requests.deleted_at is null
            ";

        // Auth as Reviewer Query
        $sql1 =
            "
            select $cols
            from g_requests
            join g_reviewers on g_requests.id = g_reviewers.request_id
            join g_approvers on g_requests.id = g_approvers.request_id
            join companies on companies.id = g_requests.company_id
            where g_reviewers.reviewer_id = $userId

            and g_requests.status = '$pending'
            and g_reviewers.status = '$approved'
            and g_requests.company_id = $companyId
            and g_requests.deleted_at is null
            ";

        $data = DB::select(DB::raw($sql.' union '.$sql1));

        // Append reviewers and approve
        foreach($data as $key => $item) {
            $item->reviewers = $this->getReviewersByRequestId($item->id);
            $item->approver = $this->getApproverByRequestId($item->id);
            $item->attachments = json_decode($item->attachments);
        }
        dd($data);
        return $data;
    }

    /**
     * Count pending list request each company for management
     * countPendingListRequestEachCompanyForManagement
     * @return array
     */
    public function countPendingListRequestEachCompanyForManagement()
    {
        $pending = config('app.pending');
        $approved = config('app.approved');
        $data = [];
        $companies = DB::table('companies')->get();
        foreach ($companies as $item) {

            // Auth as requester
            $data[$item->short_name_en] =
                DB::table('g_requests')
                    ->where('g_requests.status', '=', $pending)
                    ->where('g_requests.company_id', '=', $item->id)
                    ->where('g_requests.user_id', '=', Auth::id())
                    ->whereNull('g_requests.deleted_at')
                    ->count();

            // Auth as reviewers
            $data[$item->short_name_en] +=
                DB::table('g_requests')
                    ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                    ->where('g_requests.status', '=', $pending)
                    ->where('g_requests.company_id', '=', $item->id)
                    ->where('g_reviewers.reviewer_id', '=', Auth::id())
                    ->where('g_reviewers.status', '=', $approved)
                    ->whereNull('g_requests.deleted_at')
                    ->count();
        }
        return $data;
    }

    /**
     * Count to approve list request each company for management
     * countToApproveListRequestEachCompanyForManagement
     * @return array
     */
    public function countToApproveListRequestEachCompanyForManagement()
    {
        $pending = config('app.pending');
        $data = [];
        $companies = DB::table('companies')->get();
        foreach ($companies as $item) {
            $data[$item->short_name_en] =
                DB::table('g_requests')
                ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                ->where('g_requests.status', '=', $pending)
                ->where('g_requests.company_id', '=', $item->id)
                ->where('g_reviewers.reviewer_id', '=', Auth::id())
                ->where('g_reviewers.status', '=', $pending)
                ->whereNull('g_requests.deleted_at')
                ->count();
        }
        return $data;
    }

    /**
     * Count rejected list request each company for management
     * countRejectedListRequestEachCompanyForManagement
     * @return array
     */
    public function countRejectedListRequestEachCompanyForManagement()
    {
        $rejected = config('app.rejected');
        $data = [];
        $companies = DB::table('companies')->get();
        foreach ($companies as $item) {

            // Auth as requester
            $data[$item->short_name_en] =
                DB::table('g_requests')
                    ->where('g_requests.status', '=', $rejected)
                    ->where('g_requests.company_id', '=', $item->id)
                    ->where('g_requests.user_id', '=', Auth::id())
                    ->whereNull('g_requests.deleted_at')
                    ->count();

            // Auth as reviewers
            $data[$item->short_name_en] +=
                DB::table('g_requests')
                    ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                    ->where('g_requests.status', '=', $rejected)
                    ->where('g_requests.company_id', '=', $item->id)
                    ->where('g_reviewers.reviewer_id', '=', Auth::id())
                    ->whereNull('g_requests.deleted_at')
                    ->count();
        }
        return $data;
    }

    /**
     * Count rejected list request each company for management
     * countRejectedListRequestEachCompanyForManagement
     * @return array
     */
    public function countApprovedListRequestEachCompanyForManagement()
    {
        $approved = config('app.approved');
        $data = [];
        $companies = DB::table('companies')->get();
        foreach ($companies as $item) {

            // Auth as requester
            $data[$item->short_name_en] =
                DB::table('g_requests')
                    ->where('g_requests.status', '=', $approved)
                    ->where('g_requests.company_id', '=', $item->id)
                    ->where('g_requests.user_id', '=', Auth::id())
                    ->whereNull('g_requests.deleted_at')
                    ->count();

            // Auth as reviewers
            $data[$item->short_name_en] +=
                DB::table('g_requests')
                    ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                    ->where('g_requests.status', '=', $approved)
                    ->where('g_requests.company_id', '=', $item->id)
                    ->where('g_reviewers.reviewer_id', '=', Auth::id())
                    ->whereNull('g_requests.deleted_at')
                    ->count();

            // Auth as approver
            $data[$item->short_name_en] +=
                DB::table('g_requests')
                    ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                    ->where('g_requests.status', '=', $approved)
                    ->where('g_requests.company_id', '=', $item->id)
                    ->where('g_approvers.approver_id', '=', Auth::id())
                    ->whereNull('g_requests.deleted_at')
                    ->count();

            // Auth as cc
            $data[$item->short_name_en] +=
                DB::table('g_requests')
                    ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                    ->where('g_requests.status', '=', $approved)
                    ->where('g_requests.company_id', '=', $item->id)
                    ->where('g_ccs.reviewer_id', '=', Auth::id())
                    ->whereNull('g_requests.deleted_at')
                    ->count();

        }
        return $data;
    }


    /**
     * @param $companyId
     * @return int
     */
    public function countPendingByCompany($companyId)
    {
        $pending = config('app.pending');
        $approved = config('app.approved');
        // Auth as requester
        $data =
            DB::table('g_requests')
                ->where('g_requests.status', '=', $pending)
                ->where('g_requests.company_id', '=', $companyId)
                ->where('g_requests.user_id', '=', Auth::id())
                ->whereNull('g_requests.deleted_at')
                ->count();

        // Auth as reviewers
        $data +=
            DB::table('g_requests')
                ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                ->where('g_requests.status', '=', $pending)
                ->where('g_requests.company_id', '=', $companyId)
                ->where('g_reviewers.reviewer_id', '=', Auth::id())
                ->where('g_reviewers.status', '=', $approved)
                ->whereNull('g_requests.deleted_at')
                ->count();
        return $data;
    }

    /**
     * @param $companyId
     * @return int
     */
    public function countToApproveByCompany($companyId)
    {
        $pending = config('app.pending');
        $data =
            DB::table('g_requests')
                ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                ->where('g_requests.status', '=', $pending)
                ->where('g_requests.company_id', '=', $companyId)
                ->where('g_reviewers.reviewer_id', '=', Auth::id())
                ->where('g_reviewers.status', '=', $pending)
                ->whereNull('g_requests.deleted_at')
                ->count();

        $data1 =
            DB::table('g_requests')
                ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                ->where('g_requests.status', '=', $pending)
                // ->where('g_requests.review_status', '=', 1)
                ->where('g_requests.company_id', '=', $companyId)
                ->where('g_approvers.approver_id', '=', Auth::id())
                ->where('g_approvers.status', '=', $pending)
                ->whereNull('g_requests.deleted_at')
                ->count();

        return $data+$data1;
    }

    /**
     * @param $companyId
     * @return int
     */
    public function countRejectedByCompany($companyId)
    {
        $rejected = config('app.rejected');
        // Auth as requester
        $data =
            DB::table('g_requests')
                ->where('g_requests.status', '=', $rejected)
                ->where('g_requests.company_id', '=', $companyId)
                ->where('g_requests.user_id', '=', Auth::id())
                ->whereNull('g_requests.deleted_at')
                ->count();

        // Auth as reviewers
        $data +=
            DB::table('g_requests')
                ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                ->where('g_requests.status', '=', $rejected)
                ->where('g_requests.company_id', '=', $companyId)
                ->where('g_reviewers.reviewer_id', '=', Auth::id())
                ->whereNull('g_requests.deleted_at')
                ->count();
        return $data;
    }

    /**
     * @param $companyId
     * @return int
     */
    public function countApprovedByCompany($companyId)
    {
        $approved = config('app.approved');
        // Auth as requester
        $data =
            DB::table('g_requests')
                ->where('g_requests.status', '=', $approved)
                ->where('g_requests.company_id', '=', $companyId)
                ->where('g_requests.user_id', '=', Auth::id())
                ->whereNull('g_requests.deleted_at')
                ->count();

        // Auth as reviewers
        $data +=
            DB::table('g_requests')
                ->join('g_reviewers', 'g_requests.id', '=', 'g_reviewers.request_id')
                ->where('g_requests.status', '=', $approved)
                ->where('g_requests.company_id', '=', $companyId)
                ->where('g_reviewers.reviewer_id', '=', Auth::id())
                ->whereNull('g_requests.deleted_at')
                ->count();

        // Auth as approver
        $data +=
            DB::table('g_requests')
                ->join('g_approvers', 'g_requests.id', '=', 'g_approvers.request_id')
                ->where('g_requests.status', '=', $approved)
                ->where('g_requests.company_id', '=', $companyId)
                ->where('g_approvers.approver_id', '=', Auth::id())
                ->whereNull('g_requests.deleted_at')
                ->count();

        // Auth as cc
        $data +=
            DB::table('g_requests')
                ->join('g_ccs', 'g_requests.id', '=', 'g_ccs.request_id')
                ->where('g_requests.status', '=', $approved)
                ->where('g_requests.company_id', '=', $companyId)
                ->where('g_ccs.reviewer_id', '=', Auth::id())
                ->whereNull('g_requests.deleted_at')
                ->count();

        return $data;
    }


    private function refector(){}


    public function getApprovedList()
    {
        $cols = $this->listColumn;
        $type = config('app.report');
        $userId = Auth::id();
        $companyId = DB::table('companies')
            ->where('short_name_en', '=', @$_GET['company'])->first();
        $companyId = @$companyId->id;
        $department = DB::table('company_departments')
            ->where('company_id', '=', $companyId)
            ->where('short_name', @$_GET['department'])->first();
        $departmentId = @$department->id;
        $tags = @strtolower($_GET['tags']);
        $groups = @strtolower($_GET['groups']);
        $status = config('app.approved');

        $date_from = @strtolower($_GET['date_from']);
        $date_to = @strtolower($_GET['date_to']);
        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }
        // dd(strtotime($date_from), $date_from);
        // Auth as Requester
        $sqlRequester = "
            select $cols
            from g_requests
            left join g_approvers on g_requests.id = g_approvers.request_id
            left join g_reviewers on g_requests.id = g_reviewers.request_id
            left join g_request_templates on g_request_templates.id = g_requests.template_id
            where g_requests.user_id = $userId
            and g_requests.type = '$type'
            and g_requests.status = '$status'
            and g_requests.review_status = 1
            and g_requests.deleted_at is null
            ";
        if ($companyId) {
            $sqlRequester .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sqlRequester .= " and g_requests.department_id = $departmentId";
        }
        if ($tags && $tags != 'null') {
            $sqlRequester .= " and g_requests.tags = '$tags'";
        }
        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sqlRequester .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sqlRequester .= " and g_requests.template_id = '$groups'";
            }
        }
        if ($date_from || $date_to) {
            $sqlRequester .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sqlRequester .= " group by g_requests.id";
        $sqlRequester .= " union";

        // Auth as Reviewer
        $sqlReviewer = "
            select $cols
            from g_requests
            left join g_approvers on g_requests.id = g_approvers.request_id
            join g_reviewers on g_requests.id = g_reviewers.request_id
            left join g_request_templates on g_request_templates.id = g_requests.template_id
            where g_reviewers.reviewer_id = $userId
            and g_requests.type = '$type'
            and g_requests.status = '$status'
            and g_requests.review_status = 1
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sqlReviewer .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sqlReviewer .= " and g_requests.department_id = $departmentId";
        }
        if ($tags && $tags != 'null') {
            $sqlReviewer .= " and g_requests.tags = '$tags'";
        }
        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sqlReviewer .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sqlReviewer .= " and g_requests.template_id = '$groups'";
            }
        }
        if ($date_from || $date_to) {
            $sqlReviewer .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sqlReviewer .= " group by g_requests.id";
        $sqlReviewer .= " union";


        // Auth as CC
        $sqlCC = "
            select $cols
            from g_requests
            join g_approvers on g_requests.id = g_approvers.request_id 
            join g_ccs on g_requests.id = g_ccs.request_id
            left join g_reviewers on g_requests.id = g_reviewers.request_id and g_reviewers.reviewer_id = $userId
            left join g_request_templates on g_request_templates.id = g_requests.template_id
            where g_ccs.reviewer_id = $userId
            and g_requests.type = '$type'
            and g_requests.status = '$status'
            and g_requests.review_status = 1
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sqlCC .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sqlCC .= " and g_requests.department_id = $departmentId";
        }
        if ($tags && $tags != 'null') {
            $sqlCC .= " and g_requests.tags = '$tags'";
        }
        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sqlCC .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sqlCC .= " and g_requests.template_id = '$groups'";
            }
        }
        if ($date_from || $date_to) {
            $sqlCC .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sqlCC .= " group by g_requests.id";
        $sqlCC .= " union";

        // Auth as Approver
        $sqlApprover = "
            select $cols
            from g_requests
            join g_approvers on g_requests.id = g_approvers.request_id
            left join g_reviewers on g_requests.id = g_reviewers.request_id
            left join g_request_templates on g_request_templates.id = g_requests.template_id
            where g_approvers.approver_id = $userId
            and g_requests.type = '$type'
            and g_requests.status = '$status'
            and g_approvers.status = '$status'
            and g_requests.review_status = 1
            and g_requests.deleted_at is null
            ";

        if ($companyId) {
            $sqlApprover .= " and g_requests.company_id = $companyId";
        }
        if ($departmentId) {
            $sqlApprover .= " and g_requests.department_id = $departmentId";
        }
        if ($tags && $tags != 'null') {
            $sqlApprover .= " and g_requests.tags = '$tags'";
        }
        if ($groups && $groups != 'null') {
            if ($groups == "Other" || $groups == "other") {
                $sqlApprover .= " and g_request_templates.deleted_at is not null";
            }
            else {
                $sqlApprover .= " and g_requests.template_id = '$groups'";
            }
        }
        if ($date_from || $date_to) {
            $sqlApprover .= " and g_requests.end_date BETWEEN '$from' AND '$to'";
        }
        $sqlApprover .= " group by g_requests.id";

        $data = DB::select(DB::raw($sqlRequester.' '.$sqlReviewer.' '.$sqlCC.' '.$sqlApprover));

        // // Auth as CC, Reviewer and Approver
        // $sqlAll = "
        //     select $cols
        //     from g_requests
        //     left join g_approvers on g_requests.id = g_approvers.request_id and g_approvers.approver_id = $userId
        //     left join g_reviewers on g_requests.id = g_reviewers.request_id and g_reviewers.reviewer_id = $userId
        //     left join g_ccs on g_requests.id = g_ccs.request_id and g_ccs.reviewer_id = $userId
        //     where g_requests.type = '$type'
        //     and g_requests.status = '$status'
        //     and g_requests.review_status = 1
        //     and g_requests.deleted_at is null
        //     ";

        // if ($companyId) {
        //     $sqlAll .= " and g_requests.company_id = $companyId";
        // }
        // if ($departmentId) {
        //     $sqlAll .= " and g_requests.department_id = $departmentId";
        // }
        // if ($tags && $tags != 'null') {
        //     $sqlAll .= " and g_requests.tags = '$tags'";
        // }
        // if ($groups && $groups != 'null') {
        //     $sqlAll .= " and g_requests.template_id = '$groups'";
        // }
        // $sqlAll .= " group by g_requests.id";

        // $data = DB::select(DB::raw($sqlRequester.' '.$sqlAll));

        $data = CollectionHelper::paginate(collect($data)->sortByDesc('id'), count($data), 20);

        //dd($data);
        
        // Append cc, reviewers and approver
        foreach($data as $key => $item) {
            // dd($item);
            $item->reviewers = $this->getReviewersByRequestId(@$item->id);
            $item->approver = $this->getApproverByRequestId($item->id);
            $item->ccs = $this->getCCByRequestId($item->id);
            $item->attachments = json_decode($item->attachments);
        }
        return $data;
    }
}
