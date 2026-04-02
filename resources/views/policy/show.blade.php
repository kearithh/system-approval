<!DOCTYPE html>
<html>
<head>
    <title>E-Approval</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
          margin-top: 15px;
          margin-bottom: 15px;
        }

        p {
          font-family: 'Times New Roman','Khmer OS Content';
          font-size: 16px;
          margin: 0 0 5px;
        }

        .header{
          text-align: center;
          /*text-decoration-line: underline;
          text-decoration-style: double;*/
        }

        .content {
          width: 1024px;
          padding: 30px; 
          margin: auto;
          background: white; 
          min-height: 1440px;
        }

        .body {
          border: 1px solid black !important;
          padding: 30px;
          min-height: 1380px;
        }

        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100% !important;
        }

        th, td {
          vertical-align: middle !important;
          height: 60px;
        }

        table.table_paragrap tr td {
          padding-top: 0.7rem;
          padding-bottom: 0.7rem;
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

        @page {
          size: A4;
          margin: 0 !important;
        }

        @media print {
          button {
            display: none;
          }

          .table-bordered td, .table-bordered th {
              border: 1px solid #1D1D1D !important;
          }

          body {
            margin: 0;
          }
          #action_container, #attach_file {
            display: none;
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
          @if(!can_approve_reject($data, config('app.custom_letter')))
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
        $data->reviewers_short()->merge($data->reviewers())->push($data->approver())
    ])
  </div>

  <div class="content">
    <div class="body">
      <h1>ប្រវត្តិនៃការកែសម្រួល</h1>
      <table class="table table-bordered text-center">
        <tr>
          <th style="width: 20%">លេខកែសម្រួល</th>
          <th style="width: 20%">ថ្ងៃសុពលភាព</th>
          <th style="width: 35%">បរិយាយការកែសម្រួល</th>
          <th style="width: 25%">លេខយោង</th>
        </tr>
        <tr>
          <td>{{ @$data->number_edit }}</td>
          <td>
            {{ khmer_number((\Carbon\Carbon::createFromTimestamp(strtotime(@$data->validity_date))->format('d'))) }} 
            {{ khmer_month((\Carbon\Carbon::createFromTimestamp(strtotime(@$data->validity_date))->format('m'))) }}  
            {{ khmer_number((\Carbon\Carbon::createFromTimestamp(strtotime(@$data->validity_date))->format('Y'))) }}
          </td>
          <td>{{ @$data->description }}</td>
          <td>{{ @$data->footnote }}</td>
        </tr>
      </table>
      <br>
      <h1>ការរៀបចំ និងអនុម័តឲ្យប្រើប្រាស់</h1>
      <table class="table table-bordered text-center">
        <tr>
          <th style="width: 25%">ទទួលខុសត្រូវ</th>
          <th style="width: 32%">ឈ្មោះ និងតួនាទី</th>
          <th style="width: 25%">ហត្ថលេខា</th>
          <th style="width: 18%">កាលបរិច្ឆេទ</th>
        </tr>
        <tr>
          <td class="text-left">រៀបចំដោយ</td>
          <td>
            <h1>{{ @$data->creator_object->name ?: $data->requester()->name }}</h1>
            <p>{{ @$data->creator_object->position_name ?: $data->requester()->position->name_km }}</p>
          </td>
          <td>
            <img style="height: 60px" src="{{ asset('/'.$data->requester()->signature) }}" alt="signature">
          </td>
          <td>
            {{ khmer_number($data->created_at->format('d')) }} 
            {{ khmer_month($data->created_at->format('m')) }}  
            {{ khmer_number($data->created_at->format('Y')) }}
          </td>
        </tr>
        @foreach($data->reviewers() as $reviewer)
          <tr>
            <td class="text-left">ត្រួតពិនិត្យដោយ</td>
            <td>
              <h1>{{ @json_decode(@$reviewer->user_object)->name ?: @$reviewer->name }}</h1>
              <p>{{ @json_decode(@$reviewer->user_object)->position_name ?: @$reviewer->position->name_km }}</p>
            </td>
            @if($reviewer->approve_status== config('app.approve_status_approve'))
              <td>
                <img style="height: 60px" src="{{ asset('/'.@$reviewer->signature) }}" alt="signature">
              </td>
              <td>
                {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                {{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('m')) }}
                {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
              </td>
            @else 
              <td></td>
              <td></td>
            @endif
          </tr>
        @endforeach
        <tr>
          <td class="text-left">ត្រួតពិនិត្យ និងអនុម័ត</td>
          <td>
            <h1>
              {{ @check_nickname(@$data->approver()->position_level, $data->created_at ) }}
              {{ @json_decode(@$data->approver()->user_object)->name ?: @$data->approver()->name }}
            </h1>
            @if(@$data->approver()->position_level == config('app.position_level_president'))
              <p>{{ @$data->forcompany->approver }}</p>
            @else
              <p>{{ @json_decode(@$data->approver()->user_object)->position_name ?: @$data->approver()->position->name_km }}</p>
            @endif
          </td>
          @if(@$data->approver()->approve_status == config('app.approve_status_approve'))
            <td>
              <img style="height: 60px" src="{{ asset('/'.@$data->approver()->signature) }}" alt="signature">
            </td>
            <td>
              {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d')) }} 
              {{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('m')) }}
              {{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('Y')) }}
            </td>
          @else
            <td></td>
            <td></td>
          @endif
        </tr>
      </table>
    </div>

  </div>

  @include('global.comment_modal', ['route' =>route('policy.reject', $data->id)])
  <div id="attach_file" style="width: 1024px; margin: auto;background: white; min-height: 1355px;">
    @if(@$data->attachment)
        @if(is_array($data->attachment))
            <?php $atts =  $data->attachment; ?>
            @foreach($atts as $att )
                <iframe src="{{ asset($att->src) }}#view=FitH" width="100%" height="1355px"></iframe>
            @endforeach
        @endif
    @endif
  </div>
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
                  url: "{{ action('PolicyController@approve', $data->id) }}",
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
