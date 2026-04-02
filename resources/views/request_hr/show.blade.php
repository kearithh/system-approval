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

          button {
            display: none;
          }

          .table-bordered > tbody > tr > td, .table-bordered > thead > tr > th {
              border: 1px solid #1D1D1D !important;
          }

          .border_hide > tbody > tr > td {
            border: none !important;
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

  <div class="a4" id="action_container" style=" margin: auto;background: white;">
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
          @if(!can_approve_reject($data, config('app.type_general_expense')))
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

    @include('request_hr.reviewer_table', ['reviewers' => [
        $agreeBy = $data->reviewers()->where('position', 'agree_by')->first(),
        $agreeByShort = $data->reviewers()->where('position', 'agree_by_short')->first(),
        $reviewer = $data->reviewers()->where('position', 'reviewer')->first(),
        $reviewerShort1 = $data->reviewers()->where('position', 'reviewer_short_1')->first(),
        $reviewerShort2 = $data->reviewers()->where('position', 'reviewer_short_2')->first(),
        $verify = $data->reviewers()->where('position', 'verify')->first(),
        $data->approver()
    ]])

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
            <div class="header">
              <div class="row text-left" style="margin-top: -40px">

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

                  <div class="col-sm-12">
                      <img src="{{ asset($forcompany->logo) }}" alt="logo" style="height: 56px">
                  </div>
              </div>
              <!-- for mmi -->
              @if ($data->company_id == 6)
                <p style="float: right;"><b>No. {{ @showArrayCode($data->code) }}</b></p>
              @endif
            </div>
            <br>
            <div class="body">
              <div class="row">
                <div class="col-sm-12">
                      <table class="table table-bordered">
                        <tr>
                            <td colspan="5" class="border_hide" style="border: 0">
                                <div class="float-left">
                                    អ្នកស្នើសុំ | Request by : {{ @$data->creator_object->name ?: $data->requester->name }}
                                </div>
                            </td>

                            <td colspan="6" class="border_hide" style="border: 0">
                              <table class="border_hide">
                                <tr>
                                  <td class="text-right p-0">
                                    <span>តួនាទី|Position :&nbsp;</span>
                                  </td>
                                  <td class="text-left p-0">
                                    <span> {{ @$data->creator_object->position_name ?: $data->requester->position->name_km }}</span>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-right p-0">
                                    <span>ទីតាំងការងារ|Location :&nbsp;</span>
                                  </td>
                                  <td class="text-left p-0">
                                    <span>{{ @$data->location }}</span>
                                  </td>
                                </tr>
                              </table>
                            </td>

                            <td id="approve_section" class="text-center" style="min-width: 200px">
                                <strong class="text-bold">សម្រាប់ផ្នែកអនុម័ត</strong>
                                <br>
                                <strong class="">For Approval section</strong>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="9"><i>អ្នកស្នើសុំ ត្រូវបំពេញដោយការទទួលខុសត្រូវ | Requestor should fill in this request properly.</i></td>
                            <td colspan="2" class="text-bold text-center">
                              <strong>សម្រាប់មន្រ្តីហិរញ្ញវត្ថុ</strong>
                            </td>
                            <td rowspan="14" style="vertical-align: top">
                                @include('request_hr.approve_section')
                            </td>
                        </tr>
                        <tr class="text-center">
                            <td style="min-width: 30px;">ល.រ<br> No.</td>
                            <td style="min-width: 160px">បរិយាយ <br> Description</td>
                            <td style="min-width: 160px">គោលបំណង <br> Purpose</td>
                            <td>បរិមាណ <br> QTY</td>
                            <td style="min-width: 40px">ឯកត្តា <br> Unit</td>
                            <td style="min-width: 60px">ថ្លៃឯកត្តា <br> Unit Price</td>
                            <td style="min-width: 72px">ទឹកប្រាក់ <br> Amount</td>
                            <td style="min-width: 55px">ថ្ងៃទិញចុងក្រោយ <br> Last Date of Purchasing</td>
                            <td style="min-width: 55px">ចំនួននៅសល់ <br> Remain QTY</td>
                            <td style="min-width: 50px">លេខគណនី <br> Account No.</td>
                            <td style="min-width: 50px">សមតុល្យ <br> Balance</td>
                        </tr>
                        <?php $i = 1; ?>
                        @foreach($data->items as $key => $item)
                            <tr class="finance_section">
                                <td class="text-center">{{ $i++ }}</td>
                                <td>{{ $item->desc }}</td>
                                <td>{{ $item->purpose }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-center">{{ $item->unit }}</td>
                                <td class="text-right">
                                    @if($item->currency=='KHR')
                                        {{ number_format($item->unit_price) .' ៛'}}
                                    @else
                                        {{'$ '. num_format(($item->unit_price), 4, 2) }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($item->currency=='KHR')
                                        {{ number_format($item->qty * $item->unit_price) .' ៛'}}
                                    @else
                                        {{'$ '. number_format(($item->qty * $item->unit_price),2) }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->last_purchase_date == null)
                                        N/A
                                    @else
                                        {{\Carbon\Carbon::createFromTimestamp(strtotime($item->last_purchase_date))->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->remain_qty == null || $item->remain_qty == 0)
                                        0
                                    @else
                                        {{ $item->remain_qty }}
                                    @endif
                                </td>
                                <td>
                                    <input
                                        style="width: 60px;"
                                        class="account_no"
                                        type="text"
                                        id="account_no"
                                        name="account_no[{{$item->id}}]"
                                        value="{{ $item->account_no }}"
                                        readonly="true"
                                    >
                                </td>
                                <td>
                                    <input
                                        style="width: 60px;"
                                        class="balance"
                                        type="text"
                                        id="balance"
                                        name="balance[{{$item->id}}]"
                                        value="{{ $item->balance }}"
                                        readonly="true"
                                    >

                                </td>
                            </tr>
                        @endforeach
                            @for($i = (count($data->items) - 9);  $i <= 0; $i++)
                                <tr>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endfor
                        <tr>
                            <td class="text-right" colspan="6">សរុប: </td>
                            <td colspan="5">
                                @if($data->total > 0 )
                                    <strong>{{'$ '. number_format(($data->total),2) }}</strong> &emsp;&emsp;
                                @endif
                                @if($data->total_khr > 0 )
                                    <strong>{{ number_format($data->total_khr) .' ៛'}}</strong>
                                @endif
                            </td>
                        </tr>
                    </table>
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
  <div class="page-footer a4">
    <img src="{{ asset($forcompany->footer_landscape) }}" alt="logo" style="width: 29.7cm; max-height: 70px !important; background: white">
  </div>

  @include('global.comment_modal', ['route' =>route('request_hr.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('request_hr.disable', $data->id)])

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
              // $('#hr_form').submit();
              $.ajax({
                  type: "POST",
                  url: "{{ action('RequestHRController@approve', $data->id) }}",
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
