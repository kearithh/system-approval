@extends('stsk_request.layouts.portrait')

@section('header')
    @include('request.preview_action')
    <div class="logo" style="text-align: center; padding-top: 70px">
        <img src="{{ asset($data->forcompany->logo) }}" alt="logo" style="width: 550px">
    </div>
@endsection

@section('content')
    <div style="width: 1024px; height: 1355px; margin: auto;background: white;">

        <br>
        <div class="contain" style="padding: 0 110px">
            <div class="title" style="text-align: center; font-family: 'Khmer OS Muol Light'">
                <h1 style="font-size: 16px">{{ $data->requester()->position->name }}</h1>
                <h1 style="font-size: 16px">សូមគោរពជូន</h1>
                <!-- <h1 style="font-size: 16px">
                    លោកស្រីប្រធានក្រុមប្រឹក្សាភិបាលនៃក្រុមហ៊ុន អេសធីអេសខេ អ៊ិនវេសម៉ិន គ្រុប លីមីធីត
                </h1> -->
                <h1 style="font-size: 16px">
                    {{ $data->forcompany->approver}}
                    {{ $data->forcompany->long_name }}
                </h1>
            </div>
            <br>
            <div class="content">
                <ul>
                    <li>
                        {{--                    <strong>តាមរយ</strong>៖ {{ $data->position->name }}--}}
                    </li>
                    <li>
                        <strong>កម្មវត្ថុ</strong>៖ {{ $data->purpose }}
                    </li>
                    @if ($data->reason)
                        <li>
                            <strong>មូលហេតុ</strong>៖ {{ $data->reason }}
                        </li>
                    @endif
                </ul>
                <strong>
                    ដោយមានការចំណាយលម្អិតដូចខាងក្រោម:
                </strong>
                <?php
                $vat = false;
                foreach ($data->items as $item) {
                    if ($item->vat){
                        $vat = true;
                    }
                }
                ?>

                <table>
                    <thead>
                    <tr>
                        <td>ល.រ</td>
                        <td>ឈ្មោះ</td>
                        <td>បរិយាយ</td>
                        <td>បរិមាណ</td>
                        <td>តម្លៃរាយ</td>
                        @if ($vat)
                            <td>ពន្ធអាករ(%)</td>
                        @endif
                        <td>សរុប</td>
                        <td>ផ្សេងៗ</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $total = 0; ?>
                    @foreach($data->items as $key => $item)
                        <?php $subtotal = $item->qty*$item->unit_price + ($item->qty*$item->unit_price*$item->vat)/100 ?>
                        <tr>
                            <td style="text-align: center;">{{ $key +1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->desc }}</td>
                            <td style="text-align: center;">{{ $item->qty }}</td>
                            <td style="text-align: right;">
                                @if($item->currency=='KHR')
                                    {{ number_format($item->unit_price, 2) }} ៛
                                @else
                                    $ {{ number_format($item->unit_price, 2) }}
                                @endif
                            </td>
                            @if ($vat)
                                <td style="text-align: center;">{{ $item->vat }}%</td>
                            @endif
                            <td style="text-align: right;">
                                @if($item->currency=='KHR')
                                    {{ number_format(($subtotal), 2) }} ៛
                                @else
                                    $ {{ number_format(($subtotal), 2) }}
                                @endif
                            </td>
                            <td>{{ $item->remark }}</td>
                            <?php $total += $subtotal; ?>
                        </tr>
                    @endforeach
                    <tr style="font-weight: 700">
                        <td colspan="5" style="text-align: right">សរុប</td>
                        <td colspan="3" style="text-align: center;">
                            {{'$ '. number_format(($data->total_amount_usd),2)}} &emsp;
                            {{ number_format(($data->total_amount_khr),2) .' ៛'}}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                <span>
                    តបតាមកម្មវត្ថុ និងមូលហេតុខាងលើ{{prifixGender($data->requester()->gender)}}ស្នើសុំការអនុញ្ញតពីលោកស្រី{{ $data->forcompany->approver}} ក្នុងការចំណាយដែលមានទឹកប្រាក់សរុបចំនួន {{'$ '. number_format(($data->total_amount_usd),2) }} និង​ {{ number_format(($data->total_amount_khr),2) .' ៛'}} ។

