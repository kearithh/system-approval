<span style="float: right !important;">
    @foreach($data->reviewerShorts() as $key => $value)
        @if ($value->approve_status == config('app.approve_status_approve'))
            <img  src="{{ asset($value->short_signature) }}"  
                  alt="short_sign" 
                  title="{{ @$value->name }}" 
                  style="width: 25px;">
        @endif
    @endforeach
</span>
                
<?php
  $relatedCol = count($data->reviewers());
  $allCol = $relatedCol + 2;
?>
<div class="signature">
    <div style="width: {{ (100/$allCol).'%' }}">
        <p>
            ថ្ងៃទី{{ khmer_number($data->created_at->format('d')) }}
            ខែ{{ khmer_number($data->created_at->format('m')) }}
            ឆ្នំា{{ khmer_number($data->created_at->format('Y')) }}
        </p>

        <p>ស្នើសុំដោយ៖</p>
        <p>{{ @$data->creator_object->position_name ?: $data->requester->position->name_km }}</p>
        <img style="max-height: 60px; max-width: 180px;"
             src="{{ asset('/'.$data->requester->signature) }}"
             alt="Signature">
        <p>{{ @$data->creator_object->name ?: $data->requester->name }}</p>
    </div>

    <div style="width: {{ (($relatedCol*100)/$allCol).'%' }}">
        @foreach($data->reviewers() as $item)
            @if ($item->approve_status == config('app.approve_status_approve'))
                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                    <p>
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('d')) }}
                        ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('Y')) }}
                    </p>
                    <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                    <p>{{ @json_decode(@$item->user_object)->position_name ?: $item->position_name }}</p>
                    <img style="max-height: 60px; max-width: 180px;"
                         src="{{ asset('/'.$item->signature) }}"
                         alt="Signature">
                    <p>{{ @json_decode(@$item->user_object)->name ?: $item->name }}</p>
                </div>
            @else
                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                    <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                    <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                    <p>{{ @json_decode(@$item->user_object)->position_name ?: $item->position_name }}</p>
                </div>
            @endif
        @endforeach

    </div>

    <div style="width: {{ (100/$allCol).'%' }}">
        @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
            <p>
                ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('m')) }}
                ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}
            </p>
            <p>អនុម័តដោយ៖</p>
            <p>
              @if ($data->approver()->position_level == config('app.position_level_president') )
                {{ $data->forcompany->approver }}
              @else
                {{ @json_decode(@$data->approver()->user_object)->position_name ?: $data->approver()->position_name }}
              @endif
            </p>
            <img style="max-height: 60px; max-width: 180px;"
                 src="{{ asset('/'.$data->approver()->signature) }}"
                 alt="Signature">
            <p>
              {{ @check_nickname(@$data->approver()->position_level, $data->created_at ) }}
              {{ @json_decode(@$data->approver()->user_object)->name ?: $data->approver()->name }}
            </p>
        @else
            <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
            <p>អនុម័តដោយ៖</p>
            <p>
              @if ($data->approver()->position_level == config('app.position_level_president') )
                {{ $data->forcompany->approver }}
              @else
                {{ @json_decode(@$data->approver()->user_object)->position_name ?: $data->approver()->position_name }}
              @endif
            </p>
        @endif
    </div>
</div>