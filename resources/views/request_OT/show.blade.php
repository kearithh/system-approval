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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    {{--<link href="/bootstrap3-wysihtml5.min.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
          font-family: 'Times New Roman','Khmer OS Content';
          font-weight: 400;
          font-size: 15px !important;
          line-height: normal !important;
        }

        h1 {
            font-family: 'Khmer OS Muol Light';
            font-size: 15px;
            margin: 7px 0 18px 0 !important;
        }

        p, span, b {
            font-family: 'Khmer OS Content' !important;
            /*font-size: 15px !important;*/
            margin: 1px 0 7px 0 !important;
        }

        i{
            font-family: 'Khmer OS Content' !important;
            font-size: 13px !important;
            margin: 1px 0 5px 0 !important;
        }

        .header{
          text-align: center;
        }

        .signature {
            padding-top: 50px;
            font-size: 14px !important;
        }

        .signature > div {
            float: left;
            width: 33.33%;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box
        }

        .related {
            float: left;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box;
            text-overflow: ellipsis;
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

          .pagebreak {page-break-before:always}

          button, #border_new_page {
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
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            vertical-align: middle;
            padding-left: 0.25rem;
            padding-right: 0.25rem;
            font-size: 15px;
            height: 40px;
        }

        .body{
          text-align: justify;
        }

        .contain{
          padding-left: 20px;
          padding-right: 20px;
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
        $data->reviewers()->merge($data->reviewers_short())->push($data->approver())
    ])
  </div>

  <div style="width: 1024px; margin: auto;background: white; min-height: 1355px;">
    <table style="margin-left: 50px; margin-right: 50px; width: 924px !important;">
      <thead>
        <tr>
          <td>
            <div class="page-header-space"></div>
          </td>
        </tr>
      </thead>

      <tbody style="border-style: solid; border-width: 3px;">
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

                <br>
                <img src="{{ asset($forcompany->logo) }}" alt="logo" style="height: 90px">
                <br><br>
                <h1>ទម្រង់ស្នើរសុំធ្វើការងារបន្ថែមម៉ោង</h1>
            </div>
            <div class="body">
                <p>
                    ឈ្មោះ
                      @if($data->staff_name)
                        {{$data->staff_name}} &ensp;
                      @else
                        {{$data->staff}} &ensp;
                      @endif
                    តួនាទី៖ {{$data->position_name}} &ensp;
                    អត្ដលេខការងារ៖ {{$data->staff_code}} &ensp;
                    ផ្នែក/នាយកដ្ឋាន៖
                    @if($data->department_name)
                      {{$data->department_name}}
                    @else
                      ..........................
                    @endif
                </p>
                <p>
                    ស្នើសុំចាប់ពីថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->start_date))->format('d')) }}
                    ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($data->start_date))->format('m')) }}
                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->start_date))->format('Y')) }} &ensp;
                    ដល់ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->end_date))->format('d')) }}
                    ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($data->end_date))->format('m')) }}
                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->end_date))->format('Y')) }} &ensp;
                    ចំនួន
                    @if($data->total)
                      {{$data->total}}ម៉ោង
                    @endif
                    @if($data->total && $data->total_minute)
                      និង
                    @endif
                    @if($data->total_minute)
                      {{$data->total_minute}}នាទី
                    @endif
                    (ចាប់ពីម៉ោង {{(\Carbon\Carbon::createFromTimestamp(strtotime($data->start_time))->format('h:i A'))}}
                    ដល់ម៉ោង {{(\Carbon\Carbon::createFromTimestamp(strtotime($data->end_time))->format('h:i A'))}})
                </p>
                <p>
                    <span>មូលហេតុ៖ {{$data->reason}}</span>
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
            </div>

            <?php
              $relatedCol = count($data->reviewers());
              $allCol = $relatedCol + 2;
            ?>
            <div class="signature">
                <div style="width: {{ (100/$allCol).'%' }}">
                    <p>
                        ថ្ងៃទី{{ khmer_number($data->created_at->format('d')) }}
                        ខែ{{ khmer_number($data->created_at->format('m')) }}
                        ឆ្នំា{{ khmer_number($data->created_at->format('Y')) }}
                    </p>

                    <p>ស្នើសុំដោយ៖</p>
                    <p>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</p>
                    <img style="height: 60px;"
                         src="{{ asset('/'.$data->requester()->signature) }}"
                         alt="Signature">
                    <p>{{ @$data->creator_object->name ?: $data->requester()->name }}</p>
                </div>

                <div style="width: {{ (($relatedCol*100)/$allCol).'%' }}">
                    @foreach($data->reviewers() as $item)
                        @if ($item->approve_status == config('app.approve_status_approve'))
                            <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                <p>
                                    ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('d')) }}
                                    ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('m')) }}
                                    ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('Y')) }}
                                </p>
                                <p>ពិនិត្យ និងបញ្ជាក់ដោយ៖</p>
                                <p>{{ @json_decode(@$item->user_object)->position_name ?: $item->position_name }}</p>
                                <img style="height: 60px;"
                                     src="{{ asset('/'.$item->signature) }}"
                                     alt="Signature">
                                <p>{{ @json_decode(@$item->user_object)->name ?: $item->name }}</p>
                            </div>
                        @else
                            <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                <p>ថ្ងៃទី.....ខែ......ឆ្នំា.....</p>
                                <p>ពិនិត្យ និងបញ្ជាក់ដោយ៖</p>
                                <p>{{ @json_decode(@$item->user_object)->position_name ?: $item->position_name }}</p>
                            </div>
                        @endif
                    @endforeach

                </div>

                <div style="width: {{ (100/$allCol).'%' }}">

                  @if ($data->approver()->position_level == config('app.position_level_president'))

                    @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                        <p>
                            ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                            ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('m')) }}
                            ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}
                        </p>
                        <p>អនុម័តដោយ៖</p>
                        <p>{{ $data->forcompany->approver }}</p>
                        <img style="height: 60px;"
                             src="{{ asset('/'.$data->approver()->signature) }}"
                             alt="Signature">
                        <p>{{ @json_decode(@$data->approver()->user_object)->name ?: $data->approver()->name }}</p>
                    @else
                        <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                        <p>អនុម័តដោយ៖</p>
                        <p>{{ $data->forcompany->approver }}</p>
                    @endif

                  @else

                    @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                        <p>
                            ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                            ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('m')) }}
                            ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}
                        </p>
                        <p>អនុម័តដោយ៖</p>
                        <p>{{ @json_decode(@$data->approver()->user_object)->position_name ?: $data->approver()->position_name }}</p>
                        <img style="height: 60px;"
                             src="{{ asset('/'.$data->approver()->signature) }}"
                             alt="Signature">
                        <p>
                            {{ @check_nickname(@$data->approver()->position_level, $data->created_at ) }}
                            {{ @json_decode(@$data->approver()->user_object)->name ?: $data->approver()->name }}
                        </p>
                    @else
                        <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                        <p>អនុម័តដោយ៖</p>
                        <p>{{ @json_decode(@$data->approver()->user_object)->position_name ?: $data->approver()->position_name }}</p>
                    @endif

                  @endif

                </div>
            </div>

          </td>
        </tr>
        <tr>
          <td class="contain">
            <b><i>បញ្ជាក់ៈ ការស្នើសុំធ្វើការបន្ថែមម៉ោងទាំងអស់ត្រូវមានការអនុម័តជាមុនទើបការស្នើសុំមានប្រសិទ្ឋិភាព។</i></b>
            <br><br>
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
      {!! $forcompany->footer_section  !!}
    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('request_ot.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('request_ot.disable', $data->id)])

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
                  url: "{{ action('RequestOTController@approve', $data->id) }}",
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

  $( "#note_btn" ).on( "click", function( event ) {
    event.preventDefault();
    $('#note_modal').modal('show');
  });

  $('.datepicker').datepicker({
      format: 'dd-mm-yyyy',
      todayHighlight:true,
      autoclose: true
  });

</script>
@include('global.sweet_alert')
</html>
