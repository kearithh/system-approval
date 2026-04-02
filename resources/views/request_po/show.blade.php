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
        font-size: 11px;
        line-height: normal !important;
      }

      h1{
        font-family: 'Times New Roman','Khmer OS Muol Light';
        font-weight: 400;
        font-size: 11px;
      }

      div.a4 {
          width: 29.7cm;
          /*height: 20cm;*/
          margin: auto;
      }

      table {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100% !important;
      }
      .signature {
            padding-top: 50px;
        }
        .signature > div {
            float: left;
            width: 20%;
            text-align: center;
            box-sizing: border-box
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
        .header{
          text-align: center;
        }

      .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
          padding: 4px;
          line-height: 1.42857143;
          vertical-align: middle;
          border-top: 1px solid #ddd;
      }

      .reviewer_section > div > img {
        height: 25px;
      }


      h2{
        margin-block-start: 17px;
        font-size: 11px !important;
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

      @page {
          size: A4 landscape;
          margin: 0;
        }

      @media print {
  thead {
    display: table-header-group;
  }
  tfoot {
    display: table-footer-group;
  }
  .table-bordered > tbody > tr > td, .table-bordered > thead > tr > td, .table {
              border: 1px solid #1D1D1D !important;
          }
          

  button {
    display: none;
  }

  .page-footer {
    height: auto;
    position: fixed;
    bottom: 0;
    width: 100%;
  }

  body {
    margin: 0;
  }
  
  #action_container {
    display: none;
  }

  th.no-print-border {
            border: none !important;
        }
        
}


        .contain{
          padding-left: 20px;
          padding-right: 20px;
          
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

  <div id="action_container" class="a4" style=" margin: auto;background: white;">
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
          @if(!can_approve_reject($data, config('app.type_po_request')))
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
        $requestBy = $data->reviewers()->where('position', 'request_by')->first();
        $agreeBy1 = $data->reviewers()->where('position', 'agree_by_1')->first();
        $agreeBy2 = $data->reviewers()->where('position', 'agree_by_2')->first();
        $reviewerBy = $data->reviewers()->where('position', 'reviewer_by')->first();
        $reviewerSh = $data->reviewers()->where('position', 'reviewer_sh')->first();
        $data->approver();
?>
    @include('global.rerviewer_table', ['reviewers' =>
        $data->reviewers()->merge($data->reviewers_short())->push($data->approver())
    ])

  </div>

  <div class="a4" style=" margin: auto;background: white; min-height: 700px;">
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
          <div class="row">
          <div class="col">{!! $data->forcompany->header_section  !!}</div>
          <div class="col">
          
          </div>
          </div>
            {{-- {!! $data->forcompany->header_section  !!} --}}
            
            
            <br>
            <table class="table table-bordered ">
              <thead class="header">
              </thead>
              <thead class="header">
                <tr>
                  <td colspan="3">បណ្ណបញ្ជាទិញ​/Purchase Order</td>
                  <td colspan="2">អ្នកផ្តត់ផ្គង់/Vendor</td>
                  <td colspan="5">ដឹកជញ្ជូនទៅ/Ship To</td>
                  <td style="width: 10px;">នាយកដ្ឋាន</td>
                  
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="3"><b>ថ្ងៃខែឆ្នាំ​ Date:</b> {{ optional($data->created_at)->format('d/M/Y') }}<br>
                    <b>លេខយោងបណ្ណស្នើទិញ P.R  Number:<br> {{ $data->department_po_names }}-{{ implode('-', $data->code_pr) }}</b><br>
                    <b>លេខយោងបណ្ណបញ្ជាទិញ P.O Number:<br> {{ $data->code }}</b>
                  </td>
                  <td colspan="2">
                    <b>ឈ្មោះអ្នកផ្តត់ផ្គង់:</b> {{ $data->name_kh }}<br>
                    <b>Vendor Name:</b> {{ $data->name_en }}<br>
                    <b>Address:</b> {{ $data->address_vd }}<br>
                    <b>Contact Person:</b> {{ $data->contact_ps }}<br>
                    <b>E-mail:</b> {{ $data->email }}<br>
                    <b>Mobile Phone:</b> {{ $data->mobile_phone }}<br>
                    <b>VAT:</b> {{ $data->vat_vd }}
                  </td>
                  <td colspan="5"><b>ឈ្មោះក្រុមហ៊ុន:</b> {{ $data->companie_name }}<br>
                    <b>Company:</b> {{ $data->companie_name_en }}<br>
                    <b>អាសយដ្ឋាន:</b> {{ $data->companie_address_kh }}<br>
                    <b>Address:</b> {{ $data->companie_address_en }}<br>
                    <b>VAT:</b> {{ $data->vat_st }}<br>
                    <b>ឈ្មោះអ្នកទទួល (Receiver​ Name):</b>  {{ $data->name_reciever }}<br>
                    <b>លេខទូរស័ព្ទ (Tel.​ No):</b>  {{ $data->tel }}
                  </td>
                  <td class="text-center">
                    {{ $data->department_po_name ?? 'N/A'}}
                  </td>
                </tr>
              </tbody>
            
            {{-- <div class="row">
            <div class="col">
               <h1>បណ្ណបញ្ជាទិញ​ Purchase Order</h1>
          <p><b>ថ្ងៃខែឆ្នាំ​ Date:</b> {{ optional($data->created_at)->format('d/M/Y') }}</p>
          <p><b>លេខយោងបណ្ណស្នើទិញ P.R  Number: {{ $data->department_names }}-{{ implode('-', $data->code_pr) }}</b></p>
          <p><b>លេខយោងបណ្ណបញ្ជាទិញ P.O Number: {{ $data->code }}</b></p> 
             <h1>ឈ្មោះអ្នកផ្តត់ផ្គង់: {{ $data->name_kh }}</h1>
            <h1><b>Vendor Name:</b> {{ $data->name_en }}</h1>
            <p><b>Address:</b> {{ $data->address_vd }}</p>
            <p><b>Contact Person:</b> {{ $data->contact_ps }}</p>
            <p><b>E-mail:</b> {{ $data->email }}</p>
            <p><b>Mobile Phone:</b> {{ $data->mobile_phone }}</p>
            <p><b>VAT:</b> {{ $data->vat_vd }}</p>
            </div> 
            <div class="col">
            <h1>ដឹកជញ្ជូនទៅ Ship To </h1>
            <p><b>ឈ្មោះក្រុមហ៊ុន:</b> {{ $data->companie_name }}</p> 
            <p><b>Company:</b> {{ $data->companie_name_en }}</p>
            <p><b>អាសយដ្ឋាន:</b> {{ $data->companie_address_kh }}</p>
            <p><b>Address:</b> {{ $data->companie_address_en }}</p>
            <p><b>VAT:</b> {{ $data->vat_st }}</p>
            <p><b>ឈ្មោះអ្នកទទួល (Receiver​ Name):</b>  {{ $data->name_reciever }}</p>
            <p><b>លេខទូរស័ព្ទ (Tel.​ No):</b>  {{ $data->tel }}</p>
            </div>
            </div> --}}
            <div class="body">
          
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

	            
	              <thead class="header">
	                <tr>
	                    <td>ល.រ</td>
	                    
	                    <td>បរិយាយមុខទំនិញ<br>Product Name / Description</td>
	                    <td>បរិមាណ<br>QTY</td>
                      <td style="min-width: 80px">ឯកតា<br>Unit</td>
	                    <td style="min-width: 100px">តម្លៃរាយ<br>Unit Price</td>
                      
	                    {{-- @if ($vat)
	                        <td style="min-width: 90px">ពន្ធអាករ(%)</td>
	                    @endif --}}
	                    <td style="min-width: 200px">ថ្លៃទំនិញ<br>Amount</td>
	                    {{-- <td>ផ្សេងៗ</td> --}}
                      <td >ថ្ងៃទិញចុងក្រោយ<br>Last Date of Purchasing</td>
                      <td >ថ្លៃឯកតាទិញចុងក្រោយ<br>Last Unit purchase price</td>
                      <td colspan="2">ចំនួននៅសល់<br>Remain QTY</td>
                      
                      <th  style="width: 18%; border: none; text-align:left" class="no-print-border">
                        <b>TERM & CONDITIONS</b><br>
                        1. Incoterm: {{ $data->incoterm }}<br>
                        2. Payment: {{ $data->payment }}<br>
                        3. Delivery: {{ $data->delivery }}<br>
                        4. Shipment: {{ $data->shipment }}<br>
                        5. Warranty: {{ $data->warranty }}<br>
                        6. Consignee: {{ $data->consignee }}
                    </th>
                    
	                </tr>
                  
	              </thead>
	              <tbody>
	                <?php $total = 0; ?>
	                @foreach($data->items as $key => $item)
	                    <?php $subtotal = $item->qty*$item->unit_price + ($item->qty*$item->unit_price*$item->vat)/100 ?>
	                    <tr>
	                        <td style="text-align: center;">{{ $key +1 }}</td>
	                        <td>{{ $item->name }}</td>
                          <td style="text-align: center;">{{ $item->qty }}</td>
	                        <td>{{ $item->desc }}</td>
	                        
	                        <td style="text-align: center;">
	                            @if($item->currency=='KHR')
	                                {{ number_format($item->unit_price) }} ៛
	                            @else
	                                $ {{ number_format($item->unit_price, 3) }}
	                            @endif
	                        </td>
                          
	                        {{-- @if ($vat)
	                            <td style="text-align: center;">{{ $item->vat }}%</td>
	                        @endif --}}
	                        <td style="text-align: left;">
	                            @if($item->currency=='KHR')
	                                {{ number_format($subtotal) }} ៛
	                            @else
	                                $ {{ number_format(($subtotal), 2) }}
	                            @endif
	                        </td>
	                        {{-- <td>{{ $item->other }}</td> --}}
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
                        
                        <td colspan="2" style="text-align: center;">{{ $item->lqty }}</td>

	                        <?php $total += $subtotal; ?>
	                    </tr>
                    
	                @endforeach
	                <tr style="font-weight: 700">
                  <td colspan="3"><p style="height: 110px;">គោលបំណង/Purpose: {{ $data->purpose }}</p></td>
                 
                      {{-- <table><tr>
                        <td class="child">
                            Share cost 
                        </td>
                        <td class="child">
                            ORD1: {{ $data->ord_one }}
                        </td>
                        <td class="child">
                            ORD2: {{ $data->ord_two }}
                        </td>
                        <td class="child">
                            Orchid: {{ $data->orchid }}
                        </td>
                        <td class="child">
                            S-spine: {{ $data->spine }}
                        </td>
                      </tr></table> --}}
                    
                  
	                    <td colspan="2" style="text-align: right;">
                     <p> សរុប Total:</p> 
                     
                      <p>អាករលើតម្លៃបន្ថែម/VAT​: </p> 
                      
                      <p> សរុបរួម/ USD Grand Total: </p>
                      
                      <p> សរុបរួម/ KHR Grand Total: </p> 
                      
                      <p> Exchange Rate: </p> 
                      
                      </td>
	                    <td colspan="5" style="text-align: justify;">
                      
                      <p>
	                       @if($data->total_amount_usd > 0)
                              {{ '$ ' . number_format($data->total_amount_usd, 2) }} &emsp;
                          @endif

                          @if($data->total_amount_khr > 0)
                              {{ number_format($data->total_amount_khr, 2) }}៛ &emsp;
                          @endif

	                        </p>
                          
                          <p>
                          @if(isset($data['total_amount_usd'], $data['vat']))
                              @php
                                  $totalAmountUSD = (float) $data['total_amount_usd'];
                                  $exchangeRate = (float) $data['vat'];
                              @endphp

                              @if($totalAmountUSD > 0 && $exchangeRate > 0)
                                  $ {{ number_format(($totalAmountUSD * ($exchangeRate /100)), 2) }} &emsp;
                              @endif
                              @if($data['vat']==0)
                              0
                              @endif

                              
                          @endif
                          @if(isset($data['total_amount_khr'], $data['vat']))
                              @php
                                  $totalAmountKHR = (float) $data['total_amount_khr'];
                                  $exchangeRate = (float) $data['vat'];
                              @endphp

                              @if($totalAmountKHR > 0 && $exchangeRate > 0)
                                   {{ number_format(($totalAmountKHR * ($exchangeRate /100)), 2) }}៛ &emsp;
                              @endif
                              @if($data['vat']==0)
                              0
                              @endif

                              
                          @endif
	                       </p>
                          
                          <p>
                          @if(isset($data['total_amount_usd'], $data['vat']))
                            @php
                                $totalAmountUSD = (float) $data['total_amount_usd'];
                                $vat = (float) $data['vat'];
                            @endphp

                            @if($totalAmountUSD > 0 && $vat > 0)
                                @php
                                    $vatAmount = $totalAmountUSD * ($vat / 100);
                                    $totalAmountWithVat = $totalAmountUSD + $vatAmount;
                                @endphp

                                ${{ number_format($totalAmountWithVat, 2) }}
                            @else
                                ${{ number_format($totalAmountUSD, 2) }}
                            @endif
                        @endif/
                        @if(isset($data['total_amount_khr'], $data['vat']))
                            @php
                                $totalAmountKHR = (float) $data['total_amount_khr'];
                                $vat = (float) $data['vat'];
                            @endphp

                            @if($totalAmountKHR > 0 && $vat > 0)
                                @php
                                    $vatAmount = $totalAmountKHR * ($vat / 100);
                                    $totalAmountWithVat = $totalAmountKHR + $vatAmount;
                                @endphp

                                {{ number_format($totalAmountWithVat, 2) }}៛
                            @else
                                {{ number_format($totalAmountKHR, 2) }}៛
                            @endif
                        @endif

	                        </p>
                          <p>
                          
                          @if(isset($data['total_amount_usd'], $data['exchange_rate'], $data['vat']))
                            @php
                                $totalAmountUSD = (float) $data['total_amount_usd'];
                                $exchangeRate = (float) $data['exchange_rate'];
                                $vat = (float) $data['vat'];
                            @endphp

                            @if($totalAmountUSD > 0 && $exchangeRate > 0)
                                @php
                                    $totalAmountWithVat = $totalAmountUSD + ($totalAmountUSD * ($vat / 100));
                                @endphp

                                @if($vat == 0)
                                    {{ number_format($totalAmountUSD * $exchangeRate), 2 }}៛ &emsp;
                                @else
                                    {{ number_format(($totalAmountWithVat * $exchangeRate), 2) }}៛ &emsp;
                                @endif
                            @elseif($exchangeRate == 0)
                                0
                            @endif
                        @endif

                        @if(isset($data['total_amount_khr'], $data['exchange_rate'], $data['vat']))
                                @php
                                    $totalAmountKHR = (float) $data['total_amount_khr'];
                                    $exchangeRate = (float) $data['exchange_rate'];
                                    $vat = (float) $data['vat'];
                                @endphp

                                @if($totalAmountKHR > 0 && $exchangeRate > 0)
                                    @php
                                        $totalAmountWithVat = $totalAmountKHR + ($totalAmountKHR * ($vat / 100));
                                    @endphp

                                    @if($vat == 0)
                                        $ {{ number_format($totalAmountKHR / $exchangeRate, 3) }} &emsp;
                                    @else
                                        $ {{ number_format(($totalAmountWithVat / $exchangeRate), 3) }} &emsp;
                                    @endif
                                @elseif($exchangeRate == 0)
                                    0
                                @endif
                            @endif
	                        </p>
                          <p>
                          @if($data->exchange_rate == 0)
                              0
                          @else
                              {{ $data->exchange_rate }}
                          @endif

	                        </p>
	                    </td>
	                </tr>
	              </tbody>
	            </table>
             
              {{-- <h1><b>TERM & CONDITIONS</b></h1>
              <div class="row" style="border: groove 1px; border-color: black; width: 100%; margin: auto;">
                
                <div class="col ">
                  
                  <p>1. Incoterm: {{ $data->incoterm }}</p>
                  <p>2. Payment: {{ $data->payment }}</p>
                  <p>3. Delivery: {{ $data->delivery }}</p>
                </div>
              <div class="col">
                  <p>4. Shipment: {{ $data->shipment }}</p>
                  <p>5. Warranty: {{ $data->warranty }}</p>
                  <p>6. Consignee: {{ $data->consignee }}</p>
                  <p>7. Notify Party: {{ $data->notify_party }}</p>
              </div>
            </div> --}}

              @if (@$data->forcompany->short_name_en == 'MMI')
                  <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                      
                      
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

                          <p>រៀបចំដោយ<br>Prepared By</p>
                          <p>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</p>
                          <img style="height: 60px;"
                               src="{{ asset('/'.$data->requester()->signature) }}"
                               alt="Signature">
                          <p>{{ @$data->creator_object->name_en ?: $data->requester()->name_en }}</p>
                          
                        </div>

                        
                      <div style="width: {{ (($relatedCol*100)/$allCol).'%' }}">
                        @foreach([$agreeBy1] as $reviewer)
                            @if ($reviewer)
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    @if ($reviewer->approve_status == config('app.approve_status_approve'))
                                        <p>
                                            Date: {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                                            -{{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('M')) }}-
                                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                                        </p>
                                        <p>អនុម័តដំបូងដោយ <br> Initial approved by 1</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                        <img style="height: 60px;" src="{{ asset('/'.$reviewer->signature) }}" alt="Signature">
                                        <p>{{ @json_decode($reviewer->user_object)->name_en ?: $reviewer->name_en }}</p>
                                    @else
                                        <p>Day.....Month.....Year.....</p>
                                        <p>អនុម័តដំបូងដោយ <br> Initial approved by 1</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        @foreach([$agreeBy2] as $reviewer)
                            @if ($reviewer)
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    @if ($reviewer->approve_status == config('app.approve_status_approve'))
                                        <p>
                                            Date: {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                                            -{{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('M')) }}-
                                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                                        </p>
                                        <p>អនុម័តដំបូងដោយ <br> Initial approved by 2</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                        <img style="height: 60px;" src="{{ asset('/'.$reviewer->signature) }}" alt="Signature">
                                        <p>{{ @json_decode($reviewer->user_object)->name_en ?: $reviewer->name_en }}</p>
                                    @else
                                        <p>Day.....Month.....Year.....</p>
                                        <p>អនុម័តដំបូងដោយ <br> Initial approved by 2</p>
                                        <p>{{ @json_decode($reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                       

                        @foreach([$reviewerSh] as $reviewer)
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
  <div class="page-footer a4">
    <div style="width: 100%; margin: auto; text-align: center;">
      {!! $data->forcompany->footer_section  !!}
      {{-- <img src="/img/logo/footer_ord_l_2.png" alt="" style="width: 29.7cm; max-height: 70px !important; background: white"> --}}
    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('request_po.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('request_po.disable', $data->id)])

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
                  url: "{{ action('RequestPOController@approve', $data->id) }}",
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
