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
        <p class="mb-0">រៀបចំស្នើសុំ | Requestor</p>
        <div class="">
            <img src="{{ asset('/'.$data->requester->signature) }}" alt="signature" >
            <div style="float: right">
                @if ($agreeBy && $agreeBy->approve_status == config('app.approve_status_approve'))
                    <img title="យល់ព្រម | Initial approved 1 - {{ @$agreeBy->name }}" style="height: 20px;" src="{{ asset('/'.$agreeBy->short_signature) }}" alt="short" >
                @endif
                @if ($agreeByShort && $agreeByShort->approve_status == config('app.approve_status_approve'))
                    <img title="យល់ព្រម | Initial approved 2 - {{ @$agreeByShort->name }}" style="height: 20px;" src="{{ asset('/'.$agreeByShort->short_signature) }}" alt="short" >
                @endif
                @if ($reviewerShort2 && $reviewerShort2->approve_status == config('app.approve_status_approve'))
                    <img title="យល់ព្រម | Initial approved 2 - {{ @$reviewerShort2->name }}" style="height: 20px; float: right" src="{{ asset('/'.$reviewerShort2->short_signature) }}" alt="short" >
                @endif
            </div>
            <br>
            <span>{{ @$data->creator_object->name ?: $data->requester->name }}-{{ @$data->requester->name_en }}</span>

        </div>
        <div class="row">
            <div class="col-sm-7">
                <p style="border-top:1px solid; float: left">អ្នកស្នើសុំ | Requestor</p>
            </div>
            <div class="col-sm-5">
                <span style="position: absolute; margin-top: -18px">{{ $data->created_at->format('d/m/Y') }}</span>
                <p style="border-top:1px solid;">កាលបរិច្ឆេទ | Date</p>
            </div>
        </div>
    </div>

<!-- 
    @if ($agreeBy)
        <div class="reviewer_section">

            <p class="mb-0">យល់ព្រម | Initial approved 1 </p>
            <div class="">

                @if(@$agreeBy->approved_at && $agreeBy->approve_status == config('app.approve_status_approve'))
                    
                    @if ($agreeByShort && $agreeByShort->approve_status == config('app.approve_status_approve'))
                        <img title="{{ @$agreeByShort->name }}" style="height: 20px; float: right" src="{{ asset('/'.$agreeByShort->short_signature) }}" alt="signature" >
                    @endif
                    <br>
                @else
                    @if ($agreeByShort && $agreeByShort->approve_status == config('app.approve_status_approve'))
                        <img title="{{ @$agreeByShort->name }}" style="height: 20px; float: right" src="{{ asset('/'.$agreeByShort->short_signature) }}" alt="signature" >
                    @endif
                    <br>
                    <br>
                @endif
                <span>{{ @json_decode($agreeBy->user_object)->name ?: @$agreeBy->name }}-{{ @json_decode($agreeBy->user_object)->name_en }}</span>
            </div>
            <div class="row">
                <div class="col-sm-7">
                    <p style="border-top:1px solid; float: left">ហត្ថលេខា | Signature</p>
                </div>
                <div class="col-sm-5">
                    @if (@$agreeBy->approved_at)
                        <span style="position: absolute; margin-top: -18px">{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$agreeBy->approved_at))->format('d/m/Y') }}</span>
                    @endif
                    <p style="border-top:1px solid;">កាលបរិច្ឆេទ | Date</p>
                </div>
            </div>
        </div>
    @endif

    @if ($agreeBy)
        <div class="reviewer_section">

            {{-- <p class="mb-0">យល់ព្រម | Supervisor (c)</p> --}}
            <p class="mb-0">យល់ព្រម | Initial approved 2 </p>
            <div class="">

                @if(@$agreeBy->approved_at && $agreeBy->approve_status == config('app.approve_status_approve'))
                    
                    @if ($agreeByShort && $agreeByShort->approve_status == config('app.approve_status_approve'))
                        <img title="យល់ព្រម | Initial approved 2 - {{ @$agreeByShort->name }}" style="height: 20px; float: right" src="{{ asset('/'.$agreeByShort->short_signature) }}" alt="signature" >
                    @endif
                    <br>
                @else
                    @if ($agreeByShort && $agreeByShort->approve_status == config('app.approve_status_approve'))
                        <img title="យល់ព្រម | Initial approved 2 - {{ @$agreeByShort->name }}" style="height: 20px; float: right" src="{{ asset('/'.$agreeByShort->short_signature) }}" alt="signature" >
                    @endif
                    <br>
                    <br>
                @endif
                <span>{{ @json_decode($agreeBy->user_object)->name ?: @$agreeBy->name }}-{{ @json_decode($agreeBy->user_object)->name_en }}</span>
            </div>
            <div class="row">
                <div class="col-sm-7">
                    <p style="border-top:1px solid; float: left">យល់ព្រម | Initial approved 2</p>
                </div>
                <div class="col-sm-5">
                    @if (@$agreeBy->approved_at)
                        <span style="position: absolute; margin-top: -18px">{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$agreeBy->approved_at))->format('d/m/Y') }}</span>
                    @endif
                    <p style="border-top:1px solid;">កាលបរិច្ឆេទ | Date</p>
                </div>
            </div>
        </div>
    @endif
