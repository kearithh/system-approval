@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request_hr.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

@push('css')
  <style>
    .table td {
      padding: 0.1em;
    }
  </style>
@endpush

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12 text-right">
          <button id="back" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</button>
        </div>
        <div class="col-md-12">
          <form
              enctype="multipart/form-data"
              id="requestForm"
              method="POST"
              action="{{ route('survey_report.update', $data->id) }}"
              class="form-horizontal">
              @csrf
              @method('post')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">កែប្រែរបាយការណ៍ប្រចាំថ្ងៃរបស់ប្រធានសាខា</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន</label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control company select2" name="company">
                      @foreach($company as $key => $value)
                        @if($value->id == $data->company_id)
                          <option value="{{ $value->id}} " selected="selected">{{ $value->name }}</option>
                        @else
                          <option value="{{ $value->id}} ">{{ $value->name }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2">
                    <label>សាខា<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <select class="form-control select2" required name="branch">
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($branch as $key => $value)
                      <option
                          value="{{ $value->id}}"
                          @if(@$data->branch_id == $value->id) selected @endif
                      >
                        {{ $value->name_km }} ({{ $value->short_name }})
                      </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-12">
                  <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                      <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="custom-tabs-three-admin-tab" data-toggle="pill" href="#custom-tabs-three-admin" role="tab" aria-controls="custom-tabs-three-admin" aria-selected="true">I. ផ្នែករដ្ឋបាល</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="custom-tabs-three-finance-tab" data-toggle="pill" href="#custom-tabs-three-finance" role="tab" aria-controls="custom-tabs-three-finance" aria-selected="false">II. ផ្នែកហិរញ្ញវត្ថុ</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="custom-tabs-three-hr-tab" data-toggle="pill" href="#custom-tabs-three-hr" role="tab" aria-controls="custom-tabs-three-hr" aria-selected="false">III. ផ្នែកធនធានមនុស្ស</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="custom-tabs-three-operation-tab" data-toggle="pill" href="#custom-tabs-three-operation" role="tab" aria-controls="custom-tabs-three-operation" aria-selected="false">IV. ផ្នែកប្រតិបត្តិការ</a>
                        </li>
                      </ul>
                    </div>
                    <div class="card-body">
                      <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-three-admin" role="tabpanel" aria-labelledby="custom-tabs-three-admin-tab">
                          <p><i>សូមបំពេញរបាយការណ៍លទ្ធផលការងារប្រចាំថ្ងៃរបស់អ្នកលើផ្នែករដ្ឋបាលអោយបានត្រឹមត្រូវ។</i></p>
                          
                          @include('survey_report.partials.edit_admin')

                          <!-- <a class="nav-link" id="custom-tabs-three-finance-tab1" data-toggle="pill" href="#custom-tabs-three-finance" role="tab" aria-controls="custom-tabs-three-finance" aria-selected="false">Next</a> -->

                        </div>
                        <div class="tab-pane fade" id="custom-tabs-three-finance" role="tabpanel" aria-labelledby="custom-tabs-three-finance-tab">
                          <p><i>សូមបំពេញរបាយការណ៍លទ្ធផលការងារប្រចាំថ្ងៃរបស់អ្នកលើផ្នែកហិរញ្ញវត្ថុអោយបានត្រឹមត្រូវ។</i></p>
                          
                          @include('survey_report.partials.edit_finance')

                          <!-- <a class="nav-link" id="custom-tabs-three-finance-tab1" data-toggle="pill" href="#custom-tabs-three-finance" role="tab" aria-controls="custom-tabs-three-finance" aria-selected="false">Next</a> -->
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-three-hr" role="tabpanel" aria-labelledby="custom-tabs-three-hr-tab">
                          <p><i>សូមបំពេញរបាយការណ៍លទ្ធផលការងារប្រចាំថ្ងៃរបស់អ្នកលើការគ្រប់គ្រងធនធានមនុស្សអោយបានត្រឹមត្រូវ។</i></p>
                          
                          @include('survey_report.partials.edit_hr')

                          <!-- <a class="nav-link" id="custom-tabs-three-finance-tab1" data-toggle="pill" href="#custom-tabs-three-finance" role="tab" aria-controls="custom-tabs-three-finance" aria-selected="false">Next</a> -->
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-three-operation" role="tabpanel" aria-labelledby="custom-tabs-three-operation-tab">
                          <p><i>សូមបំពេញរបាយការណ៍លទ្ធផលការងារប្រចាំថ្ងៃរបស់អ្នកលើការគ្រប់គ្រងប្រតិបត្តិការអោយបានត្រឹមត្រូវ។</i></p>
                          
                          @include('survey_report.partials.edit_operation_item')

                        <!--   <a class="nav-link" id="custom-tabs-three-finance-tab1" data-toggle="pill" href="#custom-tabs-three-finance" role="tab" aria-controls="custom-tabs-three-finance" aria-selected="false">Next</a> -->
                        </div>
                      </div>
                    </div>
                    <!-- /.card -->
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ឯកសារភ្ជាប់</label>
                  </div>
                  <div class="col-md-10">
                    <div class="row">
                      <div class="col-md-5 form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                        <input
                            type="file"
                            id="file"
                            name="file[]"
                            multiple="multiple"
                            value="{{ old('file') }}"
                        >
                      </div>

                      <div class="col-md-7">
                        @if(@$data->attachment)
                          <?php $atts = is_array($data->attachment) ? $data->attachment : json_decode($data->attachment); ?>
                          @foreach($atts as $att )
                            <a href="{{ asset($att->src) }}" target="_self">View old File: {{ $att->org_name }}</a><br>
                          @endforeach
                        @endif
                      </div>
                    </div>
                  </div>
                </div>

                  <div class="row">
                    <div class="col-sm-12 mb-1">
                      <div class="row">
                        <div class="col-md-2">
                          <label>រៀបចំដោយ</label>
                        </div>
                        <div class="col-md-10 form-group">
                          <select class="form-control select2"​​​​​ name="user_id" required>
                            @foreach($staffs as $key => $value)
                              @if($value->id == $data->user_id)
                                <option value="{{ $value->id}} " selected="selected">{{ $value->name }}</option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control reviewer select2" name="reviewers[]" required multiple>
                        @foreach($data->reviewers() as $item)
                          <option value="{{ $item->id }}" selected="selected">
                            {{ $item->name }}({{ $item->position_name }})
                          </option>
                        @endforeach

                        @foreach($reviewers as $key => $value)
                          <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>អនុម័តដោយ<span style='color: #ff0000'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control approver select2" name="approver" required>
                        @foreach($approver as $item)
                          <option value="{{ @$item->id }}" 
                            @if($item->id == @$data->approver()->id) selected @endif>
                            {{ @$item->approver_name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
              </div>

              <div class="card-footer">
                @if ($data->status == config('app.approve_status_reject'))
                  <button
                      @if ($data->user_id != \Illuminate\Support\Facades\Auth::id())
                          disabled
                          title="Only requester that able to edit the request"
                      @endif
                      type="submit"
                      value="1"
                      name="resubmit"
                      class="btn btn-info">
                      {{ __('Re-Submit') }}
                  </button>
                @else
                  <button
                      @if ($data->user_id != \Illuminate\Support\Facades\Auth::id())
                          disabled
                          title="Only requester that able to edit the request"
                      @endif
                      type="submit"
                      value="1"
                      name="submit"
                      class="btn btn-success">
                      {{ __('Update') }}
                  </button>
                @endif
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@include('survey_report.partials.js')
