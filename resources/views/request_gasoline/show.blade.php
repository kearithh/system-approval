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
          font-size: 12px;
          line-height: normal !important;
        }

        h1{
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-weight: 400;
          font-size: 14px;
        }

        div.a4 {
            width: 29.7cm;
            /*height: 20cm;*/
            margin: auto;
        }

        p {
          margin: 3px;
        }

        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100% !important;
        }

        .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
            padding: 3px;
            line-height: 1.42857143;
            vertical-align: middle;
            border-top: 1px solid #ddd;
        }

        td {
          padding: 2px;
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
          height: 40px;
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

          .table-bordered td, .table-bordered th {
              border: 1px solid #1D1D1D !important;
          }

          .border_hide{
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

        .signature > div {
            float: left;
            width: 33.33%;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box
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
          @if(!can_approve_reject($data, config('app.type_request_gasoline')))
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

    @include('request_gasoline.partials.reviewer_table', ['reviewers' =>
        $data->reviewers()->merge($data->reviewerShorts())->push($data->approver())
    ])

  </div>

  <div class="a4" style=" margin: auto;background: white; height: auto;">
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
                  <form id="hr_form" method="POST" action="{{ action('RequestGasolineController@approve', $data->id) }}">
                    @csrf
                    @method('post')
                    <div class="text-center">
                      <h1>សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម</h1>
                    </div>

                    <table>
                      <tr>
                        <td style="width: 12%"><b>ឈ្មោះបុគ្គលិក៖</b></td>
                        <td style="width: 38%">{{ @$data->staffName->name ?: 'N/A' }}</td>
                        <td style="width: 12%"><b>តួនាទី៖</b></td>
                        <td style="width: 38%">{{ @$data->staffName->position->name_km ?: 'N/A' }}</td>
                      </tr>
                      <tr>
                        <td><b>ទីតំាងការងារ៖</b></td>
                        <td>{{ @$data->forbranch->name_km }}</td>
                        <td><b>ម៉ាករថយន្ត៖</b></td>
                        <td>{{ @$data->model }}</td>
                      </tr>
                    </table>

                    <table class="table table-bordered">
                      <tr> 
                        <th class="text-center">ល.រ</th>
                        <th class="text-center" style="width: 180px">គោលដៅ</th>
                        <th class="text-center">ថ្ងៃចេញដំណើរ</th>
                        <th class="text-center">ថ្ងៃត្រឡប់មកវិញ</th>
                        <th class="text-center">កុងទ័រចេញដំណើរ</th>
                        <th class="text-center">កុងទ័រត្រឡប់មកវិញ</th>
                        <th class="text-center">គិតជាម៉ាយ</th>
                        <th class="text-center">គិតជាគីឡូ</th>
                        <th class="text-center">ចំនួនសាំង/លីត</th>
                      </tr>

                      @foreach($data->items as $key => $value)
                        <tr>
                          <td class="text-center">{{ $key+1 }}</td>
                          <td class="text-center">{{ $value->destination }}</td>
                          <td class="text-center">
                            {{(\Carbon\Carbon::createFromTimestamp(strtotime($value->date_start))->format('d-M-Y'))}}
                          </td>
                          <td class="text-center">
                            {{(\Carbon\Carbon::createFromTimestamp(strtotime($value->date_back))->format('d-M-Y'))}}
                          </td>
                          <td class="text-right">{{ number_format($value->start_number) }}</td>
                          <td class="text-right">{{ number_format($value->end_number) }}</td>
                          <td class="text-right">{{ number_format($value->miles_number, 2) }}</td>
                          <td class="text-right">{{ number_format($value->km_number, 2) }}</td>
                          <td class="text-right">{{ number_format($value->gasoline_number, 2) }}</td>
                        </tr>
                      @endforeach

                      <tr>
                        <th colspan="6" class="text-center">សរុប</th>
                        <th class="text-right">{{ number_format($data->total_miles, 2) }}</th>
                        <th class="text-right">{{ number_format($data->total_km, 2) }}</th>
                        <th class="text-right">{{ number_format($data->total_gasoline, 2) }}</th>
                      </tr>
                      <tr style="border: none !important">
                        <th colspan="7" class="border_hide" style="border: none !important"></th>
                        <th class="text-right">ថ្លៃសាំងក្នុងមួយលីត</th>
                        <th class="text-right">{{ number_format($data->price_per_l) }}</th>
                      </tr>
                      <tr>
                        <th colspan="7" class="border_hide" style="border: none !important"></th>
                        <th class="text-right">ទឹកប្រាក់សរុប</th>
                        <th class="text-right">{{ number_format($data->total_expense) }}</th>
                      </tr>
                    </table>
                    
                  </form>
                </div>
              </div>

            </div>

            @include('request_gasoline.partials.approve_section')
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
    <img src="{{ asset($forcompany->footer_landscape) }}" alt="logo" style="width: 29.7cm; height: 50px; background: white">
  </div>

  @include('global.comment_modal', ['route' =>route('request_gasoline.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('request_gasoline.disable', $data->id)])

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
                  url: "{{ action('RequestGasolineController@approve', $data->id) }}",
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
