<?php
$reviewers = $data->reviewers();
$approver = $data->approver();
$k = 0;
//dd($reviewers[0]);
$approve = config('app.approve_status_approve')
?>

<!-- Signature First Row: 2 Reviewers 1 Requester -->
<!-- <div class="signature"> -->
    <div class="col-sm-3">
        <span> 
            ធ្វើនៅ <!-- ភ្នំពេញ, --> 
            ថ្ងៃទី{{ khmer_number($data->created_at->format('d')) }} 
            ខែ{{ khmer_month($data->created_at->format('m')) }}  
            ឆ្នាំ{{ khmer_number($data->created_at->format('Y')) }}
        </span><br>
        <span>រៀបចំដោយ</span><br>
        <!-- <span>{{ $data->requester->position->name_km }}</span><br> -->
        <span>{{ @$data->creator_object->position_name ?: $data->requester->position->name_km }}</span><br>
        <span><img style="height: 60px" src="{{ asset('/'.$data->requester->signature) }}" alt="Signature"></span>
        <!-- <p class="requester-name">{{ $data->requester->name }}</p> -->
        <p class="requester-name">{{ @$data->creator_object->name ?: $data->requester->name }}</p>
    </div>

    @for ($i = 0; $i < count($reviewers); $i++)
        @if (isset($reviewers[$i]))
            <div class="col-sm-3">
                @if($reviewers[$i]->approve_status == $approve)
                    <span>
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewers[$i]->approved_at))->format('d')) }} 
                        ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($reviewers[$i]->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewers[$i]->approved_at))->format('Y')) }}
                    </span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <!-- <span>{{ $reviewers[$i]->position_name }}</span><br> -->
                    <span>{{ @json_decode($reviewers[$i]->user_object)->position_name ?: $reviewers[$i]->position_name }}</span><br>
                    <span><img style="height: 60px" src="{{ asset('/'.$reviewers[$i]->signature) }}" alt="Signature"></span><br>
                    <!-- <p>{{ $reviewers[$i]->name }}</p> -->
                    <p>{{ @json_decode($reviewers[$i]->user_object)->name ?: $reviewers[$i]->name }}</p>
                @else
                    <span>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</span><br>
                    <span>បានត្រួតពិនិត្យដោយ</span><br>
                    <!-- <span>{{ $reviewers[$i]->position_name }}</span><br> -->
                    <span>{{ @json_decode($reviewers[$i]->user_object)->position_name ?: $reviewers[$i]->position_name }}</span><br>
                    <p>&nbsp;</p>
                    <!-- <p>{{ $reviewers[$i]->name }}</p> -->
                    <p>{{ @json_decode($reviewers[$i]->user_object)->name ?: $reviewers[$i]->name }}</p>
                @endif
            </div>
        @endif
    @endfor

    @if(count($reviewers) == 1)
        <div class="col-sm-3"></div>
    @elseif(count($reviewers) == 3)
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
    @elseif(count($reviewers) == 4)
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
    @elseif(count($reviewers) == 5)
        <div class="col-sm-3"></div>
    @elseif(count($reviewers) == 7)
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
    @elseif(count($reviewers) == 8)
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
    @elseif(count($reviewers) == 9)
        <div class="col-sm-3"></div>
    @endif
    <div class="col-sm-3">
        @if(@$approver->approve_status == @$approve)
            <span>
                ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('d')) }} 
                ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('m')) }}
                ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('Y')) }}
            </span><br>
            <span>អនុម័តដោយ</span><br>
            @if($approver->position_level == config('app.position_level_president'))
                <span>{{ $data->forcompany->approver }}</span><br>
            @else
                <!-- <span>{{ $approver->position_name }}</span><br> -->
                <span>{{ @json_decode($approver->user_object)->position_name ?: $approver->position_name }}</span><br>
            @endif
            <span><img style="height: 60px" src="{{ asset('/'.$approver->signature) }}" alt="Signature"></span><br>
            <span>
                {{ @check_nickname(@$approver->position_level, $data->created_at ) }}
                <!-- {{ $approver->name }} -->
                {{ @json_decode($approver->user_object)->name ?: $approver->name }}
            </span>
        @else
            <span>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</span><br>
            <span>អនុម័តដោយ</span><br>
            @if(@$approver->position_level == config('app.position_level_president'))
                <span>{{ $data->forcompany->approver }}</span><br>
            @else
                <!-- <span>{{ @$approver->position_name }}</span><br> -->
                <span>{{ @json_decode($approver->user_object)->position_name ?: $approver->position_name }}</span><br>
            @endif
            <p>&nbsp;</p>
            <!-- <p>{{ @$approver->name }}</p> -->
            <p>{{ @json_decode($approver->user_object)->name ?: @$approver->name }}</p>
        @endif
    </div>
