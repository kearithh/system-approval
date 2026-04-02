<style>
    #action {
        position: relative;
        background: #FFF;
        z-index: 1;
        margin: auto;
        width: 1020px;
        margin-bottom: 5px;
    }
    #action {
        box-sizing: unset;
        padding-left: 5px;
    }
    #action .btn-group{
        display: inline-block;
        padding: 5px 0px;
    }
    a.btn {
        border: 1px solid #dadada;
        padding: 0px 10px;
        text-decoration: none;
        color: inherit;
    }

    #action .btn-group button {
        height: 29px;
        border: 1px solid #dadada;
        padding: 0 7px;
        min-width: 54px;
    }
    #after_action {

        margin-top: 40px;
    }

    button:disabled {
        opacity: 0.5;
    }
    button:disabled:hover, input:disabled:hover {
        cursor: not-allowed;
    }

    @media print {
        #action {
            display: none;
        }
    }
</style>

<div id="action">
    <div class="btn-group" role="group" aria-label="">
        <a href="{{ URL::previous() }}">
            <button class="btn btn-sm btn-default">
                Back
            </button>
        </a>
    </div>
    <div class="btn-group" role="group" aria-label="">
        <button type="button" onclick="window.print()" style="background: #ffc107; color:black" class="btn btn-sm btn-warning">
            Print
        </button>
    </div>
    <form style="margin: 0; display: inline-block " action="">
        <div class="btn-group" role="group" aria-label="">
            <?php
            $reviewer = $data->reviewers()->where('id', Auth::id())->first();
            $reviewerStatus = isset($reviewer->approve_status) ? $reviewer->approve_status : NULL;

            ?>
            @if(
                $data->created_by == \Illuminate\Support\Facades\Auth::id() ||
                $data->status == config('app.approve_status_approve') ||
                $reviewerStatus == config('app.approve_status_approve')

            )
                <button id="approve_btn" disabled name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">
                    Approve
                </button>
                <button id="reject_btn" disabled style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default">
                    Comment
                </button>
            @else
                <button id="approve_btn" name="approve" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">
                    Approve
                </button>
                <button id="reject_btn" style="background: #bd2130; color: white" name="reject" value="1" class="btn btn-sm btn-default">
                    Comment
                </button>
            @endif
        </div>
    </form>

</div>
