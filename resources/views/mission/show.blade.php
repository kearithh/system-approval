<!DOCTYPE html>
<html>
<head>
    <title>E-Approval</title>
    <meta charset="UTF-8">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    {{--<link href="/bootstrap3-wysihtml5.min.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
          font-family: 'Times New Roman','Khmer OS Content';
          font-weight: 400;
          font-size: 16px !important;
          line-height: normal !important;
        }

        h1 {
            font-family: 'Khmer OS Muol Light';
            font-size: 16px;
            margin: 7px 0 18px 0 !important;
        }

        p, span, b {
            font-family: 'Khmer OS Content' !important;
            font-size: 16px !important;
            margin: 1px 0 7px 0 !important;
        }

        .header{
          text-align: center;
        }

        .signature{
          padding: 15px 0 0 0;
        }

        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100% !important;
        }

        tr{
          vertical-align: top;
        }

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

        .logo {
          text-align: center !important;
          padding-top: 0;
          margin-bottom: 20px;
        }


        .page-footer, .page-footer-space {
          height: 70px;
        }

        .page-header, .page-header-space {
          height: 70px;
        }

        .page-footer {
          /*position: fixed;*/
          bottom: 0;
          width: 100%;
        }

        .footer img{
            margin-bottom: 0 !important;
        }

        @page {
          margin: 0;
          size: A4;
        }

        @media print {
          thead {
            display: table-header-group;
          }
          tfoot {
            display: table-footer-group;
          }

          .pagebreak {page-break-before:always}

          button, #border_new_page {
            display: none;
          }

          .table-bordered td, .table-bordered th {
              border: 1px solid #1D1D1D !important;
          }

          .page-footer {
            height: auto;
          }

          body {
            margin: 0;
          }
          #action_container {
            display: none;
          }
          .page-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
          }
        }

        table.table td, table.table th {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            vertical-align: middle;
            padding-left: 0.25rem;
            padding-right: 0.25rem;
            font-size: 16px;
        }

        .body{
          text-align: justify;
        }

        .contain{
          padding-left: 90px;
          padding-right: 90px;
          width: 880px;
        }

        .row {
          display: -webkit-box;
          display: -webkit-flex;
          display: -ms-flexbox;
          display: flex;
          flex-wrap: wrap;
        }
        .row > [class*='col-'] {
          /*display: flex;*/
          flex-direction: column;
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

  <div class="page-header" style="text-align: center; display: none"></div>

  <div id="action_container" style="width: 1024px; margin: auto;background: white;">
    <div id="action">
      <div class="btn-group" role="group" aria-label="">
          <button id="back" type="button" class="btn btn-sm btn-secondary" title="Back to list">
             Back
          </button>
      </div>
        @include('global.next_pre')

        <div class="btn-group" role="group" aria-label="">
        <button type="button" onclick="window.print()" class="btn btn-sm btn-warning" title="Print/Export PDF">
            Print
        </button>
      </div>

      <form style="margin: 0; display: inline-block " action="">
        <div class="btn-group" role="group" aria-label="">
          @if(!can_approve_reject($data, config('app.type_mission')))
            <button disabled name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default" title="Approve request">
                Approve
            </button>
            <button disabled style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default" title="Comment to creator update">
                Comment
            </button>
            <button disabled style="background: black; color: white" name="next" value="1" class="btn btn-sm btn-default" title="Reject request">
                Reject
            </button>
          @else
            <button id="approve_btn" name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default" title="Approve request">
                Approve
            </button>
            <button id="reject_btn" style="background: #bd2130; color: white" name="next" data-target="comment_attach" value="1" class="btn btn-sm btn-default" title="Comment to creator update">
                Comment
            </button>
            <button id="disable_btn" style="background: black; color: white" name="next" data-target="comment_attach" value="1" class="btn btn-sm btn-default" title="Reject request, creator can't update">
                Reject
            </button>
          @endif
        </div>
        @if($data->status == config('app.approve_status_approve') && @$_GET['type'] == config('app.type_mission_item'))
            <button id="note_btn" name="next" value="1" class="btn btn-sm btn-info">
                Verify
            </button>
        @endif
      </form>
    </div><br><br>

    {{-- @include('global.rerviewer_table', ['reviewers' => --}}
        {{-- $data->reviewers()->push($data->approver()) --}}
    {{-- ]) --}}

    <?php $reviewers = $data->reviewers()->push($data->approver()); ?>

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
    </div>

  </div>

  <div style="width: 1024px; margin: auto;background: white; min-height: 1355px;">
    <table>
      <thead>
        <tr>
          <td>
            <div class="page-header-space"></div>
          </td>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td class="contain">

            <div class="header">
                <?php 
                    $sections = @$data->forcompany->letterhead;
                    $created_at = strtotime($data->created_at);
                    $yes = 0;
                    $forcompany = null;

                    if ($sections) {

                        foreach($sections as $key => $section) {
                            $start = strtotime(@$section->start_effective);
                            $end = strtotime(@$section->end_effective);

                            if ($start <= $created_at && $end >= $created_at) {
                                $forcompany = $section; //contant in json
                                $yes += 1;
                            }
                        }

                    } else {

                        $forcompany = $data->forcompany;
                        $yes += 1;

                    }  

                    if ($yes == 0) { // not in json
                        $forcompany = $data->forcompany; 
                    }
                ?>
                <img src="{{ asset($forcompany->logo) }}" alt="logo" style="height: 90px">
                <br><br>
                <h1>លិខិតបញ្ជាបេសកកម្ម</h1>
                <img src="{{ asset('/img/logo/font_tt.png') }}" width="150">
                <br><br><br>
            </div>
            <div class="body">
                <p>បានឃើញការចាំបាច់របស់ការិយាល័យកណ្តាល សម្រេចចាត់តាំងបុគ្គលិក ដូចមានរាយនាមខាងក្រោមចុះទៅបំពេញបេសកកម្ម៖</p>
                <table class="table table-bordered text-center">
                  <thead class="table-info">
                      <tr class="bgcol">
                          <th>ល.រ</th>
                          <th>ឈ្មោះបុគ្គលិក</th>
                          <th>តួនាទី</th>
                      </tr>
                  </thead>
                  <tbody>
                      @if(@$data->staffs)
                          <?php $i = 1; ?>
                          <?php $staffs = is_array($data->staffs) ? $data->staffs : json_decode($data->staffs); ?>
                          @foreach($staffs as $staff )
                              <tr>
                                  <td>{{ $i++ }}</td>
                                  <td>{{ $staff->staff_name }}</td>
                                  <td>{{ $staff->position }}</td>
                              </tr>
                          @endforeach
                      @endif

                      @for($i = 0; (5 - count($staffs)) > $i; $i++)
                          <tr>
                              <td height="40">&nbsp;</td>
                              <td>&nbsp;</td>
                              <td></td>
                          </tr>
                      @endfor
                  </tbody>
                </table>

                <table class="table table-borderless mb-0">
                    <tr>
                        <td style="vertical-align: top">
                            ឲ្យធ្វើដំណើរទៅស្ថាប័ន/សាខា
                        </td>
                        <td style="vertical-align: top;"><p>:</p></td>
                        <td style="vertical-align: top">
                            @if(@$data->branch)
                                <?php $branch = is_array($data->branch) ? $data->branch : json_decode($data->branch); ?>
                                @foreach($branch as $branches )
                                    {{ $branches->branch_name }}<br>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; width: 250px;">
                            ក្នុងគោលបំណង
                        </td>
                        <td style="vertical-align: top"><p>:</p></td>
                        <td style="vertical-align: top">
                            {{$data->purpose}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="vertical-align: top">
                            <b>លក្ខខណ្ឌនៃការចុះបេសកកម្ម</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">
                            &emsp;&emsp;* ថ្ងៃចេញដំណើរ
                        </td>
                        <td style="vertical-align: top"><p>:</p></td>
                        <td style="vertical-align: top">
                            ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->start_date))->format('d')) }}
                            ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($data->start_date))->format('m')) }}
                            ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->start_date))->format('Y')) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">
                            &emsp;&emsp;* ថ្ងៃត្រឡប់មកវិញ
                        </td>
                        <td style="vertical-align: top"><p>:</p></td>
                        <td style="vertical-align: top">
                            ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->end_date))->format('d')) }}
                            ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($data->end_date))->format('m')) }}
                            ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->end_date))->format('Y')) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">
                            &emsp;&emsp;* មធ្យោបាយធ្វើដំណើរ
                        </td>
                        <td style="vertical-align: top"><p>:</p></td>
                        <td style="vertical-align: top">
                            {{$data->transportation}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="vertical-align: top">
                          {{ $data->respectfully }}
                          @foreach($data->reviewers() as $reviewer)
                              @if($reviewer->approve_status== config('app.approve_status_approve'))
                                  <span><img src="{{ asset($reviewer->short_signature) }}" alt="short_sign" style="width: 20px;"></span>
                              @endif
                          @endforeach
                        </td>
                    </tr>
                </table>

            </div>

            <div class="sign">
                <div style="float: right; text-align: center">
                    <div style="text-align: right;">
                        <p>
                            ភ្នំពេញ
                            ថ្ងៃទី{{ khmer_number($data->created_at->format('d')) }}
                            ខែ{{ khmer_month($data->created_at->format('m')) }}
                            ឆ្នាំ{{ khmer_number($data->created_at->format('Y')) }}
                        </p>
                    </div>
                    <div style="text-align: center;">
                        @if(@$data->approver()->approve_status == config('app.approve_status_approve'))
                            <p><img style="height: 60px" src="{{ asset('/'.@$data->approver()->signature) }}" alt="signature"></p>
                            <p class="requester-name">
                                {{ @check_nickname(@$data->approver()->position_level, $data->created_at ) }}
                                {{ @json_decode(@$data->approver()->user_object)->name ?: @$data->approver()->name }}
                            </p>
                        @else
                            <p>&emsp;</p>
                            <p>&emsp;</p>
                            <p>
                                {{ @check_nickname(@$data->approver()->position_level, $data->created_at ) }}
                                {{ @json_decode(@$data->approver()->user_object)->name ?: @$data->approver()->name }}
                            </p>
                        @endif
                        <p>&emsp;</p>
                    </div>
                </div>
            </div>
            @if($data->company_id != 6) <!-- mmi -->
            <div class="verify" style="clear: both">
              <hr id="border_new_page" style="margin: 0 -90px 50px -90px;">
              <p class="pagebreak"></p>
              <h1 class="text-center">សេចក្តីបញ្ជាក់</h1>

              <table style="border-collapse: collapse; width: 100%;">
                <tr>
                    <td class="text-center">
                      <i><u><h1>ពេលទៅដល់គោលដៅ</h1></u></i>
                    </td>
                    <td class="text-center">
                        <i><u><h1>ពេលត្រលប់មកវិញ</h1></u></i>
                    </td>
                </tr>

                @if($data->status == config('app.approve_status_approve'))
                    @foreach($note as $notes )
                      @if($notes->status== config('app.approve_status_approve'))
                        <tr>
                            <td style="border: 1px solid black; padding: 10px 30px 10px 30px;">
                                <span>កាលបរិច្ឆេទៈ
                                    {{(\Carbon\Carbon::createFromTimestamp(strtotime(@json_decode($notes->comment)->date_in))->format('d/m/Y'))}}
                                </span><br>
                                <span>ម៉ោងៈ
                                    {{(\Carbon\Carbon::createFromTimestamp(strtotime(@json_decode($notes->comment)->time_in))->format('h:i A'))}}
                                </span><br>
                                <span>ចំនួនបុគ្គលិកៈ {{@json_decode($notes->comment)->num_in}} </span><br>
                                <span>មតិយោបល់ៈ {{@json_decode($notes->comment)->comment_in}} </span><br>
                                <div class="text-center">
                                  <span>ហត្ថលេខា</span><br>
                                  <span>&emsp;</span><br><br><br>
                                  <!-- <span><img style="height: 60px" src="{{ asset('/'.$notes->signature) }}" alt="signature"></span><br> -->
                                  <!-- <span class="requester-name">{{ $notes->user_name }}</span><br> -->
                                </div>
                            </td>
                            <td style="border: 1px solid black; padding: 10px 30px 10px 30px;">
                                <span>កាលបរិច្ឆេទៈ
                                    {{(\Carbon\Carbon::createFromTimestamp(strtotime(@json_decode($notes->comment)->date_out))->format('d/m/Y'))}}
                                </span><br>
                                <span>ម៉ោងៈ
                                    {{(\Carbon\Carbon::createFromTimestamp(strtotime(@json_decode($notes->comment)->time_out))->format('h:i A'))}}
                                </span><br>
                                <span>ចំនួនបុគ្គលិកៈ {{@json_decode($notes->comment)->num_out}} </span><br>
                                <span>មតិយោបល់ៈ {{@json_decode($notes->comment)->comment_out}} </span><br>
                                <div class="text-center">
                                  <span>ហត្ថលេខា</span><br>
                                  <span>&emsp;</span><br><br><br>
                                  <!-- <span><img style="height: 60px" src="{{ asset('/'.$notes->signature) }}" alt="signature"></span><br> -->
                                  <!-- <span class="requester-name">{{ $notes->user_name }}</span><br> -->
                                </div>
                            </td>
                        </tr>
                      @else
                        <tr>
                            <td style="border: 1px solid black; padding: 10px 30px 10px 30px;">
                                <span>កាលបរិច្ឆេទៈ........................</span><br>
                                <span>ម៉ោងៈ...............................</span><br>
                                <span>ចំនួនបុគ្គលិកៈ.......................</span><br>
                                <span>មតិយោបល់ៈ..........................</span><br>
                                <div class="text-center">
                                  <span class="text-center">ហត្ថលេខា</span><br><br>
                                  <span>&emsp;</span><br><br><br>
                                  <!-- <span class="requester-name">{{ $notes->user_name }}</span><br> -->
                                </div>
                            </td>
                            <td style="border: 1px solid black; padding: 10px 30px 10px 30px;">
                                <span>កាលបរិច្ឆេទៈ........................</span><br>
                                <span>ម៉ោងៈ...............................</span><br>
                                <span>ចំនួនបុគ្គលិកៈ.......................</span><br>
                                <span>មតិយោបល់ៈ..........................</span><br>
                                <div class="text-center">
                                  <span class="text-center">ហត្ថលេខា</span><br>
                                  <span>&emsp;</span><br><br><br>
                                  <!-- <span class="requester-name">{{ $notes->user_name }}</span><br> -->
                                </div>
                            </td>
                        </tr>
                      @endif
                    @endforeach
                @else
                    @foreach($branch as $branches )
                        <tr>
                            <td style="border: 1px solid black; padding: 10px 30px 10px 30px;">
                                <span>កាលបរិច្ឆេទៈ........................</span><br>
                                <span>ម៉ោងៈ...............................</span><br>
                                <span>ចំនួនបុគ្គលិកៈ.......................</span><br>
                                <span>មតិយោបល់ៈ..........................</span><br>
                                <div class="text-center">
                                  <span class="text-center">ហត្ថលេខា</span><br>
                                  <span>&emsp;</span><br>
                                  <span class="requester-name"></span><br>
                                </div>
                            </td>
                            <td style="border: 1px solid black; padding: 10px 30px 10px 30px;">
                                <span>កាលបរិច្ឆេទៈ........................</span><br>
                                <span>ម៉ោងៈ...............................</span><br>
                                <span>ចំនួនបុគ្គលិកៈ.......................</span><br>
                                <span>មតិយោបល់ៈ..........................</span><br>
                                <div class="text-center">
                                  <span class="text-center">ហត្ថលេខា</span><br>
                                  <span>&emsp;</span><br>
                                  <span class="requester-name"></span><br>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif

              </table>

              <h1><u>សេចក្តីបញ្ជាក់</u></h1>
              <p>1.   ពេលទៅដល់សាខា មុនចាប់ផ្តើមបំពេញការងារ បុគ្កលិកត្រូវបង្ហាញលិខិតបញ្ជាបេកកម្មទៅសាខា។</p>
              <p>2.   ស្នើសុំសាខា ចុះហត្ថលេខា បោះត្រា និងថ្ងៃខែឆ្នាំដែលទៅដល់ និងថ្ងៃត្រឡប់មកវិញដើម្បីជាភស្តុតាង និងងាយស្រួលក្នុងការទូទាត់ប្រាក់ចំណាយផ្សេងៗ ក្នុងការបំពេញបេសកកម្មរបស់បុគ្គលិកជាមួយនាយកដ្ឋានហិរញ្ញវត្ថុ។</p>
            </div>
            @endif
          </td>
        </tr>
      </tbody>

      <tfoot>
        <tr>
          <td>
            <p style="clear: both"></p>
            <div class="page-footer-space"></div>
          </td>
        </tr>
      </tfoot>

    </table>
  </div>

  <div class="page-footer">
    <div style="width: 1024px; margin: auto; text-align: center;">
      {!! $forcompany->footer_section  !!}
    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('mission.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('mission.disable', $data->id)])
  @include('global.note_modal', ['route' =>route('mission.verify', $item->id)])


</body>
@if(! config('adminlte.enabled_laravel_mix'))
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery.inputmask.bundle.min.js') }}"></script>
    @include('adminlte::plugins', ['type' => 'js'])
    <scrypt src="/bootstrap3-wysihtml5.min.js"></scrypt>

    @yield('adminlte_js')
@else
    {{--<script src="{{ asset('js/app.js') }}"></script>--}}
@endif
<script src="{{ asset('js/sweetalert2@9.js') }}"></script>

<script>

  $( "#back" ).on( "click", function( event ) {
      if(localStorage.previous){
          window.location.href = localStorage.previous;
          // window.localStorage.removeItem('previous');
      }
      else{
          alert("Can't previous");
      }
  });

  $( "#approve_btn" ).on( "click", function( event ) {
      event.preventDefault();
      Swal.fire({
          title: 'Are you sure?',
          // text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes'
      }).then((result) => {
          if (result.value) {
              // $('#hr_form').submit();
              $.ajax({
                  type: "POST",
                  url: "{{ action('MissionController@approve', $data->id) }}",
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

  $( "#reject_btn" ).on( "click", function( event ) {
    event.preventDefault();
    $('#comment_modal').modal('show');
  });

  // reject
  $( "#disable_btn" ).on( "click", function( event ) {
    event.preventDefault();
    $('#disable_modal').modal('show');
  });

  $( "#note_btn" ).on( "click", function( event ) {
    event.preventDefault();
    $('#note_modal').modal('show');
  });

  $('.datepicker').datepicker({
      format: 'dd-mm-yyyy',
      todayHighlight:true,
      autoclose: true
  });

</script>
@include('global.sweet_alert')
</html>
