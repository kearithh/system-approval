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

        p {
          font-family: 'Times New Roman','Khmer OS Content';
          font-size: 15px;
          margin: 0;
        }

        .header{
          text-align: center;
          text-decoration-line: underline;
          text-decoration-style: double;
        }

        .signature{
          padding: 5px 0 0 2px;
          font-size: 14px;
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
          padding: 1px;
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
          height: 40px;
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
          padding-left: 70px;
          padding-right: 70px;
          width: 880px;
        }

        #highlights{
          background-color: #FFFF00;
          color: black;
         /* padding: 5px;*/
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

    <div id="reviewer" style="padding: 15px; margin-bottom: 5px">
      <table class="table table-bordered">
          <span>តារាងអ្នកត្រួតពិនិត្យ និងអនុម័ត<br>Matrix approval chart</span>
          <tr>
              <th style="min-width: 30px; text-align: center">ល.រ<br>No</th>
              <th style="min-width: 150px;">ឈ្មោះ<br>Name</th>
              <th style="min-width: 270px;">មុខដំណែង<br>Position</th>
              <th style="min-width: 70px;">ព្រម<br>Approved</th>
              <th style="min-width: 70px;">មិនព្រម<br>Disapproved</th>
              <th colspan="2" class="text-center" style="max-width: 420px;">មតិយោបល់<br>Comment</th>
              <th style="width: 100px;" class="text-center">ថ្ងៃអនុម័ត<br>Approval Date</th>
          </tr>
          <?php 
            $j = 1; 
            $reviewers = $data->reviewers()->merge($data->reviewer_shorts())->push($data->approver());
          ?>
          @foreach($reviewers as $key => $value)
              @if($value)
              <tr>
                  <td style="text-align: center">{{ $j++ }}</td>
                  <td>
                      @if (is_string(@$value->position) && strpos(@$value->position, 'short') !== false)
                          <button class="btn btn-xs btn-primary tooltipsign" style="margin-right: 2px; margin-bottom: 2px"
                                  title="Short Signature" data-toggle="tooltip"
                                  data-placement="top" type="button">
                          {{ @$value->name }}
                          </button>
                      @else
                          {{ @$value->name ? @$value->name : @$value->user_name }}
                      @endif
                  </td>
                  <td>{{ $value->position_name }}</td>
                  <td class="text-center">
                      <input disabled type="checkbox" @if ($value->approve_status == config('app.approve_status_approve')) checked @endif>
                  </td>
                  <td class="text-center">
                      <input disabled type="checkbox" 
                          @if ($value->approve_status == config('app.approve_status_reject') || $value->approve_status == config('app.approve_status_disable')) 
                              checked 
                          @endif
                      >
                  </td>
                  @if (@$value->approve_comment)
                      <td>{{ $value->approve_comment }}</td>
                  @else
                      <td>N/A</td>
                  @endif
                  <td>
                      @if (@$value->comment_attach)
                          <a href="{{ asset('/'.@$value->comment_attach) }}" target="_self">
                              <img src="{{ asset('/'.@$value->comment_attach) }}" alt="file" style="max-height:40px; width: 40px; border: 1px solid;">
                          </a>
                      @else
                          N/A
                      @endif
                  </td>
                  <td class="text-center">
                    @if(@$value->approved_at)
                        @if(@$data->company_id == 2 || @$data->company_id == 6) 
                            <!-- show time only for MFI and MMI -->
                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime(@$value->approved_at))->format('d-m-Y h:i:s a')) }}
                        @else
                            {{ (\Carbon\Carbon::createFromTimestamp(strtotime(@$value->approved_at))->format('d-m-Y')) }}
                        @endif
                    @endif
                </td>
              </tr>
              @endif
          @endforeach
      </table>

      @if(@$data->remark)
          <span>កំណត់សម្គាល់ៈ {{@$data->remark}}</span><br>
      @endif

      @if($data->resign_id)
        <span>តំណភ្ជាប់ៈ</span>
        <a href="{{ route('resign.show', @$data->resign_id) }}" target="">
          <mark id="highlights"> {{ @$link_resign->title }} </mark>
        </a><br>
      @endif

      @if(@$data->attachment)
          <span>ឯកសារភ្ជាប់ៈ</span>
          @if(is_array($data->attachment))
              <?php $atts =  $data->attachment; ?>
              @foreach($atts as $att )
                  <a class="reference_link" href="{{ asset($att->src) }}" target="_self">{{ $att->org_name }}</a><br>
              @endforeach
          @else
              <a class="reference_link" href="{{ asset('/'.@$data->attachment) }}" target="_self">{{@$data->att_name}}</a>
          @endif
      @endif
    </div>
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
                <h1>លិខិតលាឈប់ពីការងារ</h1>
              @elseif($data->types == 2)
                <h1>សំណើអនុញ្ញាតលាឈប់ពីការងារ</h1>
              @elseif($data->types == 3)
                <h1>លិខិតអនុញ្ញាតឱ្យឈប់ជាផ្លូវការ</h1>
              @endif
            </div>
            <div class="body">
              <p>
                {{ prifixGender(@$data->requester->gender) }}ឈ្មោះ {{ @$data->requester->name }} 
                &emsp;
                មានតួនាទី៖ {{ @$data->requester->position->name_km }} 
                &emsp;
                នាយកដ្ឋាន៖ {{ @$department->where('id', @$data->requester->department_id)->first()->name_km }}
              </p>
              <div class="header">
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

              <table>
                <tr>
                    <td style="width: 50px; vertical-align: top">
                        <b>តាមរយៈ</b>
                    </td>
                    <td class="text-left" style="vertical-align: top">
                        @foreach($data->reviewers() as $reviewer)
                            <p class="mb-0">{{ $reviewer->position_name }}</p>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                      <b>កម្មវត្ថុៈ</b>
                    </td>
                    <td style="vertical-align: top">
                      <p>ស្នើសុំអនុញ្ញាតលាឈប់ការងាររបស់
                        {{ prifixGenderStaff(@$data->gender) }}
                        {{ $user->where('id', $data->staff_id)->first()->name }} 
                      </p>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                      <b>យោងៈ</b>
                    </td>
                    <td style="vertical-align: top">
                      <p>
                        លិខិតលាឈប់កាលពីថ្ងៃទី 
                        <!-- {{ @$data->resign_object->resign_date }}  -->

                        {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->resign_object->resign_date))->format('d')) }}
                        ខែ {{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->resign_object->resign_date))->format('m')) }}
                        ឆ្នំា {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->resign_object->resign_date))->format('Y')) }}

                        និងរបាយការណ៌ផ្ទេរការងារចប់កាលពីថ្ងៃទី
                        <!-- {{ @$data->resign_object->report_date }} -->

                        {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->resign_object->report_date))->format('d')) }}
                        ខែ {{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->resign_object->report_date))->format('m')) }}
                        ឆ្នំា {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->resign_object->report_date))->format('Y')) }} ។
                      </p>
                    </td>
                </tr>

                <tr>
                    <td style="vertical-align: top"></td>
                    <td style="vertical-align: top">

                      @if($data->company_id == 2 || $data->company_id == 3 || $data->company_id == 14) <!-- show only mfi and ngo -->
                        ដោយផ្អែកទៅលើឯកសារយោងដែលបានបញ្ជាក់ខាងលើ និងលទ្ធផលការផ្ទេរផ្ទង់ដូចខាងក្រោម៖
                        <ul>
                          <li>  
                            ទឹកប្រាក់សកម្មៈ
                            {{ @$data->resign_object->active_amount }}
                          </li>
                          <li>
                            អតិថិជនចំនួនៈ
                            {{ @$data->resign_object->number_customer }}
                          </li>
                          <li>
                            អតិថិជនយឺតចំនួនៈ
                            {{ @$data->resign_object->late_customer }}
                          </li>
                          <li>
                            ទឹកប្រាក់សកម្មយឺតយ៉ាវៈ
                            {{ @$data->resign_object->late_amount }}
                          </li>
                          <li>
                            ឯកសារឥណទានរបស់អតិថិជនដែលខ្វះ
                            {{ @$data->resign_object->customer_credit_document }}
                          </li>
                          <li>
                            ទ្រព្យធានាអតិថិជនដែលខ្វះ
                            {{ @$data->resign_object->collateral_missing_customer }}
                          </li>
                        </ul>
                      @endif

                      <p>
                        ប្រគល់ជូន
                        {{ @$data->resign_object->handed }}
                        និងពុំបានអនុវត្តន៍ខុសចាកពីគោលការណ៍បុគ្គលិក គោលការណ៍ស្ថាប័ន ឫការគៃបន្លំ។ 
                      </p>
                      <p>
                        &emsp; &emsp; 
                        {{ prifixGender($data->requester->gender) }}
                        ស្នើសុំអនុញ្ញាតលាឈប់ពីការងាររបស់ 
                        {{ prifixGenderStaff(@$data->gender) }}
                        {{ $user->where('id', $data->staff_id)->first()->name }} 
                        ទៅតាមពេលវេលាដ៏សមគួរ។
                      </p>
                      <p>
                        &emsp; &emsp; 
                        ហេតុដូចជម្រាបជូនខាងលើ
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
                        មេត្តាសម្រេច និងអនុម័តឱ្យលាឈប់ពីការងាររបស់
                        {{ prifixGenderStaff(@$data->gender) }}
                        {{ $user->where('id', $data->staff_id)->first()->name }} 
                        ដោយក្តីអនុគ្រោះ និងសូមទទួលការគោរពដ៏ខ្ពង់ខ្ពស់ពី
                        {{ prifixGender($data->requester->gender) }}។

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
                  <span>ស្នើរដោយ: {{ @$data->creator_object->name ?: $data->requester->name }}</span><br>
                  <span>
                    កាលបរិច្ឆេទ:
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
                      <span>បានឃើញ និងបញ្ជាក់ដោយ</span><br>
                      <span>
                        {{ @json_decode($reviewers[$i]->user_object)->position_name ?: $reviewers[$i]->position_name }}: 
                        {{ @json_decode($reviewers[$i]->user_object)->name ?: $reviewers[$i]->name }}
                      </span><br>
                      <span>
                        កាលបរិច្ឆេទ:
                        {{ (\Carbon\Carbon::createFromTimestamp(strtotime($reviewers[$i]->approved_at))->format('d/m/Y')) }}
                      </span>
                    @else
                      <br><br><br>
                      <span>បានឃើញ និងបញ្ជាក់ដោយ</span><br>
                      <span>
                        {{ @json_decode($reviewers[$i]->user_object)->position_name ?: $reviewers[$i]->position_name }}:
                        .........................................
                      </span><br>
                      <span>កាលបរិច្ឆេទ: .................................................................................</span>
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
                    <span>អនុម័តដោយ</span><br>
                    @if (@$data->approver()->position_level == config('app.position_level_president'))
                        <span>
                          លោកស្រី{{ @$data->forcompany->approver }}: 
                          {{ @check_nickname(@$data->approver()->position_level, $data->created_at ) }}
                          {{ @json_decode($approver->user_object)->name ?: $approver->name }}
                        </span><br>
                    @else
                        <span>
                          {{ @json_decode($approver->user_object)->position_name ?: $approver->position_name }}: 
                          {{ @json_decode($approver->user_object)->name ?: $approver->name }}
                        </span><br>
                    @endif
                    <span>
                      កាលបរិច្ឆេទ:
                      {{ (\Carbon\Carbon::createFromTimestamp(strtotime($approver->approved_at))->format('d/m/Y')) }}
                    </span>
                  @else
                    <br><br><br>
                    <span>អនុម័តដោយ</span><br>
                    @if (@$data->approver()->position_level == config('app.position_level_president'))
                        <span>លោកស្រី{{ @$data->forcompany->approver }}: .........................................</span><br>
                    @else
                        <span>
                          {{ @json_decode($approver->user_object)->position_name ?: $approver->position_name }}: 
                          .........................................
                        </span><br>
                    @endif
                    <span>កាលបរិច្ឆេទ: .................................................................................</span>
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
