<div class="row">
    <div class="col signature">
        <div class="text-center">
            <span>
                ថ្ងៃទី{{ khmer_number($data->created_at->format('d')) }} 
                ខែ{{ khmer_month($data->created_at->format('m')) }} 
                ឆ្នាំ{{ khmer_number($data->created_at->format('Y')) }}
            </span><br>
            <span>រៀបចំដោយ</span><br>
            <span>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</span><br>
            <span><img style="height: 60px" src="{{ asset('/'.$data->requester()->signature) }}" alt="Signature"></span><br>
            <span class="requester-name">{{ @$data->creator_object->name ?: $data->requester()->name }}</span>
        </div>
    </div>
    @foreach($data->reviewers() as $reviewer)
        <div class="col signature">
            <div class="text-center">
                @if($reviewer->approve_status== config('app.approve_status_approve'))
                    <span>
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                        ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                    </span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <span>{{ @json_decode(@$reviewer->user_object)->position_name ?: $reviewer->position->name_km }}</span><br>
                    <span><img style="height: 60px" src="{{ asset('/'.$reviewer->signature) }}" alt="Signature"></span><br>
                    <span class="requester-name">{{ @json_decode(@$reviewer->user_object)->name ?: $reviewer->name }}</span>
                @else
                    <span>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <span>{{ @json_decode(@$reviewer->user_object)->position_name ?: $reviewer->position->name_km }}</span>
                @endif

            </div>
        </div>
    @endforeach
    <div class="col signature">
        <div class="text-center">
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
                <span><img style="height: 60px" src="{{ asset('/'.@$data->approver()->signature) }}" alt="Signature"></span><br>
                <span class="requester-name">
                    {{ @check_nickname(@$data->approver()->position_level, $data->created_at ) }}
                    {{ @json_decode(@$data->approver()->user_object)->name ?: @$data->approver()->name }}
                </span>
            @else
                <span>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</span><br>
                <span>អនុម័តដោយ</span><br>
                @if(@$data->approver()->position_level == config('app.position_level_president'))
                    <span>{{ @$data->forcompany->approver }}</span><br>
                @else
                    <span>{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}</span>
                @endif
            @endif
        </div>
    </div>
</div>