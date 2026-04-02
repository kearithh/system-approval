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
          font-size: 14px;
          line-height: normal !important;
        }

        h1 {
            font-family: 'Khmer OS Muol Light';
            font-size: 14px;
            margin: 7px 0 10px 0 !important;
        }

        p, span, b {
            font-family: 'Khmer OS Content' !important;
            font-size: 14px !important;
            margin: 1px 0 7px 0 !important;
        }

        .header{
          text-align: center;
        }

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
          height: 70px;
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

          .table-bordered td, .table-bordered th {
              border: 1px solid #1D1D1D !important;
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

        table.table td, table.table th {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            vertical-align: middle;
            padding-left: 0.25rem;
            padding-right: 0.25rem;
            font-size: 14px;
        }

        .body{
          text-align: justify;
        }

        .contain{
          padding-left: 60px;
          padding-right: 60px;
          width: 880px;
        }

        .col {
          padding-top: 15px;
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
          @if(!can_approve_reject($data, config('app.reschedule_loan')))
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
                <img src="{{ asset($data->forcompany->logo) }}" alt="logo" style="height: 90px">
                <br><br>
                <h1>ប្រធានសាខាៈ {{ $data->forbranch->name_km }}</h1>
                <h1>សូមគោរពជូន</h1>
                <h1>
                  @if(@$data->approver()->position_level == config('app.position_level_president'))
                      លោកស្រី{{ @$data->forcompany->approver }}
                  @else
                      លោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                  @endif
                  នៃ{{ @$data->forcompany->long_name }}
                </h1>
            </div>
            <div class="body">

                <table class="mb-0">

                    <tr>
                        <td style="width: 90px; vertical-align: top">
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
                        <td style="width: 90px; vertical-align: top">
                            <h1>កម្មវត្ថុៈ</h1>
                        </td>
                        <td style="vertical-align: top">
                            <p>{{ $data->purpose }}</p>
                        </td>
                    </tr>

                    <!-- <tr>
                        <td colspan="2">
                            <span style="font-family: 'Khmer OS Muol Light' !important">មូលហេតុៈ</span>
                            <span>{{$data->reason}}</span>
                        </td>
                    </tr> -->

                    <tr>
                        <td colspan="2">

                            <table class="table table-bordered text-center">
                              <tr class="table-success">
                                  <td colspan="8" style="font-family: 'Khmer OS Muol Light'; text-align: center;">
                                      ព័ត៌មានត្រឹមត្រូវ
                                  </td>
                              </tr>
                              <tr class="table-active">
                                  <th style="min-width: 100px;">ឈ្មោះអតិថិជន</th>
                                  <th style="min-width: 120px;">លេខគណនីកម្ចី</th>
                                  <th style="min-width: 100px;">ប្រាក់ដើម(៛)</th>
                                  <th style="min-width: 90px;">ការប្រាក់(%)</th>
                                  <th style="min-width: 100px;">រយះពេលខ្ចី</th>
                                  <th style="min-width: 110px;">សេវារដ្ឋបាល(%)</th>
                                  <th style="min-width: 110px;">អត្រាពិន័យ(%)</th>
                                  <th>របៀបសងរំលោះ</th>
                              </tr>

                              <tr>
                                  <td>{{json_decode($data->new_info)->name}}</td>
                                  <td>{{json_decode($data->new_info)->account}}</td>
                                  <td>{{json_decode($data->new_info)->balance}}៛</td>
                                  <td>{{json_decode($data->new_info)->interest}}%</td>
                                  <td>
                                    {{json_decode($data->new_info)->term}}
                                    @if((@json_decode(@$data->new_info)->type_term) == 2)
                                      សប្តាហ៍
                                    @else
                                      ខែ
                                    @endif
                                  </td>
                                  <td>{{json_decode($data->new_info)->services}}%</td>
                                  <td>
                                    @if(@json_decode($data->new_info)->penalty == null)
                                      N/A
                                    @else
                                      {{json_decode($data->new_info)->penalty}}%
                                    @endif
                                  </td>
                                  <td>{{json_decode($data->new_info)->type}}</td>
                              </tr>

                              <tr class="table-warning">
                                  <td colspan="8" style="font-family: 'Khmer OS Muol Light'; text-align: center;">
                                      ចំណុចខុស
                                  </td>
                              </tr>

                              <tr style="height: 35px;">
                                  <td>{{json_decode($data->old_info)->name}}</td>
                                  <td>{{json_decode($data->old_info)->account}}</td>
                                  <td>{{json_decode($data->old_info)->balance}}</td>
                                  <td>
                                    @if(json_decode($data->old_info)->interest == null)
                                      {{json_decode($data->old_info)->interest}}
                                    @else
                                      {{json_decode($data->old_info)->interest}}%
                                    @endif
                                  </td>
                                  <td>
                                    @if(@json_decode(@$data->old_info)->term)
                                      {{json_decode($data->old_info)->term}}
                                      @if((@json_decode(@$data->old_info)->type_term) == 2)
                                        សប្តាហ៍
                                      @else
                                        ខែ
                                      @endif
                                    @endif
                                  </td>
                                  <td>
                                    @if(json_decode($data->old_info)->services == null)
                                      {{json_decode($data->old_info)->services}}
                                    @else
                                      {{json_decode($data->old_info)->services}}%
                                    @endif
                                  </td>
                                  <td>
                                    @if(@json_decode($data->old_info)->penalty == null)
                                      N/A
                                    @else
                                      {{json_decode($data->old_info)->penalty}}%
                                    @endif
                                  </td>
                                  <td>{{json_decode($data->old_info)->type}}</td>
                              </tr>
                          </table>

                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <p>&emsp;&emsp;&emsp;
                                តបតាមកម្មវត្ថុ និងមូលហេតុខាងលើ ខ្ញុំបាទស្នើសុំការអនុញ្ញតិពី
                                @if(@$data->approver()->position_level == config('app.position_level_president'))
                                    លោកស្រី{{ @$data->forcompany->approver }}
                                @else
                                    លោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                                @endif
                                ដើម្បីកែតម្រូវតារាងកាលវិភាគសងប្រាក់របស់អតិថិជនខាងលើ។
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <p>&emsp;&emsp;&emsp;
                                អាស្រ័យដូចបានជម្រាបជូនខាងលើ
                                @if(@$data->approver()->position_level == config('app.position_level_president'))
                                    សូមលោកស្រី{{ @$data->forcompany->approver }}
                                @else
                                    សូមលោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                                @endif
                                មេត្តាពិនិត្យ និងអនុញ្ញាតិតាមការគួរ។
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <p>&emsp;&emsp;&emsp;
                                @if(@$data->approver()->position_level == config('app.position_level_president'))
                                    សូមលោកស្រី{{ @$data->forcompany->approver }}
                                @else
                                    សូមលោក{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}
                                @endif
                                មេត្តាទទួលនូវសេចក្តីគោរពដ៏ខ្ពង់ខ្ពស់ពី{{prifixGender($data->requester()->gender)}}។
                            </p>
                        </td>
                    </tr>

                </table>

            </div>

            <div class="sign">
              @include('reschedule_loan.partials.approve_section')
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

  @include('global.comment_modal', ['route' =>route('reschedule_loan.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('reschedule_loan.disable', $data->id)])

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
                  url: "{{ action('RescheduleLoanController@approve', $data->id) }}",
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
