<?php
        $agreeBy = $data->reviewers()->where('position', 'agree_by')->first();
        $agreeByShort = $data->reviewers()->where('position', 'agree_by_short')->first();
        $reviewer = $data->reviewers()->where('position', 'reviewer')->first();
        $reviewerShort1 = $data->reviewers()->where('position', 'reviewer_short_1')->first();
        $reviewerShort2 = $data->reviewers()->where('position', 'reviewer_short_2')->first();
        $data->approver();
?>

<!-- start -->
    <div class="reviewer_section mb-4">
        
        <table class="border_hide">
            <tr>
                <td colspan="2">
                    <p class="mb-0">រៀបចំស្នើសុំ | Requestor</p>
                </td>
            </tr>
            <tr>
                <td style="min-width: 100px">
                    <img src="{{ asset('/'.$data->requester->signature) }}" alt="signature" >
                    <br>
                    <span>{{ @$data->creator_object->name ?: $data->requester->name }}-{{ @$data->requester->name_en }}</span><br>
                    <p style="border-top:1px solid;">អ្នកស្នើសុំ</p>
                </td>
                <td style="vertical-align: bottom;">
                    <span>{{ $data->created_at->format('d/m/Y') }}</span>
                    <p style="border-top:1px solid;">កាលបរិច្ឆេទ</p>
                </td>
            </tr>

            @if ($agreeBy)
                <tr>
                    <td colspan="2">
                        <p class="mb-0">យល់ព្រម | Supervisor (c)</p>
                    </td>
                </tr>
                <tr>
                    <td style="min-width: 100px;">
                        @if(@$agreeBy->approved_at && $agreeBy->approve_status == config('app.approve_status_approve'))
                            <img src="{{ asset('/'.@$agreeBy->signature) }}" alt="signature">
                        @endif
                        <br>
                        <span>{{ @json_decode($agreeBy->user_object)->name ?: @$agreeBy->name }}-{{ @json_decode($agreeBy->user_object)->name_en }}</span>
                        <p style="border-top:1px solid;">អ្នកគ្រប់គ្រងផ្ទាល់</p>
                    </td>
                    <td style="vertical-align: bottom;">
                        @if ($agreeByShort && $agreeByShort->approve_status == config('app.approve_status_approve'))
                            <img title="{{ @$agreeByShort->name }}" style="height: 20px;" src="{{ asset('/'.$agreeByShort->short_signature) }}" alt="short" >
                        @endif
                        <br>
                        @if (@$agreeBy->approved_at)
                            <span>{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$agreeBy->approved_at))->format('d/m/Y') }}</span>
                        @endif
                        <p style="border-top:1px solid;">កាលបរិច្ឆេទ</p>
                    </td>
                </tr>
            @endif

            @if ($reviewer)
                <tr>
                    <td colspan="2">
                        <p class="mb-0">ត្រួតពិនិត្យ និងផ្ទៀងផ្ទាត់</p>
                    </td>
                </tr>
                <tr>
                    <td style="min-width: 100px;">
                        @if(@$reviewer->approved_at && $reviewer->approve_status == config('app.approve_status_approve'))
                            <img src="{{ asset('/'.$reviewer->signature) }}" alt="signature" >
                        @endif
                        <br>
                        <span>{{ @json_decode($reviewer->user_object)->name ?: $reviewer->name }}-{{@json_decode($reviewer->user_object)->name_en}}</span>
                    </td>
                    <td style="vertical-align: bottom;">
                        @if ($reviewerShort1 && $reviewerShort1->approve_status == config('app.approve_status_approve'))
                            <img title="{{ @$reviewerShort1->name }}" style="height: 20px;" src="{{ asset('/'.$reviewerShort1->short_signature) }}" alt="short" >
                        @endif
                        @if ($reviewerShort2 && $reviewerShort2->approve_status == config('app.approve_status_approve'))
                            <img title="{{ @$reviewerShort2->name }}" style="height: 20px;" src="{{ asset('/'.$reviewerShort2->short_signature) }}" alt="short" >
                        @endif
                        <br>
                        @if (@$reviewer->approved_at)
                            <span>{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d/m/Y') }}</span>
                        @endif
                        <p style="border-top:1px solid;">កាលបរិច្ឆេទ</p>
                    </td>
                </tr>
            @endif

            @if ($data->approver())
                <tr>
                    <td colspan="2">
                        <p class="mb-0">អនុម័តដោយ | Approved By</p>
                    </td>
                </tr>
                <tr>
                    <td style="min-width: 100px;">
                        @if($data->approver()->approve_status == config('app.approve_status_approve'))
                            <img src="{{ asset('/'.$data->approver()->signature) }}" alt="signature" >
                        @endif
                        <br>
                        <span>{{ @json_decode(@$data->approver()->user_object)->name ?: $data->approver()->name }}-{{ @json_decode(@$data->approver()->user_object)->name_en }}</span>
                        <p style="border-top:1px solid;">ហត្ថលេខា | Signature</p>
                    </td>
                    <td style="vertical-align: bottom;">
                        @if($data->approver()->approve_status == config('app.approve_status_approve'))
                            <span>{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d/m/Y') }}</span>
                        @endif
                        <p style="border-top:1px solid;">កាលបរិច្ឆេទ</p>
                        
                    </td>
                </tr>
            @endif

        </table>
    </div>

    <p></p>
