<div class="row sign justify-content-between">
    <div class="col-3">
        <div class="text-center">
            <p class="pt-1">
                ថ្ងៃទី{{ khmer_number($data->created_at->format('d')) }} 
                ខែ{{ khmer_number($data->created_at->format('m')) }}  
                ឆ្នាំ{{ khmer_number($data->created_at->format('Y')) }}
            </p>
            <p>ស្នើសុំដោយ៖</p>
            <p>{{ $data->requester()->position->name_km }}</p>
            <p><img style="height: 60px" src="{{ asset('/'.$data->requester()->signature) }}" alt="signature"></p>
            <p class="requester-name">{{ $data->requester()->name }}</p>
        </div>
    </div>

    @if (@$data->forcompany->short_name_en == 'MMI')

        @foreach($data->reviewers_short_sign() as $reviewer)
            <div class="col-3">
                <div class="text-center">
                    @if($reviewer->approve_status== config('app.approve_status_approve'))
                        <p class="pt-1">
                            ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                            ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('m')) }}
                            ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                        </p>
                        <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                        <p>{{ $reviewer->position->name_km }}</p>
                        <p><img style="height: 60px" src="{{ asset('/'.$reviewer->signature) }}" alt="signature"></p>
                        <p class="requester-name">{{ $reviewer->name }}</p>
                    @else
                        <p class="pt-1">ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                        <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                        <p>{{ $reviewer->position->name_km }}</p>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="col-3 ml-auto">
            <div class="text-center">
                @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                    <p class="pt-1">    
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->subApprover()->approved_at))->format('d')) }}
                        ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->subApprover()->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->subApprover()->approved_at))->format('Y')) }}
                    </p>
                    <p>
                        អនុម័តដោយ៖
                        @if (@$data->shortSign()->approve_status == config('app.approve_status_approve'))
                            <img style="height: 15px;"
                                  title="{{ $data->shortSign()->name }}"
                                  src="{{ asset('/'.@$data->shortSign()->short_signature) }}"
                                  alt="short_signature">
                        @endif

                        @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                            <img style="height: 15px;"
                                 title="{{ $data->approver()->name }}"
                                 src="{{ asset('/'.$data->approver()->short_signature) }}"
                                 alt="short_signature">
                        @endif
                    </p>
                    @if(@$data->subApprover()->position_level == config('app.position_level_president'))
                        <p>{{ @$data->forcompany->subApprover }}</p>
                    @else
                        <p>{{ @$data->subApprover()->position->name_km }}</p>
                    @endif
                    <p><img style="height: 60px;" src="{{ asset('/'.@$data->subApprover()->signature) }}" alt="Signature"></p>
                    <p>{{ (@$data->subApprover()->name) }}</p>
                @else
                    <p class="pt-1">ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                    <p>
                        អនុម័តដោយ៖
                        @if (@$data->shortSign()->approve_status == config('app.approve_status_approve'))
                            <img style="height: 15px;"
                                 title="{{ $data->shortSign()->name }}"
                                 src="{{ asset('/'.@$data->shortSign()->short_signature) }}"
                                 alt="short_signature">
                        @endif
                    </p>
                    @if(@$data->subApprover()->position_level == config('app.position_level_president'))
                        <p>{{ @$data->forcompany->subApprover }}</p>
                    @else
                        <p>{{ @$data->subApprover()->position->name_km }}</p>
                    @endif
                @endif
            </div>
        </div>

    @else

        @foreach($data->reviewers() as $reviewer)
            <div class="col-3">
                <div class="text-center">
                    @if($reviewer->approve_status== config('app.approve_status_approve'))
                        <p class="pt-1">
                            ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                            ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('m')) }}
                            ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                        </p>
                        <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                        <p>{{ $reviewer->position->name_km }}</p>
                        <p><img style="height: 60px" src="{{ asset('/'.$reviewer->signature) }}" alt="signature"></p>
                        <p class="requester-name">{{ $reviewer->name }}</p>
                    @else
                        <p class="pt-1">ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                        <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                        <p>{{ $reviewer->position->name_km }}</p>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="col-3 ml-auto">
            <div class="text-center">
                @if(@$data->approver()->approve_status == config('app.approve_status_approve'))
                    <p class="pt-1">     
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d')) }} 
                        ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('Y')) }}
                    </p>
                    <p>អនុម័តដោយ៖</p>
                    @if(@$data->approver()->position_level == config('app.position_level_president'))
                        <p>{{ @$data->forcompany->approver }}</p>
                    @else
                        <p>{{ @$data->approver()->position->name_km }}</p>
                    @endif
                    <p><img style="height: 60px" src="{{ asset('/'.@$data->approver()->signature) }}" alt="signature"></p>
                    <p class="requester-name">{{ @$data->approver()->name }}</p>
                @else
                    <p class="pt-1">ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                    <p>អនុម័តដោយ៖</p>
                    @if(@$data->approver()->position_level == config('app.position_level_president'))
                        <p>{{ @$data->forcompany->approver }}</p>
                    @else
                        <p>{{ @$data->approver()->position->name_km }}</p>
                    @endif
                @endif
            </div>
        </div>

    @endif

</div>