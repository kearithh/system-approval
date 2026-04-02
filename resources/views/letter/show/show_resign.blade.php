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
          text-decoration-line: underline;
          text-decoration-style: double;
        }

        /*.body{
          text-align: justify;
        }*/

        .sign{
          padding-top: 20px;
        }

        .signature{
          padding: 15px 0 0 20px;
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
        }

        span{

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
          text-align: center !important;
          padding-top: 0;
          margin-bottom: 20px;
        }

        .page-footer, .page-footer-space {
          height: 70px;
        }

        .page-header, .page-header-space {
          height: 50px;
        }

        .page-footer {
          /*position: fixed;*/
          bottom: 0;
          width: 100%;
        }

        @page {
          margin: 20mm;
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
          <button id="back" type="button" class="btn btn-sm btn-secondary">
             Back
          </button>
      </div>
      <div class="btn-group" role="group" aria-label="">
        <button type="button" onclick="window.print()" class="btn btn-sm btn-warning">
            Print
        </button>
      </div>

      <form style="margin: 0; display: inline-block " action="">
        <div class="btn-group" role="group" aria-label="">
          @if(!can_approve_reject($data, config('app.type_hr_request')))
            <button id="approve_btn" disabled name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">
                Approve
            </button>
            <button id="comment_modal" disabled style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default">
                Comment
            </button>
          @else
            <button id="approve_btn" name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">
                Approve
            </button>
            <button id="reject_btn" style="background: #bd2130; color: white" name="next" data-target="comment_attach" value="1" class="btn btn-sm btn-default">
                Comment
            </button>
          @endif
        </div>
      </form>
    </div><br><br>

    @include('global.rerviewer_table', ['reviewers' =>
        $data->reviewers()->push($data->approver())
    ])
  </div>

  <div style="width: 1024px; margin: auto;background: white; min-height: 1300px;">
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
              <h1>លិខិតលាឈប់ពីការងារ(Resignation Form)</h1>
            </div>
            <br>
            <div class="body">
              <table>
                <tr>
                  <td style="width: 50%">
                    ខ្ញុំបាទ/នាងខ្ញុំឈ្មោះ..................
                  </td>
                  <td>
                    ភេទ..............
                    <span>កាតបុគ្គលិកលេខ.....................</span>
                  </td>
                </tr>
                <tr>
                  <td style="width: 50%">
                    មានមុខងារជា..................
                  </td>
                  <td>
                    នាយកដ្ឋាន/សាខា........................
                  </td>
                </tr>
                <tr>
                  <td style="width: 50%">
                    ថ្ងៃចូលបំរើការងារ..................
                  </td>
                  <td>
                    ហានិភ័យកម្ចី(%)ក្នុង៣០ថ្ងៃ........................
                    <span>ទឹកប្រាក់សកម្ម.....................</span>
                  </td>
                </tr>
              </table>

              <h1 class="header">សូមគោរពជូន</h1>
              @if(@$data->approver()->position_level == config('app.position_level_president'))
                  <h1 class="header">{{ @$data->forcompany->approver }}</h1>
              @else
                  <h1 class="header">{{ $approver->position_name }}</h1>
              @endif
              <p>
                តាមរ៖.......................................................
                តួនាទីៈ.......................................................................................................
              </p>
              <p>
                ខ្ញុំបាទ/នាងខ្ញុំ ស្នើសុំលាឈប់ពីតួនាទីជា............................................................ពី
                {{ @$data->forcompany->name }}។
              </p>
              <p>
                មូលហេតុ.......................................................................................................................................................................
              </p>
              <p>
                អនុញ្ញាតិឱ្យឈប់ជាផ្លូវការនៅថ្ងៃទី៖.......................................................................................................................... ..........
              </p>
              <p style="text-indent: 50px;">
                អាស្រ័យដូចបានជម្រាបជូន និង មូលហេតុខាងលើ សូមប្រធាននាយិកាប្រតិបត្តិ សម្រេច និងអនុញ្ញាតដោយក្តីអនុគ្រោះ ផង។
              </p>
              <p style="text-indent: 50px;">
                សូមប្រធាននាយិកាប្រតិបត្តិ មេត្តាទទួលនូវការគោរពដ៍ខ្ពង់ខ្ពស់អំពីខ្ញុំបាទ/នាងខ្ញុំ។
              </p>
            </div>

            <div class="sign">
              <div class="row">
                <div class="col-xs-6 signature">
                  <span>
                    ធ្វើនៅការិយាល័យកណ្តាល,
                    ថ្ងៃទី​{{ khmer_number($data->created_at->format('d')) }}
                    ខែ {{ khmer_month($data->created_at->format('m')) }}
                    ឆ្នាំ{{ khmer_number($data->created_at->format('Y')) }}
                  </span><br>
                  <span>ហត្ថលេខា និងឈ្មោះបុគ្គលិក</span><br>
                  <span>
                    <img style="height: 70px;"
                         src="{{ asset('/'.$data->requester->signature) }}"
                         alt="Signature">
                  </span><br>
                  <span>{{ $data->requester->name }}</span>
                </div>

                <?php
                  $reviewers = $data->reviewers();
                  $approver = $data->approver();
                  $k = 0;
                  //dd($reviewers[0]);
                  $approve = config('app.approve_status_approve')
                ?>

                @for ($i = 0; $i < count($reviewers); $i++)
                  <div class="col-xs-6 signature">
                    @if($reviewers[$i]->approve_status == $approve)
                      <span>
                        ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewers[$i]->approved_at))->format('d')) }}
                        ខែ {{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($reviewers[$i]->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewers[$i]->approved_at))->format('Y')) }}
                      </span><br>

                      <span>បានឃើញ និងបញ្ជូនបន្ដ</span><br>
                      <span>{{ $reviewers[$i]->position_name }}</span><br>
                      <span>
                        <img style="height: 70px;"
                             src="{{ asset('/'.$reviewers[$i]->signature) }}"
                             alt="Signature">
                      </span><br>
                      <span>{{ $reviewers[$i]->name }}</span><br>

                    @else
                      <span>កាលបរិច្ឆេទៈ.................................................................................</span>
                      <span>បានឃើញ និងបញ្ជូនបន្ដ</span><br>
                      <span>{{ $reviewers[$i]->position_name }}:.........................................</span><br>
                      <br><br><br><br>

                    @endif
                  </div>
                @endfor

                @if(count($reviewers) == 1 || count($reviewers) == 3 || count($reviewers) == 5 || count($reviewers) == 7)
                  <div class="col-xs-6 signature"></div>
                @endif

                <div class="col-xs-6 signature">
                  @if($approver->approve_status == $approve)
                    <span>
                      ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d')) }}
                      ខែ {{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('m')) }}
                      ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('Y')) }}
                    </span><br>
                    <span>អនុម័តដោយ</span><br>
                    @if(@$data->approver()->position_level == config('app.position_level_president'))
                        <span>លោកស្រី{{ @$data->forcompany->approver }}</span><br>
                    @else
                        <span>{{ $approver->position_name }}: {{ $approver->name }}</span><br>
                    @endif
                    <span>
                      <img style="height: 70px;"
                           src="{{ asset('/'.$data->approver()->signature) }}"
                           alt="Signature">
                    </span><br>
                    <span> {{ $approver->name }} </span><br>

                  @else
                    <span>កាលបរិច្ឆេទៈ.................................................................................</span>
                    <span>អនុម័តដោយ</span><br>
                    @if(@$data->approver()->position_level == config('app.position_level_president'))
                        <span>លោកស្រី{{ @$data->forcompany->approver }}:.........................................</span><br>
                    @else
                        <span>{{ $approver->position_name }}:.........................................</span><br>
                    @endif
                    <br><br><br><br>
                  @endif
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
    <div style="width: 1024px; margin: auto; text-align: center;">
      {!! $data->forcompany->footer_section  !!}
    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('hr_request.reject', $data->id)])
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
                  url: "{{ action('HRRequestController@approve', $data->id) }}",
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

</script>
@include('global.sweet_alert')
</html>
