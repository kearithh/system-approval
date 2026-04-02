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
          font-size: 14px;
          line-height: normal !important;
        }

        strong {
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-size: 14px;
          font-weight: 400;
        }

        h1{
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-weight: 400;
          font-size: 14px;
        }

        .header{
          text-align: center;
          text-decoration-line: underline;
          text-decoration-style: double;
        }


        .signature{
          padding: 14px 0 0 0;
          font-size: 14px !important;
        }

        .footer img{
          width: 1200px !important;
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

        p {
            margin: 5px !important;
        }
        h2{
          margin-block-start: 17px;
          font-size: 14px !important;
          line-height: normal;
        }

        th{
          text-align: center;
        }


        div.action_btn {
          display: none;
          margin-top: 5px;
          position: fixed;
          box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        div.action_btn a {
          padding: 10px 14px;
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
          margin: 0;
          size: A4 landscape;
        }

        @media print {
          thead {
            display: table-header-group;
          }
          tfoot {
            display: table-footer-group;
          }

          button, .file, .black-list {
            display: none;
          }

          .page-footer {
            height: auto;
          }

          .table-bordered td, .table-bordered th {
            border: 1px solid #1D1D1D !important;
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

        .content {
            padding-left: 40px;
            padding-right: 40px;
        }

        .table > thead > tr > th, .table > tbody > tr > td {
          padding-left: 2px !important;
          padding-right: 2px !important;
          /*padding-top: 20px !important;
          padding-bottom: 20px !important;*/
          vertical-align: middle;
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

  <div id="action_container" style="width: 1200px; margin: auto;background: white;">
    <div id="action_button">
        <div class="btn-group" role="group" aria-label="">
            <button id="back" type="button" class="btn btn-sm btn-secondary">
               Back
            </button>
        </div>
        @include('global.next_pre')
        <div class="btn-group" role="group" aria-label="">
            <button type="button" onclick="window.print()" class="btn btn-sm btn-warning">
                Print
            </button>
        </div>
        <form style="margin: 0; display: inline-block " action="">
            <div class="btn-group" role="group" aria-label="">
                @if(!can_approve_reject($data, config('app.type_damaged_log')))
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

    @include('global.rerviewer_table', ['reviewers' => [$data->approver()]
    ])
  </div>

  <div style="width: 1200px; margin: auto;background: white; min-height: 800px;">
    <div class="row logo text-center" style="padding-top: 50px">
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
            <img src="{{ asset($forcompany->logo) }}" alt="logo" style="height: 90px">
        </div>
    </div>
    <div>
        <div class="title text-center">
            <br>
            <h1>ការអនុម័តទៅលើការរើសអ្នកត្រួតពិនិត្យ និងអនុម័តដោយស្វ័យប្រវត្ត</h1>
            <br>
        </div>

        <div class="row content">
              <div class="col-sm-12">
                  <table class="table table-bordered text-center">
                    <thead class="table-info">
                        <tr class="bgcol">
                            <th>ក្រុមហ៊ុន</th>
                            <th>នាយកដ្ឋាន</th>
                            <th>សំណើ / របាយការណ៍</th>
                            <th>ប្រភេទ</th>
                            <th>គោលការណ៍</th>
                            <th>អ្នកត្រួតពិនិត្យ</th>
                            <th>អ្នកត្រួតពិនិត្យ (ហត្ថលេខាតូច)</th>
                            <th>អ្នកអនុម័ត</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>{{ $data->company->name }}</p>
                            </td>
                            <td>
                                <p>{{ $data->department->name_km }}</p>
                            </td>
                            <td>
                                <p>{{ $data->type }}</p>
                            </td>
                            @if(@$data->type == 'report')
                                <td>
                                    {{ $data->type_report }}
                                </td>
                                <td>
                                    N/A
                                </td>
                            @else 
                                <td>
                                    {{ @$request_type->where('id', @$data->type_request)->first()->name }}
                                </td>
                                <td>
                                    @if($data->category == 1)
                                        ក្នុង
                                    @else
                                        ក្រៅ
                                     @endif
                                </td>
                            @endif
                            <td>
                              <?php $reviewers = @\App\SettingReviewerApprover::reviewerName(@$data->reviewers) ?>
                              @if(@$reviewers)
                                @foreach($reviewers as $key => $value)
                                    <p>{{@$value->name}}</p>
                                @endforeach
                              @endif
                            </td>
                            <td>
                              <?php $reviewers_short = @\App\SettingReviewerApprover::reviewerShortName(@$data->reviewers_short) ?>
                              @if(@$reviewers_short)
                                @foreach($reviewers_short as $key => $value)
                                    <p>{{@$value->name}}</p>
                                @endforeach
                              @endif
                            </td>
                            <td>
                                <p>{{ $data->approverName->name }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-12">
                <div class="rows text-center signature">
                    <div class="col-sm-3 col-sm-offset-9">
                        <?php
                            $approver = $data->approver();
                            $approve = config('app.approve_status_approve')
                        ?>
                        @if(@$approver->approve_status == @$approve)
                            <span>
                                ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('d')) }} 
                                ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('m')) }}
                                ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('Y')) }}
                            </span><br>
                            <span>អនុម័តដោយ</span><br>
                            @if($approver->position_level == config('app.position_level_president'))
                                <span>{{ $data->forcompany->approver }}</span><br>
                            @else
                                <span>{{ $approver->position_name }}</span><br>
                            @endif
                            <span><img style="height: 60px" src="{{ asset('/'.$approver->signature) }}" alt="Signature"></span><br>
                            <span>{{ $approver->name }}</span>
                        @else
                            <span>ថ្ងៃទី..... ខែ..... ឆ្នំា.....</span><br>
                            <span>អនុម័តដោយ</span><br>
                            @if(@$approver->position_level == config('app.position_level_president'))
                                <span>{{ $data->forcompany->approver }}</span><br>
                            @else
                                <span>{{ @$approver->position_name }}</span><br>
                            @endif
                            <p>&nbsp;</p>
                            <p>{{ @$approver->name }}</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>
  </div>
  <div class="page-footer">
    <div style="width: 1200px; margin: auto; text-align: center;">
      <img src="{{ asset($forcompany->footer_landscape) }}" alt="logo_footer" style="width: 1200px; background: white">
    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('setting-reviewer-approver.reject', $data->id)])

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
                  url: "{{ action('SettingController@approve', $data->id) }}",
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
