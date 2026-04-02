<!DOCTYPE html>
<html>
<head>
    <title>E-Approval</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>

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

        h1{
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-weight: 400;
          font-size: 15px;
        }

        .header{
          text-align: center;
        }

        /*.body{
          text-align: justify;
        }*/

        .sign{
          font-size: 14px;
        }

        .sign > div > div > p {
          margin: 0 0 5px !important;
        }

        .signature{
          padding: 15px 0 0 20px;
          font-size: 14px;
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

        tr{
          vertical-align: top;
        }

        td{
          padding: 5px;
          vertical-align: middle;
        }

        #item tr td {
            border: 1px solid #585858;
            padding: 5px 5px;
        }

        #item {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        #item thead tr {
            /*background: orange;*/
            font-weight: 700;
            text-align: center;
        }

        ul li {
            list-style: none;
            margin-left: -40px;
        }

        h2{
          margin-block-start: 17px;
          font-size: 15px !important;
          line-height: normal;
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

        .signature {
            padding-top: 50px;
        }

        .signature > div {
            float: left;
            width: 33.33%;
            text-align: center;
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

        .contain{
          padding-left: 95px;
          padding-right: 95px;
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
          @if(!can_approve_reject($data, config('app.type_special_expense')))
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

    {{-- @include('global.rerviewer_table', ['reviewers' =>
        $data->reviewers()->push($data->verify())->push($data->subApprover())->push($data->approver())
    ]) --}}
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
            {!! $data->forcompany->header_section  !!}
            <div class="header">
              @if (@$data->requester()->branch->branch == 0)
	                <h1>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</h1>
              @else
                  <h1>សាខា {{ @$data->creator_object->branch_name ?: @$data->requester()->branch->name_km }}</h1>
              @endif
	            <h1>សូមគោរពជូន</h1>
	            <h1 style="font-size: 16px">
	                @if (@$data->forcompany->short_name_en == 'MMI')
                      @if(@$data->subApprover()->position_level == config('app.position_level_president'))
                          លោកស្រី{{ @$data->forcompany->approver }}
                      @else
                          @if(@$data->subApprover()->gender == 'M')
                            លោក{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                          @elseif(@$data->subApprover()->gender == 'F')
                            លោកស្រី{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                          @else
                            {{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                          @endif
                      @endif
                  @else
                      @if(@$data->approver()->position_level == config('app.position_level_president'))
                          លោកស្រី{{ @$data->forcompany->approver }}
                      @else
                          @if(@$data->approver()->gender == 'M')
                            លោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                          @elseif(@$data->approver()->gender == 'F')
                            លោកស្រី{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                          @else
                            {{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                          @endif
                      @endif
                  @endif

                  {{ @$data->forcompany->long_name }}
            	</h1>
            </div>
            <br>
            <div class="body">
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
	                ដោយមានការចំណាយលម្អិតដូចខាងក្រោម៖
	            </strong>
              <!-- for mmi -->
              @if ($data->company_id == 6)
                  <p style="float: right;"><b>No. {{ @showArrayCode($data->code) }}</b></p>
              @endif
	            <?php
	                $vat = false;
	                foreach ($data->items as $item) {
	                    if ($item->vat){
	                        $vat = true;
	                    }
	                }
	            ?>

	            <table id="item">
	              <thead>
	                <tr>
	                    <td>ល.រ</td>
	                    <td style="min-width: 120px">ឈ្មោះ</td>
	                    <td>បរិយាយ</td>
	                    <td>បរិមាណ</td>
	                    <td style="min-width: 90px">តម្លៃរាយ</td>
	                    @if ($vat)
	                        <td style="min-width: 90px">ពន្ធអាករ(%)</td>
	                    @endif
	                    <td style="min-width: 90px">សរុប</td>
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
	                        <td>{{ $item->other }}</td>
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

              @if (@$data->forcompany->short_name_en == 'MMI')
                  <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                      <p>
                          តបតាមកម្មវត្ថុ និងមូលហេតុខាងលើ{{prifixGender($data->requester()->gender)}}ស្នើសុំការអនុញ្ញាតពី
                          @if(@$data->subApprover()->position_level == config('app.position_level_president'))
                              លោកស្រី{{ @$data->forcompany->approver }}
                          @else
                              @if(@$data->subApprover()->gender == 'M')
                                លោក{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                              @elseif(@$data->subApprover()->gender == 'F')
                                លោកស្រី{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                              @else
                                {{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                              @endif
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
                      </p>
                      <p>
                          អាស្រ័យដូចបានជំរាបជូនខាងលើ
                          @if(@$data->subApprover()->position_level == config('app.position_level_president'))
                              សូមលោកស្រី{{ @$data->forcompany->approver }}
                          @else
                              @if(@$data->subApprover()->gender == 'M')
                                សូមលោក{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                              @elseif(@$data->subApprover()->gender == 'F')
                                សូមលោកស្រី{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                              @else
                                សូម{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                              @endif
                          @endif
                          មេត្តាពិនិត្យ និងអនុញ្ញាតតាមការគួរ។
                      </p>
                      <p>
                          @if(@$data->subApprover()->position_level == config('app.position_level_president'))
                              សូមលោកស្រី{{ @$data->forcompany->subApprover }}
                          @else
                              @if(@$data->subApprover()->gender == 'M')
                                សូមលោក{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                              @elseif(@$data->subApprover()->gender == 'F')
                                សូមលោកស្រី{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
                              @else
                                សូម{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}
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
              @else
                  <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                      <p>
                          តបតាមកម្មវត្ថុ និងមូលហេតុខាងលើ{{prifixGender($data->requester()->gender)}}ស្នើសុំការអនុញ្ញាតពី
                          @if(@$data->approver()->position_level == config('app.position_level_president'))
                              លោកស្រី{{ @$data->forcompany->approver }}
                          @else
                              @if(@$data->approver()->gender == 'M')
                                លោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                              @elseif(@$data->approver()->gender == 'F')
                                លោកស្រី{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                              @else
                                {{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                              @endif
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
                      </p>
                      <p>
                          អាស្រ័យដូចបានជំរាបជូនខាងលើ
                          @if(@$data->approver()->position_level == config('app.position_level_president'))
                              សូមលោកស្រី{{ @$data->forcompany->approver }}
                          @else
                              @if(@$data->approver()->gender == 'M')
                                សូមលោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                              @elseif(@$data->approver()->gender == 'F')
                                សូមលោកស្រី{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                              @else
                                សូម{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                              @endif
                          @endif
                          មេត្តាពិនិត្យ និងអនុញ្ញាតតាមការគួរ។
                      </p>
                      <p>
                          @if(@$data->approver()->position_level == config('app.position_level_president'))
                              សូមលោកស្រី{{ @$data->forcompany->approver }}
                          @else
                              @if(@$data->approver()->gender == 'M')
                                សូមលោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                              @elseif(@$data->approver()->gender == 'F')
                                សូមលោកស្រី{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
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
              @endif

            </div>

            <!-- show short approver for mmi date <= 2022-09-10 -->
            @if (@$data->forcompany->short_name_en == 'MMI' && $data->created_at <= '2022-09-10')

                <?php
                  $relatedCol = count($data->reviewers_short_sign());
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
                        <p>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</p>
                        <img style="height: 60px;"
                             src="{{ asset('/'.$data->requester()->signature) }}"
                             alt="Signature">
                        <p>{{ @$data->creator_object->name ?: $data->requester()->name }}</p>
                    </div>

                    <div style="width: {{ (($relatedCol*100)/$allCol).'%' }}">
                        @foreach($data->reviewers_short_sign() as $item)
                            @if ($item->approve_status == config('app.approve_status_approve'))
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    <p>
                                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('d')) }}
                                        ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('m')) }}
                                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('Y')) }}
                                    </p>
                                    <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                                    <p>{{ @json_decode($item->user_object)->position_name ?: $item->position_name }}</p>
                                    <img style="height: 60px;"
                                         src="{{ asset('/'.$item->signature) }}"
                                         alt="Signature">
                                    <p>{{ @json_decode($item->user_object)->name ?: $item->name }}</p>
                                </div>
                            @else
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    <p>ថ្ងៃទី.....ខែ......ឆ្នំា.....</p>
                                    <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                                    <p>{{ @json_decode($item->user_object)->position_name ?: $item->position_name }}</p>
                                </div>
                            @endif
                        @endforeach

                    </div>

                    <div style="width: {{ (100/$allCol).'%' }}">

                        @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                            <p>
                                ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->subApprover()->approved_at))->format('d')) }}
                                ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->subApprover()->approved_at))->format('m')) }}
                                ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->subApprover()->approved_at))->format('Y')) }}
                            </p>
                            <p>
                                អនុម័តដោយ៖

                                @if (@$data->shortSign()->approve_status == config('app.approve_status_approve'))
                                    <img style="height: 15px;"
                                          title="{{ $data->shortSign()->name }}"
                                          src="{{ asset('/'.@$data->shortSign()->short_signature) }}"
                                          alt="short_signature">
                                @endif

                                @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                                    <img style="height: 15px;"
                                         title="{{ $data->approver()->name }}"
                                         src="{{ asset('/'.$data->approver()->short_signature) }}"
                                         alt="short_signature">
                                @endif
                            </p>
                            @if(@$data->subApprover()->position_level == config('app.position_level_president'))
                                <p>{{ @$data->forcompany->subApprover }}</p>
                            @else
                                <p>{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}</p>
                            @endif
                            <img style="height: 60px;"
                                 src="{{ asset('/'.@$data->subApprover()->signature) }}"
                                 alt="Signature">
                            <p>{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->name }}</p>
                        @else
                            <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                            <p>

                              អនុម័តដោយ៖

                              @if (@$data->shortSign()->approve_status == config('app.approve_status_approve'))
                                <img style="height: 15px;"
                                      title="{{ $data->shortSign()->name }}"
                                      src="{{ asset('/'.@$data->shortSign()->short_signature) }}"
                                      alt="short_signature">
                              @endif

                            </p>

                            @if(@$data->subApprover()->position_level == config('app.position_level_president'))
                                <p>{{ @$data->forcompany->subApprover }}</p>
                            @else
                                <p>{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}</p>
                            @endif
                        @endif
                    </div>
                  </div>

                @else

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
                          <p>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</p>
                          <img style="height: 60px;"
                               src="{{ asset('/'.$data->requester()->signature) }}"
                               alt="Signature">
                          <p>{{ @$data->creator_object->name ?: $data->requester()->name }}</p>
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
                                      <p>{{ @json_decode($item->user_object)->position_name ?: $item->position_name }}</p>
                                      <img style="height: 60px;"
                                           src="{{ asset('/'.$item->signature) }}"
                                           alt="Signature">
                                      <p>{{ @json_decode($item->user_object)->name ?: $item->name }}</p>
                                  </div>
                              @else
                                  <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                      <p>ថ្ងៃទី.....ខែ......ឆ្នំា.....</p>
                                      <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                                      <p>{{ @json_decode($item->user_object)->position_name ?: $item->position_name }}</p>
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
                              @if(@$data->approver()->position_level == config('app.position_level_president'))
                                  <p>{{ @$data->forcompany->approver }}</p>
                              @else
                                  <p>{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}</p>
                              @endif
                              <img style="height: 60px;"
                                   src="{{ asset('/'.$data->approver()->signature) }}"
                                   alt="Signature">
                              <p>
                                {{ @check_nickname(@$data->approver()->position_level, $data->created_at ) }}
                                {{ @json_decode(@$data->approver()->user_object)->name ?: $data->approver()->name }}
                              </p>
                          @else
                              <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                              <p>អនុម័តដោយ៖</p>
                              @if(@$data->approver()->position_level == config('app.position_level_president'))
                                  <p>{{ @$data->forcompany->approver }}</p>
                              @else
                                  <p>{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}</p>
                              @endif
                          @endif
                      </div>
                    </div>

                  @endif

              </div>

              {{-- @include('request.approve_section') --}}

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
      {!! $data->forcompany->footer_section  !!}
    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('request.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('request.disable', $data->id)])

</body>

<script src="{{ asset('js/sweetalert2@9.js') }}"></script>

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
