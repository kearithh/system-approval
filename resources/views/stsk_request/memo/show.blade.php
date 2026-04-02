<!DOCTYPE html>
<html>

<head>
    <title>{{ $data->title_km }}</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Latest compiled and minified CSS -->
    <!-- include libraries(jQuery, bootstrap) -->
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>


    <!-- include summernote css/js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
            font-family: 'Times New Roman','Khmer OS Content';
            font-weight: 400;
            font-size: 15px;
        }

        strong {
            font-family: 'Times New Roman','Khmer OS Muol Light';
            font-size: 15px;
            font-weight: 400;
        }

        h1 {
            font-family: 'Times New Roman','Khmer OS Muol Light';
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
            border: 0px solid #585858;
            padding-top: 5px;
            padding-bottom: 15px;
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

        table h1 {
            margin-block-start: 17px;
        }

        h1 {
            font-size: 15px;
        }

        .desc_p p {
            /*margin: 0 !important;*/
            /*padding: 0 !important;*/
            /*margin-block-start: 0 !important;*/
            /*margin-block-end: 1em;*/
            font-size: 15px !important;

        }
        .desc_p {
            padding-left: 25px;
        }

        table tr td h1, table tr td p, table tr td span {
            margin: 0 !important;
            padding: 0 !important;
            margin-block-start: 0 !important;
            margin-block-end: 1em;
            font-size: 15px !important;

        }

        /*div.desc_p, div.desc_p span, div.desc_p p {*/
            /*padding-left: 25px;*/
            /*font-size: 16px !important;*/
        /*}*/

        div.action_btn {
            display: none;
            margin-top: 5px;
            position: fixed;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        div.action_btn a {
            padding: 10px 15px;
            text-decoration: none;
            color: inherit;
            /*border: 1px solid #dadada;*/
        }
        div.action_btn div {
            border-bottom: 1px solid #dadada;
        }



        @media print {
            div.action_btn {
                display: none;
            }
            #action_container {
                display: none;
            }
            .footer img{
                position: fixed;
                bottom: 14px;
            }
            strong, table tr td h1, table tr td p, table tr td span, h1, table tr td {
                font-size: 16px;
            }
        }

        .logo {
            text-align: center;
            padding-top: 70px;
            margin-bottom: 20px;
        }

        body div p, body div span, body div a {
            line-height: 1.7;
        }

    </style>
    <style>
        #action {
            position: fixed;
            top: 0;
            margin-left: 4mm;
            background: white;
            z-index: 1;
            margin-top: 1mm;
        }
    </style>
</head>

<body style="background: #dadada">


<div id="action_container" style="width: 1024px; margin: auto;background: white;">
    <div id="action">
        <div class="btn-group" role="group" aria-label="">
            <a href="{{ URL::previous() }}" class="btn btn-default btn-sm">Back</a>
            <button type="button" onclick="window.print()" class="btn btn-sm btn-warning">
                Print
            </button>
        </div>
        @if(can($data))
            <button id="approve_btn" disabled class="btn btn-sm btn-success">
                Approve
            </button>
            <button id="reject_btn"  disabled  value="1" class="btn btn-sm btn-danger">
                Comment
            </button>
        @else
            <form action="{{ route('request_memo.approve', $data->id) }}" method="POST" id="approve_memo_form" style="margin: 0; display: inline-block">
                @csrf
                <div class="btn-group" role="group" aria-label="">
                    <button formaction="approve_memo_form" id="approve_btn" name="approve" value="1" class="btn btn-sm btn-success">
                        Approve
                    </button>
                    <button formaction="reject_memo_form" id="reject_btn"  name="reject" value="1" class="btn btn-sm btn-danger">
                        Comment
                    </button>
                </div>
            </form>
            <form action="{{ route('request_memo.reject', $data->id) }}" method="POST" id="reject_memo_form" style="margin: 0; display: inline-block">
                @csrf
            </form>
        @endif
    </div>

    <div id="reviewer" style="padding: 15px; margin-bottom: 5px">
        <br>
        <table class="table table-bordered">
            <caption>តារាងអ្នកត្រួតពិនិត្យ</caption>
            <tr>
                <td>ល.រ</td>
                <td>ឈ្មោះ</td>
                <td>មុខដំណែង</td>
                <td>ព្រម</td>
                <td>មិនព្រម</td>
                <td>មតិយោបល់</td>
            </tr>
            <?php $j = 1; ?>
            @foreach($data->approvals() as $key => $value)
                <tr>
                    <td>{{ $j++ }}</td>
                    <td>{{ $value->user_name }}</td>
                    <td>{{ $value->position_name }}</td>
                    <td>
                        <input disabled type="checkbox" @if ($value->approve_status == config('app.approve_status_approve')) checked @endif>
                    </td>
                    <td>
                        <input disabled type="checkbox" @if ($value->approve_status == config('app.approve_status_reject')) checked @endif>
                    </td>
                    <td>
                        {{ $value->approve_comment }}
                    </td>
                </tr>
            @endforeach
            @if(isset($data->approver()->name))
            <tr>
                <td>{{ $j }}</td>
                <td>{{ @$data->approver()->name }}</td>
                <td>{{ @$data->approver()->position_name_km }}</td>
                <td>
                    <input disabled type="checkbox" @if (@$data->approver()->approve_status == config('app.approve_status_approve')) checked @endif>
                </td>
                <td>
                    <input disabled type="checkbox" @if (@$data->approver()->approve_status == config('app.approve_status_reject')) checked @endif>
                </td>
                <td>
                    {{ @$data->approver()->approve_comment ? @$data->approver()->approve_comment : 'N/A' }}
                </td>
            </tr>
            @endif
        </table>
    </div>
