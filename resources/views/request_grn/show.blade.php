<!DOCTYPE html>
<html>
<head>
    <title>E-Approval</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

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

        /*.body{
          text-align: justify;
        }*/

        .sign{
          font-size: 14px;
        }

        .sign > div > div > p {
          margin: 0 0 5px !important;
        }

        .signature{
          padding: 15px 0 0 20px;
          font-size: 14px;
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

        td{
          padding: 5px;
          vertical-align: middle;
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

        .signature {
            padding-top: 50px;
        }

        .signature > div {
            float: left;
            width: 33.33%;
            text-align: center;
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
          @if(!can_approve_reject($data, config('app.type_grn')))
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

    {{-- @include('global.rerviewer_table', ['reviewers' =>
        $data->reviewers()->push($data->verify())->push($data->subApprover())->push($data->approver())
    ]) --}}
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
          <div class="row">
          <div class="col">{!! $data->forcompany->header_section  !!}</div>
          <div class="col">
          
          </div>
          </div>
          
            <br>
            <div class="row">
            <div class="col">
            {{-- <h1>ឈ្មោះអ្នកផ្តត់ផ្គង់: {{ $data->name_kh }}</h1>
            <h1><b>Vendor Name:</b> {{ $data->name_en }}</h1>
            <p><b>Supplier Name: </b> {{ $data->address_vd }}</p>
            <p><b>Delivery By: </b> {{ $data->contact_ps }}</p>
            <p><b>Tel:</b> {{ $data->mobile_phone }}</p> --}}
            <h1>បណ្ណទទួលទំនិញ​ GRN</h1>
          <p><b>ថ្ងៃខែឆ្នាំ​ Date:</b> {{ optional($data->created_at)->format('d/M/Y') }}</p>
          <p><b>លេខវិក័យបត្រ​ Invoice Number:​ {{ $data->code }}</b></p>
          <p><b>លេខយោងបណ្ណបញ្ជាទិញ P.O Number: {{ $data->codepo }}</b></p>
            <p><b>Signature: </b> @if(@$data->attachment_vd)
        @if(is_array($data->attachment_vd))
            <?php $atts =  $data->attachment_vd; ?>
            @foreach($atts as $att )
                <a class="reference_link" href="{{ asset($att->src) }}" target="_self">{{ $att->org_name }}</a><br>
            @endforeach
        @else
            <a class="reference_link" href="{{ asset('/'.@$data->attachment_vd) }}" target="_self">{{@$data->att_name_vd}}</a>
        @endif
    @endif</p>
            
            </div>
            <div class="col">
            <h1>ដឹកជញ្ជូនទៅ Ship To </h1>
            <p><b>ឈ្មោះក្រុមហ៊ុន:</b> {{ $data->companie_name }}</p> 
            <p><b>Company:</b> {{ $data->companie_name_en }}</p>
            <p><b>អាសយដ្ឋាន:</b> {{ $data->companie_address_kh }}</p> 
            <p><b>Address:</b> {{ $data->companie_address_en }}</p>
            <p><b>VAT:</b> {{ $data->vat_st }}</p>
            <p><b>ឈ្មោះអ្នកទទួល (Receiver​ Name):</b>  {{ $data->name_reciever }}</p>
            {{-- <p><b>លេខទូរស័ព្ទ (Tel.​ No):</b>  {{ $data->tel }}</p> --}}
            </div>
            </div>
            <div class="body">
	              
              @if ($data->company_id == 6)
                  <p style="float: right;"><b>No. {{ @showArrayCode($data->code) }}</b></p>
              @endif
	            <?php
	                $vat = false;
	                foreach ($data->items as $item) {
	                    if ($item->vat){
	                        $vat = true;
	                    }
	                }
	            ?>

	            <table id="item">
	              <thead>
	                <tr>
	                    <td>ល.រ</td>
	                    <td>បរិយាយមុខទំនិញ<br> Product Name / Description</td>
	                    <td>ចំនួនកម្មង់<br>Order Qty</td>
                      <td>ចំនួនប្រគល់<br>Deliverd Qty</td>
	                    <td>Note</td>
	                </tr>
	              </thead>
	              <tbody>
	                <?php $total = 0; ?>
	                @foreach($data->items as $key => $item)
	                    <?php $subtotal = $item->qty*$item->unit_price + ($item->qty*$item->unit_price*$item->vat)/100 ?>
	                    <tr>
	                        <td style="text-align: center;">{{ $key +1 }}</td>
	                        <td>{{ $item->name }}</td>
	                        <td style="text-align: center;">{{ $item->qty }}</td>
                          <td style="text-align: center;">{{ $item->dqty }}</td>
	                       
	                        <td>{{ $item->other }}</td>
	                        <?php $total += $subtotal; ?>
	                    </tr>
	                @endforeach
	                
	              </tbody>
	            </table>

              <br>
              <div class="row">
              <div class="col"><strong><b>*សំរាប់ផ្នែក/For Department: </b></strong>
            
                  {{ $data->department_name }}
              </div>
              </div><br>

              @if (@$data->forcompany->short_name_en == 'MMI')
                  <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                      
                  </div>
              @else
                  <div class="desc" style="text-align: justify; padding-bottom: 15px; text-indent : 3em;">
                      
                  </div>
              @endif

            </div>

            <!-- show short approver for mmi date <= 2022-09-10 -->
            @if (@$data->forcompany->short_name_en == 'MMI' && $data->created_at <= '2022-09-10')

                <?php
                  $relatedCol = count($data->reviewers_short_sign());
                  $allCol = $relatedCol + 2;
                ?>

                <div class="signature">
                    <div style="width: {{ (100/$allCol).'%' }}">
                        <p>
                            ថ្ងៃទី{{ khmer_number($data->created_at->format('d')) }}
                            ខែ{{ khmer_number($data->created_at->format('m')) }}
                            ឆ្នំា{{ khmer_number($data->created_at->format('Y')) }}
                        </p>

                        <p>បានទទួលដោយ:៖</p>
                        <p>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</p>
                        <img style="height: 60px;"
                             src="{{ asset('/'.$data->requester()->signature) }}"
                             alt="Signature">
                        <p>{{ @$data->creator_object->name ?: $data->requester()->name }}</p>
                    </div>

                    <div style="width: {{ (($relatedCol*100)/$allCol).'%' }}">
                        @foreach($data->reviewers_short_sign() as $item)
                            @if ($item->approve_status == config('app.approve_status_approve'))
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    <p>
                                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('d')) }}
                                        ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('m')) }}
                                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('Y')) }}
                                    </p>
                                    <p>បានពិនិត្យដោយ</p>
                                    <p>{{ @json_decode($item->user_object)->position_name ?: $item->position_name }}</p>
                                    <img style="height: 60px;"
                                         src="{{ asset('/'.$item->signature) }}"
                                         alt="Signature">
                                    <p>{{ @json_decode($item->user_object)->name ?: $item->name }}</p>
                                </div>
                            @else
                                <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                    <p>ថ្ងៃទី.....ខែ......ឆ្នំា.....</p>
                                    <p>បានពិនិត្យដោយ៖</p>
                                    <p>{{ @json_decode($item->user_object)->position_name ?: $item->position_name }}</p>
                                </div>
                            @endif
                        @endforeach

                    </div>

                    <div style="width: {{ (100/$allCol).'%' }}">

                        @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                            <p>
                                ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->subApprover()->approved_at))->format('d')) }}
                                ខែ{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->subApprover()->approved_at))->format('m')) }}
                                ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->subApprover()->approved_at))->format('Y')) }}
                            </p>
                            <p>
                               បានបញ្ជាក់ពី សវនកម្មផ្ទៃក្នុង៖

                                @if (@$data->shortSign()->approve_status == config('app.approve_status_approve'))
                                    <img style="height: 15px;"
                                          title="{{ $data->shortSign()->name }}"
                                          src="{{ asset('/'.@$data->shortSign()->short_signature) }}"
                                          alt="short_signature">
                                @endif

                                @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                                    <img style="height: 15px;"
                                         title="{{ $data->approver()->name }}"
                                         src="{{ asset('/'.$data->approver()->short_signature) }}"
                                         alt="short_signature">
                                @endif
                            </p>
                            @if(@$data->subApprover()->position_level == config('app.position_level_president'))
                                <p>{{ @$data->forcompany->subApprover }}</p>
                            @else
                                <p>{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}</p>
                            @endif
                            <img style="height: 60px;"
                                 src="{{ asset('/'.@$data->subApprover()->signature) }}"
                                 alt="Signature">
                            <p>{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->name }}</p>
                        @else
                            <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                            <p>

                              អនុម័តដោយ៖

                              @if (@$data->shortSign()->approve_status == config('app.approve_status_approve'))
                                <img style="height: 15px;"
                                      title="{{ $data->shortSign()->name }}"
                                      src="{{ asset('/'.@$data->shortSign()->short_signature) }}"
                                      alt="short_signature">
                              @endif

                            </p>

                            @if(@$data->subApprover()->position_level == config('app.position_level_president'))
                                <p>{{ @$data->forcompany->subApprover }}</p>
                            @else
                                <p>{{ @json_decode(@$data->subApprover()->user_object)->position_name ?: @$data->subApprover()->position->name_km }}</p>
                            @endif
                        @endif
                    </div>
                  </div>

                @else

                  <?php
                    $relatedCol = count($data->reviewers());
                    $allCol = $relatedCol + 2;
                  ?>

                  <div class="signature">
                      <div style="width: {{ (100/$allCol).'%' }}">
                          <p>
                              Date: {{ ($data->created_at->format('d')) }}
                              -{{ ($data->created_at->format('M')) }}-
                              {{ ($data->created_at->format('Y')) }}
                          </p>

                          <p>ប្រគល់ដេាយ <br>Delivered by</p>
                          <p>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</p>
                          <img style="height: 60px;"
                               src="{{ asset('/'.$data->requester()->signature) }}"
                               alt="Signature">
                          <p>{{ @$data->creator_object->name_en ?: $data->requester()->name_en }}</p>
                      </div>

                      <div style="width: {{ (($relatedCol*100)/$allCol).'%' }}">
                          @foreach($data->reviewers() as $item)
                              @if ($item->approve_status == config('app.approve_status_approve'))
                                  <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                      <p>
                                          Date: {{ (\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('d')) }}
                                          -{{ (\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('M')) }}-
                                          {{ (\Carbon\Carbon::createFromTimestamp(strtotime($item->approved_at))->format('Y')) }}
                                      </p>
                                      <p>ទទួលដេាយ<br>Received by</p>
                                      <p>{{ @json_decode($item->user_object)->position_name ?: $item->position_name }}</p>
                                      <img style="height: 60px;"
                                           src="{{ asset('/'.$item->signature) }}"
                                           alt="Signature">
                                      <p>{{ @json_decode($item->user_object)->name_en ?: $item->name_en }}</p>
                                  </div>
                              @else
                                  <div class="related" style="width: {{ 100/$relatedCol }}%;">
                                      <p>Day.....Month.....Year.....</p>
                                      <p>ទទួលដេាយ<br>Received by</p>
                                      <p>{{ @json_decode($item->user_object)->position_name ?: $item->position_name }}</p>
                                  </div>
                              @endif
                          @endforeach
                      </div>

                      <div style="width: {{ (100/$allCol).'%' }}">
                          @if (@$data->approver()->approve_status == config('app.approve_status_approve'))
                              <p>
                                  Date: {{ (\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('d')) }}
                                  -{{ (\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('M')) }}-
                                  {{ (\Carbon\Carbon::createFromTimestamp(strtotime($data->approver()->approved_at))->format('Y')) }}
                              </p>
                              <p>បានពិនិត្យដេាយ<br>Checked by</p>
                              @if(@$data->approver()->position_level == config('app.position_level_president'))
                                  <p>{{ @$data->forcompany->approver }}</p>
                              @else
                                  <p>{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}</p>
                              @endif
                              <img style="height: 60px;"
                                   src="{{ asset('/'.$data->approver()->signature) }}"
                                   alt="Signature">
                              <p>
                                {{ @check_nickname(@$data->approver()->position_level, $data->created_at ) }}
                                {{ @json_decode(@$data->approver()->user_object)->name_en ?: $data->approver()->name_en }}
                              </p>
                          @else
                              <p>Day.....Month.....Year.....</p>
                              <p>បានពិនិត្យដេាយ<br>Checked by</p>
                              @if(@$data->approver()->position_level == config('app.position_level_president'))
                                  <p>{{ @$data->forcompany->approver }}</p>
                              @else
                                  <p>{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}</p>
                              @endif
                          @endif
                      </div>
                    </div>

                  @endif

              </div>

              {{-- @include('request.approve_section') --}}

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

  @include('global.comment_modal', ['route' =>route('request_grn.reject', $data->id)])
  @include('global.disable_modal', ['route_disable' => route('request_grn.disable', $data->id)])

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
                  url: "{{ action('RequestGRNController@approve', $data->id) }}",
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
