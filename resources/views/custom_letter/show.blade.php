<!DOCTYPE html>
<html>
<head>
    <title>E-Approval</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
          font-size: 16px;
          line-height: normal !important;
        }

        strong {
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-size: 16px;
          font-weight: 400;
        }

        h1{
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-weight: 400;
          font-size: 16px;
          margin: 15px;
        }

        p {
          font-family: 'Times New Roman','Khmer OS Content';
          font-size: 16px;
          margin: 0 0 5px;
        }

        .header{
          text-align: center;
          /*text-decoration-line: underline;
          text-decoration-style: double;*/
        }

        .title_desc {
          font-family: 'Times New Roman','Khmer OS Muol Light';
        }

        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100% !important;
        }

        table.table td, table.table th {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            vertical-align: middle;
            padding-left: .25rem;
            padding-right: .25rem;
        }

        table.table_paragrap tr td {
          padding-top: 0.7rem;
          padding-bottom: 0.7rem;
          vertical-align: top;
        }

        .footer_paragrap {
          padding-top: 0.7rem;
          padding-bottom: 0.7rem;
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

        @page {
          size: A4;
          margin: 0 !important;
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
          padding-left: 70px;
          padding-right: 70px;
          width: 880px;
        }

        .row {
          display: -webkit-box;
          display: -webkit-flex;
          display: -ms-flexbox;
          display: flex;
          flex-wrap: wrap;
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
          @if(!can_approve_reject($data, config('app.custom_letter')))
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
        $data->reviewers_short()->merge($data->reviewers())->push($data->approver())
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
              <br>
              <h1>សូមគោរពជូន</h1>
              <h1>
                  @if(@$data->approver()->position_level == config('app.position_level_president'))
                      លោកស្រី{{@$data->forcompany->approver}}
                  @else
                      @if (@$data->approver()->gender == 'M')
                        លោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                      @elseif (@$data->approver()->gender == 'F')
                        លោកស្រី{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                      @else
                        {{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                      @endif
                  @endif
              </h1>
            </div>
            <div class="body">
              <table class="table_paragrap">
                <tr>
                  <td style="width: 70px;">
                    <span class="title_desc">កម្មវត្ថុ៖</span>
                  </td>
                  <td>
                    {{ @$data->purpose }}
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="title_desc">យោង៖</span>
                  </td>
                  <td style="text-align: left !important; font-size: 16px !important;" >
                    {!! @$data->reference !!}
                  </td>
                </tr>
                <tr>
                  <td colspan="2" class="text-justify">
                    <p>
                      &emsp; &emsp; &emsp;
                      {!! @$data->description !!}
                    </p>
                    <p class="footer_paragrap"> 
                      &emsp; &emsp; &emsp;
                      @if (@$data->approver()->position_level == config('app.position_level_president'))
                          សូមលោកស្រី{{ @$data->forcompany->approver }}
                      @else
                          @if (@$data->approver()->gender == 'M')
                            សូមលោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                          @elseif (@$data->approver()->gender == 'F')
                            សូមលោកស្រី{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                          @else
                            សូម{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                          @endif
                      @endif
                      ពិនិត្យ និងសម្រេចដោយក្តីអនុគ្រោះ។
                    </p>
                    <p> 
                      &emsp; &emsp; &emsp;
                      @if(@$data->approver()->position_level == config('app.position_level_president'))
                          សូមលោកស្រី{{@$data->forcompany->approver}}
                      @else
                          @if (@$data->approver()->gender == 'M')
                            សូមលោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                          @elseif (@$data->approver()->gender == 'F')
                            សូមលោកស្រី{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                          @else
                            សូម{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                          @endif
                      @endif 
                      ទទួលនូវការគោរពដ៏ស្មោះស្ម័គ្រអំពី{{ prifixGender($data->gender) }}។
                    
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
                  </td>
                </tr>
              </table>
            </div>

            @include('custom_letter.partials.approve_section')

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
    <div style="width: 1024px; margin: auto; text-align: center; background:white;">

      {!! $forcompany->footer_section  !!}

    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('custom_letter.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('custom_letter.disable', $data->id)])

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
                  url: "{{ action('CustomLetterController@approve', $data->id) }}",
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
