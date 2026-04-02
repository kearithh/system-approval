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
              <a class="btn btn-xs bg-success" href="/summary_report/general-expense? status={{ config('app.approve_status_approve') }}" style="font-size: 0.85rem;">
                Total Approved
                <span class="badge badge-light">{{ @$totalApproved }}</span>
              </a>
              <a class="btn btn-xs bg-orange" href="/summary_report/general-expense? status={{ config('app.approve_status_draft') }}" style="font-size: 0.85rem;">
                Total Pending
                <span class="badge badge-light">{{ @$totalPending }}</span>
              </a>
              <a class="btn btn-xs bg-danger" href="/summary_report/general-expense? status={{ config('app.approve_status_reject') }}" style="font-size: 0.85rem;">
                Total Commented
                <span class="badge badge-light">{{ @$totalCommented }}</span>
              </a>
              <a class="btn btn-xs bg-secondary" href="/summary_report/general-expense? status={{ config('app.approve_status_delete') }}" style="font-size: 0.85rem;">
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
              @include('global.search_request',['clear_url' => 'general-expense', 'branch_request' => 'hidden', 'department_request' => 'hidden', 'company_request' => []])
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
              <h4 class="card-title "><strong>General Expense List</strong></h4>
              <div class="col-sm-12 text-right">
                Total: 
                @if($khr > 0 )
                  <button class="btn btn-outline-info btn-sm">
                    {{ number_format($khr) .' ៛'}}
                  </button>
                @endif
                @if($usd > 0 )
                  <button class="btn btn-outline-primary btn-sm">
                    {{'$ '. number_format(($usd),2) }}
                  </button>
                @endif
                <button class="btn btn-outline-secondary btn-sm">{{ $total }} Records</button>
              </div>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table table-striped table-hover">
                <thead class="">
                <th>ល.រ</th>
                <th>មើល</th>
                <th>{{ __('ស្ថានភាព') }}</th>
                <th style="min-width: 152px;">ក្រុមហ៊ុន</th>
                <th style="min-width: 230px;">ចំណាយ</th>
                <th style="min-width: 152px;">ស្នើសំុដោយ</th>
                <th style="min-width: 100px">ថ្ងៃស្នើ</th>
                <th style="min-width: 130px" class="text-right">តម្លៃសរុប</th>
                </thead>
                <tbody>
                  @if($data->count())
                    <?php $i = 1; ?>
                    @foreach($data as $key => $item)
                      <tr title="Request ID: {{$item->id}}">
                        <td> {{ $i++  }}</td>
                        <td>
                          <a  href="{{ route('request_hr.show', $item->id) }}" 
                              class="preview btn btn-xs btn-info" 
                              title="View the request">
                              <i class="fa fa-eye"></i>
                          </a>
                        </td>
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
                        <td>
                            {{ @items_desc(\App\RequestHR::items_desc($item->id)) }}
                        </td>
                        <td>
                          {{ $item->requester_name }}
                        </td>
                        <td>
                          {{ created_at($item->created_at) }}
                        </td>
                        <td class="text-right">
                          @if($item->total > 0 )
                            {{'$ '. number_format(($item->total),2) }}<br>
                          @endif
                          @if($item->total_khr > 0 )
                            {{ number_format($item->total_khr) .' ៛'}}
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="7">Record Not Found!</td>
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

@push('js')
    <script type="text/javascript">
        $(document).ready(function () { 
            $(".preview").on('click', function () {
                localStorage.previous = window.location.href ;
            });
        });
    </script>
@endpush