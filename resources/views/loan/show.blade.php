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
          font-size: 14px;
          line-height: normal !important;
        }

        strong {
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-size: 14px;
          font-weight: 400;
        }

        h1{
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-weight: 400;
          font-size: 14px;
        }

        .header{
          text-align: center;
          text-decoration-line: underline;
          text-decoration-style: double;
        }


        .signature{
          padding: 14px 0 0 0;
          font-size: 14px !important;
        }

        .footer img{
          width: 1200px !important;
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

        span{

        }

        h2{
          margin-block-start: 17px;
          font-size: 14px !important;
          line-height: normal;
        }

        th{
          text-align: center;
        }


        div.action_btn {
          display: none;
          margin-top: 5px;
          position: fixed;
          box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        div.action_btn a {
          padding: 10px 14px;
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

          button, .file, .black-list {
            display: none;
          }

          .page-footer {
            height: auto;
          }

          .table-bordered td, .table-bordered th {
            border: 1px solid #1D1D1D !important;
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

        /*.contain{
          padding-left: 95px;
          padding-right: 95px;
          width: 880px;
        }*/

        .content {
            padding-left: 40px;
            padding-right: 40px;
        }

        .table > thead > tr > th, .table > tbody > tr > td {
          padding-left: 2px !important;
          padding-right: 2px !important;
          vertical-align: middle;
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

  <div id="action_container" style="width: 1200px; margin: auto;background: white;">
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
          @if(!can_approve_reject($data, config('app.type_loans')))
            <button id="approve_btn" disabled name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">
                Approve
            </button>
          @else
            <button @if (!@$data->viewedReference()) disabled @endif id="approve_btn" name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">
                Approve
            </button>
          @endif
          
          @if(!can_reject($data, config('app.type_loans')) && !can_approve_reject($data, config('app.type_loans')))
            <button id="comment_modal" disabled style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default">
                Comment
            </button>
            <button id="disable_btn" disabled style="background: black; color: white" name="next" value="1" class="btn btn-sm btn-default">
                Reject
            </button>
          @else
            <button id="reject_btn" style="background: #bd2130; color: white" title="ផ្តល់យោបល់ឲ្យកែប្រែ និងធ្វើម្តងទៀត" name="next" data-target="comment_attach" value="1" class="btn btn-sm btn-default">
                Comment
            </button>
            <button id="disable_btn" style="background: black; color: white" title="បដិសេធសំណើ" name="next" data-target="comment_attach" value="1" class="btn btn-sm btn-default">
                Reject
            </button>
          @endif
        </div>
      </form>
    </div><br><br>
      @if (!@$data->viewedReference())
          <br>
          <div class="alert alert-danger mb-0" role="alert" style="border-radius: 0">
              សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យឯកសារភ្ជាប់ជាមុនសិន មុនពេលអនុម័តសំណើរ! សូមអគុណ!
          </div>
      @endif

    @include('loan.partials.rerviewer_table', ['reviewers' =>
        $data->reviewers()->merge($data->reviewers_short())->push($data->approver())
    ])
  </div>

  <div style="width: 1200px; margin: auto;background: white; min-height: 800px;">
    <div class="row logo text-center" style="padding-top: 30px">
        <div class="col-sm-12">
            <img src="{{ asset($data->forcompany->logo) }}" alt="logo" style="height: 90px">
        </div>
    </div>
    <div>
        <div class="title text-center">
            <h1>របាយការណ៍អនុម័តរបស់គណៈកម្មការឥណទាន</h1>
            <h1>
                @if ($data->type_loan == 1)
                    (ឥណទានថ្មី)
                @elseif ($data->type_loan == 3)
                    (ឥណទានចាស់)
                @elseif ($data->type_loan == 2)
                    (ឥណទានរៀបចំឡើងវិញ)
                @elseif ($data->type_loan == 4)
                    (ឥណទានរៀបចំឡើងវិញលើកទី១)
                @elseif ($data->type_loan == 5)
                    (ឥណទានរៀបចំឡើងវិញលើកទី២)
                @elseif ($data->type_loan == 6)
                    (ឥណទានរៀបចំឡើងវិញលើកទី៣)
                @elseif ($data->type_loan == 7)
                    (ឥណទានរៀបចំឡើងវិញលើកទី៤)
                @elseif ($data->type_loan == 8)
                    (ឥណទានរៀបចំឡើងវិញលើកទី៥)
                @elseif ($data->type_loan == 9)
                    (ឥណទានរៀបចំឡើងវិញលើកទី៦)
                @elseif ($data->type_loan == 10)
                    (ឥណទានរៀបចំឡើងវិញលើកទី៧)
                @elseif ($data->type_loan == 11)
                    (ឥណទានរៀបចំឡើងវិញលើកទី៨)
                @elseif ($data->type_loan == 12)
                    (ឥណទានរៀបចំឡើងវិញលើកទី៩)
                @elseif ($data->type_loan == 13)
                    (ឥណទានរៀបចំឡើងវិញលើកទី១០)
                @endif
            </h1>
        </div>
        
        @if (@$data->company_id == 2)
            @if (@$data->aml->status == 1)
                <div class="alert black-list alert-danger text-center mb-0" role="alert" style="border-radius: 0">
                    អតិថិជនស្ថិតនៅក្នុង Black list មិនអាចអនុញ្ញាតឥណទានបានទេ!!!
                </div>
            @else
                <div class="alert black-list alert-success text-center mb-0" role="alert" style="border-radius: 0">
                    អតិថិជនមិនស្ថិតនៅក្នុង Black list ទេ
                </div>
            @endif

            <div class="row content">
                <div class="col-sm-12">
                    <table class="table table-bordered text-center">
                      <thead class="table-info">
                          <tr class="bgcol">
                              <th>ឈ្មោះសាខា</th>
                              <th>ឈ្មោះមន្រ្តីឥណទាន</th>
                              <th>ឈ្មោះអ្នកខ្ចី</th>
                              <th>ឈ្មោះអ្នករួមខ្ចី</th>
                              <th style="min-width: 100px;">ទំហំឥណទាន (រៀល)</th>
                              <th style="min-width: 100px;">រយៈពេលខ្ចី <br/> (ខែ/សប្តាហ៍)</th>
                              <th>អត្រាការប្រាក់ (%)</th>

                              @if($data->service_object)
                                  <th>សេវារៀបចំឥណទាន (%)</th>
                                  <th>សេវាត្រួតពិនិត្យឥណទាន (%)</th>
                                  <th>សេវាប្រមូលឥណទាន (%)</th>
                              @else
                                  <th>សេវារដ្ឋបាល (%)</th>
                              @endif

                              <th>របៀបសង</th>
                              <th>អនុម័តតាមគោលការណ៍</th>
                              <th>ស្ថានភាព</th>
                              <!-- <th style="width: 150px;" class="file">ឯកសារភ្ជាប់</th> -->
                          </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td>{{ $data->forbranch->name_km }}</td>
                              <td>{{ $data->credit }}</td>
                              <td>{{ $data->borrower }}</td>
                              <td>
                                  <?php
                                      $part = json_decode($data->participants);
                                  ?>
                                  {{ $part ? implode(", ",$part) : '' }}
                              </td>
                              <td>{{ number_format($data->money) }} ៛</td>
                              <td>
                                  {{ $data->times }} 
                                  @if($data->type_time == 1)
                                    ខែ
                                  @else
                                    សប្តាហ៍
                                  @endif
                              </td>
                              <td>{{ $data->interest }} %</td>

                              @if($data->service_object)
                                  <td>{{ @$data->service_object->arrangement }} %</td>
                                  <td>{{ @$data->service_object->check }} %</td>
                                  <td>{{ @$data->service_object->collection }} %</td>
                              @else
                                  <td>{{ $data->service }} %</td>
                              @endif
                              
                              <td>

                                  @if($data->types == 1)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ១សប្តាហ៍ម្តង
                                  @elseif($data->types == 2)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ២សប្តាហ៍ម្តង
                                  @elseif($data->types == 3)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ខែ
                                  @elseif($data->types == 4)
                                    សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៤ខែម្តង
                                  @elseif($data->types == 5)
                                    សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៦ខែម្តង
                                  @elseif($data->types == 6)
                                    សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៨ខែម្តង
                                  @elseif($data->types == 7)
                                    សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ១២ខែម្តង
                                  @elseif($data->types == 8)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ៤ខែម្តង
                                  @elseif($data->types == 9)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ៦ខែម្តង
                                  @elseif($data->types == 10)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ៨ខែម្តង
                                  @elseif($data->types == 11)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ១២ខែម្តង
                                  @elseif($data->types == 12)
                                    បង់រំលស់តែការប្រាក់រៀងរាល់ខែ
                                  @endif

                              </td>
                              <td>
                                @if($data->principle == 1)
                                    អនុម័តតាមគោលការណ៍
                                @else($data->principle == 0)
                                    អនុម័តខុសគោលការណ៍
                                @endif
                              </td>
                              <td>
                                @if($data->status == config('app.approve_status_draft'))
                                    រង់ចាំ
                                @elseif($data->status == config('app.approve_status_approve'))
                                    បានអនុម័ត
                                @elseif($data->status == config('app.approve_status_reject'))
                                    សូមធ្វើការកែប្រែ
                                @else
                                    បានបដិសេធន៍
                                @endif
                              </td>
                              <!-- <td class="file">
                                @if($data->attachment)
                                  <?php $atts = is_array($data->attachment) ? $data->attachment : json_decode($data->attachment); ?>
                                  @foreach($atts as $att )
                                    <a href="{{ asset($att->src) }}" target="_self">{{ $att->org_name }}</a><br>
                                  @endforeach
                                @endif
                              </td> -->
                          </tr>
                      </tbody>
                  </table>
                  @if(@$data->comment)
                      <span class="text-primary"> អនុសាសន៍របស់គណៈកម្មការ៖ </span><br>
                      <span class="text-primary"> {{ @$data->comment }} </span>
                  @endif
              </div>
              <div class="col-sm-12">
                  <div class="rows text-center signature">
                      @include('loan.partials.approve_section')
                  </div>
              </div>
          </div>

      @else 

          <div class="row content">
                <div class="col-sm-12">
                    <table class="table table-bordered text-center">
                      <thead class="table-info">
                          <tr class="bgcol">
                              <th>ឈ្មោះសាខា</th>
                              <th>ឈ្មោះមន្រ្តីឥណទាន</th>
                              <th>ឈ្មោះអ្នកខ្ចី</th>
                              <th>ឈ្មោះអ្នករួមខ្ចី</th>
                              <th style="min-width: 100px;">ទំហំឥណទាន (រៀល)</th>
                              <th style="min-width: 100px;">រយៈពេលខ្ចី <br/> (ខែ/សប្តាហ៍)</th>
                              <th>អត្រាការប្រាក់ (%)</th>
                              <th>សេវារដ្ឋបាល (%)</th>
                              <th>របៀបសង</th>
                              <th>អនុម័តតាមគោលការណ៍</th>
                              <th>ស្ថានភាព</th>
                              <!-- <th style="width: 150px;" class="file">ឯកសារភ្ជាប់</th> -->
                          </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td>{{ $data->forbranch->name_km }}</td>
                              <td>{{ $data->credit }}</td>
                              <td>{{ $data->borrower }}</td>
                              <td>
                                  <?php
                                      $part = json_decode($data->participants);
                                  ?>
                                  {{ $part ? implode(", ",$part) : '' }}
                              </td>
                              <td>{{ number_format($data->money) }} ៛</td>
                              <td>
                                  {{ $data->times }} 
                                  @if($data->type_time == 1)
                                    ខែ
                                  @else
                                    សប្តាហ៍
                                  @endif
                              </td>
                              <td>{{ $data->interest }} %</td>
                              <td>{{ $data->service }} %</td>
                              <td>

                                  @if($data->types == 1)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ១សប្តាហ៍ម្តង
                                  @elseif($data->types == 2)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ២សប្តាហ៍ម្តង
                                  @elseif($data->types == 3)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ខែ
                                  @elseif($data->types == 4)
                                    សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៤ខែម្តង
                                  @elseif($data->types == 5)
                                    សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៦ខែម្តង
                                  @elseif($data->types == 6)
                                    សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៨ខែម្តង
                                  @elseif($data->types == 7)
                                    សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ១២ខែម្តង
                                  @elseif($data->types == 8)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ៤ខែម្តង
                                  @elseif($data->types == 9)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ៦ខែម្តង
                                  @elseif($data->types == 10)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ៨ខែម្តង
                                  @elseif($data->types == 11)
                                    សងការប្រាក់ និងប្រាក់ដើមរាល់ ១២ខែម្តង
                                  @elseif($data->types == 12)
                                    បង់រំលស់តែការប្រាក់រៀងរាល់ខែ
                                  @endif

                              </td>
                              <td>
                                @if($data->principle == 1)
                                    អនុម័តតាមគោលការណ៍
                                @else($data->principle == 0)
                                    អនុម័តខុសគោលការណ៍
                                @endif
                              </td>
                              <td>
                                @if($data->status == config('app.approve_status_draft'))
                                    រង់ចាំ
                                @elseif($data->status == config('app.approve_status_approve'))
                                    បានអនុម័ត
                                @elseif($data->status == config('app.approve_status_reject'))
                                    សូមធ្វើការកែប្រែ
                                @else
                                    បានបដិសេធន៍
                                @endif
                              </td>
                              <!-- <td class="file">
                                @if($data->attachment)
                                  <?php $atts = is_array($data->attachment) ? $data->attachment : json_decode($data->attachment); ?>
                                  @foreach($atts as $att )
                                    <a href="{{ asset($att->src) }}" target="_self">{{ $att->org_name }}</a><br>
                                  @endforeach
                                @endif
                              </td> -->
                          </tr>
                      </tbody>
                  </table>
                  @if(@$data->comment)
                      <span class="text-primary"> អនុសាសន៍របស់គណៈកម្មការ៖ </span><br>
                      <span class="text-primary"> {{ @$data->comment }} </span>
                  @endif
              </div>
              <span style="float: right;">
                @foreach($data->reviewers_short() as $key => $value)
                  @if ($value->approve_status == config('app.approve_status_approve'))
                    <img  src="{{ asset($value->short_signature) }}"  
                          alt="short_sign" 
                          title="{{ @$value->name }}" 
                          style="width: 25px;">
                  @endif
                @endforeach
              </span>
              <div class="col-sm-12">
                  <div class="rows text-center signature">
                      @include('loan.partials.approve_section')
                  </div>
              </div>
          </div>
      @endif

    </div>
  </div>
  <div class="page-footer">
    <div style="width: 1200px; margin: auto; text-align: center;">
      <img src="{{ asset($data->forcompany->footer_landscape) }}" alt="logo_footer" style="width: 1200px; background: white">
    </div>
  </div>

  @include('global.approve_modal', ['route_approve' => route('loan.approve', $data->id), 'comment' => $data->comment])
  @include('global.comment_modal', ['route' => route('loan.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('loan.disable', $data->id)])

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

  // approve with comment
  $( "#approve_btn" ).on( "click", function( event ) {
    event.preventDefault();
    $('#approve_modal').modal('show');
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

  // Update auth view reference
  $( ".reference_link" ).on( "click", function( event ) {
      $.ajax({
          type: "POST",
          url: "{{ route('loan.view.reference') }}",
          data: {
              _token: "{{ csrf_token() }}",
              request_id: "{{ $data->id }}"
          },
          dataType: "json",
          success: function(data) {
              console.log(data)
          },
          error: function(data) {
              console.log('Error: '+data)
          }
      });
  });

</script>
@include('global.sweet_alert')
</html>
