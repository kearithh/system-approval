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
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet"> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script> -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
          font-family: 'Times New Roman','Khmer OS Content';
          font-weight: 400;
          font-size: 15px;
          line-height: normal !important;
        }

        strong {
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-size: 15px;
          font-weight: 400;
        }

        h1, h2{
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
          width: 100% !important;
        }

        table thead tr {
          /*background: orange;*/
          font-weight: 700;
          text-align: center;
        }

        table h1 {
          margin-block-start: 5px !important;
        }

        h2{
          margin-block-start: 5px;
          margin-block-end: 5px;
          font-size: 15px !important;
          line-height: normal;
        }

        .desc_p p {
          font-size: 15px !important;
        }
        .desc_p {
          /*text-align: justify ;*/
          /*padding-left: 25px;*/
        }

        table tr td h1, table tr td p, table tr td span {
          margin: 0 !important;
          padding: 0 !important;
          margin-block-start: 0 !important;
          margin-block-end: 1em;
          font-size: 15px !important;

        }

        table#points tbody tr td ul li,
        table#points tbody tr td ol li {
            /*padding-left: 15px !important;*/
            margin-left: 0 !important;
        }

        table#points tbody tr td ul,
        table#points tbody tr td ol {
            margin-left: -23px;
        }

        table tbody tr td ul li {
          /*padding-left: 15px !important;*/
          margin-left: 0 !important;
        }

        table#points > tbody tr td ul {
          margin-left: -23px !important;
        }

        div.title div h1 {
          font-size: 15px !important;
          /*line-height: 2.5;*/
          margin-top: 20px !important;
          margin-bottom: 10px !important;
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
          text-align: center;
          padding-top: 0;
          margin-bottom: 20px;
        }

        .page-footer, .page-footer-space {
          height: 70px;
        }

        .page-header, .page-header-space {
          height: 40px;
        }

        .page-footer {
          /*position: fixed;*/
          bottom: 0;
          width: 100%;
        }

        /*.page {
          page-break-after: always;
        }*/

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

          button {
            display: none;
          }

          body {
            margin: 0;
          }
          #action_container {
            display: none;
          }
          .page-footer {
            position: fixed;
            /*bottom: 10px;*/
            width: 100%;
          }
        }

        body div p, body div span, body div a {
          line-height: 1.7;
          /*line-height: normal;*/
        }
        .contain{
          padding-left: 70px;
          padding-right: 70px;
          width: 880px;
        }

        .point_width{
          width: 90px;
        }
        .desc_width{
          width: 744px !important;
        }

        #highlights{
          background-color: #FFFF00;
          color: black;
         /* padding: 5px;*/
        }

        #abrogation {
          position: fixed;
          top: 50%;
          width: 100%;
          text-align: center !important;
          font-size: 50px;
          color: red;
          opacity: 30%;
          transform: rotate(-45deg);
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
  @if($data->abrogation_status)
    <b id="abrogation">MEMO ត្រូវបាននិរាករណ៍</b>
  @endif
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
      @if(!can_approve_reject($data, config('app.type_memo')))
          <div class="btn-group" role="group" aria-label="">
              <button disabled name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default" title="Approve request">
                  Approve
              </button>
              <button disabled style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default" title="Comment to creator update">
                  Comment
              </button>
              <button disabled style="background: black; color: white" name="next" value="1" class="btn btn-sm btn-default" title="Reject request">
                  Reject
              </button>
          </div>
      @else
        <div class="btn-group" role="group" aria-label="">
          <button id="approve_btn" name="approve" value="1" class="btn btn-sm btn-success" title="Approve request">
            Approve
          </button>
          <button id="reject_btn" name="reject" value="1" class="btn btn-sm btn-danger" title="Comment to creator update">
            Comment
          </button>
          <button id="disable_btn" name="disable" value="1" class="btn btn-sm btn-secondary" style="background: black; color: white" title="Reject request, creator can't update">
            Reject
          </button>
        </div>
        <form action="{{ route('request_memo.approve', $data->id) }}" method="POST" id="approve_memo_form" style="margin: 0; display: inline-block">
            @csrf
        </form>
      @endif
    </div>

    <div id="reviewer" style="padding: 15px; margin-bottom: 5px">
      <br>
      <table class="table table-bordered">
        <caption>តារាងអ្នកត្រួតពិនិត្យ<br>Matrix approval chart</caption>
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
        @foreach($data->reviewers() as $key => $value)
          <tr>
            <td>{{ $j++ }}</td>
            <td>{{ $value->user_name }}</td>
            <td>{{ $value->position_name }}</td>
            <td>
              <input disabled type="checkbox" @if ($value->approve_status == config('app.approve_status_approve')) checked @endif>
            </td>
            <td>
              <input disabled type="checkbox" 
                        @if ($value->approve_status == config('app.approve_status_reject') || $value->approve_status == config('app.approve_status_disable')) 
                            checked 
                        @endif
                    >
            </td>
            <td>
              {{ $value->approve_comment }}
            </td>
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
        @endforeach
        @if(isset($data->approver()->name))
          <tr>
            <td>{{ $j }}</td>
            <td>{{ @check_nickname(@$data->approver()->position_level, @$data->created_at ) }} {{ @$data->approver()->name }}</td>
            <td>{{ @$data->approver()->position_name }}</td>
            <td>
              <input disabled type="checkbox" @if (@$data->approver()->approve_status == config('app.approve_status_approve')) checked @endif>
            </td>
            <td>
              <input disabled type="checkbox" 
                @if (@$data->approver()->approve_status == config('app.approve_status_reject') || @$data->approver()->approve_status == config('app.approve_status_disable')) 
                    checked 
                @endif
              >
            </td>
            <td>
              {{ @$data->approver()->approve_comment ? @$data->approver()->approve_comment : 'N/A' }}
            </td>
            <td>
              @if (@$data->approver()->comment_attach)
                <a href="{{ asset('/'.@$value->comment_attach) }}" target="_self">
                  <img src="{{ asset('/'.@$value->comment_attach) }}" alt="file" style="max-height:40px; width: 40px; border: 1px solid;">
                </a>
              @else
                N/A
              @endif
            </td>
            <td class="text-center">
                @if(@$data->approver()->approved_at)
                    @if(@$data->company_id == 2 || @$data->company_id == 6) 
                        <!-- show time only for MFI and MMI -->
                        {{ (\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d-m-Y h:i:s a')) }}
                    @else
                        {{ (\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d-m-Y')) }}
                    @endif
                @endif
            </td>
          </tr>
        @endif
      </table>
      <span>ស្នើរដោយៈ</span>
      <span style="color: #32B222;">{{ @$data->requester->name }}</span><br>
      @if(@$data->remark)
        <span>កំណត់សម្គាល់ៈ {{@$data->remark}}</span><br>
      @endif

      @if($data->hr_id)
        <span>តំណភ្ជាប់ៈ</span>
        <!-- <a href="/hr_request/{{ @$data->hr_id }}/show" target="_self">
          <mark id="highlights"> {{ @$hr->title }} </mark>
        </a><br> -->
        <a href="{{ route('hr_request.show', @$data->hr_id) }}" target="">
          <mark id="highlights"> {{ @$hr->title }} </mark>
        </a><br>
      @endif

      @if($data->attachment)
        <span>ឯកសារភ្ជាប់ៈ</span>
        <a href="{{ asset('/'.@$data->attachment) }}" target="_self">{{@$data->att_name}}</a>
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

            {!! $forcompany->header_section  !!}

            <div class="subtitle" style="float: left">
                @if($data->forcompany->id < 4)
                  <p>ការិយាល័យកណ្ដាល</p>
                @endif

                <?php
                  $set_date = \Carbon\Carbon::createFromTimestamp(strtotime('17-06-2021')); //set start date 
                  $date = \Carbon\Carbon::createFromTimestamp(strtotime(@$data->start_date));
                ?>
                
                @if($date > $set_date)
                    <p title="Number / MMYY Short Name Company">លេខៈ {{ khmer_number($data->no) }} 
                      / {{ @khmer_number($data->start_date->format('m')) }}{{ @khmer_number($data->start_date->format('y')) }}

                      @if (@$data->approver()->position_level == config('app.position_level_president') 
                          || @$data->approver()->position_level == config('app.position_level_ceo') )
                              {{ $data->forcompany->short_name_km }}
                      @else 
                              {{ @$data->approver()->short_department }}
                      @endif
                    </p>
                @else
                    <p title="Number / YY Short Name Company">លេខៈ {{ khmer_number($data->no) }} 
                      / {{ @khmer_number($data->start_date->format('y')) }} 
                      {{ $data->forcompany->short_name_km }}
                    </p>
                @endif

                {{-- <p title="Number / YY Short Name Company">លេខៈ {{ khmer_number($data->no) }} 
                  / {{ @khmer_number($data->start_date->format('y')) }} 
                  {{ $data->forcompany->short_name_km }}
                </p> --}}
            </div>
            <div class="title" style="text-align: center; clear: both;">
              <div>
                <h2>
                  <!-- សេចក្តីសម្រេច -->
                  @if ($data->types == 'សេចក្តីណែនាំ')
                    សេចក្តីណែនាំ
                  @elseif ($data->types == 'សេចក្តីជូនដំណឹង')
                    សេចក្តីជូនដំណឹង
                  @else
                    សេចក្តីសម្រេច
                  @endif
                </h2>
                <h2>ស្តីពី</h2>
                <h2>{{ $data->title_km }}</h2>

                @if (!in_array($data->company_id, [7, 8])) <!-- not in TSP & MHT -->
                  <p></p>
                  <img src="{{ asset('/img/logo/font_tt.png') }}" width="150">
                  <h2>
                    @if ($data->approver()->position_level == config('app.position_level_president'))
                      {{ $data->forcompany->approver }}
                      {{ $data->forcompany->long_name }}
                    @else
                      {{ $data->approver()->position_name }}
                      {{ $data->forcompany->long_name }}
                    @endif
                  </h2>
                @endif

              </div>
            </div>
            <div class="content page" style="margin-top: 15px;">
              @if(@$data->company_id !=7 && @$data->company_id !=8)
                <div class="desc desc_p" style="text-align: left;">
                  {!! $data->reference !!}
                </div>
              @endif
              
              <h2 style="text-align: center; margin-top: 2px; margin-bottom: 10px">
                @if($data->types == "សេចក្តីណែនាំ")
                  {{$data->apply_for}}
                @else
                  សម្រេច/Hereby Decided
                @endif
              </h2>
              <table id="points" style="text-align: justify;">
                <tr style="vertical-align: top">
                  <td class="point_width"><h1>ប្រការ ០១៖</h1><h4>Clause 01:</h4></td>
                  <td class="desc_width" style="padding-top: 0"><?= $data->point[0] ?></td>
                </tr>
                @if (isset($data->point[1]) && $data->point[1])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ០២៖</h1><h4>Clause 02:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[1] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[2]) &&$data->point[2])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ០៣៖</h1><h4>Clause 03:</h4></td>
                    <td style="padding-top: 0"><?=$data->point[2] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[3]) && $data->point[3])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ០៤៖</h1><h4>Clause 04:</h4></td>
                    <td style="padding-top: 0"><?=$data->point[3] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[4]) && $data->point[4])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ០៥៖</h1><h4>Clause 05:</h4></td>
                    <td style="padding-top: 0"><?=$data->point[4] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[5]) && $data->point[5])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ០៦៖</h1><h4>Clause 06:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[5] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[6]) && $data->point[6])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ០៧៖</h1><h4>Clause 07:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[6] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[7]) && $data->point[7])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ០៨៖</h1><h4>Clause 08:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[7] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[8]) && $data->point[8])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ០៩៖</h1><h4>Clause 09:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[8] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[9]) && $data->point[9])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ១០៖</h1><h4>Clause 10:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[9] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[10]) && $data->point[10])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ១១៖</h1><h4>Clause 11:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[10] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[11]) && $data->point[11])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ១២៖</h1><h4>Clause 12:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[11] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[12]) && $data->point[12])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ១៣៖</h1><h4>Clause 13:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[12] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[13]) && $data->point[13])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ១៤៖</h1><h4>Clause 14:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[13] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[14]) && $data->point[14])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ១៥៖</h1><h4>Clause 15:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[14] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[15]) && $data->point[15])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ១៦៖</h1><h4>Clause 16:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[15] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[16]) &&$data->point[16])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ១៧៖</h1><h4>Clause 17:</h4></td>
                    <td style="padding-top: 0"><?=$data->point[16] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[17]) && $data->point[17])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ១៨៖</h1><h4>Clause 18:</h4></td>
                    <td style="padding-top: 0"><?=$data->point[17] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[18]) && $data->point[18])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ១៩៖</h1><h4>Clause 19:</h4></td>
                    <td style="padding-top: 0"><?=$data->point[18] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[19]) && $data->point[19])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ២០៖</h1><h4>Clause 20:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[19] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[20]) && $data->point[20])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ២១៖</h1><h4>Clause 21:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[20] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[21]) && $data->point[21])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ២២៖</h1><h4>Clause 22:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[21] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[22]) && $data->point[22])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ២៣៖</h1><h4>Clause 23:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[22] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[23]) && $data->point[23])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ២៤៖</h1><h4>Clause 24:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[23] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[24]) && $data->point[24])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ២៥៖</h1><h4>Clause 25:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[24] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[25]) && $data->point[25])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ២៦៖</h1><h4>Clause 26:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[25] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[26]) && $data->point[26])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ២៧៖</h1><h4>Clause 27:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[26] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[27]) && $data->point[27])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ២៨៖</h1><h4>Clause 28:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[27] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[28]) && $data->point[28])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ២៩៖</h1><h4>Clause 29:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[28] ?></td>
                  </tr>
                @endif
                @if (isset($data->point[29]) && $data->point[29])
                  <tr style="vertical-align: top">
                    <td><h1>ប្រការ ៣០៖</h1><h4>Clause 30:</h4></td>
                    <td style="padding-top: 0"><?= $data->point[29] ?></td>
                  </tr>
                @endif
              </table>

              <div class="mb-5 small-sign text-right" style="margin-bottom: 0px">
                @foreach($data->reviewers() as $key => $value)
                  @if ($value->approve_status == config('app.approve_status_approve'))
                    <img  src="{{ asset($value->short_signature) }}"  
                          alt="short_sign" 
                          title="{{ @$value->user_name }}" 
                          style="width: 20px; margin-top: -30px;">
                  @endif
                @endforeach
              </div>

              <div class="big-sign">
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

                  @if ($data->approver()->position_level == config('app.position_level_president'))

                    @if ($data->status == config('app.approve_status_approve'))
                      <h2 style="text-align: center;">
                        {{ $data->forcompany->approver }}
                      </h2>
                      <img style="width: 100px; margin-top: 10px;"
                           src="{{ asset('/'.$data->approver()->signature) }}"
                           alt="Signature"
                      >
                    @else
                      <h2 style="text-align: center;">
                        {{ $data->forcompany->approver }}
                      </h2>

                      <br><br>

                    @endif

                  @else

                    @if ($data->status == config('app.approve_status_approve'))
                      <h2 style="text-align: center;">
                        {{ @json_decode($data->approver()->user_object)->position_name ?: $data->approver()->position_name }}
                      </h2>
                      <img style="width: 100px; margin-top: 10px;"
                           src="{{ asset('/'.$data->approver()->signature) }}"
                           alt="Signature"
                      >
                    @else
                      <h2 style="text-align: center;">
                        {{ @json_decode($data->approver()->user_object)->position_name ?: $data->approver()->position_name }}
                      </h2>
                      <br><br>
                    @endif

                    <br><br>

                  @endif

                  <h1 style="margin-top: 10px;">
                    {{ @check_nickname($data->approver()->position_level, $data->created_at ) }} 
                    {{ @json_decode($data->approver()->user_object)->name ?: $data->approver()->name }}
                  </h1>
                </div>

                <div class="copy" style="clear: both">
                  <div class="left" style="float: left; margin-top: -20px">
                    <h2>ចម្លងជូន</h2>
                    @if ($data->types == 'សេចក្តីណែនាំ' || $data->types == 'សេចក្តីសម្រេច' || $data->types == 'សេចក្តីជូនដំណឹង')
                      <p>- ដូចប្រការ {{ khmer_number($data->practise_point) }} <b>"ដើម្បីអនុវត្ត"/All Department for implementation</b></p>
                    @else
                      <p>- សាមីខ្លួន</p>
                    @endif
                    <p>- ឯកសារ_កាលប្បវត្តិ/Chronological Order</p>
                  </div>
                </div>
              </div>
            </div>
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
    <div style="width: 1024px; margin: auto;">

      {!! $forcompany->footer_section  !!}

    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('request_memo.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('request_memo.disable', $data->id)])

</body>

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

  // approve
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

  // comment
  $( "#reject_btn" ).on( "click", function( event ) {
    event.preventDefault();
    $('#comment_modal').modal('show');
  });

  // reject
  $( "#disable_btn" ).on( "click", function( event ) {
    event.preventDefault();
    $('#disable_modal').modal('show');
  });

</script>
<!-- @include('global.sweet_alert') -->
@if (session('status') == 1)

  @if($hr && $hr->status == 1)
    <script>
      // pending
      Swal.fire({
        title: 'Success!',
        text: 'សូមមេត្តាជួយអនុម័តលើតំណភ្ជាប់',
        icon: 'warning',
        confirmButtonText: 'OK'
      }).then((result) => {
        if (result) {
          window.open("{{ route('hr_request.show', @$data->hr_id) }}", '_blank');
        }
      })
    </script>

  @elseif($hr && $hr->status == 3)
    <script>
      // pending
      Swal.fire({
        title: 'Success!',
        text: 'សូមមេត្តាជួយពិនិត្យលើតំណភ្ជាប់',
        icon: 'warning',
        confirmButtonText: 'OK'
      }).then((result) => {
        if (result) {
          window.open("{{ route('hr_request.show', @$data->hr_id) }}", '_blank');
        }
      })
    </script>

  @else
    <script>
      Swal.fire({
        title: 'Success!',
        text: 'The request has been success',
        icon: 'success',
        timer: '2000',
      })
    </script>
  @endif

@elseif (session('status') == 2)
  <script>
    Swal.fire({
      title: 'Success!',
      text: 'The request has been success',
      icon: 'success',
      timer: '2000',
    })
  </script>
@endif

</html>
