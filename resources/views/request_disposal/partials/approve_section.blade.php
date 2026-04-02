<?php
$reviewers = $data->reviewers();
$approver = $data->approver();
$k = 0;
//dd($reviewers[0]);
$approve = config('app.approve_status_approve')
?>

<!-- Signature First Row: 2 Reviewers 1 Requester -->
<div class="signature">
    @for ($i = 0; $i < 2; $i++)
        @if (isset($reviewers[$i]))
            <div class="col">
                @if($reviewers[$i]->approve_status == $approve)
                    <p>ថ្ងៃទី {{ stringToDay($reviewers[$i]->approved_at) }} ខែ {{ stringToMonth($reviewers[$i]->approved_at) }} ឆ្នំា {{ stringToYear($reviewers[$i]->approved_at) }}</p>
                    <p>បានត្រួតពិនិត្យដោយ</p>
                    <p>{{ $reviewers[$i]->position_name }}</p>
                    <p><img style="height: 60px" src="{{ asset('/'.$reviewers[$i]->signature) }}" alt=""></p>
                    <p>{{ $reviewers[$i]->name }}</p>
                @else
                    <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                    <p>បានត្រួតពិនិត្យដោយ</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                @endif
            </div>
        @endif
    @endfor
    <!-- Offset 1 Col if Reviewers < 2 -->
    @if(count($reviewers) < 1)
        <div class="col"><p>&nbsp;</p></div>
    @endif

    <div class="col">
        <p>ធ្វើនៅ ភ្នំពេញ, ថ្ងៃទី​{{ $data->created_at->format('d') }} ខែ {{ $data->created_at->format('m') }}  ឆ្នាំ២០២០</p>
        <p>រៀបចំដោយ</p>
        <p>{{ $data->requester->position->name_km }}</p>
        <p><img style="height: 60px" src="{{ asset('/'.$data->requester->signature) }}" alt=""></p>
        <p class="requester-name">{{ $data->requester->name }}</p>
    </div>
</div>


<hr>
<!-- Signature Second Row: 2: If reviewers < 5 have 1 Approver else move Approver to Third Row -->
@if(count($reviewers) < 5)
    <div class="signature">
        @for ($j = 2; $j < 4; $j++)
            @if (isset($reviewers[$j]))
                <?php $k++ ?>
                <div class="col">
                    @if($reviewers[$j]->approve_status == $approve)
                        <p>ថ្ងៃទី {{ stringToDay($reviewers[$j]->approved_at) }} ខែ {{ stringToMonth($reviewers[$j]->approved_at) }} ឆ្នំា {{ stringToYear($reviewers[$j]->approved_at) }}</p>
                        <p>បានត្រួតពិនិត្យដោយ</p>
                        <p>{{ $reviewers[$j]->position_name }}</p>
                        <p><img style="height: 60px" src="{{ asset('/'.$reviewers[$j]->signature) }}" alt=""></p>
                        <p>{{ $reviewers[$j]->name }}</p>
                    @else
                        <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                        <p>បានត្រួតពិនិត្យដោយ</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                    @endif
                </div>
            @else
                <!-- Offset 1 Col if Reviewers < 2 -->
                <div class="col"><p>&nbsp;</p></div>
            @endif
        @endfor
        @if(count($reviewers) < 5)
            <div class="col">
                @if($approver->approve_status == $approve)
                    <p>ថ្ងៃទី {{ stringToDay($approver->approved_at) }} ខែ {{ stringToMonth($approver->approved_at) }} ឆ្នំា {{ stringToYear($approver->approved_at) }}</p>
                    <p>បានត្រួតពិនិត្យដោយ</p>
                    <p>{{ $approver->position_name }}</p>
                    <p><img style="height: 60px" src="{{ asset('/'.$approver->signature) }}" alt=""></p>
                    <p>{{ $approver->name }}</p>
                @else
                    <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                    <p>អនុម័តដោយ</p>
                    <p>ប្រធាននាយិកាប្រតិបត្តិ</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                @endif
            </div>
        @endif
    </div>
@endif

{{--<br><br><br>--}}
{{--<br><br>--}}

{{--@if(count($reviewerPosition) > 2)--}}
{{--    <div class="signature">--}}
{{--        <div class="col">--}}
{{--            @if(count($reviewerPosition) > 2)--}}
{{--                <p>បានត្រួតពិនិត្យដោយ</p>--}}
{{--                <p>{{ $reviewerPosition[2]->name_km }}</p>--}}
{{--                @if($reviewerPosition[2]->status == 2)--}}
{{--                    <p><img style="height: 60px" src="{{ asset('/'.$reviewerPosition[2]->signature) }}" alt=""></p>--}}
{{--                    <p>{{ $reviewerPosition[2]->reviewer_name }}</p>--}}
{{--                @endif--}}
{{--            @else--}}
{{--                <p>&nbsp;</p>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--        <div class="col">--}}
{{--            @if(count($reviewerPosition) > 3)--}}
{{--                <p>បានត្រួតពិនិត្យដោយ</p>--}}
{{--                <p>{{ $reviewerPosition[3]->name_km }}</p>--}}
{{--                @if($reviewerPosition[3]->status == 2)--}}

