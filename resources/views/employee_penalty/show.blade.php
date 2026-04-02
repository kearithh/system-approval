<!DOCTYPE html>
<html>
<head>
    <title>E-Approval</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
          font-family: 'Times New Roman','Khmer OS Content';
          font-weight: 400;
          font-size: 13px;
          line-height: normal !important;
        }

        h1 {
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-size: 14px;
          margin: 5px 0 1px 0 !important;
        }

        p, span, b {
            font-family: 'Khmer OS Content' !important;
            /*font-size: 15px !important;*/
            margin: 0 !important;
        }

        h2{
          margin-block-start: 2px;
          font-size: 14px !important;
          font-family: 'Times New Roman','Khmer OS Muol Light';
          line-height: 20px;
        }

        .header{
          text-align: center;
        }

        .signature{
          padding: 0 0 0 0;
        }

        .related {
            float: left;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box;
            text-overflow: ellipsis;
        }

        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100% !important;
        }

        /*tr{
          vertical-align: top;
        }*/

        td{
          padding: 2px;
        }

        .item tr td {
            border: 1px solid #585858;
            padding: 2px;
        }

        .item tr th {
            border: 1px solid #585858;
            padding: 2px;
            text-align: center;
        }

        .item {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        .item thead tr {
            /*background: orange;*/
            font-weight: 700;
            text-align: center;
        }

        ul li {
            list-style: none;
            margin-left: -40px;
        }

        div.action_btn {
          display: none;
          margin-top: 5px;
          position: fixed;
          box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        div.action_btn a {
          padding: 10px 16px;
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
          /*margin-bottom: 20px;*/
        }

        .page-footer, .page-footer-space {
          height: 70px;
        }

        .page-header, .page-header-space {
          height: 60px;
        }

        .page-footer {
          /*position: fixed;*/
          bottom: 0;
          width: 100%;
        }

        .signature > div {
            float: left;
            width: 33.33%;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box
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

          button {
            display: none;
          }

          .page-footer {
            height: auto;
          }

          .bg-success {
            background-color: #dff0d8 !important;
          }

          .bg-info {
            background-color: #d9edf7 !important;
          }

          .bg-warning {
            background-color: #fcf8e3 !important;
          }

          .crossed{
            background-image: linear-gradient(to bottom right,  transparent calc(50% - 1px), red, transparent calc(50% + 1px)) !important; 
          }

          body {
            margin: 0;
            -webkit-print-color-adjust:exact;
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

        .contain{
          padding-left: 50px;
          padding-right: 50px;
          width: 880px;
        }

        .crossed{
          background-image: linear-gradient(to bottom right,  transparent calc(50% - 1px), red, transparent calc(50% + 1px)); 
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
          @if(!can_approve_reject($data, config('app.type_employee_penalty')))
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
      </form>
    </div><br><br>

    @include('global.rerviewer_table', ['reviewers' =>
        $data->reviewers()->merge($data->reviewers_short())->push($data->approver())
    ])

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

            <div class="header">
	            <h2>
                {{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}
              </h2>
	            <h2>សូមគោរពជូន</h2>
	            <h2 style="font-size: 16px">
                @if ($data->approver()->position_level == config('app.position_level_president'))
                  {{ $data->forcompany->approver }}
                  {{ $data->forcompany->long_name }}
                @else
                  {{ @json_decode(@$data->approver()->user_object)->position_name ?: $data->approver()->position_name }}
                  {{ $data->forcompany->long_name }}
                @endif
            	</h2>
              <h2>ស្នើរសុំទទូលប្រាក់ពិន័យបុគ្គលិក</h2>
            </div>
            <div class="body">
              <table >
                  <tr>
                      <td style="width: 100px; vertical-align: top">
                          <h1>តាមរយៈ</h1>
                      </td>
                      <td class="text-left" style="vertical-align: top">
                          @foreach($data->reviewers() as $reviewer)
                              <p class="mb-0">{{ @json_decode(@$reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                          @endforeach
                      </td>
                  </tr>
                  <tr>
                      <td style="vertical-align: top">
                        <h1>កម្មវត្ថុៈ</h1>
                      </td>
                      <td style="vertical-align: top">
                        <p>{{ $data->subject }}</p>
                      </td>
                  </tr>
                  <tr>
                      <td style="vertical-align: top">
                        <h1>គោលបំណងៈ</h1>
                      </td>
                      <td style="vertical-align: top">
                        <p>{{ $data->purpose }}</p>
                      </td>
                  </tr>
                  <tr>
                      <td style="vertical-align: top" colspan="2">
                        <p>
                            &emsp;&emsp;&emsp;តបតាមកម្មវត្ថុ និងគោលបំណងដូចបានជម្រាបជូនខាងលើ ខ្ញុំមានកិតិយសសូមជម្រាបជូន
                            @if(@$data->approver()->position_level == config('app.position_level_president'))
                                លោកស្រី{{@$data->forcompany->approver}}
                            @else
                                @if($data->approver()->gender == 'M')
                                  លោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                                @elseif($data->approver()->gender == 'F')
                                  លោកស្រី{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                                @else
                                  {{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                                @endif
                            @endif
                            មេត្តាត្រួតពិនិត្យ និងអនុម័តលើសំណើដើម្បីបញ្ចប់បញ្ហាជាមួយបុគ្គលិកមានឈ្មោះដូចខាងក្រោម៖
                        </p>
                      </td>
                  </tr>
              </table>
              <b><u>១. ចំនួនទឹកប្រាក់ដែលទទួលបានពីបុគ្គលិក</u></b>
	            <table class="item">
	              <thead>
	                <tr  class="bg-success">
	                    <td style="min-width: 130px" class="text-center">ឈ្មោះបុគ្គលិក</td>
	                    <td style="min-width: 280px">បរិយាយ</td>
	                    <td style="min-width: 120px">ចំនួនទឹកប្រាក់</td>
	                    <td style="min-width: 100px">ផ្សេងៗ</td>
	                </tr>
	              </thead>
	              <tbody>
	                @foreach($data->items as $key => $item)
	                    <tr>
                        @if($key == 0)
	                        <td class="text-center" rowspan="{{count(@$data->items)}}">{{ $item->name }}</td>
                        @endif
	                        <td>{{ $item->desc }}</td>
                          <td style="text-align: right;">
                              @if($item->currency=='KHR')
                                  {{ number_format($item->total) }} ៛
                              @else
                                  $ {{ number_format(($item->total), 2) }}
                              @endif
                          </td>
	                        <td>{{ $item->other }}</td>
	                    </tr>
	                @endforeach
	              </tbody>
	            </table>
              <br>
              <b><u>២. ចំនួនទឹកប្រាក់ដែលត្រូវស្នើសុំទូទាត់ឲ្យអតិថិជនវិញ</u></b>

              <table class="item" style="max-width: 100px">
                  <tr class="bg-info">
                      <th rowspan="2">ល.រ</th>
                      <th style="min-width: 90px" rowspan="2">ឈ្មោះអតិថិជន</th>
                      <th style="min-width: 70px" rowspan="2">CID</th>
                      <th style="min-width: 120px" rowspan="2">ប្រាក់ដើមនៅជំពាក់</th>
                      <th style="min-width: 100px" rowspan="2">ទឹកប្រាក់គៃបន្លំ</th>
                      <th style="min-width: 85px" colspan="3">ទឹកប្រាក់ត្រូវទូទាត់ក្នុងប្រព័ន្ធ</th>
                      <th style="min-width: 85px" colspan="2">ទឹកប្រាក់ស្នើសុំកាត់ចេញ</th>
                      <th style="min-width: 85px" rowspan="2">សំគាល់</th>
                  </tr>
                  <tr class="bg-warning">
                      <th style="min-width: 85px">ប្រាក់ដើម</th>
                      <th style="min-width: 85px">ការប្រាក់</th>
                      <th style="min-width: 85px">ប្រាក់សរុប / <br>ស្នើសុំទូទាត់</th>
                      <th style="min-width: 85px">ការប្រាក់</th>
                      <th style="min-width: 85px">ប្រាក់ពិន័យ</th>
                  </tr>
                  <?php 
                      $totalIndebted = 0;
                      $totalFraud = 0; 
                      $totalSystemRincipal = 0; 
                      $totalSystemRate = 0; 
                      $totalSystem = 0; 
                      $totalCutRate = 0; 
                      $totalCutPenalty = 0;
                      $currency = 'KHR';  
                  ?>
                  @forelse($data->customerItems as $key => $item)
                      <tr>
                          <?php 
                              $totalIndebted += $item->indebted;
                              $totalFraud += $item->fraud; 
                              $totalSystemRincipal += $item->system_rincipal; 
                              $totalSystemRate += $item->system_rate; 
                              $totalSystem += $item->system_total; 
                              $totalCutRate += $item->cut_rate; 
                              $totalCutPenalty += $item->cut_penalty;  
                          ?> 
                          <td class="text-center">{{ $key +1 }}</td>
                          <td>{{ $item->cus_name }}</td>
                          <td class="text-center">{{ $item->cid }}</td>
                          @if($item->currency=='KHR')
                              <td class="text-right">
                                  {{ number_format($item->indebted) }} ៛
                              </td>
                              <td class="text-right">
                                  {{ number_format($item->fraud) }} ៛
                              </td>
                              <td class="text-right">
                                  {{ number_format($item->system_rincipal) }} ៛
                              </td>
                              <td class="text-right">
                                  {{ number_format($item->system_rate) }} ៛
                              </td>
                              <td class="text-right">
                                  {{ number_format($item->system_total) }} ៛
                              </td>
                              <td class="text-right">
                                  {{ number_format($item->cut_rate) }} ៛
                              </td>
                              <td class="text-right">
                                  {{ number_format($item->cut_penalty) }} ៛
                              </td>
                          @else
                              <td class="text-right">
                                  $ {{ number_format(($item->indebted), 2) }}
                              </td>
                              <td class="text-right">
                                  $ {{ number_format(($item->fraud), 2) }}
                              </td>
                              <td class="text-right">
                                  $ {{ number_format(($item->system_rincipal), 2) }}
                              </td>
                              <td class="text-right">
                                  $ {{ number_format(($item->system_rate), 2) }}
                              </td>
                              <td class="text-right">
                                  $ {{ number_format(($item->system_total), 2) }}
                              </td>
                              <td class="text-right">
                                  $ {{ number_format(($item->cut_rate), 2) }}
                              </td>
                              <td class="text-right">
                                  $ {{ number_format(($item->cut_penalty), 2) }}
                              </td>
                          @endif
                          <td>{{ $item->remark }}</td>
                      </tr>

                  @empty
                      <tr>
                        <th colspan="11">Empty!</th>
                      </tr>
                  @endforelse

                  @if(count($data->customerItems) > 0)
                      <tr>
                          <th colspan="3">សរុប</th>
                          @if($currency=='KHR')
                              <th class="text-right">
                                  {{ number_format($totalIndebted) }} ៛
                              </th>
                              <th class="text-right">
                                  {{ number_format($totalFraud) }} ៛
                              </th>
                              <th class="text-right">
                                  {{ number_format($totalSystemRincipal) }} ៛
                              </th>
                              <th class="text-right">
                                  {{ number_format($totalSystemRate) }} ៛
                              </th>
                              <th class="text-right">
                                  {{ number_format($totalSystem) }} ៛
                              </th>
                              <th class="text-right">
                                  {{ number_format($totalCutRate) }} ៛
                              </th>
                              <th class="text-right">
                                  {{ number_format($totalCutPenalty) }} ៛
                              </th>
                              <th class="crossed"></th>
                          @else
                             <th class="text-right">
                                  $ {{ number_format(($totalIndebted), 2) }}
                              </th>
                              <th class="text-right">
                                  $ {{ number_format(($totalFraud), 2) }}
                              </th>
                              <th class="text-right">
                                  $ {{ number_format(($totalSystemRincipal), 2) }}
                              </th>
                              <th class="text-right">
                                  $ {{ number_format(($totalSystemRate), 2) }}
                              </th>
                              <th class="text-right">
                                  $ {{ number_format(($totalSystem), 2) }}
                              </th>
                              <th class="text-right">
                                  $ {{ number_format(($totalCutRate), 2) }}
                              </th>
                              <th class="text-right">
                                  $ {{ number_format(($totalCutPenalty), 2) }}
                              </th>
                              <th></th>
                          @endif
                      </tr>
                  @endif
              </table>

              <br>
              <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                  <p>
                      អាស្រ័យហេតុដូចបានជំរាបជូនខាងលើ 
                      @if(@$data->approver()->position_level == config('app.position_level_president'))
                          សូមលោកស្រី{{@$data->forcompany->approver}}
                      @else
                          @if($data->approver()->gender == 'M')
                            សូមលោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km}}
                          @elseif($data->approver()->gender == 'F')
                            សូមលោកស្រី{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km}}
                          @else
                            សូម{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km}}
                          @endif
                      @endif
                      មេត្តាពិនិត្យ និងអនុញ្ញាតតាមការគួរ។
                  </p>
                  <p>
                      @if(@$data->approver()->position_level == config('app.position_level_president'))
                          សូមលោកស្រី{{ @$data->forcompany->approver }}
                      @else
                          @if($data->approver()->gender == 'M')
                            សូមលោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km}}
                          @elseif($data->approver()->gender == 'F')
                            សូមលោកស្រី{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km}}
                          @else
                            សូម{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                          @endif
                      @endif
                      មេត្តាទទួលនូវសេចក្ដីគោរពដ៏ខ្ពង់ខ្ពស់ពី{{prifixGender($data->requester()->gender)}}។
                      <span>
                        @foreach($data->reviewers_short() as $key => $value)
                          @if ($value->approve_status == config('app.approve_status_approve'))
                            <img  src="{{ asset($value->short_signature) }}"  
                                  alt="short_sign" 
                                  title="{{ @$value->name }}" 
                                  style="width: 25px;">
                          @endif
                        @endforeach
                      </span>
                  </p>
              </div>

            </div>

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
                    <p>{{ $data->requester()->position->name_km }}</p>
                    <img style="max-height: 60px; max-width: 180px;"
                         src="{{ asset('/'.$data->requester()->signature) }}"
                         alt="Signature">
                    <p>{{ $data->requester()->name }}</p>
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

  @include('global.comment_modal', ['route' =>route('employee_penalty.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('employee_penalty.disable', $data->id)])

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
                  url: "{{ action('EmployeePenaltyController@approve', $data->id) }}",
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
@include('global.sweet_alert')
</html>
