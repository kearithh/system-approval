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
        }

        .signature{
          padding: 1px 0 0 20px;
          font-size: 14px !important;
        }

        .related {
            float: left;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box;
            text-overflow: ellipsis;
        }

        p{
          margin: 0 !important;
        }

        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100% !important;
        }

        /*tr{
          vertical-align: top;
        }*/

        td{
          padding: 5px 0 5px 0;
        }

        .item tr td {
            border: 1px solid #585858;
            padding: 2px 4px 2px 4px;
        }

        .item {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        .item thead tr {
            /*background: orange;*/
            font-weight: 700;
            text-align: center;
        }

        ul li {
            list-style: none;
            margin-left: -40px;
        }

        h2{
          margin-block-start: 17px;
          font-size: 15px !important;
          line-height: normal;
        }

        h1, .h1, h2, .h2, h3, .h3 {
            margin-top: 10px !important;
            margin-bottom: 10px;
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
          height: 30px;
        }

        .page-footer {
          /*position: fixed;*/
          bottom: 0;
          width: 100%;
        }

        .signature > div {
            float: left;
            width: 33.33%;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box
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
          @if(!can_approve_reject($data, $data->types))
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
        $data->reviewers()->merge($data->reviewers_short())->push($data->verify())->push($data->approver())
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
            {!! $data->forcompany->header_section  !!}
            <div class="header">
	            <h1>
                សាខា
                @if(@$data->forbranch->name_km)
                  {{ @$data->forbranch->name_km }}
                @else
                  {{ @$data->requester()->branch->name_km }}
                @endif 
              </h1>
	            <h1>សូមគោរពជូន</h1>
	            <h1 style="font-size: 16px">
	                @if ($data->requester()->branch_id)
	                    {{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position_name }}
	                @else
	                    {{ @$data->forcompany->approver }}
	                @endif
	                {{ $data->forcompany->long_name }}
            	</h1>
            </div>
            <br>
            <div class="body">
              <table>
                <tr>
                  <td style="width: 75px; vertical-align: top">
                      <strong>តាមរយៈ</strong>
                  </td>
                  <td class="text-left">
                      @foreach($data->reviewers() as $reviewer)
                          <p>{{ @json_decode(@$reviewer->user_object)->position_name ?: $reviewer->position_name }}</p>
                      @endforeach
                  </td>
                </tr>
                <tr>
                  <td style="width: 75px; vertical-align: top">
                      <strong>កម្មវត្ថុៈ</strong>
                  </td>
                  <td class="text-left">
                      @if (@$data->subject_obj)
                          <?php $subject_obj = @$data->subject_obj ?>
                          សំណើស្នើសុំបញ្ឈប់ការប្រាក់ និងកាត់ការប្រាក់ហួសកាលកំណត់ចំនួន {{ number_format(@$subject_obj->interest_past) }} រៀល 
                          @if(@$subject_obj->type_loan == 2)
                              ចំពោះកម្ចីលុបចេញពីបញ្ជី (Write Off)
                          @else
                              ចំពោះអតិថិជនយឺតយ៉ាវ (Loan Default)
                          @endif
                          ដែលមានឈ្មោះ {{ @$subject_obj->customer_name }} 
                          ភេទ {{genderKhmer(@$subject_obj->gender)}}
                          CID៖ {{ @$subject_obj->cid }} 
                          ថ្ងៃបើកប្រាក់ {{ @$subject_obj->date_open }} 
                          ចំនួនថ្ងៃយឺត {{ @$subject_obj->number_day_late }} ថ្ងៃ។
                      @else  
                          {{ $data->purpose }}
                      @endif
                  </td>
                </tr>
                @if ($data->reason)
                  <tr>
                    <td style="width: 75px; vertical-align: top">
                        <strong>មូលហេតុៈ</strong>
                    </td>
                    <td class="text-left">
                        {{ $data->reason }}
                    </td>
                  </tr>
                @endif
              </table>

              <?php $interest_obj = @$data->interest_obj ?>
              @if(@$interest_obj)
                  <table class="item">
                    <thead>
                        <tr>
                            <td style="width: 25%">ប្រាក់ដើមនៅសល់(៛)</td>
                            <td style="width: 25%">ការប្រាក់យល់ព្រមសង(៛)</td>
                            <td style="width: 25%">រយៈពេលខ្ចី(ខែ)</td>
                            <td style="width: 25%">អត្រាការប្រាក់(%)</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>{{ number_format(@$interest_obj->remain_amount) }} ៛</td>
                            <td>{{ number_format(@$interest_obj->interest_repay) }} ៛</td>
                            <td>{{ @$interest_obj->period }} ខែ</td>
                            <td>{{ @$interest_obj->interest_rate }} %</td>
                        </tr>
                    </tbody>
                  </table>
                  <br>
              @endif
              
	            <table class="item">
	              <thead>
  	                <tr>
  	                    <td style="min-width: 50px">ល.រ</td>
  	                    <td style="min-width: 300px">បរិយាយ</td>
  	                    <td style="min-width: 120px">ទឹកប្រាក់</td>
  	                    <td style="min-width: 140px">ទឹកប្រាក់ប្រមូលបាន</td>
  	                    <td style="min-width: 145px">ភាគរយប្រមូលបាន(%)</td>
  	                    <td style="min-width: 80px">ផ្សេងៗ</td>
  	                </tr>
	              </thead>
	              <tbody>
	                <?php $total = 0; ?>
	                @foreach($data->items as $key => $item)
	                    <tr>
	                        <td style="text-align: center;">{{ $key +1 }}</td>
	                        <td>{{ $item->desc }}</td>
	                        <td style="text-align: right;">
                              @if($item->currency=='KHR')
                                  {{ number_format($item->amount) }} ៛
                              @else
                                  $ {{ number_format(($item->amount), 2) }}
                              @endif 
                          </td>
                          <td style="text-align: right;">
                              @if($item->currency=='KHR')
                                  {{ number_format($item->amount_collect) }} ៛
                              @else
                                  $ {{ number_format(($item->amount_collect), 2) }}
                              @endif 
                          </td>
	                        <td style="text-align: right;">
	                            @if($item->percentage)
                                  {{ $item->percentage }} %
                              @endif 
	                        </td>
	                        <td>{{ $item->other }}</td>
	                    </tr>
	                @endforeach
	              </tbody>
	            </table>

              <br>
              <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                  {!! $data->describe !!}
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
              </div>

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
                      <img style="max-height: 60px; max-width: 180px;"
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
                                  <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                                  <p>{{ @json_decode(@$item->user_object)->position_name ?: $item->position_name }}</p>
                                  <img style="max-height: 60px; max-width: 180px;"
                                       src="{{ asset('/'.$item->signature) }}"
                                       alt="Signature">
                                  <p>{{ @json_decode(@$item->user_object)->name ?: $item->name }}</p>
                              </div>
                          @else
                              <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                  <p>ថ្ងៃទី.....ខែ......ឆ្នំា.....</p>
                                  <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                                  <p>{{ @json_decode(@$item->user_object)->position_name ?: $item->position_name }}</p>
                              </div>
                          @endif
                      @endforeach

                  </div>

                  <div style="width: {{ (100/$allCol).'%' }}">
                      @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                          <p>
                              ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                              ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('m')) }}
                              ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}
                          </p>
                          <p>អនុម័តដោយ៖</p>
                          <p>{{ @json_decode(@$data->approver()->user_object)->position_name ?: $data->approver()->position_name }}</p>
                          <img style="max-height: 60px; max-width: 180px;"
                               src="{{ asset('/'.$data->approver()->signature) }}"
                               alt="Signature">
                          <p>
                            {{ @check_nickname($data->approver()->position_level, $data->created_at ) }}
                            {{ @json_decode(@$data->approver()->user_object)->name ?: $data->approver()->name }}
                          </p>
                      @else
                          <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                          <p>អនុម័តដោយ៖</p>
                          <p>{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position_name }}</p>
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
      {!! $data->forcompany->footer_section  !!}
    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('penalty.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('penalty.disable', $data->id)])

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
                  url: "{{ action('PenaltyController@approve', $data->id) }}",
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
