<?php $authId = \Illuminate\Support\Facades\Auth::id(); ?>
<div id="action">
    <div class="btn-group" role="group" aria-label="">
        <button id="back" type="button" title="Back to List" class="btn btn-sm btn-secondary">
            Back
        </button>
    </div>
    @include('global.next_pre')
    <div class="btn-group" role="group" aria-label="">
        <button @if ($data->type == config('app.report')) disabled @endif type="button" onclick="window.print()" class="btn btn-sm btn-warning">
            Print
        </button>
    </div>
{{--    @if(can_approve_or_reject($data, config('app.report')))--}}
        <form action="{{ route('re.item-approve', $data->id) }}" method="POST" id="action_approve" style="margin: 0; display: inline-block">
            @csrf
            <div class="btn-group" role="group" aria-label="">
                <button @if (!in_array($authId, (array)@$object->approvable[0])) disabled @endif id="approve_btn" name="approve" value="1" class="btn btn-sm btn-success">
                    Approve
                </button>

            </div>
        </form>
        <form action="{{ route('re.item-reject', $data->id) }}" method="POST" id="action_reject" style="margin: 0; display: inline-block">
            @csrf
            <button @if (!in_array($authId, (array)@$object->rejectable[0])) disabled @endif id="reject_btn"  name="reject" value="1" class="btn btn-sm btn-danger">
                Comment
            </button>
        </form>
{{--    @else--}}
{{--        <button id="approve_btn" disabled class="btn btn-sm btn-success">--}}
{{--            Approve--}}
{{--        </button>--}}
{{--        <button id="comment_modal"  disabled  value="1" class="btn btn-sm btn-danger">--}}
{{--            Comment--}}
{{--        </button>--}}
{{--    @endif--}}
</div>
