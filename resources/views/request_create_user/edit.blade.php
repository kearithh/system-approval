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
              action="{{ route('request_create_user.update', $data->id) }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">កែប្រែទម្រង់ស្នើរសុំបង្កើតឈ្មោះអ្នកប្រើប្រាស់ប្រព័ន្ធ</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">

                  <div class="row">
                    <div class="col-md-2">
                      <label>ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select id="company" class="form-control company select2" name="company_id">
                        @foreach($company as $key => $value)
                          <option value="{{ $value->id }}"
                                  @if($data->company_id == $value->id))
                                    selected
                                  @endif
                          >
                            {{ $value->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                    
                  <?php $user_obj = @$data->request_object ?>

                  <div class="row">
                    <div class="col-sm-2">
                      <label><span id="posi">ឈ្មោះអ្នកស្នើរសុំ (EN)</span><span style='color: red'>*</span></label>
                    </div>
                    <div class="col-sm-10 form-group">
                      <input type="text" name="request_object[name_en]" value="{{ $user_obj->name_en }}" class="form-control" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-sm-2">
                      <label><span id="posi">ឈ្មោះអ្នកស្នើរសុំ (KH)</span><span style='color: red'>*</span></label>
                    </div>
                    <div class="col-sm-10 form-group">
                      <input type="text" name="request_object[name_kh]" value="{{ $user_obj->name_kh }}" class="form-control" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ភេទ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control select2" name="request_object[gender]" id="gender" required >
                        <option value=""><< ជ្រើសរើស >></option>
                        <option value="M" 
                              @if($user_obj->gender == 'M')
                                selected
                              @endif
                        >ប្រុស</option>
                        <option value="F"
                              @if($user_obj->gender == 'F')
                                selected
                              @endif
                        >ស្រី</option>
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-sm-2">
                      <label><span id="posi">តួនាទី / មុខងារ</span><span style='color: red'>*</span></label>
                    </div>
                    <div class="col-sm-10 form-group">
                      <select class="form-control select2" name="request_object[position]" id="position" required>
                        <option value=""><< ជ្រើសរើស >></option>
                        @foreach($position as $key => $value)
                          <option value="{{ $value->id}}"  
                              @if($user_obj->position == $value->id)
                                selected
                              @endif
                          >
                            {{ $value->name_km }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-sm-2">
                      <label>ការិយាល័យ / សាខា<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-sm-10 form-group">
                      <select class="form-control company select2" name="request_object[branch]" id="branch" required>
                        <option value=""><< ជ្រើសរើស >></option>
                        @foreach($branch as $key => $value)
                          <option value="{{ $value->id}}"
                              @if($user_obj->branch == $value->id)
                                selected
                              @endif
                          >
                            {{ $value->name_km }} ({{ $value->short_name }})
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-sm-2 department">
                      <label>នាយកដ្ឋាន<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-sm-10 form-group department">
                      <select class="form-control select2" name="request_object[department]" required>
                        <option value=""><< ជ្រើសរើស >></option>
                        @foreach($department as $key => $value)
                          <option value="{{ $value->id}}"
                              @if($user_obj->department == $value->id)
                                selected
                              @endif
                          >
                            {{ $value->name_km }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row" id="request_all">
                    <div class="col-md-2">
                      <label>ប្រភេទនៃការស្នើរសុំ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select id="type_all" class="form-control select2" name="types[]" multiple>
                        @foreach(config('app.types_request_user') as $key => $value)
                          <option value="{{ $value }}" @if(in_array($value, @$data->types)) selected @endif >
                            {{ $value }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row" id="request_skp">
                    <div class="col-md-2">
                      <label>ប្រភេទនៃការស្នើរសុំ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select id="type_skp" class="form-control select2" name="types_skp[]" multiple>
                        @foreach(config('app.types_request_user_skp') as $key => $value)
                          <option value="{{ $value }}" @if(in_array($value, @$data->types)) selected @endif >
                            {{ $value }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row" id="request_mmi">
                    <div class="col-md-2">
                      <label>ប្រភេទនៃការស្នើរសុំ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select id="type_mmi" class="form-control select2" name="types_mmi[]" multiple>
                        @foreach(config('app.types_request_user_mmi') as $key => $value)
                          <option value="{{ $value }}" @if(in_array($value, @$data->types)) selected @endif >
                            {{ $value }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="request_resign_show">
                    <div class="row">
                      <div class="col-sm-2">
                        <label>គោលបំណង<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-sm-10 form-group">
                        <textarea class="form-control" required rows="3" name="purpose">{{$data->purpose}}</textarea>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>បរិយាយ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <textarea
                          class="point_textarea form-control"
                          name="description"
                          required
                      >{{$data->description}}</textarea>
                    </div>
                  </div>

                  <div class="request_resign_show">
                    <div class="row">
                      <div class="col-sm-2">
                        <label>ស្នើសុំបន្ថែម</label>
                      </div>
                      <div class="col-sm-10 form-group">
                        <textarea class="form-control" rows="3" name="more">{{$data->more}}</textarea>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>កំណត់សំគាល់</label>
                    </div>
                    <div class="col-md-10 form-group">
                      <input type="text" 
                          class="form-control"
                          name="remark"
                          value="{{$data->remark}}" 
                      >
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>
                        ឯកសារភ្ជាប់
                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                           title="ហត្ថលេខា ឬឯកសារដែលពាក់ព័ន្ធ"
                           data-placement="top"></i>
                      </label>
                    </div>
                    <div class="col-md-10 form-group">
                      <div class="row">
                        <div class="col-md-5">
                          <input
                              type="file"
                              id="file"
                              name="file"
                          >
                        </div>

                        <div class="col-md-7">
                          @if(@$data->attachment)
                            <a href="{{ asset('/'.@$data->attachment) }}" target="_self">View old File</a>
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
                        @foreach($staffs as $key => $value)
                          @if($value->id == $data->user_id)
                            <option value="{{ $value->id}}" selected="selected">{{ $value->name }}</option>
                          @endif
                        @endforeach()
                      </select><br/>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ពិនិត្យបន្តដោយ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control select2" name="reviewer" required>
                        <option value=""> << ជ្រើសរើស >> </option>
                        @foreach($reviewer as $item)
                          <option value="{{ $item->id}}" @if($item->id == @$data->reviewer()->reviewer_id) selected @endif >
                            {{ $item->reviewer_name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>
                        បញ្ជាក់ដោយ
                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                           title="សេចក្តីបញ្ជាក់ពីផ្នែកជំនាញនៃនាយកដ្ឋានព័ន៌មានវិទ្យា"
                           data-placement="top"></i>
                        <span style='color: red'>*</span>
                      </label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control select2" name="verify" required>
                        <option value=""> << ជ្រើសរើស >> </option>
                        @foreach($verify as $item)
                          <option value="{{ $item->id }}" @if($item->id == @$data->verify()->reviewer_id) selected @endif >
                            {{ $item->reviewer_name }}
                          </option>
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
                          <option value="{{ @$item->id }}" @if($item->id == @$data->approver()->id) selected @endif>{{ @$item->name }}({{$item->position_name}})</option>
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

@include('request_create_user.partials.add_more_js')

