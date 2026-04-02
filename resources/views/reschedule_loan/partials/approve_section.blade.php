
<div class="row">
    <div class="col">
        <div class="text-center">
            <span>
                {{ $data->forbranch->name_km }}
                ថ្ងៃទី{{ khmer_number($data->created_at->format('d')) }}
                ខែ{{ khmer_month($data->created_at->format('m')) }}
                ឆ្នាំ{{ khmer_number($data->created_at->format('Y')) }}
            </span><br>
            <span>រៀបចំដោយ</span><br>
            <span>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</span><br>
            <span><img style="height: 60px" src="{{ asset('/'.$data->requester()->signature) }}" alt="signature"></span><br>
            <span class="requester-name">{{ @$data->creator_object->name ?: $data->requester()->name }}</span><br>
        </div>
    </div>
    <?php $mis = $data->reviewers()->where('approve_position', 'reviewer_mis')->whereNotNull('reviewer_id')->first(); ?>
    @if(@$mis) 
        <div class="col">
            <div class="text-center">
                @if($mis->approve_status== config('app.approve_status_approve'))
                    <span>
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($mis->approved_at))->format('d')) }}
                        ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($mis->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($mis->approved_at))->format('Y')) }}
                    </span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <span>{{ @json_decode(@$mis->user_object)->position_name ?: $mis->position->name_km }}</span><br>
                    <span><img style="height: 60px" src="{{ asset('/'.$mis->signature) }}" alt="signature"></span><br>
                    <span class="requester-name">{{ @json_decode(@$mis->user_object)->position_name ?: $mis->name }}</span><br>
                @else
                    <span>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <span>{{ @json_decode(@$mis->user_object)->position_name ?: $mis->position->name_km }}</span><br>
                @endif
            </div>
        </div>
    @endif

    <?php $rm = $data->reviewers()->where('approve_position', 'reviewer_rm')->whereNotNull('reviewer_id')->first(); ?>
    @if(@$rm) 
        <div class="col">
            <div class="text-center">  
                @if(@$rm->approve_status == config('app.approve_status_approve'))
                    <span>
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($rm->approved_at))->format('d')) }}
                        ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($rm->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($rm->approved_at))->format('Y')) }}
                    </span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <span>{{ @json_decode(@$rm->user_object)->position_name ?: @$rm->position->name_km }}</span><br>
                    <span><img style="height: 60px" src="{{ asset('/'.$rm->signature) }}" alt="signature"></span><br>
                    <span class="requester-name">{{ @json_decode(@$rm->user_object)->position_name ?: @$rm->name }}</span><br>
                @else
                    <span>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <span>{{ @json_decode(@$rm->user_object)->position_name ?: @$rm->position->name_km }}</span><br>
                @endif
            </div>
        </div>
    @endif
</div>
<div class="row">

    <?php $hfn = $data->reviewers()->where('approve_position', 'reviewer_hfn')->whereNotNull('reviewer_id')->first(); ?>
    @if($hfn)
        <div class="col">
            <div class="text-center">
                @if($hfn->approve_status == config('app.approve_status_approve'))
                    <span>
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($hfn->approved_at))->format('d')) }}
                        ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($hfn->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($hfn->approved_at))->format('Y')) }}
                    </span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <span>{{ @json_decode(@$hfn->user_object)->position_name ?: $hfn->position->name_km }}</span><br>
                    <span><img style="height: 60px" src="{{ asset('/'.$hfn->signature) }}" alt="signature"></span><br>
                    <span class="requester-name">{{ @json_decode(@$hfn->user_object)->position_name ?: @$hfn->name }}</span><br>
                @else
                    <span>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <span>{{ @json_decode(@$hfn->user_object)->position_name ?: $hfn->position->name_km }}</span><br>
                @endif
            </div>
        </div>
    @endif

    <?php $hoo = $data->reviewers()->where('approve_position', 'reviewer_hoo')->whereNotNull('reviewer_id')->first(); ?>
    @if($hoo)
        <div class="col">
            <div class="text-center">
                @if($hoo->approve_status == config('app.approve_status_approve'))
                    <span>
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($hoo->approved_at))->format('d')) }}
                        ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($hoo->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($hoo->approved_at))->format('Y')) }}
                    </span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <span>{{ @json_decode(@$hoo->user_object)->position_name ?: $hoo->position->name_km }}</span><br>
                    <span><img style="height: 60px" src="{{ asset('/'.$hoo->signature) }}" alt="signature"></span><br>
                    <span class="requester-name">{{ @json_decode(@$hoo->user_object)->position_name ?: @$hoo->name }}</span><br>
                @else
                    <span>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <span>{{ @json_decode(@$hoo->user_object)->position_name ?: $hoo->position->name_km }}</span><br>
                @endif
            </div>
        </div>
    @endif
    
    <div class="col">
        <div class="text-center">
            <!-- <span>ធ្វើនៅការិយាល័យកណ្តាល, ថ្ងៃទី​{{ khmer_number($data->created_at->format('d')) }} ខែ {{ khmer_month($data->created_at->format('m')) }}  ឆ្នាំ{{ khmer_number($data->created_at->format('Y')) }}</span><br> -->
            @if(@$data->approver()->approve_status == config('app.approve_status_approve'))
                <span>
                    ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d')) }}
                    ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('m')) }}
                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('Y')) }}
                </span><br>
                <span>អនុម័តដោយ</span><br>
                @if(@$data->approver()->position_level == config('app.position_level_president'))
                    <span>{{ @$data->forcompany->approver }}</span><br>
                @else
                    <span>{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}</span><br>
                @endif
                <span><img style="height: 60px" src="{{ asset('/'.@$data->approver()->signature) }}" alt="signature"></span><br>
                <span class="requester-name">
                    {{ @check_nickname(@$data->approver()->position_level, $data->created_at ) }}
                    {{ @json_decode(@$data->approver()->user_object)->name ?: @$data->approver()->name }}
                </span><br>
            @else
                <span>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</span><br>
                <span>អនុម័តដោយ</span><br>
                @if(@$data->approver()->position_level == config('app.position_level_president'))
                    <span>{{ @$data->forcompany->approver }}</span><br>
                @else
                    <span>{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}</span><br>
                @endif
            @endif

        </div>
    </div>
</div>