{{--                    <p><img style="height: 60px" src="{{ asset('/'.$reviewerPosition[3]->signature) }}" alt=""></p>--}}
{{--                    <p>{{ $reviewerPosition[3]->reviewer_name }}</p>--}}
{{--                @endif--}}
{{--            @else--}}
{{--                <p>&nbsp;</p>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--        <div class="col">--}}
{{--            @if(count($reviewerPosition) > 4)--}}
{{--                <p>បានត្រួតពិនិត្យដោយ</p>--}}
{{--                <p>{{ $reviewerPosition[4]->name }}</p>--}}
{{--                @if($reviewerPosition[4]->status == 2)--}}
{{--                    <p><img style="height: 60px" src="{{ asset('/'.$reviewerPosition[4]->signature) }}" alt=""></p>--}}
{{--                    <p>{{ $reviewerPosition[4]->reviewer_name }}</p>--}}
{{--                @endif--}}
{{--            @else--}}
{{--                <div class="col">--}}
{{--                    <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>--}}
{{--                    <p>អនុម័តដោយ</p>--}}
{{--                    <p>ប្រធាននាយិកាប្រតិបត្តិ</p>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}

{{--<br><br><br>--}}
{{--<br><br><br>--}}

{{--<div class="signature">--}}
{{--    <div class="col">--}}
{{--        @if(count($reviewerPosition) > 5)--}}
{{--            <p>បានត្រួតពិនិត្យដោយ</p>--}}
{{--            <p>{{ $reviewerPosition[5]->name_km }}</p>--}}
{{--            @if($reviewerPosition[5]->status == 2)--}}
{{--                <p><img style="height: 60px" src="{{ asset('/'.$reviewerPosition[5]->signature) }}" alt=""></p>--}}
{{--                <p>{{ $reviewerPosition[5]->reviewer_name }}</p>--}}
{{--            @endif--}}
{{--        @else--}}
{{--            <p>&nbsp;</p>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
{{--<!----------------------------------------------->--}}
{{--<!----------------------------------------------->--}}
{{--<div class="signature">--}}
{{--    <div class="col">--}}
{{--        @if(count($reviewerPosition) > 5)--}}
{{--            <p>បានត្រួតពិនិត្យដោយ</p>--}}
{{--            <p>{{ $reviewerPosition[5]->name_km }}</p>--}}
{{--            @if($reviewerPosition[5]->status == 2)--}}
{{--                <p><img style="height: 60px" src="{{ asset('/'.$reviewerPosition[5]->signature) }}" alt=""></p>--}}
{{--                <p>{{ $reviewerPosition[5]->reviewer_name }}</p>--}}
{{--            @endif--}}
{{--        @else--}}
{{--            <p>&nbsp;</p>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--    <div class="col">--}}
{{--        @if(count($reviewerPosition) > 6)--}}
{{--            <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>--}}
{{--            <p>បានត្រួតពិនិត្យដោយ</p>--}}
{{--            <p>{{ $reviewerPosition[6]->name_km }}</p>--}}
{{--            @if($reviewerPosition[1]->status == 6)--}}
{{--                <p><img style="height: 60px" src="{{ asset('/'.$reviewerPosition[1]->signature) }}" alt=""></p>--}}
{{--                <p>{{ $reviewerPosition[6]->reviewer_name }}</p>--}}
{{--            @endif--}}
{{--        @else--}}
{{--            <p>&nbsp;</p>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--    <div class="col">--}}
{{--        @if(count($reviewerPosition) > 5)--}}
{{--            <div class="col">--}}
{{--                <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>--}}
{{--                <p>អនុម័តដោយ</p>--}}
{{--                <p>ប្រធាននាយិកាប្រតិបត្តិ</p>--}}
{{--            </div>--}}
{{--        @else--}}
{{--            <p>&nbsp;</p>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
{{--<hr>--}}


{{--            <div style="float: right">--}}
{{--                @if ($data)--}}
{{--                    <h1>ប្រធាននាយិកាប្រតិបត្តិ</h1>--}}
{{--                    <img style="width: 150px;"--}}
{{--                         src="{{ asset('/'.$data->ceo->signature) }}"--}}
{{--                         alt="Signature">--}}
{{--                @else--}}

{{--                    <p>ថ្ងៃទី........ខែ..........ឆ្នំា2019</p>--}}
{{--                    <p>អនុម័តដោយ៖</p>--}}
{{--                    <p>ប្រធានក្រុមប្រឹក្សាភិបាល</p>--}}

{{--                @endif--}}
{{--            </div>--}}
{{--            <div class="copy" style="clear: both">--}}
{{--                <div class="left" style="float: left">--}}
{{--                    <h1>ចម្លងជូន</h1>--}}
{{--                    <p>- ដូចប្រការ {{ khmer_number(count($data->point)) }} <b>"ដើម្បីអនុវត្ត"</b></p>--}}
{{--                    <p>- ឯកសារ_កាលប្បវត្តិ</p>--}}
{{--                </div>--}}
{{--                <div class="right" style="float: right;">--}}
{{--                    <h1>គួន ធីតា</h1>--}}
{{--                </div>--}}
{{--            </div>--}}
