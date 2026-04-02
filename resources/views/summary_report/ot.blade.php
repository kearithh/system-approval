@extends('adminlte::page', ['activePage' => 'request', 'titlePage' => __('Request')])

@section('plugins.Select2', true)

@section('css')
  <style>
    .table td {
      vertical-align: middle;
    }
  </style>
@stop

@section('content')
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-md-12">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <a class="btn btn-xs bg-success" href="/summary_report/ot-report? status={{ config('app.approve_status_approve') }}" style="font-size: 0.85rem;">
                Total Approved
                <span class="badge badge-light">{{ @$totalApproved }}</span>
              </a>
              <a class="btn btn-xs bg-orange" href="/summary_report/ot-report? status={{ config('app.approve_status_draft') }}" style="font-size: 0.85rem;">
                Total Pending
                <span class="badge badge-light">{{ @$totalPending }}</span>
              </a>
              <a class="btn btn-xs bg-danger" href="/summary_report/ot-report? status={{ config('app.approve_status_reject') }}" style="font-size: 0.85rem;">
                Total Commented
                <span class="badge badge-light">{{ @$totalCommented }}</span>
              </a>
              <a class="btn btn-xs bg-secondary" href="/summary_report/ot-report? status={{ config('app.approve_status_delete') }}" style="font-size: 0.85rem;">
                Total Deleted
                <span class="badge badge-light">{{ @$totalDeleted }}</span>
              </a>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @include('global.search_request',['clear_url' => 'ot-report', 'branch_request' => 'hidden', 'department_request' => 'hidden', 'company_request' => []])
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 col-lg-12">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title "><strong>OT Staff List</strong></h4>
              <div class="col-sm-12 text-right">
                Total: 
                <button class="btn btn-outline-secondary btn-sm">{{ $total }} Records</button>
              </div>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table table-striped table-hover">
                <thead class="">
                  <th style="min-width: 50px;">ល.រ</th>
                  <th style="min-width: 50px;">{{ __('ស្ថានភាព') }}</th>
                  <th style="min-width: 152px;">ក្រុមហ៊ុន</th>
                  <th class="text-center" style="min-width: 120px;">{{ __('កូដ') }}</th>
                  <th style="min-width: 190px;">ឈ្មោះបុគ្គលិកថែមម៉ោង</th>
                  <th style="min-width: 120px;">អត្តលេខការងារ</th>
                  <th style="min-width: 130px;">{{ __('ចាប់ពីថ្ងៃទី') }}</th>
                  <th style="min-width: 130px;">{{ __('ដល់ថ្ងៃទី') }}</th>
                  <th style="min-width: 180px;">{{ __('រយៈពេល') }}</th>
                  <th style="min-width: 180px;">{{ __('ចន្លោះ') }}</th>
                  <th style="min-width: 152px;">{{ __('ស្នើសំុដោយ') }}</th>
                  <th style="min-width: 245px;">{{ __('អនុម័តដោយ') }}</th>
                  <th style="min-width: 175px;">ថ្ងៃស្នើ</th>
                </thead>
                <tbody>
                    @if($data->count())
                        <?php $i = 1; ?>
                        @foreach($data as $key => $item)
                            <tr title="Request ID: {{$item->id}}">
                                <td> {{ $i++ }}</td>
                                <td>
                                  @if($item->deleted_at)
                                    <button class="btn btn-xs bg-dark">Deleted</button>
                                  @else
                                    {{ request_status($item) }}
                                  @endif
                                </td>
                                <td>
                                  {{ $item->company_name }}
                                </td>
                                <td class="text-center">{{ @showArrayCode($item->code) }}</td>
                                <td>
                                    {{ @(\App\RequestOT::staffName($item->staff)->name) }}
                                </td>
                                <td>
                                    {{ @$item->staff_code }}
                                </td>
                                <td>
                                    {{(\Carbon\Carbon::createFromTimestamp(strtotime(@$item->start_date))->format('d-m-Y'))}}
                                </td>
                                <td>
                                    {{(\Carbon\Carbon::createFromTimestamp(strtotime(@$item->end_date))->format('d-m-Y'))}}
                                </td>
                                <td>
                                    @if(@$item->total)
                                        {{@$item->total}} ម៉ោង
                                    @endif
                                    @if(@$item->total && @$item->total_minute)
                                        និង
                                    @endif
                                    @if(@$item->total_minute)
                                        {{@$item->total_minute}} នាទី
                                    @endif
                                </td>
                                <td>
                                    {{(\Carbon\Carbon::createFromTimestamp(strtotime(@$item->start_time))->format('h:i A'))}}
                                    -
                                    {{(\Carbon\Carbon::createFromTimestamp(strtotime(@$item->end_time))->format('h:i A'))}}
                                </td>
                                <td>{{ $item->requester_name }}</td>
                                <td>{{ @approver_position(\App\RequestOT::approverName($item->id)) }}</td>
                                <td>{{ created_at($item->created_at) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11">Record Not Found!</td>
                        </tr>
                    @endif
                </tbody>
              </table>
              {{ $data->appends($_GET)->links() }}
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
@endsection

