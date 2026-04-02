<?php

namespace App\Model;

use App\Traits\CRUDable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupRequestTemplate extends Model
{
    use CRUDable;

    use SoftDeletes;

    protected $table = 'g_request_templates';

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
        'name',
        'status',
        'cc',

        'tags',
        'properties',
        'attachments',

        'start_date',
        'end_date',
        'created_by',
        'updated_by',

        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = ['start_date', 'end_date'];

    /**
     * @var string[]
     */
    protected $casts = [
        'properties' => 'object',
        'attachments' => 'array',
        'cc' => 'array',
    ];

    /**
     * @param null $requestId
     * @return Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getApproverByRequestId($requestId = null)
    {
        $requestId = $requestId ? $requestId : $this->id;
        $data = DB::table('users')
            ->join('positions', 'positions.id', '=', 'users.position_id')
            ->join('g_approver_templates', 'g_approver_templates.approver_id', '=', 'users.id')
            ->join('g_request_templates', 'g_request_templates.id', '=', 'g_approver_templates.request_id')
            ->where('g_request_templates.id', '=', $requestId)
            ->select([
                'users.name',
                'users.username',
                'positions.name_km',
                'g_approver_templates.status as approve_status',
                'g_approver_templates.approved_at',
                'g_approver_templates.rejected_at',
            ])
            ->first()
        ;
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
            ->join('g_reviewer_templates', 'g_reviewer_templates.reviewer_id', '=', 'users.id')
            ->join('g_request_templates', 'g_request_templates.id', '=', 'g_reviewer_templates.request_id')

            ->where('g_request_templates.id', '=', $requestId)
            ->select([
                'users.name',
                'users.username',
                'positions.name_km',
                'g_reviewer_templates.status as review_status',
                'g_reviewer_templates.approved_at',
                'g_reviewer_templates.rejected_at',
            ])
            ->get()
            ;
        return $data;
    }

    /**
     * @param $type
     * @param null $companyId
     * @param null $departmentId
     * @param null $tags
     * @param null $status
     * @param null $userId
     * @param null $isGetTemplate
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function getRelatedRequestByUser
    (
        $type,
        $companyId = null,
        $departmentId = null,
        $tags = null,
        $status = null,
        $userId = null,
        $isGetTemplate = null
    ) {
        $data = $this->getRelatedRequestByUserQuery(
            $type,
            $companyId,
            $departmentId,
            $tags,
            $status,
            $userId,
            $isGetTemplate
        );

        $data = $data->get();

        // Append reviewers and approver
        foreach($data as $key => $item) {
            $item->reviewers = $this->getReviewersByRequestId($item->id);
            $item->approver = $this->getApproverByRequestId($item->id);
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
     * @param $isGetTemplate
     * @return \Illuminate\Database\Query\Builder
     */
    public function getRelatedRequestByUserQuery(
        $type,
        $companyId = null,
        $departmentId = null,
        $tags = null,
        $status = null,
        $userId = null,
        $isGetTemplate = null
    )
    {
        $userId = $userId ? $userId : Auth::id();

        $data = DB::table('g_request_templates');
        $data = $data->join('users', 'g_request_templates.user_id', '=', 'users.id');
        $data = $data->where('g_request_templates.type', '=', $type);
        $data = $data->whereNull('g_request_templates.deleted_at');
        if (!$isGetTemplate) {
            $data = $data->where('g_request_templates.user_id', '=', $userId);
        }
        if ($companyId) {
            $data = $data->where('g_request_templates.company_id', '=', $companyId);
        }
        if ($departmentId) {
            $data = $data->where('g_request_templates.department_id', '=', $departmentId);
        }
        if ($tags) {
            $data = $data->where('g_request_templates.tags', '=', $tags);
        }
        if ($status) {
            $data = $data->where('g_request_templates.status', '=', $status);
        }

        $data = $data->select([
            'g_request_templates.*',

        ]);



        $relatedData = DB::table('g_request_templates');
        $relatedData = $relatedData->join('users', 'g_request_templates.user_id', '=', 'users.id');
        $relatedData = $relatedData->join('g_reviewer_templates', 'g_reviewer_templates.reviewer_id', '=', 'users.id');

        $relatedData = $relatedData->whereNull('g_request_templates.deleted_at');
        $relatedData = $relatedData->where('g_request_templates.type', '=', $type);
        $relatedData = $relatedData->where('g_reviewer_templates.reviewer_id', '=', $userId);

        if ($companyId) {
            $relatedData = $relatedData->where('g_request_templates.company_id', '=', $companyId);
        }
        if ($departmentId) {
            $relatedData = $relatedData->where('g_request_templates.department_id', '=', $departmentId);
        }
        if ($tags) {
            $relatedData = $relatedData->where('g_request_templates.tags', '=', $tags);
        }
        if ($status) {
            $relatedData = $relatedData->where('g_request_templates.status', '=', $status);
        }

        $relatedData = $relatedData->select([
            'g_request_templates.*',

        ])
        ->union($data)
        ;
//        dd($relatedData);
        return $relatedData;
    }

    /**
     * @param $type
     * @param null $companyId
     * @param null $departmentId
     * @param null $tags
     * @param null $status
     * @param null $userId
     * @return int
     */
    public function getTotalRelatedRequestByUser
    (
        $type,
        $companyId = null,
        $departmentId = null,
        $tags = null,
        $status = null,
        $userId = null
    ) {
        $data = $this->getRelatedRequestByUserQuery(
            $type,
            $companyId,
            $departmentId,
            $tags,
            $status,
            $userId
        );

        $data = $data->count();

        return $data;
    }


    /**
     * @param $requestId
     * @param $reviewerIds
     * @return bool
     */
    public function storeReviewers($reviewerIds, $requestId = null)
    {
        $requestId = $requestId ? $requestId : $this->id;
        $reviewers = DB::table('users')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.id', $reviewerIds)
            ->select(['users.*', 'positions.name_km as position_name'])
            ->get();
        foreach ($reviewers as $key => $item) {
            $reviewerParam[$key] = [
                'request_id' => $requestId,
                'reviewer_id' => $item->id,
                'reviewer_name' => $item->name,
                'reviewer_position' => $item->position_name,
                'status' => config('app.pending'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        $data = DB::table('g_reviewer_templates')->insert(@$reviewerParam);
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
        $data = DB::table('g_approver_templates')->insert(@$approverParam);
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
        $data = DB::table('g_request_templates');
        $data = $data->join('users', 'g_request_templates.user_id', '=', 'users.id');
        $data = $data->where('g_request_templates.type', '=', $type);
        $data = $data->where('g_request_templates.user_id', '=', $userId);
        if ($companyId) {
            $data = $data->where('g_request_templates.company_id', '=', $companyId);
        }
        if ($status) {
            $data = $data->where('g_request_templates.status', '=', $status);
        }
        $data = $data->select([
            'g_request_templates.company_id',
            'g_request_templates.company_name',
            'g_request_templates.department_id',
            'g_request_templates.department_name',
            DB::raw('count(g_request_templates.department_id) as total')
        ])
        ->groupBy('g_request_templates.department_id')
        ;

        $relatedData = DB::table('g_request_templates');
        $relatedData = $relatedData->join('users', 'g_request_templates.user_id', '=', 'users.id');
        $relatedData = $relatedData->join('g_reviewer_templates', 'g_reviewer_templates.reviewer_id', '=', 'users.id');
        $relatedData = $relatedData->where('g_request_templates.type', '=', $type);
        $relatedData = $relatedData->where('g_reviewer_templates.reviewer_id', '=', $userId);
        if ($companyId) {
            $relatedData = $relatedData->where('g_request_templates.company_id', '=', $companyId);
        }
        if ($status) {
            $relatedData = $relatedData->where('g_request_templates.status', '=', $status);
        }
        $relatedData = $relatedData->select([
            'g_request_templates.company_id',
            'g_request_templates.company_name',
            'g_request_templates.department_id',
            'g_request_templates.department_name',
            DB::raw('count(g_request_templates.department_id) as total')
        ])
            ->unionAll($data)
            ->groupBy('g_request_templates.department_id')
            ->get()
        ;
        return $relatedData;
    }
//
//    /**
//     * @param $type
//     * @param $tags
//     * @param null $companyId
//     * @param null $departmentId
//     * @param null $status
//     * @param null $userId
//     * @return \Illuminate\Support\Collection
//     */
//    public function getTotalRequestEachTags(
//        $type,
//        $companyId = null,
//        $departmentId = null,
//        $status = null,
//        $userId = null
//    )
//    {
//        $userId = $userId ? $userId : Auth::id();
//        $data = DB::table('g_request_templates');
//        $data = $data->join('users', 'g_request_templates.user_id', '=', 'users.id');
//        $data = $data->where('g_request_templates.type', '=', $type);
//        $data = $data->where('g_request_templates.user_id', '=', $userId);
//        if ($companyId) {
//            $data = $data->where('g_request_templates.company_id', '=', $companyId);
//        }
//        if ($departmentId) {
//            $data = $data->where('g_request_templates.department_id', '=', $departmentId);
//        }
//        if ($status) {
//            $data = $data->where('g_request_templates.status', '=', $status);
//        }
//        $data = $data->select([
//            'g_request_templates.company_id',
//            'g_request_templates.company_name',
//            'g_request_templates.department_id',
//            'g_request_templates.department_name',
//            'g_request_templates.tags',
//            DB::raw('count(g_request_templates.tags) as total')
//        ])
//            ->groupBy('g_request_templates.tags')
//        ;
//
//        $relatedData = DB::table('g_request_templates');
//        $relatedData = $relatedData->join('users', 'g_request_templates.user_id', '=', 'users.id');
//        $relatedData = $relatedData->join('g_reviewer_templates', 'g_reviewer_templates.reviewer_id', '=', 'users.id');
//        $relatedData = $relatedData->where('g_request_templates.type', '=', $type);
//        $relatedData = $relatedData->where('g_reviewer_templates.reviewer_id', '=', $userId);
//        if ($companyId) {
//            $relatedData = $relatedData->where('g_request_templates.company_id', '=', $companyId);
//        }
//        if ($departmentId) {
//            $data = $data->where('g_request_templates.department_id', '=', $departmentId);
//        }
//        if ($status) {
//            $relatedData = $relatedData->where('g_request_templates.status', '=', $status);
//        }
//        $relatedData = $relatedData->select([
//            'g_request_templates.company_id',
//            'g_request_templates.company_name',
//            'g_request_templates.department_id',
//            'g_request_templates.department_name',
//            'g_request_templates.tags',
//            DB::raw('count(g_request_templates.tags) as total')
//        ])
//            ->unionAll($data)
//            ->groupBy('g_request_templates.tags')
//            ->get()
//        ;
//        return $relatedData;
//    }

    public function getReviewerId()
    {
        $data = DB::table('g_reviewer_templates')->where('request_id', '=', $this->id)->pluck('reviewer_id');
        return @$data->toArray();
    }

    public function getApproverId()
    {
        $data = DB::table('g_approver_templates')->where('request_id', '=', $this->id)->pluck('approver_id')->first();
        return $data;
    }

}
/**
 * GroupRequest
 *  Company
 *  Department
 *  Tags     > Daily, Weekly, Monthly
 *      name
 *      value
 *
 *
 *  Reference_property
 *      reference_id
 *      name
 *      value
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
/*
$casts is a property to json_decode auto
$table->json('properties');
protected $casts = [
    'properties' => 'array'
];










*/
