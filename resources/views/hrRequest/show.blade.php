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

        p {
          display: block;
          margin-block-start: 0;
          margin-block-end: 0;
          margin-inline-start: 0;
          margin-inline-end: 0;
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
          @if(!can_approve_reject($data, config('app.type_hr_request')))
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
              @if($data->types == 0)
                <h1>សំណើសុំតម្លើងប្រាក់ម៉ោងបុគ្គលិក</h1>
              @elseif($data->types == 1)
                <h1>សំណើសុំតែងតាំងបុគ្គលិក</h1>
              @elseif($data->types == 2)
                <h1>សំណើសុំផ្លាស់ប្ដូរសាខាបុគ្គលិក</h1>
              @elseif($data->types == 3)
                <h1> សំណើសុំបុគ្គលិក | NEW HIRED STAFF REQUEST </h1>
              @elseif($data->types == 4)
                <h1>សំណើសុំផ្លាស់ប្ដូរតួនាទីបុគ្គលិក</h1>
              @elseif($data->types == 5)
                <h1>សំណើសុំតម្លើងប្រាក់បៀវត្សរ៍បុគ្គលិក</h1>
              @elseif($data->types == 6)
                <h1>សំណើសុំផ្លាស់ប្ដូរម៉ោងការងារបុគ្គលិក</h1>
              @endif
            </div>
            <br>
            <div class="body">
              <table>
                <tr>
                  <td style="width: 50%">
                    {{ prifixGender($data->requester->gender) }}ឈ្មោះ | Name {{ $data->requester->name }}-{{ $data->requester->name_en }}
                  </td>
                  <td>
                    មានតួនាទីជា | Position:
                    <span>{{ $position->where('id', $data->requester->position_id)->first()->name_km }}</span>
                  </td>
                </tr>
                <tr>
                  <td>
                    {{$data->title}}ឈ្មោះ | Name
                    <span>{{ $user->where('id', $data->staff_id)->first()->name }}-{{ $user->where('id', $data->staff_id)->first()->name_en }}</span>
                  </td>
                  <td>
                    @if($data->types == 0)
                      ថ្ងៃចូលបម្រើការងារ | Date of entry {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->doe))->format('d')) }}
                      ខែ | Month {{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($data->doe))->format('m')) }}
                      ឆ្នាំ | Years {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->doe))->format('Y')) }}
                    @endif
                  </td>
                </tr>

                @if($data->types == 3)
                  <tr>
                    <td>
                      ដែលមានតួនាទី​ | Position:
                      <span>{{ @$position->where('id', $data->old_position)->first()->name_km }}</span>
                    </td>
                    <td>
                      <!-- នាយកដ្ឋាន/សាខាៈ
                      <span>{{ @$department->where('id', $data->old_department)->first()->name_km }}</span>
                      @if($data->old_department != "" && $data->old_branch != "")
                        /
                      @endif
                      <span>{{ @$branch->where('id', $data->old_branch)->first()->name_km }}</span> -->

                      @if($data->old_branch > 1)
                        សាខា​ | Branch: {{ @$branch->where('id', $data->old_branch)->first()->name_km }}
                      @else
                        <!-- នាយកដ្ឋាន/សាខាៈ -->
                        <span>{{ @$department->where('id', $data->old_department)->first()->name_km }}</span>
                        @if($data->old_department != "" && $data->old_branch != "")
                          /
                        @endif
                        <span>{{ @$branch->where('id', $data->old_branch)->first()->name_km }}</span>
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      ប្រាក់បៀរវត្សរ៍គោល | Base Salary:
                      <span>{{ $data->old_salary }}</span>
                    </td>
                  </tr>
                  
                @elseif($data->types == 6)
          
                  <tr>
                    <td>
                      ដែលមានតួនាទីបច្ចុប្បន្ន | With current role: 
                      <span>{{ @$position->where('id', $data->old_position)->first()->name_km }}</span>
                    </td>
                    <td>
                      @if($data->old_branch > 1)
                        សាខា | Branch {{ @$branch->where('id', $data->old_branch)->first()->name_km }}
                      @else
                        នាយកដ្ឋាន/សាខា | Department/Branch:
                        <span>{{ @$department->where('id', $data->old_department)->first()->name_km }}</span>
                        @if($data->old_department != "" && $data->old_branch != "")
                          /
                        @endif
                        <span>{{ @$branch->where('id', $data->old_branch)->first()->name_km }}</span>
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <td>
                        ម៉ោងការងារចាស់ | Old working hours:
                      <span>{{ $data->old_timetable}}</span>
                    </td>
                    <td>
                        ម៉ោងការងារថ្មី | New working hours:
                      <span>{{ $data->new_timetable }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                        ថ្ងៃធ្វើការ | Working days:
                      <span>{{ $data->working_day }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                        ប្រាក់បៀវត្សរ៍គោលបច្ចុប្បន្ន | Current base salary: 
                      <span>{{ $data->old_salary }}</span>
                    </td>
                    <td>
                        ប្រាក់បៀវត្សរ៍ថ្មី | New salary:
                      <span>{{ $data->new_salary }}</span>
                    </td>
                  </tr>
                @else
                  <tr>
                    <td>
                      ដែលមានតួនាទីបច្ចុប្បន្ន | With current Position:
                      <span>{{ $position->where('id', $data->old_position)->first()->name_km }}</span>
                    </td>
                    <td>
                      @if($data->old_branch > 1)
                        សាខា | Branch: {{ @$branch->where('id', $data->old_branch)->first()->name_km }}
                      @else
                        <!-- នាយកដ្ឋាន/សាខាៈ -->
                        <span>{{ @$department->where('id', $data->old_department)->first()->name_km }}</span>
                        @if($data->old_department != "" && $data->old_branch != "")
                          /
                        @endif
                        <span>{{ @$branch->where('id', $data->old_branch)->first()->name_km }}</span>
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <td>
                      តួនាទីថ្មី | New Position
                      <span>{{ @$position->where('id', $data->new_position)->first()->name_km }}</span>
                    </td>
                    <td>
                      <!-- នាយកដ្ឋាន/សាខាៈ
                      <span>{{ @$department->where('id', $data->new_department)->first()->name_km }}</span>
                      @if($data->new_department != "" && $data->new_branch != "")
                        /
                      @endif
                      <span>{{ @$branch->where('id', $data->new_branch)->first()->name_km }}</span> -->

                      @if($data->new_branch > 1)
                        សាខា | Branch {{ @$branch->where('id', $data->new_branch)->first()->name_km }}
                      @else
                        <!-- នាយកដ្ឋាន/សាខាៈ -->
                        <span>{{ @$department->where('id', $data->new_department)->first()->name_km }}</span>
                        @if($data->new_department != "" && $data->new_branch != "")
                          /
                        @endif
                        <span>{{ @$branch->where('id', $data->new_branch)->first()->name_km }}</span>
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <td>
                      @if($data->types == 0)
                        ប្រាក់ម៉ោងបច្ចុប្បន្ន | Current Hours:
                      @else
                        ប្រាក់បៀវត្សរ៍គោលបច្ចុប្បន្ន | Current base salary
                      @endif
                      <span>{{ $data->old_salary }}</span>
                    </td>
                    <td>
                      @if($data->types == 0)
                        ប្រាក់ម៉ោងថ្មី | New hourly pay:
                      @else
                        ប្រាក់បៀវត្សរ៍ថ្មី | New salary:
                      @endif
                      <span>{{ $data->new_salary }}</span>
                    </td>
                  </tr>
                @endif

                @if($data->types == 0)
                  <tr>
                    <td colspan="2">
                      ទឹកប្រាក់ម៉ោងត្រូវបានតម្លើង | Hourly rate increased
                      {{ $data->increase }}
                    </td>
                  </tr>
                @endif

                <tr>
                  <td colspan="2">
                    មានប្រសិទ្ធភាពចាប់ពីថ្ងៃទី | Effective from {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->effective_date))->format('d')) }}
                    ខែ | Month {{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($data->effective_date))->format('m')) }}
                    ឆ្នាំ | Years {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->effective_date))->format('Y')) }}
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    @if($data->types == 1)
                      មូលហេតុនៃការស្នើសុំតែងតាំងបុគ្គលិក | Reason for requesting staff appointment:
                    @elseif($data->types == 2)
                      មូលហេតុនៃការស្នើសុំផ្លាស់ប្តូរបុគ្គលិក | Reasons for requesting a change of staff:
                    @elseif($data->types == 3)
                      មូលហេតុនៃការសំណើសុំបុគ្គលិក | Reasons for requesting staff: 
                    @elseif($data->types == 6)
                      មូលហេតុនៃការសំណើសុំផ្លាស់ប្ដូរម៉ោងការងារបុគ្គលិក | Reason for requesting to change staff working hours:
                    @else
                      មូលហេតុ | Reason
                    @endif
                    <span>
                      {!! $data->reason !!}
                    </span>
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
                  </td>
                </tr>
              </table>
            </div>

            <div class="sign">
              <div class="row">
                <div class="col-xs-6 signature">
                  <span>
                    <img style="height: 70px;"
                         src="{{ asset('/'.$data->requester->signature) }}"
                         alt="Signature">
                  </span><br>
                  <span>ស្នើរដោយ​ |Requested by {{ @$data->creator_object->name ?: $data->requester->name }}-{{ $data->requester->name_en }}</span><br>
                  <span>
                    កាលបរិច្ឆេទ | Date:
                    {{ (\Carbon\Carbon::createFromTimestamp(strtotime($data->created_at))->format('d/m/Y')) }}
                  </span>
                </div>

                <?php
                  $reviewers = $data->reviewers();
                  $approver = $data->approver();
                  $k = 0;
                  $approve = config('app.approve_status_approve')
                ?>

                @for ($i = 0; $i < count($reviewers); $i++)
                  <div class="col-xs-6 signature">
                    @if($reviewers[$i]->approve_status == $approve)
                      <div style="height:70px;max-width: 150px;vertical-align: middle;text-align: center;">
                          <img style="max-height:100%; max-width:100%"
                               src="{{ asset('/'.$reviewers[$i]->signature) }}"
                               alt="Signature">
                      </div>
                      <span>បានឃើញ និងបញ្ជាក់ដោយ | Seen and Verified By </span><br>
                      <span>
                        {{ @json_decode($reviewers[$i]->user_object)->position_name ?: $reviewers[$i]->position_name }}: 
                        {{ @json_decode($reviewers[$i]->user_object)->name ?: $reviewers[$i]->name }}
                      </span><br>
                      <span>
                        កាលបរិច្ឆេទ | Date:
                        {{(\Carbon\Carbon::createFromTimestamp(strtotime($reviewers[$i]->approved_at))->format('d/m/Y'))}}
                      </span>
                    @else
                      <br><br><br><br>
                      <span>បានឃើញ និងបញ្ជាក់ដោយ | Seen and Verified By</span><br>
                      <span>
                        {{ @json_decode($reviewers[$i]->user_object)->position_name ?: $reviewers[$i]->position_name }}:
                        .........................................
                      </span><br>
                      <span>កាលបរិច្ឆេទ | Date: .................................................................................</span>
                    @endif
                  </div>
                @endfor

                @if(count($reviewers) == 1 || count($reviewers) == 3 || count($reviewers) == 5 || count($reviewers) == 7)
                  <div class="col-xs-6 signature"></div>
                @endif

                <div class="col-xs-6 signature">
                  @if($approver->approve_status == $approve)
                    <span>
                      <img style="height: 70px;"
                           src="{{ asset('/'.$data->approver()->signature) }}"
                           alt="Signature">
                    </span><br>
                    <span>អនុម័តដោយ | Approved By</span><br>
                    @if(@$data->approver()->position_level == config('app.position_level_president'))
                        <span>
                          លោកស្រី{{ @$data->forcompany->approver }}: 
                          {{ @check_nickname($data->approver()->position_level, $data->created_at ) }}
                          {{ @json_decode($approver->user_object)->name ?: $approver->name }}
                        </span><br>
                    @else
                        <span>
                          {{ @json_decode($approver->user_object)->position_name ?: $approver->position_name }}: 
                          {{ @json_decode($approver->user_object)->name ?: $approver->name }}
                        </span><br>
                    @endif
                    <span>
                      កាលបរិច្ឆេទៈ
                      {{(\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('d/m/Y'))}}
                    </span>
                  @else
                    <br><br><br><br>
                    <span>អនុម័តដោយ | Approved By</span><br>
                    @if(@$data->approver()->position_level == config('app.position_level_president'))
                        <span>លោកស្រី{{ @$data->forcompany->approver }}:.........................................</span><br>
                    @else
                        <span>
                          {{ @json_decode($approver->user_object)->position_name ?: $approver->position_name }}:
                          .........................................
                        </span><br>
                    @endif
                    <span>កាលបរិច្ឆេទ | Date: .................................................................................</span>
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

      {!! $forcompany->footer_section  !!}

    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('hr_request.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('hr_request.disable', $data->id)])

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
