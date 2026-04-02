<div id="reviewer" style="padding: 15px; margin-bottom: 5px">
    <table class="table table-bordered">
        <span>តារាងអ្នកត្រួតពិនិត្យ និងអនុម័ត<br>Matrix approval chart</span>
        <tr>
            <th style="min-width: 30px; text-align: center">ល.រ<br>No</th>
            <th style="min-width: 150px;">ឈ្មោះ<br>Name</th>
            <th style="min-width: 270px;">មុខដំណែង<br>Position</th>
            <th style="min-width: 70px;">ព្រម<br>Approved</th>
            <th style="min-width: 70px;">មិនព្រម<br>Disapproved</th>
            <th colspan="2" class="text-center" style="max-width: 420px;">មតិយោបល់<br>Comment</th>
            <th style="width: 100px;" class="text-center">ថ្ងៃអនុម័ត<br>Approval Date</th>
        </tr>
        <?php $j = 1; ?>
        @foreach($reviewers as $key => $value)
            @if($value)
            <tr>
                <td style="text-align: center">{{ $j++ }}</td>
                <td>
                    @if (is_string(@$value->position) && strpos(@$value->position, 'short') !== false)
                        <button class="btn btn-xs btn-primary tooltipsign" style="margin-right: 2px; margin-bottom: 2px"
                                title="Short Signature" data-toggle="tooltip"
                                data-placement="top" type="button">
                        {{ @$value->name }}
                        </button>
                    @else
                        {{ @check_nickname(@$value->position_level, @$value->created_at ) }} 
                        {{ @$value->name ? @$value->name : @$value->user_name }}
                    @endif
                </td>
                <td>{{ $value->position_name }}</td>
                <td class="text-center">
                    <input disabled type="checkbox" @if ($value->approve_status == config('app.approve_status_approve')) checked @endif>
                </td>
                <td class="text-center">
                    <input disabled type="checkbox" 
                        @if ($value->approve_status == config('app.approve_status_reject') || $value->approve_status == config('app.approve_status_disable')) 
                            checked 
                        @endif
                    >
                </td>
                @if (@$value->approve_comment)
                    <td>{{ $value->approve_comment }}</td>
                @else
                    <td>N/A</td>
                @endif
                <td>
                    @if (@$value->comment_attach)
                        <a href="{{ asset('/'.@$value->comment_attach) }}" target="_self">
                            <img src="{{ asset('/'.@$value->comment_attach) }}" alt="file" style="max-height:40px; width: 40px; border: 1px solid;">
                        </a>
                    @else
                        N/A
                    @endif
                </td>
                <td class="text-center">
                    @if(@$value->approved_at)
                        @if(@$data->company_id == 2 || @$data->company_id == 6) 
                            <!-- show time only for MFI and MMI -->
                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime(@$value->approved_at))->format('d-m-Y h:i:s a')) }}
                        @else
                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime(@$value->approved_at))->format('d-m-Y')) }}
                        @endif
                    @endif
                </td>
            </tr>
            @endif
        @endforeach
    </table>

    @if(@$data->remark)
        <span>កំណត់សម្គាល់ៈ {{@$data->remark}}</span><br>
    @endif

    @if(@$data->attachment)
        <span>ឯកសារភ្ជាប់ៈ</span>
        @if(is_array($data->attachment))
            <?php $atts =  $data->attachment; ?>
            @foreach($atts as $att )
                <a class="reference_link" href="{{ asset($att->src) }}" target="_self">{{ $att->org_name }}</a><br>
            @endforeach
        @else
            <a class="reference_link" href="{{ asset('/'.@$data->attachment) }}" target="_self">{{@$data->att_name}}</a>
        @endif
    @endif

    @if($data->resign_id)
        <span>តំណភ្ជាប់ៈ</span>
        <a href="{{ route('resign.show', @$data->resign_id) }}" target="">
          <mark id="highlights"> {{ @$link_resign->title }} </mark>
        </a><br>
    @endif
    
</div>