-->

    @if ($reviewer)
        <div class="reviewer_section mb-4">
            <p class="mb-0">ត្រួតពិនិត្យ | Verification</p> 
            <div class="">
                @if(@$reviewer->approved_at && $reviewer->approve_status == config('app.approve_status_approve'))
                    <img src="{{ asset('/'.$reviewer->signature) }}" alt="signature" >
                @else
                    <br><br>
                @endif
                <span>{{ @json_decode($reviewer->user_object)->name ?: $reviewer->name }}-{{ @json_decode($reviewer->user_object)->name_en }}</span>
            </div>
            <div class="row">
                <div class="col-sm-7">
                    <p style="border-top:1px solid; float: left">ហត្ថលេខា | Signature</p>
                </div>
                <div class="col-sm-5">
                    @if (@$reviewer->approved_at)
                        <span style="position: absolute; margin-top: -18px">{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d/m/Y') }}</span>
                    @endif

                    <p style="border-top:1px solid;">កាលបរិច្ឆេទ | Date</p>
                </div>
            </div>
        </div>
    @endif

    @if ($reviewerShort1)
        <div class="reviewer_section mb-4">
            <p class="mb-0">អនុម័តដោយ | Final Approved 1</p>
            <div class="" >
                @if(@$reviewerShort1->approved_at && $reviewerShort1->approve_status == config('app.approve_status_approve'))
                    <img src="{{ asset('/'.$reviewerShort1->signature) }}" alt="signature" >
                @else
                    <br>
                    <br>
                @endif
                <span>{{ @json_decode($reviewerShort1->user_object)->name ?: $reviewerShort1->name }}-{{ @json_decode($reviewerShort1->user_object)->name_en }}</span>
            </div>
            <div class="row">
                <div class="col-sm-7">
                    <p style="border-top:1px solid; float: left">ហត្ថលេខា | Signature</p>
                </div>
                <div class="col-sm-5">
                    @if (@$reviewerShort1->approved_at)
                        <span style="position: absolute; margin-top: -18px">{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime($reviewerShort1->approved_at))->format('d/m/Y') }}</span>
                    @endif

                    <p style="border-top:1px solid;">កាលបរិច្ឆេទ | Date</p>
                </div>
            </div>
        </div>
    @endif

    <!-- start -->
    @if ($data->approver())
        <div class="reviewer_section">
            <p class="mb-0">អនុម័តដោយ | Final Approved 2 </p>
            <div class="" >
                @if($data->approver()->approve_status == config('app.approve_status_approve'))
                    <img src="{{ asset('/'.$data->approver()->signature) }}" alt="signature" >
                @else
                    <br>
                    <br>
                @endif
                <span>{{ @json_decode(@$data->approver()->user_object)->name ?: $data->approver()->name }}-{{ @json_decode(@$data->approver()->user_object)->name_en }}</span>
            </div>
            <div class="row">
                <div class="col-sm-7">
                    <p style="border-top:1px solid; float: left">ហត្ថលេខា | Signature</p>
                </div>
                <div class="col-sm-5">
                    @if($data->approver()->approve_status == config('app.approve_status_approve'))
                        <span style="position: absolute; margin-top: -18px">{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d/m/Y') }}</span>
                    @endif

                    <p style="border-top:1px solid;">កាលបរិច្ឆេទ | Date</p>
                </div>
            </div>
        </div>
    @endif

    {{-- @if ($data->approver())
        <div class="reviewer_section">
            <p class="mb-0">អនុម័តដោយ | Final Approved 1 </p>
            <div class="" >
                @if($data->approver()->approve_status == config('app.approve_status_approve'))
                    <img src="{{ asset('/'.$data->approver()->signature) }}" alt="signature" >
                @else
                    <br>
                    <br>
                @endif
                <span>{{ @json_decode(@$data->approver()->user_object)->name ?: $data->approver()->name }}-{{ @json_decode(@$data->approver()->user_object)->name_en }}</span>
            </div>
            <div class="row">
                <div class="col-sm-7">
                    <p style="border-top:1px solid; float: left">ហត្ថលេខា | Signature</p>
                </div>
                <div class="col-sm-5">
                    @if($data->approver()->approve_status == config('app.approve_status_approve'))
                        <span style="position: absolute; margin-top: -18px">{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d/m/Y') }}</span>
                    @endif

                    <p style="border-top:1px solid;">កាលបរិច្ឆេទ | Date</p>
                </div>
            </div>
        </div>
    @endif

    @if ($data->approver())
        <div class="reviewer_section">
            <p class="mb-0">អនុម័តដោយ | Final Approved 2</p>
            <div class="" >
                @if($data->approver()->approve_status == config('app.approve_status_approve'))
                    <img src="{{ asset('/'.$data->approver()->signature) }}" alt="signature" >
                @else
                    <br>
                    <br>
                @endif
                <span>{{ @json_decode(@$data->approver()->user_object)->name ?: $data->approver()->name }}-{{ @json_decode(@$data->approver()->user_object)->name_en }}</span>
            </div>
            <div class="row">
                <div class="col-sm-7">
                    <p style="border-top:1px solid; float: left">ហត្ថលេខា | Signature</p>
                </div>
                <div class="col-sm-5">
                    @if($data->approver()->approve_status == config('app.approve_status_approve'))
                        <span style="position: absolute; margin-top: -18px">{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d/m/Y') }}</span>
                    @endif

                    <p style="border-top:1px solid;">កាលបរិច្ឆេទ | Date</p>
                </div>
            </div>
        </div>
    @endif --}}
    <!-- end -->
    <p></p>
