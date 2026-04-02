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
        <button id="back" type="button" class="btn btn-sm btn-secondary"> Back</button>
      </div>

    </div><br><br>
    <div id="reviewer" style="padding: 15px; margin-bottom: 5px">
      @if(@$data->attachment)
        <span>ស្នើរដោយៈ</span>
        <span>{{ @$data->requester->name }}</span><br>
        <span>ចំណងជើងៈ</span>
        <span style="color: #32B222;">{{@$data->title}}</span><br>            
        <span>ឯកសារភ្ជាប់ៈ</span>
        <a href="{{ asset($data->attachment->src) }}" target="_self">{{ $data->attachment->org_name }}</a><br>
      @endif
    </div>

  </div>

  <div id="attach_file" style="width: 1024px; margin: auto;background: white; min-height: 1355px;">
    @if(@$data->attachment)
      <iframe src="{{ asset($data->attachment->src) }}#view=FitH" width="100%" height="1355px"></iframe>
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

</script>
</html>
