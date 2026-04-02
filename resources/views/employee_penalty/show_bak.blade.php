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
          font-size: 17px;
          line-height: normal !important;
        }

        strong {
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-size: 17px;
          font-weight: 400;
        }

        h1 {
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-size: 17px;
          margin: 8px 0 1px 0 !important;
        }

        p, span, b {
            font-family: 'Khmer OS Content' !important;
            font-size: 17px !important;
            margin: 0 !important;
        }

        h2{
          margin-block-start: 5px;
          font-size: 17px !important;
          font-family: 'Times New Roman','Khmer OS Muol Light';
          line-height: normal;
        }

        .header{
          text-align: center;
        }

        /*.body{
          text-align: justify;
        }*/

        .sign{
          padding-top: 20px;
        }

        .signature{
          padding: 17px 0 0 20px;
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

        /*tr{
          vertical-align: top;
        }*/

        td{
          padding: 5px;
        }

        #item tr td {
            border: 1px solid #585858;
            padding: 5px 5px;
        }

        #item {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        #item thead tr {
            /*background: orange;*/
            font-weight: 700;
            text-align: center;
        }

        ul li {
            list-style: none;
            margin-left: -40px;
        }

        div.action_btn {
          display: none;
          margin-top: 5px;
          position: fixed;
          box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        div.action_btn a {
          padding: 10px 16px;
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

        .signature {
            padding-top: 50px;
        }

        .signature > div {
            float: left;
            width: 33.33%;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box
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
          @if(!can_approve_reject($data, config('app.type_employee_penalty')))
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

    @include('global.rerviewer_table', ['reviewers' =>
        $data->reviewers()->push($data->verify())->push($data->approver())
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
	            <h2>
                {{$data->requester()->position->name_km}}
              </h2>
	            <h2>សូមគោរពជូន</h2>
	            <h2 style="font-size: 16px">
                @if ($data->approver()->position_level == config('app.position_level_president') || $data->company_id != '6')
                  {{ $data->forcompany->approver }}
                  {{ $data->forcompany->long_name }}
                @else
                  {{ $data->approver()->position_name }}
                  {{ $data->forcompany->long_name }}
                @endif
            	</h2>
              <h2>ស្នើរសុំទទូលប្រាក់ពិន័យបុគ្គលិក</h2>
            </div>
            <br>
            <div class="body">
              <table >
                  <tr>
                      <td style="width: 100px; vertical-align: top">
                          <h1>តាមរយៈ</h1>
                      </td>
                      <td class="text-left" style="vertical-align: top">
                          @foreach($data->reviewers() as $reviewer)
                              <p class="mb-0">{{ $reviewer->position_name }}</p>
                          @endforeach
                      </td>
                  </tr>
                  <tr>
                      <td style="vertical-align: top">
                        <h1>កម្មវត្ថុៈ</h1>
                      </td>
                      <td style="vertical-align: top">
                        <p>{{ $data->subject }}</p>
                      </td>
                  </tr>
                  <tr>
                      <td style="vertical-align: top">
                        <h1>គោលបំណងៈ</h1>
                      </td>
                      <td style="vertical-align: top">
                        <p>{{ $data->purpose }}</p>
                      </td>
                  </tr>
                  <tr>
                      <td style="vertical-align: top" colspan="2">
                        <p>
                            &emsp;&emsp;&emsp;តបតាមកម្មវត្ថុ និងគោលបំណងដូចបានជម្រាបជូនខាងលើ ខ្ញុំមានកិតិយសសូមជម្រាបជូន
                            @if(@$data->approver()->position_level == config('app.position_level_president'))
                                លោកស្រី{{@$data->forcompany->approver}}
                            @else
                                @if($data->approver()->gender == 'M')
                                  លោក{{@$data->approver()->position->name_km}}
                                @elseif($data->approver()->gender == 'F')
                                  លោកស្រី{{@$data->approver()->position->name_km}}
                                @else
                                  {{@$data->approver()->position->name_km}}
                                @endif
                            @endif
                            មេត្តាត្រួតពិនិត្យ និងអនុម័តលើសំណើដើម្បីបញ្ចប់បញ្ហាជាមួយបុគ្គលិកមានឈ្មោះដូចខាងក្រោម៖
                        </p>
                      </td>
                  </tr>
              </table>

	            <table id="item">
	              <thead>
	                <tr>
	                    <td style="min-width: 50px">ល.រ</td>
	                    <td style="min-width: 130px">ឈ្មោះបុគ្គលិក</td>
	                    <td style="min-width: 280px">បរិយាយ</td>
	                    <td style="min-width: 120px">ទឹកប្រាក់ពិន័យ</td>
	                    <td style="min-width: 120px">សរុបទឹកប្រាក់</td>
	                    <td style="min-width: 100px">សំគាល់</td>
	                </tr>
	              </thead>
	              <tbody>
	                <?php $total = 0; ?>
	                @foreach($data->items as $key => $item)
	                    <tr>
	                        <td style="text-align: center;">{{ $key +1 }}</td>
	                        <td>{{ $item->name }}</td>
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
                                  {{ number_format($item->total) }} ៛
                              @else
                                  $ {{ number_format(($item->total), 2) }}
                              @endif
                          </td>
	                        <td>{{ $item->other }}</td>
	                    </tr>
	                @endforeach
	                <tr style="font-weight: 700">
	                    <td colspan="4" style="text-align: right">សរុប</td>
	                    <td colspan="3" style="text-align: center;">
	                        @if($data->total_amount_usd > 0 )
	                            {{'$ '. number_format(($data->total_amount_usd),2) }} &emsp;
	                        @endif
	                        @if($data->total_amount_khr > 0 )
	                            {{ number_format($data->total_amount_khr) .' ៛'}}
	                        @endif
	                    </td>
	                </tr>
	              </tbody>
	            </table>

              <br>
              <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                  <p>
                      អាស្រ័យហេតុដូចបានជំរាបជូនខាងលើ 
                      @if(@$data->approver()->position_level == config('app.position_level_president'))
                          សូមលោកស្រី{{@$data->forcompany->approver}}
                      @else
                          @if($data->approver()->gender == 'M')
                            សូមលោក{{@$data->approver()->position->name_km}}
                          @elseif($data->approver()->gender == 'F')
                            សូមលោកស្រី{{@$data->approver()->position->name_km}}
                          @else
                            សូម{{@$data->approver()->position->name_km}}
                          @endif
                      @endif
                      មេត្តាពិនិត្យ និងអនុញ្ញាតតាមការគួរ។
                  </p>
                  <p>
                      @if(@$data->approver()->position_level == config('app.position_level_president'))
                          សូមលោកស្រី{{@$data->forcompany->approver}}
                      @else
                          @if($data->approver()->gender == 'M')
                            សូមលោក{{@$data->approver()->position->name_km}}
                          @elseif($data->approver()->gender == 'F')
                            សូមលោកស្រី{{@$data->approver()->position->name_km}}
                          @else
                            សូម{{@$data->approver()->position->name_km}}
                          @endif
                      @endif
                      មេត្តាទទួលនូវសេចក្ដីគោរពដ៏ខ្ពង់ខ្ពស់ពី{{prifixGender($data->requester()->gender)}}។
                  </p>
              </div>

            </div>

            <?php
              $relatedCol = count($data->reviewers());
              $allCol = $relatedCol + 2;
            ?>


                <div class="signature">
                    <div style="width: {{ (100/$allCol).'%' }}">
                        <p>
                            ថ្ងៃទី {{ khmer_number($data->created_at->format('d')) }}
                            ខែ {{ khmer_number($data->created_at->format('m')) }}
                            ឆ្នំា{{ khmer_number($data->created_at->format('Y')) }}
                        </p>

                        <p>ស្នើសុំដោយ៖</p>
                        <p>{{ $data->requester()->position->name_km }}</p>
                        <img style="max-height: 60px; max-width: 180px;"
                             src="{{ asset('/'.$data->requester()->signature) }}"
                             alt="Signature">
                        <p>{{ $data->requester()->name }}</p>
                    </div>

                    <div style="width: {{ (($relatedCol*100)/$allCol).'%' }}">
                        <p>
                            @if (@$data->reviewers()->first()->approved_at)
                                ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->reviewers()->first()->approved_at))->format('d')) }}
                                ខែ {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->reviewers()->first()->approved_at))->format('m')) }}
                                ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->reviewers()->first()->approved_at))->format('Y')) }}
                            @else
                                ថ្ងៃទី.....ខែ......ឆ្នំា.....
                            @endif
                        </p>
                        <p>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</p>
                        @foreach($data->reviewers() as $item)
                            @if ($item->approve_status == config('app.approve_status_approve'))
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    <p>{{ $item->position_name }}</p>
                                    <img style="max-height: 60px; max-width: 180px;"
                                         src="{{ asset('/'.$item->signature) }}"
                                         alt="Signature">
                                    <p>{{ $item->name }}</p>
                                </div>
                            @else
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    <p>{{ $item->position_name }}</p>
                                </div>
                            @endif
                        @endforeach

                    </div>

                    <div style="width: {{ (100/$allCol).'%' }}">
                        @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                            <p>
                                ថ្ងៃទី {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                                ខែ {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('m')) }}
                                ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}
                            </p>
                            <p>អនុម័តដោយ៖</p>
                            <p>
                              @if ($data->approver()->position_level == config('app.position_level_president') )
                                {{ $data->forcompany->approver }}
                              @else
                                {{ $data->approver()->position_name }}
                              @endif
                            </p>
                            <img style="max-height: 60px; max-width: 180px;"
                                 src="{{ asset('/'.$data->approver()->signature) }}"
                                 alt="Signature">
                            <p>{{ ($data->approver()->name) }}</p>
                        @else
                            <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                            <p>អនុម័តដោយ៖</p>
                            <p>
                              @if ($data->approver()->position_level == config('app.position_level_president') )
                                {{ $data->forcompany->approver }}
                              @else
                                {{ $data->approver()->position_name }}
                              @endif
                            </p>
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

  @include('global.comment_modal', ['route' =>route('employee_penalty.reject', $data->id)])
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
                  url: "{{ action('EmployeePenaltyController@approve', $data->id) }}",
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

  $( "#reject_btn" ).on( "click", function( event ) {
    event.preventDefault();
    $('#comment_modal').modal('show');
  });

</script>
@include('global.sweet_alert')
</html>
