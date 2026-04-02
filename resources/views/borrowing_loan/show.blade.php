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
          font-size: 18px;
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
          /*margin: 15px;*/
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

        .title_desc {
          font-family: 'Times New Roman','Khmer OS Muol Light';
        }

        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100% !important;
        }

        table.table td, table.table th {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            vertical-align: middle;
            padding-left: .25rem;
            padding-right: .25rem;
        }

        table.table_paragrap tr td {
          padding-top: 0.7rem;
          padding-bottom: 0.7rem;
          vertical-align: top;
        }

        .footer_paragrap {
          padding-top: 0.7rem;
          padding-bottom: 0.7rem;
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
          padding-left: 70px;
          padding-right: 70px;
          width: 880px;
          text-align: justify;
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
        $data->reviewers()->push($data->approver())
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
           <!--  {!! $data->forcompany->header_section  !!} -->
            <div class="header">
              <h1>ព្រះរាជាណាចក្រកម្ពុជា</h1>
              <h1>ជាតិ សាសនា ព្រះមហាក្សត្រ</h1>
              <img src="{{ asset('/img/logo/font_tt_borrowing.png') }}" width="150">
              <h1><u>កិច្ចសន្យាទទួលប្រាក់កម្ចី</u></h1>
            </div>
            <div class="body">
              <div class="text-center">
                <p>
                  កិច្ចសន្យានេះធ្វើឡើងនៅ {{ @$data->forbranch->name_km }} 
                  ថ្ងៃទី {{ khmer_number(@$data->created_at->format('d')) }} 
                  ខែ {{ khmer_month(@$data->created_at->format('m')) }} 
                  ឆ្នាំ {{ khmer_number($data->created_at->format('Y')) }} 
                </p>
                <h1><u>រវាងៈ</u></h1>
              </div>
              <span> 
                <strong><u>ភាគីកូនបំណុល៖</u></strong> ក្រុមហ៊ុន <strong> សហគ្រិនភាព អេសធីអេសខេ លីមីតធីត </strong>
                តំណាងពេញច្បាប់ ដោយ{{ @$data->debtor_obj->title }} {{ @$data->debtor_obj->name }}
                អត្តសញ្ញាណប័ណ្ណលេខ {{ @$data->debtor_obj->nid }}
                តួនាទី {{ @$data->debtor_obj->position }}
                លេខទូរស័ព្ទ {{ @$data->debtor_obj->phone }}
                មានអាសយដ្ឋានចុះបញ្ជី ផ្ទះលេខ០៩ ផ្លូវលេខ ១១៥ ភូមិ តាក្តុល សង្កាត់ តាក្តុល ក្រុង តាខ្មៅ ខេត្តកណ្តាល។
              </span>

              <div class="text-center">
                <h1><u>និងៈ</u></h1>
              </div>
                <span> 
                  <strong><u>ភាគីម្ចាស់បំណុល៖</u></strong> 
                  {{ @$data->creditor_obj->title }} {{ @$data->creditor_obj->name }}
                  មានអត្តសញ្ញាណប័ណ្ណសញ្ជាតិខ្មែរលេខ {{ @$data->creditor_obj->nid }}
                  មានអាសយដ្ឋានបច្ចុប្បន្ននៅផ្ទះលេខ {{ @$data->creditor_obj->home }}
                  ផ្លូវលេខ {{ @$data->creditor_obj->street }} 
                  ភូមិ {{ @$data->creditor_obj->village }}
                  សង្កាត់/ឃុំ {{ @$data->creditor_obj->commune }}
                  ខណ្ឌ/ស្រុក {{ @$data->creditor_obj->district }} 
                  ក្រុង/ខេត្ត {{ @$data->creditor_obj->province }} ។
                </span><br>
                
                <b>ក្រោយពីបានពិភាក្សាគ្នារួចមកភាគី “ម្ចាស់បំណុល” និងភាគី “កូនបំណុល” បានព្រមព្រៀងគ្នាតាមលក្ខខណ្ឌដូចខាងក្រោម</b><br>
                
                <h1> ប្រការ១៖  លក្ខខណ្ឌរួម </h1>
                
                <ul>
                  <li>
                    ទឹកប្រាក់ខ្ចីៈ 
                    @if($data->currency == 'KHR')
                        {{ number_format(@$data->amount_number) .' ៛'}}
                    @else
                        {{'$ '. number_format((@$data->amount_number), 2) }}
                    @endif
                    ({{ @$data->amount_text }})
                  </li>
                  <li>រយៈពេលខ្ចី {{ @$data->period }} ខែ
                    គិតចាប់ពីថ្ងៃទី {{ khmer_number(@$data->from->format('d')) }} 
                    ខែ {{ khmer_month(@$data->from->format('m')) }} 
                    ឆ្នាំ {{ khmer_number($data->from->format('Y')) }} 
                    ដល់ថ្ងៃទី {{ khmer_number(@$data->to->format('d')) }}  
                    ខែ {{ khmer_month(@$data->to->format('m')) }} 
                    ឆ្នាំ {{ khmer_number(@$data->to->format('Y')) }}
                  </li>
                  <li>
                    អត្រាការប្រាក់ប្រចាំឆ្នាំ ស្មើនឹង {{ @$data->interest }}% 
                    ដែលភាគី <b>“កូនបំណុល”</b> ត្រូវបង់ជូនភាគី <b>“ម្ចាស់បំណុល”</b>។<br>
                    ការគណនាការប្រាក់ត្រូវផ្អែកលើសមតុល្យប្រាក់ កម្ចី ដោយគិតលើមូលដ្ឋាន៣៦៥ថ្ងៃ ក្នុងមួយឆ្នាំ។
                  </li>
                  <li>
                    របៀបសងប្រាក់ៈ 
                    <br>
                    − ការប្រាក់ត្រូវគណនាត្រឹមចុងខែ និងត្រូវទូទាត់មិនឲ្យលើសពីថ្ងៃទី០៥នៃខែបន្ទាប់។
                    <br>
                    − ប្រាក់ដើមត្រូវទូទាត់វិញនៅថ្ងៃបញ្ចប់កិច្ចសន្យា។
                  </li>
                </ul>

                <h1> ប្រការ២៖ លក្ខខណ្ឌសងប្រាក់ </h1>

                <table>
                  <tr>
                    <td style="vertical-align: top; width: 50px;"> ២.១. </td>
                    <td>
                      ភាគី <b>“កូនបំណុល”</b> ត្រូវសងប្រាក់អោយបានត្រឹមត្រូវតាមកាលបរិច្ឆេទត្រូវសងដូចមានក្នុងតារាងកាលវិភាគសងប្រាក់ដែលបានភ្ជាប់ជាមួយ។
                    </td>
                  </tr>
                  <tr>
                    <td style="vertical-align: top"> ២.២. </td>
                    <td>
                      ក្នុងករណីភាគី <b>“កូនបំណុល”</b> ខកខានមិនបានសងប្រាក់ លើសពី៧ថ្ងៃ តាមតារាងកាលវិភាគសងប្រាក់នោះទេ ភាគី <b>“កូនបំណុល”</b> យល់ព្រមបង់បន្ថែមនូវប្រាក់ពិន័យអោយភាគី <b>“ម្ចាស់បំណុល”</b>។ ចំនួនប្រាក់ពិន័យនេះ ត្រូវគណនា ស្មើនឹងប្រាក់ដែលខកខានមិនបានសងគុណនឹងអត្រា 3% ក្នុងមួយខែ។
                    </td>
                  </tr>
                  <tr>
                    <td style="vertical-align: top"> ២.៣. </td>
                    <td>
                      ករណីភាគី <b>“ម្ចាស់បំណុល”</b> មានបំណងដកប្រាក់មុនកាលកំណត់ត្រូវជូនដំណឹងដល់ភាគី <b>“កូនបំណុល”</b> អោយបានមុនយ៉ាងតិចរយៈពេល៣០ថ្ងៃ។ ហើយម្ចាស់បំណុលនឹងមិនទទួលបានការប្រាក់រយៈពេល៣០ថ្ងៃនេះឡើយ។
                    </td>
                  </tr>
                </table>

                <h1> ប្រការ៣៖ មធ្យោបាយនៃការផ្ទេរប្រាក់ </h1>

                <table>
                  <tr>
                    <td style="vertical-align: top; width: 50px;"> ៣.១. </td>
                    <td>
                      ភាគី <b>“ម្ចាស់បំណុល”</b> 
                      ត្រូវផ្ទេរប្រាក់ដូចប្រការ០១ ចូលទៅក្នុងគណនី ធនាគាររបស់ភាគី <b>“កូនបំណុល”</b> នៅ<br>
                      − ធនាគារ SATHAPANA BANK ដែលមានឈ្មោះគណនីៈ Kuon Thida និងលេខគណនីៈ 00319962 (ប្រាក់រៀល)។<br>
                      − ធនាគារ SATHAPANA BANK ដែលមានឈ្មោះគណនីៈ Kuon Thida និងលេខគណនីៈ 00319921 (ប្រាក់ដុល្លារ)។<br>
                      − ឬអាចមកប្រគល់នៅការិយាល័យ ដែលមានអាសយដ្ឋាន 
                      បច្ចុប្បន្ននៅផ្ទះលេខ {{ @$data->debtor_transfer->home }} 
                      ផ្លូវលេខ {{ @$data->debtor_transfer->street }} 
                      ភូមិ {{ @$data->debtor_transfer->village }} 
                      សង្កាត់/ឃុំ {{ @$data->debtor_transfer->commune }} 
                      ខណ្ឌ/ស្រុក {{ @$data->debtor_transfer->district }} 
                      ក្រុង/ខេត្ត {{ @$data->debtor_transfer->province }} ។
                    </td>
                  </tr>
                  <tr>
                    <td style="vertical-align: top"> ៣.២. </td>
                    <td>
                      ភាគី <b>“កូនបំណុល”</b> ត្រូវផ្ទេរប្រាក់ដូចក្នុងតារាងកាលវិភាគសងប្រាក់ ចូលទៅក្នុងគណនីធនាគាររបស់ភាគី <b>“ម្ចាស់បំណុល”</b>  
                      នៅធនាគារ {{ @$data->creditor_transfer->bank }} 
                      ដែលមានឈ្មោះគណនីៈ {{ @$data->creditor_transfer->acc_name }} 
                      និងលេខគណនីៈ {{ @$data->creditor_transfer->acc_number }} ។
                    </td>
                  </tr>
                </table>
                
                <h1> ប្រការ៤៖  ច្បាប់គ្រប់គ្រង និងការដោះស្រាយវិវាទ </h1>
                
                 <table>
                  <tr>
                    <td style="vertical-align: top; width: 50px;"> ៤.១. </td>
                    <td>
                      កិច្ចសន្យាទទួលប្រាក់កម្ចីនេះ ត្រូវគ្រប់គ្រងដោយច្បាប់នៃព្រះរាជាណាចក្រកម្ពុជា។
                    </td>
                  </tr>
                  <tr>
                    <td style="vertical-align: top"> ៤.២. </td>
                    <td>
                      ភាគី <b>“ម្ចាស់បំណុល”</b> និងភាគី <b>“កូនបំណុល”</b> ត្រូវធ្វើការដោះស្រាយវិវាទ ដែលកើតឡើងទាក់ទងនឹងកិច្ចសន្យាទទួលប្រាក់កម្ចីនេះ។ ប្រសិនបើការដោះស្រាយដោយការយោគយល់មិនទទួលបានលទ្ធផល នោះវិវាទ ត្រូវដោះស្រាយតាមរយៈប្រព័ន្ធតុលាការនៃព្រះរាជាណាចក្រកម្ពុជា។
                    </td>
                  </tr>
                  <tr>
                    <td style="vertical-align: top"> ៤.៣. </td>
                    <td>
                      ករណីភាគី <b>“កូនបំណុល”</b> បំពានលើកិច្ចសន្យាភាគី <b>“ម្ចាស់បំណុល”</b> ទាមទារអោយភាគី <b>“កូនបំណុល”</b> សងគ្រប់ចំនួននូវប្រាក់ដើម ការប្រាក់ និងទឹកប្រាក់ពិន័យដែលត្រូវបង់ យោងតាមកិច្ចសន្យានេះ។
                    </td>
                  </tr>
                  <tr>
                    <td style="vertical-align: top"> ៤.៤. </td>
                    <td>
                      ករណីមានការបំពានកិច្ចសន្យាដែលឈានទៅដល់ការដោះស្រាយតាមប្រព័ន្ធតុលាការ រាល់ការចំណាយទៅលើព្រហ្មទណ្ឌ តុលាការ និងការចំណាយផ្សេងៗទៀតដែលពាក់ព័ន្ធនឹងជំលោះ ជាបន្ទុករបស់ភាគីរំលោភ លើកិច្ចសន្យា។
                    </td>
                  </tr>
                </table>

                <h1> ប្រការ៥៖ ការទទួលស្គាល់របស់គូភាគី </h1>

                <table>
                  <tr>
                    <td style="vertical-align: top; width: 50px;"> ៥.១. </td>
                    <td>
                      ភាគី <b>“ម្ចាស់បំណុល”</b> និងភាគី <b>“កូនបំណុល”</b> ទទួលស្គាល់ថា កិច្ចសន្យានេះត្រូវបានធ្វើឡើងដោយមានការព្រមព្រៀងពិតប្រាកដ និងដោយសេរី គ្មានការបង្ខិតបង្ខំ ពីភាគីណាមួយឡើយ ហើយមានសុពលភាព និងមានប្រសិទ្ធភាពអនុវត្តចាប់ពីថ្ងៃចុះហត្ថលេខា និងផ្តិតមេដៃស្តាំនេះតទៅ។
                    </td>
                  </tr>
                  <tr>
                    <td style="vertical-align: top"> ៥.២. </td>
                    <td>
                      កិច្ចសន្យានេះត្រូវបានធ្វើឡើង ចំនួន០២ (ពីរ) ច្បាប់ដើមជាភាសាខ្មែរ ដើម្បីតម្កល់ទុកនៅៈ<br>
                      <span>
                        - ភាគី <b>“ម្ចាស់បំណុល”</b> ................................... ១ច្បាប់ដើម<br>
                        - ភាគី <b>“កូនបំណុល”</b> ...................................... ១ច្បាប់ដើម
                        @foreach($data->reviewers() as $key => $value)
                          @if ($value->approve_status == config('app.approve_status_approve'))
                            <img  src="{{ asset($value->short_signature) }}"  
                                  alt="short_sign" 
                                  title="{{ @$value->name }}" 
                                  style="width: 25px;">
                          @endif
                        @endforeach

                        @if(@$data->approver()->approve_status == config('app.approve_status_approve'))
                          <img  src="{{ asset($data->approver()->short_signature) }}"  
                                alt="short_sign" 
                                title="{{ @$data->approver()->name }}" 
                                style="width: 25px;">
                        @endif
                      </span>
                    </td>
                  </tr>
                </table>

                <table>
                  <tr class="text-center">
                    <td style="width: 50%;"> 
                      <h1>ស្នាមមេដៃស្តាំ និងត្រាភាគី “កូនបំណុល”</h1> 
                      <p style="padding-top: 130px;"> {{ @$data->debtor_obj->name }} </p>
                    </td>
                    <td> 
                      <h1>ស្នាមមេដៃស្តាំ ភាគី “ម្ចាស់បំណុល”</h1>
                      <p style="padding-top: 130px;">.............................................................</p>
                    </td>
                  </tr>
                </table>

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
      <!-- {!! $data->forcompany->footer_section  !!} -->
    </div>
  </div>

  @include('global.comment_modal', ['route' =>route('borrowing_loan.reject', $data->id)])
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

  $("#approve_btn").on( "click", function( event ) {
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
                  url: "{{ action('BorrowingLoanController@approve', $data->id) }}",
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

  $( "#reject_btn" ).on( "click", function( event ) {
    event.preventDefault();
    $('#comment_modal').modal('show');
  });

</script>
@include('global.sweet_alert')
</html>
