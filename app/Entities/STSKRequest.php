<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class STSKRequest extends Model
{
    protected $fillable = [
        'id',
        'status',
        'type',
        'company_id',
        'branch_code',
        'department_id',
        'requester_id',
    ];

    public function dynamicProperties()
    {

    }

    public function items()
    {

    }

    public function requester()
    {

    }

    public function reviewers()
    {

    }

    public function approver()
    {

    }


}

"
1 STSKRequest
    id
    status
        1 pending, 2 approved, 3 reject, 4, re-pending
    type
        memo, se, ge, disposal, etc...
    template()
        show_memo, show_se, show_ge, show_disposal etc...

    1 request_property()
        id
        name
        value

    1 request_item()
        id
        request_id
        1 request_item_property()
            name
            value
        1 request_item_attach()
            id
            origin_name
            src

    1 request_attach()
        id
        origin_name
        src

    requester_id
    1 reviewer()
        id
        request_id
        reviewer_id
        status
        approved_at
        commented_at
        reviewer_comment()
            id
            reviewer_id
            desc
            src
            original_attach


    1 approver()
        id
        request_id
        approver_id
        status
        approved_at
        commented_at
        reviewer_comment()
            id
            reviewer_id
            desc
            src
            original_attach

























";
