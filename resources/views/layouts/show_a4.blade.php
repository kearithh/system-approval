<!DOCTYPE html>
<html>
<head>
    <title>{{ @$data->title_km }}</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Latest compiled and minified CSS -->
    <!-- include libraries(jQuery, bootstrap) -->
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>

    <!-- include summernote css/js -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet"> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script> -->
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

        h1, h2{
          font-family: 'Times New Roman','Khmer OS Muol Light';
          font-weight: 400;
        }

        .desc {
          text-align: center;
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
        .related {
          float: left;
          text-align: center;
          /*border: 1px solid;*/
          box-sizing: border-box;
          text-overflow: ellipsis;
        }

        table tr td {
          border: 0px solid #585858;
          padding-top: 5px;
          padding-bottom: 15px;
        }

        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100% !important;
        }

        table thead tr {
          /*background: orange;*/
          font-weight: 700;
          text-align: center;
        }

        table h1 {
          margin-block-start: 17px;
        }

        h2{
          margin-block-start: 17px;
          font-size: 15px !important;
          line-height: normal;
        }

        .desc_p p {
          font-size: 15px !important;
        }
        .desc_p {
          /*text-align: justify ;*/
          /*padding-left: 25px;*/
        }

        table tr td h1, table tr td p, table tr td span {
          margin: 0 !important;
          padding: 0 !important;
          margin-block-start: 0 !important;
          margin-block-end: 1em;
          font-size: 15px !important;

        }

        table#points tbody tr td ul li,
        table#points tbody tr td ol li {
            padding-left: 15px !important;
            margin-left: 0 !important;
        }

        table#points tbody tr td ul,
        table#points tbody tr td ol {
            margin-left: -23px;
        }

        table tbody tr td ul li {
          padding-left: 15px !important;
          margin-left: 0 !important;
        }

        table#points > tbody tr td ul {
          margin-left: -23px !important;
        }

        div.title div h1 {
          font-size: 15px !important;
          /*line-height: 2.5;*/
          margin-top: 20px !important;
          margin-bottom: 10px !important;
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
          height: 50px;
        }

        .page-footer {
          /*position: fixed;*/
          bottom: 0;
          width: 100%;
        }

        .page {
          page-break-after: always;
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

          body {
            margin: 0;
          }
          #action_container {
            display: none;
          }
          .page-footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
          }
        }

        body div p, body div span, body div a {
          line-height: 1.7;
          /*line-height: normal;*/
        }
        .contain{
          padding-left: 95px;
          padding-right: 95px;
          width: 880px;
        }

        .point_width{
          width: 90px;
        }
        .desc_width{
          width: 744px !important;
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

    @yield('content')
    @isset($data)
      <div class="page-footer">
        <div style="width: 1024px; margin: auto;">
          {!! $data->footerSection()  !!}
        </div>
      </div>
    @endisset

</body>

<script src="{{ asset('js/sweetalert2@9.js') }}"></script>

@stack('js')
@include('global.sweet_alert')
</html>
