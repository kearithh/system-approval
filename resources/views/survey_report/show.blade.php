<!DOCTYPE html>
<html>
<head>
    <title>E-Approval</title>
    <meta charset="UTF-8">
    @if(! config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

        @include('adminlte::plugins', ['type' => 'css'])

        @yield('adminlte_css_pre')

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

        @yield('adminlte_css')
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
    {{--<link href="/bootstrap3-wysihtml5.min.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
          font-family: 'Times New Roman','Khmer OS Content';
          font-weight: 400;
          font-size: 15px;
          line-height: normal !important;
        }

        h1 {
            font-family: 'Khmer OS Muol Light';
            font-size: 15px;
            margin: 5px 0 10px 0 !important;
        }

        p, span, b {
            font-family: 'Khmer OS Content' !important;
            font-size: 15px !important;
            margin: 0 0 0 0 !important;
        }

        .header{
          text-align: center;
        }

        .sign{
          padding: 15px 0 0 0;
        }

        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100% !important;
        }

        tr{
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

        .logo {
          text-align: center !important;
          padding-top: 0;
          margin-bottom: 20px;
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

        .footer img{
            margin-bottom: 0 !important;
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

          .table-bordered td, .table-bordered th {
              border: 1px solid #1D1D1D !important;
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

        table.table td, table.table th {
            padding-top: 0rem;
            padding-bottom: 0rem;
            vertical-align: middle;
            padding-left: 0.25rem;
            padding-right: 0.25rem;
            font-size: 15px;
        }

        .contain{
          padding-left: 90px;
          padding-right: 90px;
          width: 880px;
        }

        .row {
          display: -webkit-box;
          display: -webkit-flex;
          display: -ms-flexbox;
          display: flex;
          flex-wrap: wrap;
        }
        /*.row > [class*='col-'] {
          display: flex;
          flex-direction: column;
        }
*/
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
          @if(!can_approve_reject($data, config('app.survey_report')))
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

            <div class="header">
                <img src="{{ asset($data->forcompany->logo) }}" alt="logo" style="height: 90px">
                <h1>{{ $data->requester()->position->name_km }}</h1>
                <h1>សូមគោរពជូន</h1>
                <h1 style="font-size: 16px">
                    @if ($data->requester()->branch_id)
                        {{ @$data->approver()->position_name }}
                    @else
                        {{ @$data->forcompany->approver}}
                    @endif
                    {{ $data->forcompany->long_name }}
                </h1>
            </div>
            <div class="body">
                <table  class="table table-borderless mb-0">
                    <tr>
                        <td style="width: 50px; vertical-align: top">
                            <h1>តាមរយៈ</h1>
                        </td>
                        <td class="text-left" style="vertical-align: top">
                            @foreach($data->reviewers() as $reviewer)
                                <p class="mb-0">{{ $reviewer->position_name }}</p>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="vertical-align: top">
                            <h1>សូមរាយការណ៍</h1>
                        </td>
                    </tr>
                </table>

                <p class="text-primary"><b>I. ផ្នែករដ្ឋបាល</b></p>
                <table id="item">
                <thead>
                  <tr>
                      <td style="min-width: 50px">ល.រ</td>
                      <td style="min-width: 200px">ឈ្មោះ</td>
                      <td style="min-width: 100px">បានធ្វើ ឬមិនបានធ្វើ</td>
                      <td style="min-width: 300px">ករណីមានបញ្ហា សូមបញ្ជាក់មូលហេតុ</td>
                  </tr>
                </thead>
                <tbody>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">1</td>
                      <td>គ្រប់គ្រង Assets ចូលថ្មី</td>
                      <td>
                        @if(@$data->admin->asset_new == 1)
                          បានធ្វើ
                        @elseif(@$data->admin->asset_new == 2)
                          មិនបានធ្វើ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->admin->asset_new_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">2</td>
                      <td>ការពិនិត្យ និងបិទកូដលើ Assets ថ្មី</td>
                      <td>
                        @if(@$data->admin->asset_review_new == 1)
                          បានធ្វើ
                        @elseif(@$data->admin->asset_review_new == 2)
                          មិនបានធ្វើ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->admin->asset_review_new_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">3</td>
                      <td>ធ្វើកំណត់ហេតុលើ Assets ខូច</td>
                      <td>
                        @if(@$data->admin->asset_old == 1)
                          បានធ្វើ
                        @elseif(@$data->admin->asset_old == 2)
                          មិនបានធ្វើ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->admin->asset_old_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">4</td>
                      <td>ធ្វើលិខិតប្រគល់/ទទួល ការផ្ទេ Assets</td>
                      <td>
                        @if(@$data->admin->asset_receive == 1)
                          បានធ្វើ
                        @elseif(@$data->admin->asset_receive == 2)
                          មិនបានធ្វើ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->admin->asset_receive_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">5</td>
                      <td>ពិនិត្យការរៀបចំទុកដាក់ឯកសារកម្ចីថ្មី</td>
                      <td>
                        @if(@$data->admin->loan_save == 1)
                          បានធ្វើ
                        @elseif(@$data->admin->loan_save == 2)
                          មិនបានធ្វើ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->admin->loan_save_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">6</td>
                      <td>ការពិនិត្យការទុកដាក់ឯកសារគណនេយ្យ</td>
                      <td>
                        @if(@$data->admin->account_save == 1)
                          បានធ្វើ
                        @elseif(@$data->admin->account_save == 2)
                          មិនបានធ្វើ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->admin->account_save_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">7</td>
                      <td>ការពិនិត្យការទុកដាក់ឯកសារទ្រព្យធានាកម្ចី</td>
                      <td>
                        @if(@$data->admin->property_save == 1)
                          បានធ្វើ
                        @elseif(@$data->admin->property_save == 2)
                          មិនបានធ្វើ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->admin->property_save_node }}</td>
                  </tr>
                </tbody>
              </table>
              <br>
              <p class="text-primary"><b>II. ផ្នែកហិរញ្ញវត្ថុ</b></p>
              <table id="item">
                <thead>
                  <tr>
                      <td style="min-width: 50px">ល.រ</td>
                      <td style="min-width: 200px">ឈ្មោះ</td>
                      <td style="min-width: 100px">បានធ្វើ ឬមិនបានធ្វើ</td>
                      <td style="min-width: 300px">ករណីមានបញ្ហា សូមបញ្ជាក់មូលហេតុ</td>
                  </tr>
                </thead>
                <tbody>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">1</td>
                      <td>ការគ្រប់គ្រងសាច់ប្រាក់</td>
                      <td>
                        @if(@$data->finance->cash_manage == 1)
                          បានរាប់
                        @elseif(@$data->finance->cash_manage == 2)
                          មិនបានរាប់
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->finance->cash_manage_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">2</td>
                      <td>ពិនិត្យសាច់ប្រាក់លើស/ខ្វះ(ត្រូវមានកំណត់ហេតុ)</td>
                      <td>
                        @if(@$data->finance->cash_manage_doc == 1)
                          បានធ្វើ
                        @elseif(@$data->finance->cash_manage_doc == 2)
                          មិនបានធ្វើ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->finance->cash_manage_doc_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">3</td>
                      <td>ពិនិត្យ ការខ្ចប់សាច់ប្រាក់ប្រចាំថ្ងៃ</td>
                      <td>
                        @if(@$data->finance->daily_cash == 1)
                          មាន
                        @elseif(@$data->finance->daily_cash == 2)
                          មិនមាន
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->finance->daily_cash_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">4</td>
                      <td>ពិនិត្យនិង អនុម័តលើឯកសារចំណាយ</td>
                      <td>
                        @if(@$data->finance->approve_expense == 1)
                          បានពិនិត្យ និងអនុម័ត
                        @elseif(@$data->finance->approve_expense == 2)
                          មិនបានពិនិត្យ និងអនុម័ត
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->finance->approve_expense_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">5</td>
                      <td>ពិនិត្យការបិទបញ្ជី (MB win)</td>
                      <td>
                        @if(@$data->finance->check_mb == 1)
                          បានពិនិត្យ
                        @elseif(@$data->finance->check_mb == 2)
                          មិនបានពិនិត្យ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->finance->check_mb_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">6</td>
                      <td>ពិនិត្យលើការ Post WO</td>
                      <td>
                        @if(@$data->finance->check_wo == 1)
                          បានពិនិត្យត្រឹមត្រូវ
                        @elseif(@$data->finance->check_wo == 2)
                          មិនបានពិនិត្យត្រឹមត្រូវ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->finance->check_wo_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">7</td>
                      <td>ប្រតិបត្តិការសាច់ប្រាក់នៅធនាគារ</td>
                      <td>
                        @if(@$data->finance->cash_bank == 1)
                          បានពិនិត្យ
                        @elseif(@$data->finance->cash_bank == 2)
                          មិនបានពិនិត្យ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->finance->cash_bank_node }}</td>
                  </tr>
                </tbody>
              </table>
              <br>
              <p class="text-primary"><b>III. ផ្នែកធនធានមនុស្ស</b></p>
              <table id="item">
                <thead>
                  <tr>
                      <td style="min-width: 50px">ល.រ</td>
                      <td style="min-width: 200px">ឈ្មោះ</td>
                      <td style="min-width: 100px">បានធ្វើ ឬមិនបានធ្វើ</td>
                      <td style="min-width: 300px">ករណីមានបញ្ហា សូមបញ្ជាក់មូលហេតុ</td>
                  </tr>
                </thead>
                <tbody>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">1</td>
                      <td>ត្រួតពិនិត្យថ្ងៃចូលធ្វើការរបស់បុគ្គលិកថ្មី/ប្តូរតួនាទី/ប្តូរសាខា</td>
                      <td>
                        @if(@$data->hr->date_staff == 1)
                          បានជូនដំណឹង
                        @elseif(@$data->hr->date_staff == 2)
                          មិនបានជូនដំណឹង
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->hr->date_staff_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">2</td>
                      <td>ពិនិត្យការខ្វះបុគ្គលិក</td>
                      <td>
                        @if(@$data->hr->staff_lack == 1)
                          បានពិនិត្យ
                        @elseif(@$data->hr->staff_lack == 2)
                          មិនបានពិនិត្យ
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->hr->staff_lack_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">3</td>
                      <td>ការចុះ Home Visit បុគ្គលិគថ្មី</td>
                      <td>
                        @if(@$data->hr->home_visit == 1)
                          បានចុះ Home Visit
                        @elseif(@$data->hr->home_visit == 2)
                          មិនបានចុះ Home Visit
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->hr->home_visit_node }}</td>
                  </tr>
                  <tr style="vertical-align: middle;">
                      <td class="text-center">4</td>
                      <td>ការចុះដោះស្រាយបុគ្គលិករំលោភកិច្ចសន្យា</td>
                      <td>
                        @if(@$data->hr->violate_contract == 1)
                          បានចុះទាន់ពេល
                        @elseif(@$data->hr->violate_contract == 2)
                          មិនបានចុះទាន់ពេល
                        @else
                          មានបញ្ហា
                        @endif
                      </td>
                      <td>{{ @$data->hr->violate_contract_node }}</td>
                  </tr>
                </tbody>
              </table>
              <br>
              <p class="text-primary"><b>IV. ផ្នែកប្រតិបត្តិការ</b></p>
              <p class="text-success"><b>1. ចំនួនអតិថិជនបានផ្សព្វផ្សាយក្នុងខែ</b></p>
              <b>{{ number_format(@$data->number_customer) }}</b>
              <br>
              <p class="text-success"><b>2. ប្រៀបធៀបលទ្ធផលប្រចាំខែ</b></p>
              <table id="item">
                <thead>
                  <tr>
                      <td style="min-width: 50px">ល.រ</td>
                      <td style="min-width: 200px">បរិយាយលទ្ធិផល</td>
                      <td style="min-width: 100px">ចំនូន</td>
                      <td style="min-width: 100px">លំអៀងធៀបខែចាស់</td>
                      <td style="min-width: 100px">ចំនួនកើន ឬថយ</td>
                      <td style="min-width: 100px">ករណីមិនសម្រចបញ្ជាក់មូលហេតុ</td>
                  </tr>
                </thead>
                <tbody>
                  @if(@$data->compare_monthly)
                    <?php 
                        $compare_monthly = is_array(@$data->compare_monthly) ? @$data->compare_monthly : json_decode(@$data->compare_monthly); 
                    ?>
                    @foreach(@$compare_monthly as $key => $monthly )
                      <tr style="vertical-align: middle;">
                          <td class="text-center">{{ $key +1 }}</td>
                          <td>{{ @$monthly->name }}</td>
                          <td>{{ number_format(@$monthly->total) }}</td>
                          <td class="text-center">
                            @foreach(config('app.bias_type') as $key => $value)
                              @if ($value->val == @$monthly->bias)
                                {{ $value->name_km }}
                              @endif
                            @endforeach
                          </td>
                          <td>{{ number_format(@$monthly->amount) }}</td>
                          <td>{{ @$monthly->reason }}</td>
                      </tr>
                    @endforeach
                  @endif
                </tbody>
              </table>

              <br>
              <p class="text-success"><b>3. ប្រៀបធៀបលទ្ធផលប្រចាំថ្ងៃ</b></p>
              <table id="item">
                <thead>
                  <tr>
                      <td style="min-width: 50px">ល.រ</td>
                      <td style="min-width: 200px">បរិយាយលទ្ធិផល</td>
                      <td style="min-width: 100px">ចំនូន</td>
                      <td style="min-width: 100px">លំអៀងធៀបថ្ងែចាស់</td>
                      <td style="min-width: 100px">ចំនួនកើន ឬថយ</td>
                      <td style="min-width: 100px">ករណីមិនសម្រចបញ្ជាក់មូលហេតុ</td>
                  </tr>
                </thead>
                <tbody>
                  @if(@$data->compare_daily)
                    <?php 
                        $compare_daily = is_array(@$data->compare_daily) ? @$data->compare_daily : json_decode(@$data->compare_daily); 
                    ?>
                    @foreach(@$compare_daily as $key => $daily)
                      <tr style="vertical-align: middle;">
                          <td class="text-center">{{ $key +1 }}</td>
                          <td>{{ @$daily->name }}</td>
                          <td>{{ number_format(@$daily->total) }}</td>
                          <td class="text-center">
                            @foreach(config('app.bias_type') as $key => $value)
                              @if ($value->val == @$daily->bias)
                                {{ $value->name_km }}
                              @endif
                            @endforeach
                          </td>
                          <td>{{ number_format(@$daily->amount) }}</td>
                          <td>{{ @$daily->reason }}</td>
                      </tr>
                    @endforeach
                  @endif
                </tbody>
              </table>

              <br>
              <p class="text-success"><b>4. ប្រៀបធៀបលទ្ធផលជាមួយផែនការ</b></p>
              <table id="item">
                <thead>
                  <tr>
                      <td style="min-width: 50px">ល.រ</td>
                      <td style="min-width: 200px">បរិយាយលទ្ធិផល</td>
                      <td style="min-width: 100px">ចំនូន</td>
                      <td style="min-width: 100px">លំអៀងធៀបផែនកា</td>
                      <td style="min-width: 100px">ចំនួនកើន ឬថយ</td>
                      <td style="min-width: 100px">ករណីមិនសម្រចបញ្ជាក់មូលហេតុ</td>
                  </tr>
                </thead>
                <tbody>
                  @if(@$data->compare_plan)
                    <?php 
                        $compare_plan = is_array(@$data->compare_plan) ? @$data->compare_plan : json_decode(@$data->compare_plan); 
                    ?>
                    @foreach(@$compare_plan as $key => $plan)
                      <tr style="vertical-align: middle;">
                          <td class="text-center">{{ $key +1 }}</td>
                          <td>{{ @$plan->name }}</td>
                          <td>{{ number_format(@$plan->total) }}</td>
                          <td class="text-center">
                            @foreach(config('app.bias_type') as $key => $value)
                              @if ($value->val == @$plan->bias)
                                {{ $value->name_km }}
                              @endif
                            @endforeach
                          </td>
                          <td>{{ number_format(@$plan->amount) }}</td>
                          <td>{{ @$plan->reason }}</td>
                      </tr>
                    @endforeach
                  @endif
                </tbody>
              </table>

            </div>

            @include('survey_report.partials.approve_section')
            
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

  @include('global.comment_modal', ['route' =>route('survey_report.reject', $data->id)])
</body>
@if(! config('adminlte.enabled_laravel_mix'))
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery.inputmask.bundle.min.js') }}"></script>
    @include('adminlte::plugins', ['type' => 'js'])
    <scrypt src="/bootstrap3-wysihtml5.min.js"></scrypt>

    @yield('adminlte_js')
@else
    {{--<script src="{{ asset('js/app.js') }}"></script>--}}
@endif
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
                  url: "{{ action('SurveyReportController@approve', $data->id) }}",
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
