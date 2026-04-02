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
          font-size: 14px;
        }

        div.a4 {
            width: 1024px;
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
          size: A4 portrait;
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

          .table-bordered td, .table-bordered th {
              border: 1px solid #1D1D1D !important;
          }

          .border_hide{
            border: 0 !important;
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
          padding-left: 70px;
          padding-right: 70px;
        }
        .same-level-div{
          display: inline-block;
        }
        #highlights{
          background-color: #FFFF00;
          color: black;
         /* padding: 5px;*/
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
          @if(!can_approve_reject($data, config('app.type_cash_advance')))
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

    @include('cash_advance.partials.reviewer_table', ['reviewers' =>
        $data->reviewers()->merge($data->reviewerShorts())->push($data->approver())
    ])

  </div>

  <div class="a4" style=" margin: auto;background: white; min-height: 1300px;">
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
              <div class="row text-left">
                  <div class="col-sm-12">

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

                      <img src="{{ asset($forcompany->logo) }}" alt="logo" style="height: 56px">

                  </div>
              </div>
            </div>
            <div class="body">
              <div class="row">
                <div class="col-sm-12">
                  <form id="hr_form" method="POST" action="{{ action('CashAdvanceController@approve', $data->id) }}">
                      @csrf
                      @method('post')
                      <div class="text-right">
                        កាលបរិច្ឆេទ | Date: {{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$data->created_at))->format('d/m/Y')}}
                      </div>
                      <div class="text-center">
                        @if(@$data->type_advance == config('app.advance'))
                          <h1>ទម្រង់បុរេប្រទាន</h1>
                          <h1><b>ADVANCE</b></h1>
                        @elseif(@$data->type_advance == config('app.clear_advance'))
                          <h1>ទម្រង់ជម្រះបុរេប្រទាន</h1>
                          <h1><b>ADVANCE CLEARANCE</b></h1>
                        @elseif(@$data->type_advance == config('app.reimbursement'))
                          <h1>ទម្រង់ទូទាត់ការចំណាយ</h1>
                          <h1><b>REIMBURSEMENT</b></h1>
                        @endif
                      </div>
                      <p>ការិយាល័យ | Office: {{ @$data->forbranch->name_km }}</p>
                      <table class="table table-bordered">
                        <tr> 
                            <td class="col-md-3" colspan="2">
                                <div class="float-left">
                                  @if(@$data->type_advance == config('app.advance'))
                                    <strong>បុរេប្រទាន | Advance</strong>
                                  @elseif(@$data->type_advance == config('app.clear_advance'))
                                    <strong>ទម្រង់ជម្រះបុរេប្រទាន | Advance Clearance</strong>
                                  @elseif(@$data->type_advance == config('app.reimbursement'))
                                    <strong>ទម្រង់ទូទាត់ការចំណាយ | Reimbursement</strong>
                                  @endif
                                </div>
                            </td>

                            <td id="approve_section" class="col-md-3 text-center" class="text-center" style="min-width: 150px">
                                <strong class="text-bold">ហត្ថលេខា | Signature  </strong>
                            </td>
                        </tr>

                        <tr>
                            <td style="min-width: 230px; vertical-align: top">
                              @if($data->type==1)
                                <input disabled type="checkbox" checked > បេសកម្មធ្វើដំណើរ | Travel mission (a/c .........) <br>
                                <input disabled type="checkbox" > ផ្សេងៗ | Other (a/c .........)
                              @else
                                <input disabled type="checkbox" > បេសកម្មធ្វើដំណើរ | Travel mission (a/c .........)<br>
                                <input disabled type="checkbox" checked > ផ្សេងៗ | Other (a/c .........)
                              @endif
                            </td>
                            <td style="vertical-align: top; min-width: 500px">
                              @if($data->type==1)
                                <p>{{@$data->title}}</p>
                                <p>...............................................</p>
                              @else
                                <p>...............................................</p>
                                <p>{{@$data->title}}</p>
                              @endif
                              <br>
                              <?php $i = 1; ?>
                              @foreach($data->items as $key => $item)
                                @if(@$item->currency == "USD")
                                  <p>
                                    {{$item->name}} 
                                    ( {{ $item->qty }} * $ {{ number_format($item->unit_price, 2) }} 
                                    = $ {{ number_format(($item->qty * $item->unit_price), 2) }} )
                                    {{ @$item->date ? 
                                      (\Carbon\Carbon::createFromTimestamp(strtotime(@$item->date))->format('d-m-Y')) : ''
                                    }}
                                  </p>
                                @else
                                  <p>
                                    {{$item->name}} 
                                    ( {{ $item->qty }} * {{ number_format($item->unit_price) }} ៛ 
                                    = {{ number_format($item->qty * $item->unit_price) }} ៛ )
                                    {{ @$item->date ? 
                                      (\Carbon\Carbon::createFromTimestamp(strtotime(@$item->date))->format('d-m-Y')) : ''
                                    }}
                                  </p>
                                @endif
                              @endforeach
                              <br>
                              @if(@$data->type_advance == config('app.advance'))
                                <p>
                                  <strong>
                                    សរុបទឹកប្រាក់ជាលេខ | Amount in figure:
                                    <span>
                                      [
                                        @if($data->total > 0 )
                                            {{'$ '. number_format(($data->total),2) }}
                                        @endif

                                        @if($data->total > 0 && $data->total_khr > 0)
                                            &emsp;
                                        @endif

                                        @if($data->total_khr > 0 )
                                            {{ number_format($data->total_khr) .' ៛'}}
                                        @endif
                                      ]
                                    </span>
                                  </strong>
                                </p>
                                <p>
                                  <strong style="padding-top:-10px">
                                    សរុបទឹកប្រាក់ជាអក្សរ | Amount in words:
                                    <span>[ {{ $data->total_letter }} ]</span>
                                  </strong>
                                </p>
                              @else
                                @if(@$data->advance_obj->currency_advance == "USD")
                                  <p>
                                    <strong>
                                      I- ទឺកប្រាក់បុរេប្រទាន | Advance amount:
                                      <span>
                                        @if(@$data->advance_obj->advance > 0 )
                                          {{ '$ '. number_format(@$data->advance_obj->advance, 2) }}
                                        @endif
                                      </span>
                                    </strong>
                                  </p>
                                  <p>
                                    <strong>
                                      II- ចំណាយសរុប | Total expenses:
                                      <span>
                                        @if(@$data->advance_obj->expense > 0 )
                                          {{ '$ '. number_format(@$data->advance_obj->expense, 2) }}
                                        @endif
                                      </span>
                                    </strong>
                                  </p>
                                  <p>
                                    <strong>
                                      III- ទឹកប្រាក់ត្រូវបង់ចូលក្រុមហ៊ុន [ករណី I > II]:
                                      <span>
                                        @if(@$data->advance_obj->company > 0 )
                                          {{ '$ '. number_format(@$data->advance_obj->company, 2) }}
                                        @endif
                                      </span>
                                    </strong>
                                  </p>
                                  <p>
                                    <strong>
                                      IV- ទឹកប្រាក់ត្រូវបង់អោយបុគ្គលិក [ករណី I < II]:
                                      <span>
                                        @if(@$data->advance_obj->staff > 0 )
                                          {{ '$ '. number_format(@$data->advance_obj->staff, 2) }}
                                        @endif
                                      </span>
                                    </strong>
                                  </p>

                                @else
                                  <p>
                                    <strong>
                                      I- ទឺកប្រាក់បុរេប្រទាន | Advance amount:
                                      <span>
                                        @if(@$data->advance_obj->advance > 0 )
                                          {{ number_format(@$data->advance_obj->advance) .' ៛'}}
                                        @endif
                                      </span>
                                    </strong>
                                  </p>
                                  <p>
                                    <strong>
                                      II- ចំណាយសរុប | Total expenses:
                                      <span>
                                        @if(@$data->advance_obj->expense > 0 )
                                          {{ number_format(@$data->advance_obj->expense) .' ៛'}}
                                        @endif
                                      </span>
                                    </strong>
                                  </p>
                                  <p>
                                    <strong>
                                      III- ទឹកប្រាក់ត្រូវបង់ចូលក្រុមហ៊ុន [ករណី I > II]:
                                      <span>
                                        @if(@$data->advance_obj->company > 0 )
                                          {{ number_format(@$data->advance_obj->company) .' ៛'}}
                                        @endif
                                      </span>
                                    </strong>
                                  </p>
                                  <p>
                                    <strong>
                                      IV- ទឹកប្រាក់ត្រូវបង់អោយបុគ្គលិក [ករណី I < II]:
                                      <span>
                                        @if(@$data->advance_obj->staff > 0 )
                                          {{ number_format(@$data->advance_obj->staff) .' ៛'}}
                                        @endif
                                      </span>
                                    </strong>
                                  </p>
                                @endif
                              @endif

                            </td>

                            <td>
                                <div class="text-center">
                                  <span>ស្នើរសុំដោយ | Requested by:</span></br>
                                  <img src="{{ asset('/'.$data->requester->signature) }}" style="height: 35px;" alt="signature"></br>
                                  <span>{{ $data->requester->name }}</span>
                                  <p>{{ $data->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="text-center"> 
                                  @foreach($data->reviewers() as $item)
                                      <span>ត្រួតពិនិត្យដោយ | Verified by:</span></br>
                                      @if (@$item->approve_status == config('app.approve_status_approve'))
                                          <div class="related">
                                              <img style="height: 35px;"
                                                  src="{{ asset('/'.$item->signature) }}"
                                                  alt="Signature"></br>
                                              <span>{{ $item->name }}</span>
                                              <p>{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('d/m/Y') }}</p>
                                          </div>
                                      @else
                                          <div class="related">
                                              </br><br>
                                              <span>{{ $item->name }}</span>
                                              <br>
                                              <br>
                                          </div>
                                      @endif
                                  @endforeach
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td style="vertical-align: top;" colspan="2">

                              <div style="float: right;">
                                @foreach($data->reviewerShorts() as $item)
                                    @if (@$item->approve_status == config('app.approve_status_approve'))
                                        <img style="height: 20px;"
                                            src="{{ asset('/'.$item->short_signature) }}"
                                            title="{{$item->name}}" 
                                            alt="short_signature"></br>
                                    @endif
                                @endforeach
                              </div>

                              <p><strong>ឯកសារភ្ជាប់ | Reference</strong></p>
                              @if($data->type==1)
                                <input disabled type="checkbox" checked > លិខិតបេសកម្មធ្វើដំណើរ | Travel mission letter <br>
                              @endif

                              @if(@$data->attachment)
                                  <?php $atts = is_array($data->attachment) ? $data->attachment : json_decode($data->attachment); ?>
                                  @foreach($atts as $att )
                                      <!-- <a href="{{ asset($att->src) }}" target="_self">View old File: {{ $att->org_name }}</a><br> -->
                                      <input disabled type="checkbox" checked > {{ $att->org_name }} <br>
                                  @endforeach
                              @endif
                            </td>
                            <td class="col-md-3">
                              <div class="text-center">
                                <p>អនុម័តដោយ | Approved by:</p>
                                @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                                    <img style="height: 35px;"
                                        src="{{ asset('/'.@$data->approver()->signature) }}"
                                        alt="Signature"><br>
                                    <span>
                                      {{ @check_nickname($data->approver()->position_level, $data->created_at ) }}
                                      {{ (@$data->approver()->name) }}
                                    </span>
                                    <p>{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d/m/Y') }}</p>
                                @else
                                    <br><br>
                                    <span>
                                      {{ @check_nickname($data->approver()->position_level, $data->created_at ) }}
                                      {{ (@$data->approver()->name) }}
                                    </span>
                                    <br><br>
                                @endif
                              </div>
                            </td>
                        </tr>
                        @if(@$data->type_advance == config('app.advance'))
                          <tr>
                            <td style="vertical-align: top;" colspan="2">
                              <!-- សម្គាល់៖ បុរេប្រទាននេះ ត្រូវធ្វើការជម្រះរៀងរាល់ថ្ងៃច័ន្ទ បន្ទាប់ពីបញ្ចប់បេសកម្ម ឬទទួលបានឯកសារគ្រប់គ្រាន់ ។ -->
                              សម្គាល់៖ {{ $data->note }}
                            </td>
                            <td class="text-center">
                                <span>ទទួលដោយ | Received by:</span></br>
                                <br><br>
                                <span>{{ (@$data->receiver()->name) }}</span>
                                <br><br>

                                <!-- @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                                    <img src="{{ asset('/'.@$data->receiver()->signature) }}" alt="signature"></br>
                                    <span>{{@$data->receiver()->name}}</span>
                                    <p>{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d/m/Y') }}</p>
                                @else
                                    <br>
                                    <span>{{ (@$data->receiver()->name) }}</span>
                                    <br>
                                @endif -->
                            </td>
                          </tr>
                        @endif
                    </table>
                    
                  </form>
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
    <img src="{{ asset($forcompany->footer_landscape) }}" alt="logo" style="width: 1024px; height: 50px; background: white">
  </div>

  @include('global.comment_modal', ['route' =>route('cash_advance.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('cash_advance.disable', $data->id)])

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
                  url: "{{ action('CashAdvanceController@approve', $data->id) }}",
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
