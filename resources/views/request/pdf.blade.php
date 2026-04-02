<!DOCTYPE html>
<html>

<head>
    <title>E-Approval</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @if(! config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

        @include('adminlte::plugins', ['type' => 'css'])

        @yield('adminlte_css_pre')

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

        @yield('adminlte_css')
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: 'Times New Roman', 'Khmer OS Content';
            font-weight: 400;
        }

        strong {
            font-family: 'Khmer OS Muol Light';
            font-size: 14px;
            font-weight: 400;
        }

        h1 {
            font-family: 'Khmer OS Muol Light';
            font-weight: 400;
        }

        .desc {
            text-align: center;
        }

        .signature {
            padding-top: 50px;
        }

        .signature > div {
            float: left;
            width: 33.33%;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box
        }
        .related {
            float: left;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box;
            text-overflow: ellipsis;
        }

        table tr td {
            border: 1px solid #585858;
            padding: 5px 5px;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        table thead tr {
            /*background: orange;*/
            font-weight: 700;
            text-align: center;
        }

        ul li {
            list-style: none;
            margin-left: -40px;
        }
        @media print {
            .footer img {
                position: absolute;
                bottom: 0;
                margin-bottom: 0 !important;
                width: 100%;
            }
            @page
            {
                size: A4 portrait;
                margin: 0;
                width: 29.7cm;
                height: 21cm;
            }
        }
    </style>
</head>

<body style="background: #dadada">
@include('request.preview_action')
<div style="width: 1024px; min-height: 1355px; margin: auto; background: white;">
    <div class="logo" style="text-align: center; padding-top: 70px">
        <img src="{{ asset($data->forcompany->logo) }}" alt="logo" style="width: 550px">
    </div>
    <br>
    <div class="contain" style="padding: 0 110px">
        <div class="title" style="text-align: center; font-family: 'Khmer OS Muol Light'">
            <h1 style="font-size: 16px">{{ $data->requester()->position->name }}</h1>
            <h1 style="font-size: 16px">សូមគោរពជូន</h1>
            <!-- <h1 style="font-size: 16px">
                លោកស្រីប្រធានក្រុមប្រឹក្សាភិបាលនៃក្រុមហ៊ុន អេសធីអេសខេ អ៊ិនវេសម៉ិន គ្រុប លីមីធីត
            </h1> -->
            <h1 style="font-size: 16px">
                @if ($data->requester()->branch_id)
                    {{ $data->approver()->position_name }}
                @else
                    {{ $data->forcompany->approver}}
                @endif
                {{ $data->forcompany->long_name }}
            </h1>
        </div>
        <br>
        <div class="content">
            <ul>
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
                                {{ number_format($item->unit_price) }} ៛
                            @else
                                $ {{ number_format($item->unit_price, 2) }}
                            @endif
                        </td>
                        @if ($vat)
                            <td style="text-align: center;">{{ $item->vat }}%</td>
                        @endif
                        <td style="text-align: right;">
                            @if($item->currency=='KHR')
                                {{ number_format($subtotal) }} ៛
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

                        @if($data->total_amount_usd > 0 )
                            {{'$ '. number_format(($data->total_amount_usd),2) }} &emsp;
                        @endif
                        @if($data->total_amount_khr > 0 )
                            {{ number_format($data->total_amount_khr) .' ៛'}}
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
            <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                <p>
                    តបតាមកម្មវត្ថុ និងមូលហេតុខាងលើ{{prifixGender($data->requester()->gender)}}ស្នើសុំការអនុញ្ញតពី
                    @if($data->requester()->branch_id)
                        {{$data->approver()->position_name}}
                    @else
                        {{$data->forcompany->approver}}
                    @endif
                    ក្នុងការចំណាយដែលមានទឹកប្រាក់សរុបចំនួន
                        @if($data->total_amount_usd > 0 )
                            {{'$ '. number_format(($data->total_amount_usd),2) }}
                        @endif

                        @if($data->total_amount_usd > 0 && $data->total_amount_khr > 0)
                            និង 
                        @endif

                        @if($data->total_amount_khr > 0 )
                            {{ number_format($data->total_amount_khr) .' ៛'}}
                        @endif
                        ។

{{--                    {{ num2khtext(123405.5) }}--}}
{{--                    (ដប់ពីរដុល្លា នឹងដប់ប្រាំពីរសេន)។--}}
                </p>
                <p>
                    អាស្រ័យដូចបានជំរាបជូនខាងលើ សូមលោក
                    @if($data->requester()->branch_id)
                        {{$data->approver()->position_name}}
                    @else
                        {{$data->forcompany->approver}}
                    @endif
                    មេត្តាពិនិត្យ និងអនុញ្ញាតតាមការគួរ។
                </p>
                <p>
                    សូមលោក
                    @if($data->requester()->branch_id)
                        {{$data->approver()->position_name}}
                    @else
                        {{$data->forcompany->approver}}
                    @endif
                    មេត្តាទទួលនូវសេចក្ដីគោរពដ៏ខ្ពង់ខ្ពស់ពី{{prifixGender($data->requester()->gender)}}។
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
                            ថ្ងៃទី {{ khmer_number($data->created_at->format('d')) }}
                            ខែ {{ khmer_number($data->created_at->format('m')) }}
                            ឆ្នំា{{ khmer_number($data->created_at->format('Y')) }}
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
                                ថ្ងៃទី.....ខែ......ឆ្នំា.....
                            @endif
                        </p>
                        <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                        @foreach($data->reviewers() as $item)
                            @if ($item->approve_status == config('app.approve_status_approve'))
                                <p>{{ $item->position_name }}</p>
                                <img style="height: 60px;"
                                     src="{{ asset('/'.$item->signature) }}"
                                     alt="Signature">
                                <p>{{ $item->name }}</p>
                            @else
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    <p>{{ $item->position_name }}</p>
                                </div>
                            @endif
                        @endforeach

                    </div>

                    <div style="width: {{ (100/$allCol).'%' }}">
                        @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                            <p>
                                ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                                ខែ {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('m')) }}
                                ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}
                            </p>
                            <p>អនុម័តដោយ៖</p>
                            <p>{{$data->approver()->position_name}}</p>
                            <img style="height: 60px;"
                                 src="{{ asset('/'.$data->approver()->signature) }}"
                                 alt="Signature">
                            <p>{{ ($data->approver()->name) }}</p>
                        @else
                            <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                            <p>អនុម័តដោយ៖</p>
                            <p>{{$data->approver()->position_name}}</p>
                        @endif
                    </div>
                </div>

            @else
                <div class="signature">
                    <div style="width: {{ (100/$allCol).'%' }}">
                        <p>
                            ថ្ងៃទី {{ khmer_number($data->created_at->format('d')) }}
                            ខែ {{ khmer_number($data->created_at->format('m')) }}
                            ឆ្នំា{{ khmer_number($data->created_at->format('Y')) }}
                        </p>

                        <p>ស្នើសុំដោយ៖</p>
                        <p style="margin-top: -15px">{{ $data->requester()->position->name_km }}</p>
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
                                ថ្ងៃទី.....ខែ......ឆ្នំា.....
                            @endif
                        </p>
                        <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                        <?php
                            $reviewers = $data->reviewers();
                        ?>
                        <!-- Check company and ceo-->
                        @if (@$data->forcompany->short_name_en == 'MMI' && @$data->total_amount_usd >= 1000)
                            <?php
                                foreach ($reviewers as $key => $value) {
                                    if ($value->username == 'norngmakara') {
                                        $approver = $value;
                                        unset($reviewers[$key]);
                                    }
                                }
                            ?>
                        @endif
                        @foreach($reviewers as $item)
                            @if ($item->approve_status == config('app.approve_status_approve'))
                                <div class="related" @if (@$data->forcompany->short_name_en == 'MMI' && @$data->total_amount_usd >= 1000) style="width: {{ 100/($relatedCol-1) }}%;" @else style="width: {{ 100/$relatedCol }}%;" @endif>
                                    <p>{{ $item->position_name ? $item->position_name : $item->position_name_kh }}</p>
                                    <img style="height: 60px;"
                                         src="{{ asset('/'.$item->signature) }}"
                                         alt="Signature">
                                    <p>{{ $item->name }}</p>
                                </div>
                            @else
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    <p>{{ $item->position_name ? $item->position_name_km : $item->position_name }}</p>
                                </div>
                            @endif
                        @endforeach

                    </div>

                    @if (@$data->forcompany->short_name_en == 'MMI' && @$data->total_amount_usd >= 1000)
                        <div style="width: {{ (100/$allCol).'%' }}">
                            @if (@$approver->approve_status == config('app.approve_status_approve'))
                                <p>
                                    ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('d')) }}
                                    ខែ {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('m')) }}
                                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('Y')) }}

                                </p>
                                <p>
                                    អនុម័តដោយ៖
                                    @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                                        <img style="height: 15px;"
                                             title="{{ $data->approver()->name }}"
                                             src="{{ asset('/'.$data->approver()->short_signature) }}"
                                             alt="Signature">
                                    @endif
                                </p>
                                <p style="margin-top: -15px">{{ typePositionCEO($data->forcompany->type) }} </p>
                                <img style="height: 60px;"
                                     src="{{ asset('/'.$approver->signature) }}"
                                     alt="Signature">

                                <p>{{ ($approver->name) }}</p>
                            @else
                                <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                                <p>អនុម័តដោយ៖</p>
                                <p style="margin-top: -15px">{{ $data->forcompany->approver}}</p>
                            @endif
                        </div>
                    @else
                        <div style="width: {{ (100/$allCol).'%' }}">
                            @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                                <p>
                                    ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                                    ខែ {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('m')) }}
                                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}

                                </p>
                                <p>អនុម័តដោយ៖</p>
                                <p style="margin-top: -15px">{{ typePositionCEO($data->forcompany->type) }} </p>
                                <img style="height: 60px;"
                                     src="{{ asset('/'.$data->approver()->signature) }}"
                                     alt="Signature">

                                <p>{{ ($data->approver()->name) }}</p>
                            @else
                                <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                                <p>អនុម័តដោយ៖</p>
                                <p style="margin-top: -15px">{{ $data->forcompany->approver}}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

