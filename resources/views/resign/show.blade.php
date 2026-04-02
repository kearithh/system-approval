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
        }

        p {
          font-family: 'Times New Roman','Khmer OS Content';
          font-size: 16px;
          margin: 0 0 2px;
        }

        .header{
          text-align: center;
          text-decoration-line: underline;
          text-decoration-style: double;
        }

        .signature{
          padding: 10px 0 0 20px;
          font-size: 15px;
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
          height: 20px;
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
          padding-left: 60px;
          padding-right: 60px;
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
          @if(!can_approve_reject($data, config('app.type_resign')))
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
        $data->reviewers()->merge($data->reviewer_shorts()->push($data->approver()))
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
              @if($data->types == 1)
                <h1>លិខិតលាឈប់ពីការងារ(Resignation Form)</h1>
              @elseif($data->types == 2)
                <h1>ទម្រង់ស្នើអនុញ្ញាតលាឈប់ពីការងារ</h1>
              @elseif($data->types == 3)
                <h1>លិខិតអនុញ្ញាតឱ្យឈប់ជាផ្លូវការ</h1>
              @endif
            </div>
            <div class="body">
              <p>
                {{ prifixGender($data->gender) }}ឈ្មោះ(Name) {{ $user->where('id', $data->staff_id)->first()->name }} 
                &emsp;
                ភេទ(Sex)៖ 
                  @if($data->gender == 'M')
                    ប្រុស
                  @else
                    ស្រី
                  @endif
                &emsp;
                កាតបុគ្គលិកលេខ(Employee ID)៖ {{ $data->card_id }}
              </p>
              <p>
                មានតួនាទី / មុខងារ(Position)៖ {{ $position->where('id', $data->position)->first()->name_km }}
                &emsp;
                @if(@$branch->where('id', $data->branch)->first()->branch == 1)
                  សាខា(Head Office/Branch)៖ {{ @$branch->where('id', $data->branch)->first()->name_km }}
                @else
                  នាយកដ្ឋាន(Department)៖ {{ @$department->where('id', $data->department)->first()->name_km }}
                @endif
              </p>
              <p>
                ថ្ងែចូលបម្រើការងារ(Joining Date)៖ {{ (\Carbon\Carbon::createFromTimestamp(strtotime($data->doe))->format('d/m/Y')) }}។
              </p>
              @if(@$data->is_contract)
                <table>
                  <tr>
                    <td>
                      <input disabled type="checkbox" @if(@$data->is_contract == 1) checked @endif >
                      <span>ចប់កុងត្រា(Ended Contract)</span>
                    </td>
                    <td>
                      <input disabled type="checkbox" @if(@$data->is_contract == 3) checked @endif >
                      <span>សាកល្បងការងារ</span>
                      <input disabled type="checkbox" @if(@$data->is_contract == 2) checked @endif >
                      <span>មិនទាន់ចប់កុងត្រា</span>
                      
                      <spen>
                        យល់ព្រមសងសំណងតាមកិច្ចសន្យាចំនួន(Not 
			end contract and agree to pay follow the contract)៖
                        {{ @$data->is_contract == 2 ? @$data->contract : ".........." }} Month.
                      </spen>
                    </td>
                  </tr>
                </table>
              @endif
              <div class="header">
                <h1>សូមគោរពជូន(To)</h1>
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
              <p>
                តាមរយៈ(Refer to)៖ {{ @$data->reviewers()->first()->name }}
                &emsp;
                តួនាទី(Position) {{ $data->reviewers()->first()->position_name }}
              </p>
              <p>
                {{ prifixGender($data->gender) }}
                ស្នើរសុំលាឈប់ពីតួនាទី(I request to resign from my position){{ $position->where('id', $data->position)->first()->name_km }}
                &emsp;
                ពី(from) {{ $data->forcompany->long_name }}។
              </p>
              <p>
                មូលហេតុ(Reason) {{ $data->reason }}។
              </p>
              <p> 
                &emsp; &emsp;
                អាស្រ័យដូចបានជំរាបជូន និងមូលហេតុខាងលើ
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
                មេត្តាសម្រេច និងអនុញ្ញាតដោយក្តីអនុគ្រោះផង។(As mentioned above, please kindly decide and allow)
              </p>
              <p> 
                &emsp; &emsp;
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

                មេត្តាទទួលនូវសេចក្ដីគោរពដ៏ខ្ពង់ខ្ពស់ពី{{ prifixGender($data->gender) }}។(Please, ladies and gentlemen, receive  the highest respect from me.)
              
                <span>
                  @foreach($data->reviewer_shorts() as $key => $value)
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

            <div class="sign">
              <div class="row">
                <div class="col-xs-6 signature">
                  <span>
                    <img style="height: 70px;"
                         src="{{ asset('/'.$data->requester->signature) }}"
                         alt="Signature">
                  </span><br>
                  <span>ស្នើរដោយ(Submitted by): {{ @$data->creator_object->name ?: $data->requester->name }}</span><br>
                  <span>
                    កាលបរិច្ឆេទ(Date):
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
                      <span>បានឃើញ និងបញ្ជាក់ដោយ(Seen and confirmed by name)</span><br>
                      <span>
                        {{ @json_decode($reviewers[$i]->user_object)->position_name ?: $reviewers[$i]->position_name }}:
                        {{ @json_decode($reviewers[$i]->user_object)->name ?: $reviewers[$i]->name }}
                      </span><br>
                      <span>
                        កាលបរិច្ឆេទ(Date):
                        {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewers[$i]->approved_at))->format('d/m/Y')) }}
                      </span>
                    @else
                      <br><br><br><br>
                      <span>បានឃើញ និងបញ្ជាក់ដោយ(Seen and confirmed by name)</span><br>
                      <span>
                        {{ @json_decode($reviewers[$i]->user_object)->position_name ?: $reviewers[$i]->position_name }}:
                        .........................................
                      </span><br>
                      <span>កាលបរិច្ឆេទ(Date): .................................................................................</span>
                    @endif
                  </div>
                @endfor

                @if (count($reviewers) == 1 || count($reviewers) == 3 || count($reviewers) == 5 || count($reviewers) == 7)
                  <div class="col-xs-6 signature"></div>
                @endif

                <div class="col-xs-6 signature">
                  @if ($approver->approve_status == $approve)
                    <span>
                      <img style="height: 70px;"
                           src="{{ asset('/'.$data->approver()->signature) }}"
                           alt="Signature">
                    </span><br>
                    <span>អនុម័តដោយ(Approved by name)</span><br>
                    @if (@$data->approver()->position_level == config('app.position_level_president'))
                        <span>លោកស្រី{{ @$data->forcompany->approver }}: {{ $approver->name }}</span><br>
                    @else
                        <span>
                          {{ @json_decode($approver->user_object)->position_name ?: $approver->position_name }}:
                          {{ @json_decode($approver->user_object)->name ?: $approver->name }}
                        </span><br>
                    @endif
                    <span>
                      កាលបរិច្ឆេទ(Date):
                      {{ (\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('d/m/Y')) }}
                    </span>
                  @else
                    <br><br><br><br>
                    <span>អនុម័តដោយ(Approved by name)</span><br>
                    @if (@$data->approver()->position_level == config('app.position_level_president'))
                        <span>លោកស្រី{{ @$data->forcompany->approver }}: .........................................</span><br>
                    @else
                        <span>{{ @json_decode($approver->user_object)->position_name ?: $approver->position_name }}:
                        .........................................</span><br>
                    @endif
                    <span>កាលបរិច្ឆេទ(Date): .................................................................................</span>
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
    <div style="width: 1024px; margin: auto; text-align: center; background:white;">
      <b>
        <i>បញ្ជាក់ៈ ទម្រង់ស្នើរសុំលាឈប់គ្រាន់តែជាទម្រង់ជូនដំណឹងក្នុងការលាលែងពីការងារមិនមែនជាលិខិតបញ្ជាក់ពីការកំណត់ថ្ងៃចុងក្រោយបំពេញការងាររបស់
        <br>
        សមុីខ្លួននោះទេ។ សាមុីបុគ្គលិកអាចឈប់បានលុះត្រាតែមានលិខិតអនុញ្ញាតបញ្ជាក់ថ្ងៃចុងក្រោយដោយប្រធានក្រុមប្រឹក្សាភិបាល ឬអ្នកមានសិទ្ធិ។</i>
        <br><br>
        <i>Note: The resignation form is only a resignation notice, not a last day for the employee. Employee may resign only with the latest deadline <br><br>letter from the Chairman of the Board or an authorized person.</i>
      </b>

      {!! $forcompany->footer_section  !!}

    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('resign.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('resign.disable', $data->id)])

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
                  url: "{{ action('ResignController@approve', $data->id) }}",
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
