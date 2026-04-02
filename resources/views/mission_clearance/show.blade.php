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

        h1{
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-weight: 400;
          font-size: 15px;
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
            padding: 5px;
            line-height: 1.42857143;
            vertical-align: middle;
            border-top: 1px solid #ddd;
        }

        td {
          padding: 5px;
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
          padding-left: 50px;
          padding-right: 50px;
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
          @if(!can_approve_reject($data, config('app.type_mission_clearance')))
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

    @include('mission_clearance.partials.reviewer_table', ['reviewers' =>
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
                  <form id="hr_form" method="POST" action="{{ action('MissionClearanceController@approve', $data->id) }}">
                      @csrf
                      @method('post')
                      <div class="text-center">
                        <h1>ទម្រង់ជម្រះបេសកម្ម</h1>
                        <h1><b>MISSION CLEARANCE</b></h1>
                      </div>
                      <p>ការិយាល័យ | Office: {{ @$data->forbranch->name_km }}</p>
                      <p>ទទួលដោយ | Received by: {{ @$data->receiverBy->name ?: 'N/A' }}</p>
                      <p>កាលបរិច្ឆេទ | Date: {{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$data->created_at))->format('d/m/Y')}}</p>
                      <table class="table table-bordered">
                        <tr> 
                            <td class="col-md-3" style="min-width: 500px;">
                                <div class="float-left">
                                  <strong>ទម្រង់ជម្រះបេសកម្ម | Mission Clearance</strong>
                                </div>
                            </td>

                            <td id="approve_section" class="col-md-3 text-center" class="text-center" style="max-width: 30px !important">
                                <strong class="text-bold">ហត្ថលេខា | Signature  </strong>
                            </td>
                        </tr>

                        <tr>
                            <td style="vertical-align: top;">
                              <table class="table table-bordered">
                                <tr>
                                  <td colspan="5">
                                    <strong>I- ទឺកប្រាក់បុរេប្រទាន | Advance amount:</strong>
                                  </td>
                                  <td class="text-right">
                                    <strong>{{ number_format(@$data->advance) .' ៛'}}</strong>
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="5">
                                    <strong>II- ចំណាយសរុប | Total expenses:</strong>
                                  </td>
                                  <td class="text-right">
                                    <strong>{{ number_format(@$data->expense) .' ៛'}}</strong>
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="6">&nbsp;</td>
                                </tr>
                                <tr>
                                  <td style="width: 30px;">ល.រ</td>
                                  <td class="text-center" style="min-width: 100px;">ថ្ងៃ ខែ ឆ្នាំ</td>
                                  <td class="text-center" style="min-width: 130px;">បេសកម្មសាខា</td>
                                  <td class="text-right" style="min-width: 120px;">របបអាហារ</td>
                                  <td class="text-right" style="min-width: 140px;">ចំណាយផ្សេងៗ</td>
                                  <td class="text-right" style="width: 130px;">សរុប</td>
                                </tr>
                                <?php 
                                  $total_diet = $total_fees = 0;
                                ?>
                                @foreach($data->items as $key => $value)
                                  <?php 
                                    $total_diet += $value->diet;
                                    $total_fees += $value->fees;
                                  ?>
                                  <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td class="text-center">
                                      {{(\Carbon\Carbon::createFromTimestamp(strtotime($value->date))->format('d-M-Y'))}}
                                    </td>
                                    <td class="text-center">{{ $value->branch_name }}</td>
                                    <td class="text-right">{{ number_format($value->diet) }} ៛</td>
                                    <td class="text-right">{{ number_format($value->fees) }} ៛</td>
                                    <td class="text-right">{{ number_format($value->amount) }} ៛</td>
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
                                  </tr>
                                @endfor
                                <tr>
                                  <td colspan="3" class="text-right">
                                    <strong>សរុប</strong>
                                  </td>
                                  <td class="text-right">
                                    <strong><?= number_format($total_diet) ?> ៛</strong>
                                  </td>
                                  <td class="text-right">
                                    <strong><?= number_format($total_fees) ?> ៛</strong>
                                  </td>
                                  <td></td>
                                </tr>
                                <tr>
                                  <td colspan="5">
                                    <strong>III- ទឹកប្រាក់ត្រូវបង់ចូលក្រុមហ៊ុន [ករណី I > II]:</strong>
                                  </td>
                                  <td class="text-right">

                                    @if(@$data->company_transfer > 0 )
                                      <strong>{{ number_format(@$data->company_transfer) .' ៛'}}</strong>
                                    @endif
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="5">
                                    <strong>IV- ទឹកប្រាក់ត្រូវបង់អោយបុគ្គលិក [ករណី I < II]:</strong>
                                  </td>
                                  <td class="text-right">
                                    @if(@$data->staff_transfer > 0 )
                                      <strong>{{ number_format(@$data->staff_transfer) .' ៛'}}</strong>
                                    @endif
                                  </td>
                                </tr>
                              </table>

                            </td>

                            <td style="vertical-align: top;">
                                <div class="text-center" style="min-height: 200px;">
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
                            <td style="vertical-align: top;">

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
                              @if($data->type == 1)
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
                                      {{ @json_decode(@$data->approver()->user_object)->name ?: @$data->approver()->name }}
                                    </span>
                                    <p>{{ \Illuminate\Support\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d/m/Y') }}</p>
                                @else
                                    <br><br>
                                    <span>
                                      {{ @check_nickname($data->approver()->position_level, $data->created_at ) }}
                                      {{ @json_decode(@$data->approver()->user_object)->name ?: @$data->approver()->name }}
                                    </span>
                                    <br><br>
                                @endif
                              </div>
                            </td>
                        </tr>
                        @if (@$data->remark)
                          <tr>
                            <td style="vertical-align: top;">
                              <p><strong>កំណត់សម្គាល់ | remark</strong></p>
                              <p>{{ @$data->remark }}</p>
                            </td>
                            <td></td>
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

  @include('global.comment_modal', ['route' =>route('mission_clearance.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('mission_clearance.disable', $data->id)])

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
                  url: "{{ action('MissionClearanceController@approve', $data->id) }}",
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
