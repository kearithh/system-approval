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
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
          font-family: 'Times New Roman','Khmer OS Content';
          font-weight: 400;
          font-size: 16px;
          line-height: normal !important;
        }

        h1 {
            font-family: 'Khmer OS Muol Light';
            font-size: 16px;
            margin: 8px 0 1px 0 !important;
        }

        p, span, b {
            font-family: 'Khmer OS Content' !important;
           /* font-size: 15px !important;*/
            margin: 3px 0 1px 0 !important;
        }

        .header{
          text-align: center;
        }

        .signature{
          /*padding: 15px 0 0 0;*/
          font-size: 14px !important;
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
          text-align: center;
          padding-top: 0;
          margin-bottom: 20px;
        }

        .page-footer, .page-footer-space {
          height: 70px;
        }

        .page-header, .page-header-space {
          height: 35px;
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

          .border td, .border th {
              border: 1px solid #1D1D1D !important;
          }

          table tr .table-secondary {
            background-color: #d6d8db !important;
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

        .border td, .border th {
            border: 1px solid #1D1D1D !important;
        }

        table.table td, table.table th {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            vertical-align: middle;
            padding-left: 0.25rem;
            padding-right: 0.25rem;
            font-size: 15px;
        }

        .col{
            padding-right: 0;
            padding-left: 0;
        }

        .contain{
          padding-left: 70px;
          padding-right: 70px;
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
        @include('global.next_pre')

        <div class="btn-group" role="group" aria-label="">
        <button type="button" onclick="window.print()" class="btn btn-sm btn-warning">
            Print
        </button>
      </div>

      <form style="margin: 0; display: inline-block " action="">
        <div class="btn-group" role="group" aria-label="">
          @if(!can_approve_reject($data, config('app.type_request_disable_user')))
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

    @include('request_disable_user.rerviewer_table', ['reviewers' =>
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
                <h1>ឧបសម្ព័ន្ធ</h1>
                <h1>ទម្រង់បិទ/លុប និងកែប្រែអ្នកប្រើប្រាស់ប្រព័ន្ធ</h1>
                <br>
            </div>
            <div class="body">

                <table class="table table-bordered border">
                    {{--
                        <tr>
                            <td class="table-secondary" style="width: 18%">
                                <p> ឈ្មោះអ្នកស្នើរសុំ ៖ </p>
                            </td>
                            <td style="width: 32%">
                                <p> {{ @$data->request_object->name_kh }} ({{ @$data->request_object->name_en }})</p>
                            </td>
                            <td class="table-secondary" style="width: 18%">
                                <p> ភេទ ៖ </p>
                            </td>
                            <td style="width: 32%">
                                <p> {{ @genderKhmer(@$data->request_object->gender) }} </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-secondary">
                                <p> នាយកដ្ឋាន ៖ </p>
                            </td>
                            <td>
                                <p> {{ @$department->where('id', @$data->request_object->department)->first()->name_km }} </p>
                            </td>
                            <td class="table-secondary">
                                <p> ការិយាល័យ / សាខា ៖ </p>
                            </td>
                            <td>
                                <p> {{ @$branch->where('id', @$data->request_object->branch)->first()->name_km }} </p>
                            </td>
                        </tr>
                    --}}

                    <?php
                        $is_branch = @$branch->where('id', @$data->request_object->branch)->first();
                    ?>

                    <tr>
                        <td class="table-secondary" style="width: 18%">
                            <p> ឈ្មោះអ្នកស្នើរសុំ ៖ </p>
                        </td>
                        <td style="width: 32%">
                            <p> {{ @$data->request_object->name_kh }} ({{ @$data->request_object->name_en }})</p>
                        </td>
                        <td class="table-secondary" style="width: 18%">
                            <p> នាយកដ្ឋាន / សាខា ៖ </p>
                        </td>
                        <td style="width: 32%">
                            @if(@$is_branch->branch == 1)
                                <p> {{ @$is_branch->name_km }} </p>
                            @else
                                <p> {{ @$department->where('id', @$data->request_object->department)->first()->name_km }} </p>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td class="table-secondary">
                            <p> មុខតំណែង ៖ </p>
                        </td>
                        <td>
                            <p> {{ @$position->where('id', @$data->request_object->position)->first()->name_km }} </p>
                        </td>
                        <td class="table-secondary">
                            <p> កាលបរិច្ឆេទស្នើសុំ ៖ </p>
                        </td>
                        <td>
                            <p> {{ (\Carbon\Carbon::createFromTimestamp(strtotime($data->created_at))->format('d/m/Y')) }} </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="table-secondary">
                            <p> ប្រភេទនៃការស្នើសុំ ៖ </p>
                        </td>
                        @if(@$data->company_id == 6) <!-- mmi -->
                            <td colspan="3">
                                <?php $items = @$data->types ?>
                                @foreach(config('app.types_request_user_mmi') as $key => $value)
                                    <input disabled type="checkbox" @if(in_array($value, @$items)) checked @endif >
                                    <span>{{ $value }}</span>
                                    &emsp; &emsp;&emsp;
                                @endforeach
                            </td>
                        @elseif(@$data->company_id == 1 || @$data->company_id == 2 || @$data->company_id == 3 || @$data->company_id == 14) <!-- stsk and skp -->
                            <td colspan="3">
                                <?php $items = @$data->types ?>
                                @foreach(config('app.types_request_user_skp') as $key => $value)
                                    <input disabled type="checkbox" @if(in_array($value, @$items)) checked @endif >
                                    <span>{{ $value }}</span>
                                    &emsp; &emsp;&emsp;
                                @endforeach
                            </td>
                        @else
                            <td colspan="3">
                                <?php $items = @$data->types ?>
                                @foreach(config('app.types_request_user') as $key => $value)
                                    <input disabled type="checkbox" @if(in_array($value, @$items)) checked @endif >
                                    <span>{{ $value }}</span>
                                    &emsp; &emsp;&emsp;
                                @endforeach
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td class="table-secondary">
                            <p> គោលបំណង ៖ </p>
                        </td>
                        <td colspan="3">
                            <p> {{ @$data->purpose }} </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="table-secondary">
                            <u><h1> ការបរិយាយ </h1></u>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="height: 100px;">
                           <p>{!! @$data->description !!}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="table-secondary">
                            <u><h1> មូលហេតុនៃការស្នើសុំ </h1></u>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="height: 80px;">
                            &emsp;&emsp;&emsp;
                            @foreach(config('app.types_disable_user') as $key => $value)
                                <!-- <input disabled type="checkbox" @if(@in_array($key, @$data->type_reason)) checked @endif > -->
                                @if(@in_array($key, @$data->type_reason))
                                    [&nbsp;✓&nbsp;]
                                @else 
                                    [&emsp;]
                                @endif
                                <span>{{ $value }}</span>
                                &emsp;&emsp;&emsp;&emsp;
                            @endforeach
                            <br>
                            <p class="text-justify" style="padding: 0 50px 0 50px">{{ @$data->reason }}</p>
                        </td>
                    </tr>
                </table>

                <?php 
                    $reviewer = $data->reviewer(); 
                    $verify = $data->verify(); 
                    $approver = $data->approver(); 
                ?>

                <table class="table table-bordered text-center border">
                    <tr>
                        <td style="width: 25%" class="table-secondary">
                            <p> ឈ្មោះអ្នកស្នើរសុំ </p>
                        </td>
                        <td style="width: 25%" class="table-secondary">
                            <p> ហត្ថលេខា </p>
                        </td>
                        <td style="width: 25%" class="table-secondary">
                            <p> អ្នកត្រួតពិនិត្យ </p>
                        </td>
                        <td style="width: 25%" class="table-secondary">
                            <p> ហត្ថលេខា </p>
                        </td>
                    </tr>
                    <tr style="height: 80px;">
                        <td style="width: 25%">
                            <p> {{ @$data->creator_object->name }} </p>
                            <p> {{ @$data->creator_object->position_name }} </p>
                        </td>
                        <td style="width: 25%">
                            <img style="height: 60px;"
                                 src="{{ asset('/'.@$data->creator_object->signature) }}"
                                 alt="Signature">
                            <br>
                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime($data->created_at))->format('d/m/Y')) }}
                        </td>

                        @if(@$reviewer->approve_status == config('app.approve_status_approve'))
                            <td style="width: 25%">
                                <p>
                                    {{ json_decode(@$reviewer->user_object)->name }} 
                                </p>
                                <p> {{ json_decode(@$reviewer->user_object)->position_name }} </p>
                            </td>
                            <td style="width: 25%">
                                <img style="height: 60px;"
                                     src="{{ asset('/'.json_decode(@$reviewer->user_object)->signature) }}"
                                     alt="Signature">
                                <br>
                                ({{ (\Carbon\Carbon::createFromTimestamp(strtotime(@$reviewer->approved_at))->format('d/m/Y')) }})
                            </td>
                        @else
                            <td style="width: 25%">
                                <p> </p>
                            </td>
                            <td style="width: 25%">
                                <p> </p>
                            </td>
                        @endif

                    </tr>
                </table>

                <!-- mmi or (mfi and date submit > 01-06-2021) -->
                @if(@$data->company_id == 6 || (@$data->company_id == 2 && @$data->created_at > '2021-06-01')) 

                    <table class="table table-bordered text-center border">
                        <tr>
                            <td style="width: 67%" class="table-secondary">
                                <u><h1> សេចក្តីបញ្ជាក់ពីផ្នែកជំនាញនៃនាយកដ្ឋានព័ត៌មានវិទ្យា </h1></u>
                            </td>
                            <td style="width: 33%" class="table-secondary">
                                <p> ហត្ថលេខា និងឈ្មោះ </p>
                            </td>
                        </tr>
                        <tr style="height: 100px;">
                            <td style="width: 67%">
                                <p> </p>
                            </td>
                            <td style="width: 33%">
                                <p> </p>
                            </td>
                        </tr>
                    </table>

                    <table class="table table-bordered text-center border">
                        <tr>
                            <td style="width: 33%" class="table-secondary">
                                <p> ការអនុម័ត </p>
                            </td>
                            <td style="width: 34%" class="table-secondary">
                                <p> នាយកដ្ឋាន </p>
                            </td>
                            <td style="width: 33%" class="table-secondary">
                                <p> ហត្ថលេខា និងឈ្មោះ </p>
                            </td>
                        </tr>
                        @if(
                            @$verify->approve_status == config('app.approve_status_approve') && 
                            @$approver->approve_status == config('app.approve_status_approve')
                        )
                            <tr style="height: 100px;">
                                <td style="width: 33%">
                                    <p>បានត្រួតពិនិត្យ និងចាំបាច់សម្រាប់ប្រើប្រាស់</p>
                                </td>
                                <td style="width: 34%">
                                    <p>ព័ត៌មានវិទ្យា</p>
                                </td>
                                <td style="width: 33%">
                                    <img style="height: 60px;"
                                         src="{{ asset('/'.json_decode(@$verify->user_object)->signature) }}"
                                         alt="Signature"><br>
                                    <span>
                                        {{ json_decode(@$verify->user_object)->name }}
                                        ({{ (\Carbon\Carbon::createFromTimestamp(strtotime(@$verify->approved_at))->format('d/m/Y')) }})
                                    </span>
                                </td>
                            </tr>
                        @else
                            <tr style="height: 100px;">
                                <td style="width: 33%">
                                    <p> </p>
                                </td>
                                <td style="width: 34%">
                                    <p> </p>
                                </td>
                                <td style="width: 33%">
                                    <p> </p>
                                </td>
                            </tr>
                        @endif
                    </table>

                    @if(@$approver->approve_status == config('app.approve_status_approve'))
                        <span style="margin-right: 0; float: right; margin-top: -10px !important;">
                            <img style="width: 30px;"
                                 src="{{ asset('/'.json_decode(@$approver->user_object)->short_signature) }}"
                                 alt="short_sign"
                                 title="{{ json_decode(@$approver->user_object)->name }}">
                        </span>
                    @endif

                @else 

                    <table class="table table-bordered text-center border">
                        <tr>
                            <td style="width: 67%" class="table-secondary">
                                <u><h1> សេចក្តីបញ្ជាក់ពីផ្នែកជំនាញនៃនាយកដ្ឋានព័ត៌មានវិទ្យា </h1></u>
                            </td>
                            <td style="width: 33%" class="table-secondary">
                                <p> ហត្ថលេខា និងឈ្មោះ </p>
                            </td>
                        </tr>
                        @if(@$verify->approve_status == config('app.approve_status_approve'))
                            <tr style="height: 100px;">
                                <td style="width: 67%">
                                    {{-- <p> {{ @$verify->approve_comment }} </p> --}}
                                    <p>បានត្រួតពិនិត្យ និងចាំបាច់សម្រាប់ប្រើប្រាស់</p>
                                </td>
                                <td style="width: 33%">
                                    <img style="height: 60px;"
                                         src="{{ asset('/'.json_decode(@$verify->user_object)->signature) }}"
                                         alt="Signature"><br>
                                    <span>
                                        {{ json_decode(@$verify->user_object)->name }}
                                        ({{ (\Carbon\Carbon::createFromTimestamp(strtotime(@$verify->approved_at))->format('d/m/Y')) }})
                                    </span>
                                </td>
                            </tr>
                        @else
                            <tr style="height: 100px;">
                                <td style="width: 67%">
                                    <p> </p>
                                </td>
                                <td style="width: 33%">
                                    <p> </p>
                                </td>
                            </tr>
                        @endif
                    </table>

                    <table class="table table-bordered text-center border">
                        <tr>
                            <td style="width: 33%" class="table-secondary">
                                <p> ការអនុម័ត </p>
                            </td>
                            <td style="width: 34%" class="table-secondary">
                                <p> តួនាទី </p>
                            </td>
                            <td style="width: 33%" class="table-secondary">
                                <p> ហត្ថលេខា និងឈ្មោះ </p>
                            </td>
                        </tr>
                        @if(@$approver->approve_status == config('app.approve_status_approve'))
                            <tr style="height: 100px;">
                                <td style="width: 33%">
                                    {{-- <p>{{ @$approver->approve_comment }}</p> --}}
                                    <p>ឯកភាព</p>
                                </td>
                                <td style="width: 34%">
                                    @if(@$data->company_id == 2) <!--  MFI -->
                                        <p>ប្រធាននាយកដ្ឋានព័ត៌មានវិទ្យា</p>
                                    @else
                                        <p> {{ json_decode(@$approver->user_object)->position_name }} </p>
                                    @endif
                                </td>
                                <td style="width: 33%">
                                    <img style="height: 60px;"
                                         src="{{ asset('/'.json_decode(@$approver->user_object)->signature) }}"
                                         alt="Signature"><br>
                                    <span>
                                        {{ json_decode(@$approver->user_object)->name }}
                                        ({{ (\Carbon\Carbon::createFromTimestamp(strtotime(@$verify->approved_at))->format('d/m/Y')) }})
                                    </span>
                                </td>
                            </tr>
                        @else
                            <tr style="height: 100px;">
                                <td style="width: 33%">
                                    <p> </p>
                                </td>
                                <td style="width: 34%">
                                    <p> </p>
                                </td>
                                <td style="width: 33%">
                                    <p> </p>
                                </td>
                            </tr>
                        @endif
                    </table>

                @endif

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
      {!! $forcompany->footer_section  !!}
    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('request_disable_user.reject', $data->id)])
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
              $.ajax({
                  type: "POST",
                  url: "{{ action('RequestDisableUserController@approve', $data->id) }}",
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
