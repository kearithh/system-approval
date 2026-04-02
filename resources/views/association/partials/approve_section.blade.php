<div class="row justify-content-between">
    <div class="col-4">
        <div class="text-center">
            <p>
                ថ្ងៃទី{{ khmer_number($data->created_at->format('d')) }} 
                ខែ{{ khmer_month($data->created_at->format('m')) }}  
                ឆ្នាំ{{ khmer_number($data->created_at->format('Y')) }}
            </p>
            <p>រៀបចំដោយ</p>
            <p>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</p>
            <p><img style="height: 60px" src="{{ asset('/'.$data->requester()->signature) }}" alt="signature"></p>
            <p class="requester-name">{{ @$data->creator_object->name ?: $data->requester()->name }}</p>
        </div>
    </div>
    @foreach($data->reviewers() as $reviewer)
        <div class="col-4">
            <div class="text-center">
                @if($reviewer->approve_status== config('app.approve_status_approve'))
                    <p>
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                        ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                    </p>
                    <p>បានត្រួតពិនិត្យដោយ</p>
                    <p>{{ @json_decode(@$reviewer->user_object)->position_name ?: $reviewer->position->name_km }}</p>
                    <p><img style="height: 60px" src="{{ asset('/'.$reviewer->signature) }}" alt="signature"></p>
                    <p class="requester-name">{{ @json_decode(@$reviewer->user_object)->name ?: $reviewer->name }}</p>
                @else
                    <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                    <p>បានត្រួតពិនិត្យដោយ</p>
                    <p>{{ @json_decode(@$reviewer->user_object)->position_name ?: $reviewer->position->name_km }}</p>
                @endif

            </div>
        </div>
    @endforeach

    <div class="col-4 ml-auto">
        <div class="text-center">
            @if(@$data->approver()->approve_status == config('app.approve_status_approve'))
                <p>     
                    ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d')) }} 
                    ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('m')) }}
                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('Y')) }}
                </p>
                <p>អនុម័តដោយ</p>
                <p>ប្រធានសមាគមសុវត្ថិភាពសហគមន៍</p>
                <p><img style="height: 60px" src="{{ asset('/'.@$data->approver()->signature) }}" alt="signature"></p>
                <p class="requester-name">{{ @json_decode(@$data->approver()->user_object)->name ?: @$data->approver()->name }}</p>
            @else
                <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                <p>អនុម័តដោយ</p>
                <p>ប្រធានសមាគមសុវត្ថិភាពសហគមន៍</p>
            @endif
        </div>
    </div>
</div>