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
          @if(!can_approve_reject($data, config('app.type_pr_request')))
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
    <?php
        $verifyBy1 = $data->reviewers()->where('position', 'verify_by_1')->first();
        $verifyBy2 = $data->reviewers()->where('position', 'verify_by_2')->first(); 
        $verifyBy3 = $data->reviewers()->where('position', 'verify_by_3')->first();      
        $finalShort= $data->reviewers()->where('position', 'final_short')->first();
        $data->approver();
?>
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
           <h1>សំណើបញ្ជាទិញ/Purchase Request​​</h1>
	            
            </div>
            <br>
            <div class="body">
                               
    <div class="row">
        <div class="col"><b>នាយកដ្ឋាន/Department:</b><br>
            
    {{ $data->department_name }}


        </div>
        <div class="col"><b>ស្នើរសុំដោយ/Request by:</b>
          <br>{{ @$data->requester()->name_en }}
        </div>
        <div class="col"><b>សំរាប់/FOR:</b><br>
            @if(@$data->remarks)
                <span>{{@$data->remarks}}</span><br>
            @endif
        </div>
        <div class="col"><b>កាលបរិច្ឆេទ/Date:</b>
            {{ optional($data->created_at)->format('d/M/Y') }}
        </div>
    </div>
    <ul>
    <li>
            <b>លេខស្នើរសុំទិញ/Purchase Requisition Number: {{ $data->department_names }}-{{ $data->code }}</b>
              </b>
        
    </li>
    <li>
        <b>មូលហេតុការស្នើរសុំទិញ​ Reason for purchase/ព័ត៌មានគម្រោងលំអិតProject detail:</b><br> {{ $data->purpose }}
    </li>
    </ul>
    <br>
    <div class="row">
        <div class="col-4">
            <b>ប្រភពតម្រូវការ/Sourcing requirement:</b><br>
            <input type="checkbox" id="sourcing_requirement_yes" name="sourcing_requirement_yes" value="1" @if($data->sourcing_requirement_yes) checked @endif disabled>
            <label for="sourcing_requirement_yes">Yes</label><br>
            <input type="checkbox" id="sourcing_requirement_no" name="sourcing_requirement_no" value="0" @if(!$data->sourcing_requirement_yes) checked @endif disabled>
            <label for="sourcing_requirement_no">No</label> 
        </div>
        <div class="col-4">
            <b>អ្នកផ្គត់ផ្គង់ដែលមានស្រាប់​ Prefer supplier/សំណើរអ្នកផ្គត់ផ្គង់Single supplier Requirement:</b><br>
            <input type="checkbox" id="prefer_supplier_yes" name="prefer_supplier_yes" value="1" @if($data->prefer_supplier_yes) checked @endif disabled>
            <label for="prefer_supplier_yes">Yes</label><br>
            <input type="checkbox" id="prefer_supplier_no" name="prefer_supplier_no" value="0" @if(!$data->prefer_supplier_yes) checked @endif disabled>
            <label for="prefer_supplier_no">No</label> 
        </div>
        <div class="col-4">
            <b>សំណើរដេញថ្លៃ/Tender Requirement:</b><br>
            <input type="checkbox" id="tender_requirement_yes" name="tender_requirement_yes" value="1" @if($data->tender_requirement_yes) checked @endif disabled>
            <label for="tender_requirement_yes">Yes</label><br>
            <input type="checkbox" id="tender_requirement_no" name="tender_requirement_no" value="0" @if(!$data->tender_requirement_yes) checked @endif disabled>
            <label for="tender_requirement_no">No</label> 
        </div>
    </div>
	            <strong>
	                Purchasing Information
	            </strong>
              <!-- for mmi -->
              @if ($data->company_id == 6)
                  <p style="float: right;"><b>No. {{ @showArrayCode($data->code) }}</b></p>
              @endif
	            <?php
	                
	            ?>

	            <table id="item">
	              <thead>
	                <tr>
	                    <td>ល.រ<br>No</td>
	                    <td style="min-width: 120px">មុខទំនិញ<br>Item</td>
	                    <td>ទំហំ Scope<br>Specification</td>
	                    <td>រូបភាព<br>Pictures</td>
	                    <td style="min-width: 90px">បរិមាណ<br>Quantity</td>
	                    <td>ឯកតា<br>Unit</td>
                      <td >ថ្ងៃទិញចុងក្រោយ<br>Last Date of Purchasing</td>
                      <td >ថ្លៃឯកតាទិញចុងក្រោយ<br>Last Unit purchase price</td>
                      <td >ចំនួននៅសល់<br>Remain QTY</td>
	                </tr>
	              </thead>
	              <tbody>
	                <?php $total = 0;$sumQty = 0; // Initialize a variable to store the sum of quantities ?>
	                @foreach($data->items as $key => $item)
	                    <?php $subtotal = $item->qty + $item->qty ?>
                      <?php
                $sumQty += $item->qty; // Add each item's quantity to the sum?>
	                    <tr>
	                        <td style="text-align: center;">{{ $key +1 }}</td>
	                        <td>{{ $item->name }}</td>
	                        <td>{{ $item->desc }}</td>
                          
                          
        
	                        <td style="text-align: center;">
                @if($item->attachment)
                @if(is_array($item->attachment))
                    @foreach($item->attachment as $att)
                        <img src="{{ asset($att->src) }}" alt="{{ $att->org_name }}" style="max-width: 100px; max-height: 100px;"><br>
                    @endforeach
                @else
                    <img src="{{ asset('/'.$item->attachment) }}" alt="{{ $item->att_name }}" style="max-width: 100px; max-height: 100px;">
                @endif
            @endif
            </td>
        <td style="text-align: center;">{{ $item->qty }}</td>

	                        <td>{{ $item->other }}</td>
	                        <td style="text-align: center;">@if ($item->ldp)
                            {{ \Carbon\Carbon::parse($item->ldp)->format('d/m/Y') }}
                          @else
                            <!-- Display nothing or a placeholder like 'N/A' -->N/A
                          @endif</td>
                          <td style="text-align: center;">
                            @if($item->lunit_price == 0)
                                N/A
                            @else
                                @if($item->currency == 'KHR')
                                    {{ number_format($item->lunit_price) }} ៛
                                @else
                                    $ {{ number_format($item->lunit_price, 3) }}
                                @endif
                            @endif
                        </td>
                        
                          <td style="text-align: center;">{{ $item->lqty }}</td>
	                    </tr>
	                @endforeach
	                <tr style="font-weight: 700">
	                    <td colspan="4" style="text-align: left">Total Item</td>
	                    <td colspan="5" style="text-align: center;">
	                        {{ $sumQty }}
	                    </td>
	                </tr>
	              </tbody>
	            </table>
              <br>
            <b>អ្នកផ្គត់ផ្គង់ដែលអាចទទួលយកបាន/Potential Supplier</b>
              <table id="item">
	              <thead>
	                <tr> 
	                    <td style="min-width: 120px">អ្នកផ្គត់ផ្គង់/Vendor</td>
	                    <td>អុីម៉ែល Email/លេខទូរស័ព្ទ​ Phone</td>   
	                </tr>
	              </thead>
	              <tbody>
	                    <tr>
	                        <td>{{ $data->reason }}</td>
	                        <td>{{ $data->ep }}</td>
                          
	                        
	                        <?php $total += $subtotal; ?>
	                    </tr>  
	              </tbody>
	            </table>
              <br>

              @if (@$data->forcompany->short_name_en == 'MMI')
                  
                    
              @else
                  <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                     
                      {{-- <p>
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
                      </p> --}}
                  </div>
              @endif

            </div>

            <!-- show short approver for mmi date <= 2022-09-10 -->
            @if (@$data->forcompany->short_name_en == 'MMI' && $data->created_at <= '2022-09-10')

                <?php
                  $relatedCol = count($data->reviewers_short_sign());
                  $allCol = $relatedCol + 2;
                ?>
                @else

                  <?php
                    $relatedCol = count($data->reviewers());
                    $allCol = $relatedCol + 2;
                  ?>

                  <div class="signature">
                      <div style="width: {{ (100/$allCol).'%' }}">
                          <p>
                             Date: {{ ($data->created_at->format('d')) }}
                              -{{ ($data->created_at->format('M')) }}-
                              {{ ($data->created_at->format('Y')) }}
                          </p>

                          <p>ស្នើសុំដោយ<br>Requested by</p>
                          <p>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_en }}</p>
                          <img style="height: 60px;"
                               src="{{ asset('/'.$data->requester()->signature) }}"
                               alt="Signature">
                          <p>{{ @$data->requester()->name_en }}</p>
                      </div>

                      <div style="width: {{ (($relatedCol*100)/$allCol).'%' }}">
                        @foreach([$verifyBy1] as $reviewer)
                            @if ($reviewer)
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    @if ($reviewer->approve_status == config('app.approve_status_approve'))
                                        <p>
                                            Date: {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                                            -{{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('M')) }}-
                                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                                        </p>
                                        <p>អនុម័តដំបូង <br> Initial Approval 1</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                        <img style="height: 60px;" src="{{ asset('/'.$reviewer->signature) }}" alt="Signature">
                                        <p>{{ @json_decode($reviewer->user_object)->name_en ?: $reviewer->name_en }}</p>
                                    @else
                                        <p>Day.....Month.....Year.....</p>
                                        <p>អនុម័តដំបូង <br> Initial Approval 1</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        @foreach([$verifyBy2] as $reviewer)
                            @if ($reviewer)
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    @if ($reviewer->approve_status == config('app.approve_status_approve'))
                                        <p>
                                            Date: {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                                            -{{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('M')) }}-
                                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                                        </p>
                                        <p>អនុម័តដោយ <br> Non Medical or Medical</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                        <img style="height: 60px;" src="{{ asset('/'.$reviewer->signature) }}" alt="Signature">
                                        <p>{{ @json_decode($reviewer->user_object)->name_en ?: $reviewer->name_en }}</p>
                                    @else
                                        <p>Day.....Month.....Year.....</p>
                                        <p>អនុម័តដោយ <br> Non Medical or Medical</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        @foreach([$verifyBy3] as $reviewer)
                            @if ($reviewer)
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    @if ($reviewer->approve_status == config('app.approve_status_approve'))
                                        <p>
                                            Date: {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                                            -{{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('M')) }}-
                                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                                        </p>
                                        <p>អនុម័តដំបូង <br> Initial Approval 2</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                        <img style="height: 60px;" src="{{ asset('/'.$reviewer->signature) }}" alt="Signature">
                                        <p>{{ @json_decode($reviewer->user_object)->name_en ?: $reviewer->name_en }}</p>
                                    @else
                                        <p>Day.....Month.....Year.....</p>
                                        <p>អនុម័តដំបូង <br> Initial Approval 2</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    
                   
                        

                        @foreach([$finalShort] as $reviewer)
                            @if ($reviewer)
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    @if ($reviewer->approve_status == config('app.approve_status_approve'))
                                        <p>
                                            Date: {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                                             -{{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('M')) }}-
                                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                                        </p>
                                        <p>អនុម័តចុងក្រោយដោយ <br> Final Approved By 1</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                        <img style="height: 60px;" src="{{ asset('/'.$reviewer->signature) }}" alt="Signature">
                                        <p>{{ @json_decode($reviewer->user_object)->name_en ?: $reviewer->name_en }}</p>
                                    @else
                                        <p>Day.....Month.....Year.....</p>
                                        <p>អនុម័តចុងក្រោយដោយ <br> Final Approved By 1</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>




                      <div style="width: {{ (100/$allCol).'%' }}">
                          @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                              <p>
                                  Date: {{ (\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                                  -{{ (\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('M')) }}-
                                  {{ (\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}
                              </p>
                              <p>អនុម័តចុងក្រោយដោយ <br> Final Approved By 2</p>
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
                                {{ @json_decode(@$data->approver()->user_object)->name_en ?: $data->approver()->name_en }}
                              </p>
                          @else
                              <p>Day.....Month.....Year.....</p>
                              <p>អនុម័តចុងក្រោយដោយ <br> Final Approved By 2</p>
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

  @include('global.comment_modal', ['route' =>route('request_pr.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('request_pr.disable', $data->id)])

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
                  url: "{{ action('RequestPRController@approve', $data->id) }}",
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
