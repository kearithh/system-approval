@extends('adminlte::page', ['activePage' => @$_GET['company'], 'titlePage' => __('Dashboard')])
@section('plugins.Select2', true)
@section('plugins.Pace', true)
@push('css')
<style>
    .table td, .table th {
        padding: .75rem .3rem;
    }
    .small-label {
        margin-top: 5px;
        font-size: 12px;
        opacity: 0.7;
    }
    .group-col {
        margin-left: 7px;
    }
</style>
@endpush

@section('content')
  <div class="content">
    <div class="container-fluid">
      <!-- /.row -->
      <div class="row">
        <div class="col-sm-12">
          @include('group_request.partials.breakcrum')
        </div>
      </div>
      <div class="row">
          <div class="group-col">
              @include('group_request.partials.submenu_department')
          </div>
          <div class="group-col">
              @include('group_request.partials.submenu_daily')
          </div>
          <div class="group-col" v-if="groups">
              @include('group_request.partials.submenu_group')
          </div>
          <div class="group-col ">

              <?php
                  $url = basename(Request::url());
                  $tags = @$_GET['tags'];
                  $groups = @$_GET['groups'];
                  $date_from = @$_GET['date_from'];
                  $date_to = @$_GET['date_to'];
              ?>

              @if ($data)
                  <div class="card" style="overflow-x: auto">
                      <div class="card-header card-header-primary">
                          <h4 class="card-title "><strong>Report List</strong></h4>
                          <div style="float: right;">
                            <form
                                  id="requestForm"
                                  method="GET"
                                  action="{{ $url }}"
                                  class="form-horizontal">
                              @csrf

                              <input type="hidden" name="company" value="{{ @$company }}">
                              <input type="hidden" name="type" value="{{ @$type }}">
                              <input type="hidden" name="department" value="{{ @$department }}">
                              <input type="hidden" name="tags" value="{{ @$tags }}">
                              <input type="hidden" name="groups" value="{{ @$groups }}">

                              <table>
                                <td style="vertical-align: middle;">
                                  <strong>របាយការណ៍សម្រាប់ថ្ងៃ៖</strong>
                                </td>
                                <td>
                                  <input type="text" name="date_from" autocomplete="off" placeholder="ចាប់ពី" value="{{ @$date_from }}" class="form-control form-control-sm datepicker">
                                </td>
                                <td>
                                  <input type="text" name="date_to" autocomplete="off" placeholder="ដល់" value="{{ @$date_to }}" class="form-control form-control-sm datepicker">
                                </td>
                                <td>
                                  <button type="submit" name="" class="btn btn-sm btn-info">
                                    <i class="fas fa-search"></i>
                                    ស្វែងរក
                                  </button>
                                </td>
                              </table>

                            </form>
                          </div>
                      </div>
                      <div class="table-responsive" style="padding: 0 10px">
                          <table class="table table-striped">
                              <thead class="">
                                  <th style="min-width: 50px">ល.រ</th>
                                  <th style="min-width: 115px">{{ __('សកម្ម') }}</th>
                                  <th style="min-width: 180px;">{{ __('ឈ្មោះរបាយការណ៍') }}</th>
                                  <th style="min-width: 115px;">ថ្ងៃបញ្ជូន</th>
                                  <th style="min-width: 150px;">{{ __('អ្នកត្រួតពិនិត្យ') }}</th>
                                  <th style="min-width: 150px;">{{ __('បញ្ជូនបន្ត(CC)') }}</th>
                                  <th style="min-width: 160px;">របាយការណ៍សម្រាប់ថ្ងៃ</th>
                              </thead>
                              <tbody>
                              <?php $i = 1; ?>
                                  @if($data->count())
                                      @foreach($data as $key => $item)
                                          <tr>
                                              <td class="text-center"> {{ $i++ }}</td>
                                              <td class="td-actions">
                                                  @include('group_request.partials.crud_action', ['uri' => 'group_request/item', 'object' => $item, 'type' => config('app.app.report')])
                                              </td>
                                              <td style="">
                                                  @if($item->status == config('app.approved'))
                                                      <a href="" style="color: inherit">
                                                          <p class="mb-0" style="">{{ @$item->name }}</p>
                                                      </a>
                                                      <p class="mb-0  small-label">
                                                          Status: <button class="btn btn-xs bg-success" style="line-height: 1; padding: 0px; font-size: 11px;" title="done" type="button">&nbsp; {{ @$item->status }} &nbsp;</button>&emsp;&emsp;
                                                          Creator: <span class="text-primary">{{ $item->user_name }}</span>&emsp;&emsp;
                                                          Deadline: <span class="text-danger">{{ string_to_time($item->end_date)->format('l jS') }}</span>&emsp;&emsp;
                                                          Approve By: <span class="text-success">{{ @$item->approver->name }}</span>

                                                      </p>
                                                  @else
                                                      <p class="mb-0" style="">{{ @$item->name }}</p>
                                                      <p class="mb-0 small-label">
                                                          Status: <button class="btn btn-xs @if($item->status == 'rejected') bg-danger @else bg-orange @endif" style="line-height: 1; padding: 0px; font-size: 11px;" title="done" type="button">&nbsp;{{ @$item->status }}&nbsp;</button>&emsp;&emsp;
                                                          Creator: <span class="text-primary">{{ $item->user_name }}</span>&emsp;&emsp;
                                                          Deadline: <span class="text-danger">{{ string_to_time($item->end_date)->format('D jS') }}</span>&emsp;&emsp;
                                                          Approve By: <span class="text-success">{{ @$item->approver->name }}</span>
                                                      </p>
                                                  @endif
                                              </td>
                                              <td>
                                                  @if($item->attachments)
                                                      {{ @$item->attachments[0]->uploaded_at }}
                                                  @else
                                                      N/A
                                                  @endif
                                              </td>
                                              <td style="vertical-align: top">{{ reviewers_list($item->reviewers) }}</td>
                                              <td style="vertical-align: top">{{ @reviewers_list($item->ccs) }}</td>
                                              <td>
                                                  {{(\Carbon\Carbon::createFromTimestamp(strtotime(@$item->end_date))->format('d/m/Y'))}}
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
                      </div>
                  </div>
                  {{ $data->appends($_GET)->links() }}

              @else
                  <div class="card">
                      <div class="card-header card-header-primary">
                          <h4 class="card-title ">{{ __('Please Select Type') }}</h4>
                      </div>
                      <div class="table-re    sponsive" style="padding: 0 10px">
                      </div>
                  </div>
              @endif
          </div>

      </div>
    </div>
  </div>

  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
@endsection

@push('js')
  <script>
      @if(session('status'))
      Swal.fire({
          title: 'Success',
          text: '{{ session('message') }}',
          icon: 'success',
          timer: '2500',
      });
      @endif
      $('.sidebar-mini').addClass("sidebar-collapse");
  </script>
@endpush
