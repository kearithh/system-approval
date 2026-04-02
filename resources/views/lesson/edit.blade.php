@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12 text-right">
          <button id="back" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</button>
        </div>
        <div class="col-md-12">
          <form
              id="requestForm"
              method="POST"
              action="{{ route('lesson.update', $data->id) }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Edit Lesson</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control company select2" name="company_id">
                      @foreach($company as $key => $value)
                        <option value="{{ $value->id }}"
                                @if($data->company_id == $value->id))
                                  selected
                                @endif
                        >{{ $value->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ចំណងជើង<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                      <input
                              type="text"
                              class="form-control"
                              name="title"
                              required
                              value="{{ @$data->title }}"
                        >
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ឯកសារភ្ជាប់</label>
                  </div>
                  <div class="col-md-10">
                    <div class="row">

                      <div class="col-md-5 form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                        <div id="validate"></div>
                        <input
                            type="file"
                            id="file"
                            name="file"
                            value="{{ old('file') }}"
                        >
                      </div>

                      <div class="col-md-7">
                        @if(@$data->attachment)
                          <a href="{{ asset($data->attachment->src) }}" target="_self">View old File: {{ $data->attachment->org_name }}</a><br>
                        @endif
                      </div>

                    </div>

                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>រៀបចំដោយ</label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="user_id" required>
                      @foreach($staffs as $item)
                        @if($item->id == $data->created_by)
                          <option value="{{ $item->id }}" selected="selected">{{ $item->staff_name }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                </div>

                <?php 
                  $departments_arr = @$data->departments;
                  $positions_arr = @$data->positions;
                ?>

                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់នាយកដ្ឋាន</label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" id="departments" name="departments[]" multiple>
                      @foreach($department as $key => $value)
                        <option value="{{ $value->id }}" 
                          @if(!empty($departments_arr) && in_array($value->id, @$departments_arr)) selected @endif 
                        >
                          {{ $value->name_km }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់តួនាទី</label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" id="positions" name="positions[]" multiple>
                      @foreach($position as $key => $value)
                        <option value="{{ $value->id }}" 
                          @if(!empty($positions_arr) && in_array($value->id, @$positions_arr)) selected @endif
                        >
                          {{ $value->name_km }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

              </div>

              <div class="card-footer">
                  <button
                      @if($data->created_by != auth()->id())
                          disabled
                          title="Only requester that able to edit the request"
                      @endif
                      type="submit"
                      value="1"
                      name="submit"
                      class="btn btn-success"
                  >
                      Update
                  </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@include('lesson.partials.js')