</div>

<div style="width: 1024px; margin: auto;background: white; min-height: 1355px;">

    {!! $data->forcompany->header_section  !!}

    <div class="contain" style="padding: 0 110px">
        <div class="subtitle"​ style="float: left">
            <p>ការិយាល័យកណ្ដាល</p>
            <p>លេខៈ {{ khmer_number($data->no) }} /២០ {{ $data->forcompany->short_name_km }}</p>
        </div>

        <div class="title" style="text-align: center; clear: both">
            <h1>សេចក្ដីសម្រេច</h1>
            <h1>ស្តីពី</h1>
            <h1 style="margin-bottom: 15px">{{ $data->title_km }}</h1>
            <p></p>
            <img src="{{ asset('/img/logo/font_tt.png') }}" width="150">
            <h1 style="margin-top: 25px;">
                {{ $data->forcompany->approver}}
                {{ $data->forcompany->long_name }}
            </h1>
        </div>
        <div class="content" style="margin-top: 15px;">
            <div class="desc desc_p" style="text-align: left;">
                <?= $data->forcompany->reference; ?>
            </div>

            <h1 style="text-align: center; margin-top: 20px; margin-bottom: 20px">សម្រេច</h1>
            <table>
                <tr style="vertical-align: top">
                    <td style="min-width: 100px"><h1>ប្រការ ០១៖</h1></td>
                    <td style="padding-top: 0"><?= $data->point[0] ?></td>
                </tr>
                </tr>
                @if (isset($data->point[1]) && $data->point[1])
                    <tr style="vertical-align: top">
                        <td><h1>ប្រការ ០២៖</h1></td>
                        <td style="padding-top: 0"><?= $data->point[1] ?></td>
                    </tr>
                @endif

                @if (isset($data->point[2]) &&$data->point[2])
                    <tr style="vertical-align: top">
                        <td><h1>ប្រការ ០៣៖</h1></td>
                        <td style="padding-top: 0"><?=$data->point[2] ?></td>
                    </tr>
                @endif
                @if (isset($data->point[3]) && $data->point[3])
                    <tr style="vertical-align: top">
                        <td><h1>ប្រការ ០៤៖</h1></td>
                        <td style="padding-top: 0"><?=$data->point[3] ?></td>
                    </tr>
                @endif
                @if (isset($data->point[4]) && $data->point[4])
                    <tr style="vertical-align: top">
                        <td><h1>ប្រការ ០៥៖</h1></td>
                        <td style="padding-top: 0"><?=$data->point[4] ?></td>
                    </tr>
                @endif
                @if (isset($data->point[5]) && $data->point[5])
                    <tr style="vertical-align: top">
                        <td><h1>ប្រការ ០៦៖</h1></td>
                        <td style="padding-top: 0"><?= $data->point[5] ?></td>
                    </tr>
                @endif
                @if (isset($data->point[6]) && $data->point[6])
                    <tr style="vertical-align: top">
                        <td><h1>ប្រការ ០៧៖</h1></td>
                        <td style="padding-top: 0"><?= $data->point[6] ?></td>
                    </tr>
                @endif
                @if (isset($data->point[7]) && $data->point[7])
                    <tr style="vertical-align: top">
                        <td><h1>ប្រការ ០៨៖</h1></td>
                        <td style="padding-top: 0"><?= $data->point[7] ?></td>
                    </tr>
                @endif
                @if (isset($data->point[8]) && $data->point[8])
                    <tr style="vertical-align: top">
                        <td><h1>ប្រការ ០៩៖</h1></td>
                        <td style="padding-top: 0"><?= $data->point[8] ?></td>
                    </tr>
                @endif
                @if (isset($data->point[9]) && $data->point[9])
                    <tr style="vertical-align: top">
                        <td><h1>ប្រការ ១០៖</h1></td>
                        <td style="padding-top: 0"><?= $data->point[9] ?></td>
                    </tr>
                @endif
            </table>

            <div class="mb-5" style="margin-bottom: 0px">

                @foreach($data->approvals() as $key => $value)
                    @if ($value->approve_status == config('app.approve_status_approve'))
                    <div class="text-right">
                        <img src="{{ asset($value->short_signature) }}" alt="short_signature" style="width: 20px; margin-top: -74px">
                    </div>
                    @endif
                @endforeach
            </div>


            <div>
                <div>
                    <p style="text-align: right;">
                        {{$data->khmer_date}}
                    </p>
                </div>
                <div style="float: right; text-align: center">
                    <p style="text-align: right;">
                        រាជធានីភ្នំពេញ, ថ្ងៃទី {{ khmer_number($data->start_date->format('d')) }}
                        ខែ{{ khmer_month($data->start_date->format('m')) }}
                        ឆ្នាំ{{ khmer_number($data->start_date->format('Y')) }}
                    </p>

                    @if ($data->status == config('app.approve_status_approve'))
                        <h1 style="text-align: center;">
                            {{ $data->forcompany->approver}}
                        </h1>
                        <img style="width: 100px; margin-top: 10px;"
                             src="{{ asset('/'.$data->approver()->signature) }}"
                             alt="Signature">
                    @else
                        <h1 style="text-align: center;">
                            {{ $data->forcompany->approver}}
                        </h1>
                        <br><br>
                    @endif
                    <h1 style="margin-top: 10px;">គួន ធីតា</h1>
                </div>
            </div>

            <div class="copy" style="clear: both">
                <div class="left" style="float: left; margin-top: -20px">
                    <h1>ចម្លងជូន</h1>
                    <p>- ដូចប្រការ {{ khmer_number($data->practise_point) }} <b>"ដើម្បីអនុវត្ត"</b></p>
                    <p>- ឯកសារ_កាលប្បវត្តិ</p>
                </div>
            </div>
        </div>
    </div>
<p style="clear: both"></p>
</div>
<div style="width: 1024px; margin: auto;">
    {!! $data->forcompany->footer_section  !!}
</div>

</body>

<script src="{{ asset('js/sweetalert2@9.js') }}"></script>

<script>
    // window.print();

    approve();
    reject();
    function approve()
    {
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
                    $('#approve_memo_form').submit();
                }
            })
        });
    }

    function reject()
    {
        $( "#reject_btn" ).on( "click", function( event ) {
            event.preventDefault();
            Swal.fire({
                title: 'Please put the reason',
                // text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                input: 'text',
                showLoaderOnConfirm: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'Comment is required!'
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('request_memo.reject', $data->id) }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            request_id: "{{ $data->id }}",
                            comment: result.value,
                        },
                        dataType: "json",
                        success: function(data) {
                            if (data.status) {
                                Swal.fire({
                                    title: 'Reject!',
                                    text: 'The request has been reject',
                                    icon: 'success',
                                    timer: '2000',
                                })
                                location.reload();
                            }
                            console.log(data.request_token)
                        },
                        error: function(data) {
                            console.log(data)
                        }
                    });
                }

            })
        });
    }
</script>
@include('global.sweet_alert')
</html>
