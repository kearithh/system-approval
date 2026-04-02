<!DOCTYPE html>
<html>
<head>
    <title>E-Approval</title>
    <meta charset="UTF-8">
    @if(! config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

        @include('adminlte::plugins', ['type' => 'css'])

        @yield('adminlte_css_pre')

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

        @yield('adminlte_css')
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
    {{--<link href="/bootstrap3-wysihtml5.min.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
          font-family: 'Times New Roman','Khmer OS Content';
          font-weight: 400;
          font-size: 12px;
          line-height: normal !important;
        }

        h1 {
            font-family: 'Khmer OS Muol Light';
            font-size: 13px;
            margin: 7px 0 7px 0 !important;
        }

        p, span, b {
            font-family: 'Khmer OS Content' !important;
            font-size: 12px !important;
            margin: 2px !important;
        }

        .sign .row .col .text-center p{
          font-size: 12px !important;
        }

        .header{
          text-align: center;
        }

        /*.body{
          text-align: justify;
        }*/


        .signature{
          padding: 15px 0 0 0;
        }

        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100% !important;
        }

        tr{
          vertical-align: top;
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
          height: 40px;
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

          button {
            display: none;
          }

          .table-bordered td{
              border: 1px solid #1D1D1D !important;
          }

          .table-bordered th {
              border: 1px solid #1D1D1D !important;
              background-color: #86cfda !important;
          }

          .page-footer {
            height: auto;
          }

          body {
            margin: 0;
            -webkit-print-color-adjust:exact;
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

        table.table td, table.table th {
            padding: 0.2rem;
            vertical-align: middle;
            font-size: 12px;
            height: 25px;
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

  <div id="action_container" style="width: 29.7cm; margin: auto;background: white;">
    <div id="action">
      <div class="btn-group" role="group" aria-label="">
          <button id="back" type="button" class="btn btn-sm btn-secondary" title="Back to list">
              Back
          </button>
      </div>
      <div class="btn-group" role="group" aria-label="">
          <button type="button" onclick="window.print()" class="btn btn-sm btn-warning" title="Print/Export PDF">
              Print
          </button>
      </div>

      <form style="margin: 0; display: inline-block " action="">
        <div class="btn-group" role="group" aria-label="">
          @if(!can_approve_reject($data, config('app.transfer_asset')))
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
        $data->reviewers()->merge($data->reviewers_short())->push($data->approver())
    ])
  </div>

  <div style="width: 29.7cm; margin: auto;background: white; min-height: 18cm;">
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

              <img src="{{ asset($forcompany->logo) }}" alt="logo" style="height: 70px">

              <h1>សំណើសុំផ្ទេរសម្ភារៈ </h1>
            </div>
            <div class="body">
                <br>
                <table class="table table-bordered text-center">
                    <thead class="table-info">
                        <tr>
                            <th style="min-width: 30px;">ល.រ</th>
                            <th style="min-width: 100px">ឈ្មោះសម្ភារៈ</th>
                            <th style="min-width: 100px">ឈ្មោះ</th>
                            <th style="min-width: 100px">តួនាទី</th>
                            <th style="min-width: 100px">ការបរិយាយ</th>
                            <th style="min-width: 100px">ផ្ទេរពីស្ថាប័ន</th>
                            <th style="min-width: 100px">ផ្ទេរទៅស្ថាប័នថ្មី</th>
                            <th style="min-width: 100px">ផ្សេងៗ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach($data->items as $key => $item)
                            <tr class="finance_section">
                                <td>{{ $i++ }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->staff }}</td>
                                <td>{{ $item->position }}</td>
                                <td>{{ $item->detail }}</td>
                                <td>
                                    {{ @$company->where('id', $item->from)->first()->long_name }}
                                </td>
                                <td>
                                    {{ @$company->where('id', $item->to)->first()->long_name }}
                                </td>
                                <td>{{ $item->other }}</td>
                            </tr>
                        @endforeach

                        @for($i = 0; (5 - count($data->items)) > $i; $i++)
                            <tr>
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
                    </tbody>
                </table>

                <span style="float: right !important;">
                  @foreach($data->reviewers_short() as $key => $value)
                    @if ($value->approve_status == config('app.approve_status_approve'))
                      <img  src="{{ asset($value->short_signature) }}"  
                            alt="short_sign" 
                            title="{{ @$value->name }}" 
                            style="width: 25px;">
                    @endif
                  @endforeach
                </span>
            </div>

            <div class="sign">
              @include('transfer_asset.partials.approve_section')
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
    <div style="width: 29.7cm; margin: auto; text-align: center;">
      <img src="{{ asset($forcompany->footer_landscape) }}" alt="logo" style="width: 29.7cm; background: white">
    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('transfer_asset.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('transfer_asset.disable', $data->id)])

</body>
@if(! config('adminlte.enabled_laravel_mix'))
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery.inputmask.bundle.min.js') }}"></script>
    @include('adminlte::plugins', ['type' => 'js'])
    <scrypt src="/bootstrap3-wysihtml5.min.js"></scrypt>

    @yield('adminlte_js')
@else
    {{--<script src="{{ asset('js/app.js') }}"></script>--}}
@endif
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
                  url: "{{ action('TransferAssetController@approve', $data->id) }}",
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