</div>
<div style="width: 1024px; margin: auto; margin-top: -66.06px">
    <div class="footer">
        <img src="{{ asset($data->forcompany->footer) }}" alt="footer" style="width: 1024px; margin-bottom: -10px">
    </div>
    @include('global.comment_modal', ['route' =>route('request.reject', $data->id)])
</div>

@if(! config('adminlte.enabled_laravel_mix'))
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery.inputmask.bundle.min.js') }}"></script>
    {{--<scrypt src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></scrypt>--}}
    @include('adminlte::plugins', ['type' => 'js'])
    <scrypt src="/bootstrap3-wysihtml5.min.js"></scrypt>

    @yield('adminlte_js')
@else
    {{--<script src="{{ asset('js/app.js') }}"></script>--}}
@endif
<script src="{{ asset('js/sweetalert2@9.js') }}"></script>

</body>

<script>

    $( "#back" ).on( "click", function( event ) {
        if(localStorage.previous){
            window.location.href = localStorage.previous;
            window.localStorage.removeItem('previous');
        }
        else{
            alert("Can't previous");
        }
    });

    $( "#approve_btn" ).on( "click", function( event ) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                $('#hr_form').submit();
                $.ajax({
                    type: "POST",
                    url: "{{ action('RequestFormController@approve', $data->id) }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        request_id: "{{ $data->id }}"
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.status) {
                            Swal.fire({
                                title: 'Approved!',
                                text: 'The request has been approved',
                                icon: 'success',
                                timer: '2000',
                            });
                            setTimeout(function(){
                                location.reload();
                            }, 2000);
                        }
                    },
                    error: function(data) {
                        console.log(data)
                    }
                });
            }
        })
    });
    $( "#reject_btn" ).on( "click", function( event ) {
        event.preventDefault();
        $('#comment_modal').modal('show');

        // Swal.fire({
        //     title: 'Please put the reason',
        //     icon: 'warning',
        //     showCancelButton: true,
        //     confirmButtonColor: '#3085d6',
        //     cancelButtonColor: '#d33',
        //     confirmButtonText: 'Yes',
        //     input: 'text',
        //     showLoaderOnConfirm: true,
        //     inputValidator: (value) => {
        //         if (!value) {
        //             return 'Comment is required!'
        //         }
        //     },
        //     allowOutsideClick: () => !Swal.isLoading()
        // }).then((result) => {

        //     if (result.value) {
        //         $.ajax({
        //             type: "POST",
        //             url: "{{ action('RequestFormController@reject', $data->id) }}",
        //             data: {
        //                 _token: "{{ csrf_token() }}",
        //                 request_id: "{{ $data->id }}",
        //                 comment: result.value,
        //             },
        //             dataType: "json",
        //             success: function(data) {
        //                 if (data.status) {
        //                     Swal.fire({
        //                         title: 'Reject!',
        //                         text: 'The request has been reject',
        //                         icon: 'success',
        //                         timer: '2000',
        //                     })
        //                     setTimeout(function(){
        //                         location.reload();
        //                     }, 2000);
        //                 }
        //                 console.log(data.request_token)
        //             },
        //             error: function(data) {
        //                 console.log(data)
        //             }
        //         });
        //     }

        // })
    });
</script>
</html>
