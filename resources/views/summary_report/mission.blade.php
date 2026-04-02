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
            <!-- /.card-header -->
            <div class="card-body">
              @include('global.search_request',['clear_url' => 'mission', 'branch_request' => 'hidden', 'department_request' => 'hidden', 'company_request' => []])
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
              <h4 class="card-title "><strong>Mission List</strong></h4>
              <div class="col-sm-12 text-right">
                <button class="btn btn-outline-secondary btn-sm">Total: {{ $total }} Records</button>
              </div>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table table-striped table-hover">
                <thead class="">
                  <th>ល.រ</th>
                  <th>មើល</th>
                  <th>ស្ថានភាព</th>
                  <th style="min-width: 152px;">ក្រុមហ៊ុន</th>
                  <th style="min-width: 152px;">ស្នើសុំដោយ</th>
                  <th style="min-width: 152px;">ឈ្មោះបុគ្គលិក</th>
                  <th style="min-width: 100px">ថ្ងៃចុះបេសកកម្ម</th>
                  <th style="min-width: 250px">កម្មវត្ថុ</th>
                </thead>
                <tbody>
                  @if($data->count())
                    <?php $i = 1; ?>
                    @foreach($data as $key => $item)
                      <tr title="Request ID: {{ @$item['id'] }}">
                        <td> {{ $i++  }}</td>
                        <td>
                          @if(@$item['department_id'] == 4) <!-- hide content for audit -->
                            <button disabled class="preview btn btn-xs btn-info" >
                                <i class="fa fa-eye"></i>
                            </a>
                          @else
                            <a  href="{{ route('mission.show', @$item['id']) }}" 
                                class="preview btn btn-xs btn-info" 
                                title="View the request">
                                <i class="fa fa-eye"></i>
                            </a>
                          @endif
                        </td>
                        <td>
                          @if ($item['deleted_at'])
                            <button class="btn btn-xs bg-secondary" title="Request was Deleted" >Deleted</button>
                          @else
                            {{ mission_status(@$item['status']) }}
                          @endif
                        </td>
                        <td>{{ @$item['company_name'] }}</td>
                        <td>{{ @$item['requester_name'] }}</td>
                        <td>
                          ID: {{ @$item['staff_id'] }}<br>
                          Name: {{ @$item['staff'] }}
                        </td>
                        <td>
                          {{ start_date(@$item['start_date']) }}
                          {{ start_date(@$item['end_date']) }}
                        </td>
                        <td>
                          @if(@$item['department_id'] == 4) <!-- hide content for department audit -->
                            <i>The content is confidential, preview not availble.</i> 
                          @else
                            {{ @$item['purpose'] }} 
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="9">Record Not Found!</td>
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