{{--                    {{ num2khtext(123405.5) }}--}}
                    {{--                    (ដប់ពីរដុល្លា នឹងដប់ប្រាំពីរសេន)។--}}
                </span>
                    <p>
                        អាស្រ័យដូចបានជំរាបជូនខាងលើ សូមលោកស្រី{{ $data->forcompany->approver}}មេត្តាពិនិត្យ និងអនុញ្ញាតតាមការគួរ។
                    </p>
                    <p>
                        សូមលោកស្រី{{$data->forcompany->approver}}មេត្តាទទួលនូវសេចក្ដីគោរពដ៏ខ្ពង់ខ្ពស់ពី{{prifixGender($data->requester()->gender)}}។
                    </p>
                </div>

                <?php

                $relatedCol = count($data->reviewers());
                $allCol = $relatedCol + 2;
                ?>

                @if ($data->requester()->branch_id)
                    <div class="signature">
                        <div style="width: {{ (100/$allCol).'%' }}">
                            <p>
                                ថ្ងៃទី {{ khmer_number($data->updated_at->format('d')) }}
                                ខែ {{ khmer_number($data->updated_at->format('m')) }}
                                ឆ្នំា{{ khmer_number($data->updated_at->format('Y')) }}
                            </p>

                            <p>ស្នើសុំដោយ៖</p>
                            <p>{{ $data->requester()->position->name_km }}</p>
                            <img style="height: 60px;"
                                 src="{{ asset('/'.$data->requester()->signature) }}"
                                 alt="Signature">
                            <p>{{ $data->requester()->name }}</p>
                        </div>

                        <div style="width: {{ (($relatedCol*100)/$allCol).'%' }}">
                            <p>
                                @if (@$data->reviewers()->first()->approved_at)
                                    ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->reviewers()->first()->approved_at))->format('d')) }}
                                    ខែ {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->reviewers()->first()->approved_at))->format('m')) }}
                                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->reviewers()->first()->approved_at))->format('Y')) }}
                                @else
                                    ថ្ងៃទី.....ខែ......ឆ្នំា២០២០
                                @endif
                            </p>
                            <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>

                            <div style="margin-top: -15px">
                                @foreach($data->reviewers() as $item)
                                    @if ($item->approve_status == config('app.approve_status_approve'))
                                        <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                            <p>{{ $item->position_name_km }}</p>
                                            <img style="height: 60px;"
                                                 src="{{ asset('/'.$item->signature) }}"
                                                 alt="Signature">
                                            <p>{{ $item->name }}</p>
                                        </div>
                                    @else
                                        <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                            <p>{{ $item->position_name }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                        </div>

                        <div style="width: {{ (100/$allCol).'%' }}">
                            @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                                <p>
                                <p>
                                    ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                                    ខែ {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('m')) }}
                                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}
                                </p>
                                <p>អនុម័តដោយ៖</p>
                                <p>ប្រធាននាយកដ្ឋានប្រតិបត្តិការ</p>
                                <img style="height: 60px;"
                                     src="{{ asset('/'.$data->approver()->signature) }}"
                                     alt="Signature">
                            @else
                                <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                                <p>អនុម័តដោយ៖</p>
                                <p>ប្រធាននាយកដ្ឋានប្រតិបត្តិការ</p>
                            @endif
                        </div>
                    </div>


                @else
                    <div class="signature">
                        <div style="width: {{ (100/$allCol).'%' }}">
                            <p>
                                ថ្ងៃទី {{ khmer_number($data->updated_at->format('d')) }}
                                ខែ {{ khmer_number($data->updated_at->format('m')) }}
                                ឆ្នំា{{ khmer_number($data->updated_at->format('Y')) }}
                            </p>

                            <p>ស្នើសុំដោយ៖</p>
                            <p>{{ $data->requester()->position->name_km }}</p>
                            <img style="height: 60px;"
                                 src="{{ asset('/'.$data->requester()->signature) }}"
                                 alt="Signature">
                            <p>{{ $data->requester()->name }}</p>
                        </div>

                        <div style="width: {{ (($relatedCol*100)/$allCol).'%' }}">
                            <p>
                                {{--                            {{ dd($data->reviewers()->first()->approved_at) }}--}}
                                @if (@$data->reviewers()->first()->approved_at)
                                    ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->reviewers()->first()->approved_at))->format('d')) }}
                                    ខែ {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->reviewers()->first()->approved_at))->format('m')) }}
                                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->reviewers()->first()->approved_at))->format('Y')) }}
                                @else
                                    ថ្ងៃទី.....ខែ......ឆ្នំា២០២០
                                @endif
                            </p>
                            <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>

                            <div style="margin-top: -15px">
                                @foreach($data->reviewers() as $item)
                                    @if ($item->approve_status == config('app.approve_status_approve'))
                                        <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                            <p>{{ $item->position_name_km ? $item->position_name_km : $item->position_name }}</p>
                                            <img style="height: 60px;"
                                                 src="{{ asset('/'.$item->signature) }}"
                                                 alt="Signature">
                                            <p>{{ $item->name }}</p>
                                        </div>
                                    @else
                                        <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                            <p>{{ $item->position_name_km ? $item->position_name_km : $item->position_name }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                        </div>

                        <div style="width: {{ (100/$allCol).'%' }}">
                            @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                                <p>
                                    ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                                    ខែ {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('m')) }}
                                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}

                                </p>
                                <p>អនុម័តដោយ៖</p>
                                <p>{{ typePositionCEO($data->forcompany->type) }} </p>
                                <img style="height: 60px;"
                                     src="{{ asset('/'.$data->approver()->signature) }}"
                                     alt="Signature">

                                <p>{{  ($data->approver()->name) }}</p>
                            @else
                                <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                                <p>អនុម័តដោយ៖</p>
                                <p>{{ $data->forcompany->approver}}</p>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
@endsection

@section('footer')
    <div style="width: 1024px; margin: auto; margin-top: -66.06px">
        <div class="footer" style="background: orange; clear: both;">
            <img src="{{ asset($data->forcompany->footer) }}" alt="footer" style="width: 1024px; margin-bottom: -10px">
        </div>
    </div>
@endsection
