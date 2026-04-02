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
          font-size: 15px;
          line-height: normal !important;
        }

        h1 {
            font-family: 'Khmer OS Muol Light';
            font-size: 15px;
            margin: 5px 0 10px 0 !important;
        }

        p, span, b {
            font-family: 'Khmer OS Content' !important;
            font-size: 15px !important;
            margin: 0 0 0 0 !important;
        }

        .header{
          text-align: center;
        }

        .sign{
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
          height: 50px;
        }

        .page-footer {
          /*position: fixed;*/
          bottom: 0;
          width: 100%;
        }

        .footer img{
            margin-bottom: 0 !important;
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

          .table-bordered td, .table-bordered th {
              border: 1px solid #1D1D1D !important;
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

        table.table td, table.table th {
            padding-top: 0rem;
            padding-bottom: 0rem;
            vertical-align: middle;
            padding-left: 0.25rem;
            padding-right: 0.25rem;
            font-size: 15px;
        }

        .contain{
          padding-left: 90px;
          padding-right: 90px;
          width: 880px;
        }

        .row {
          display: -webkit-box;
          display: -webkit-flex;
          display: -ms-flexbox;
          display: flex;
          flex-wrap: wrap;
        }
        /*.row > [class*='col-'] {
          display: flex;
          flex-direction: column;
        }
*/
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
          @if(!can_approve_reject($data, config('app.type_request_ot')))
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
        $data->reviewers()->push($data->approver())
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

            <div class="header">
                <img src="/img/logo/logo_association.png" alt="logo" style="height: 120px">
                <h1>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</h1>
                <h1>សូមគោរពជូន</h1>
                <h1>ប្រធានសមាគមសុវត្ថិភាពសហគមន៍</h1>
            </div>
            <div class="body">
                <table  class="table table-borderless mb-0">
                    <tr>
                        <td style="width: 50px; vertical-align: top">
                            <h1>តាមរយៈ</h1>
                        </td>
                        <td class="text-left" style="vertical-align: top">
                            @foreach($data->reviewers() as $reviewer)
                                <p class="mb-0">
                                    {{ @json_decode(@$reviewer->user_object)->position_name ?: $reviewer->position_name }}
                                </p>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50px; vertical-align: top">
                            <h1>កម្មវត្ថុៈ</h1>
                        </td>
                        <td style="vertical-align: top">
                            <p>{{ $data->purpose }}</p>
                            <p>&emsp;&emsp;&emsp;{!! $data->description !!}</p>
                            <p>{!! $data->verify !!}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p>
                                អាស្រ័យដូចបានជំរាបជូនខាងលើ សូមលោកស្រីប្រធាន សមាគមសុវត្ថិភាពសហគមន៍ មេត្តាពិនិត្យ និងអនុញ្ញាតិតាមការគួរ។ 
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p>
                                សូមលោកស្រីប្រធាន មេត្តាទទួលនូវសេចក្ដីគោរពដ៏ខ្ពង់ខ្ពស់អំពីខ្ញុំបាទ៕
                            </p>
                        </td>
                    </tr>
                </table>

            </div>

            @include('association.partials.approve_section')
            
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

  @include('global.comment_modal', ['route' =>route('association.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('association.disable', $data->id)])

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
                  url: "{{ action('AssociationController@approve', $data->id) }}",
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
