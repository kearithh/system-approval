@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

@section('content')
  
  @if (@auth()->user()->branch->branch == 1)
      <h2 style="color: red"> User in Branch can't use</h2>
      <?= die() ?>
  @endif
        
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  id="requestForm"
                  method="POST"
                  action="{{ route('request_create_user.store') }}"
                  enctype="multipart/form-data"
                  class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('ទម្រង់ស្នើរសុំបង្កើតឈ្មោះអ្នកប្រើប្រាស់ប្រព័ន្ធ') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">

                <div class="row">
                  <div class="col-md-2">
                    <label>ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control company select2" id="company" name="company_id">
                      @foreach($company as $key => $value)
                        <option value="{{ $value->id }}"
                                @if(Auth::user()->company_id == $value->id))
                                    selected
                                @endif
                        >
                          {{ $value->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2">
                    <label><span id="posi">ឈ្មោះអ្នកស្នើរសុំ (EN)</span><span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <input type="text" name="request_object[name_en]" class="form-control" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2">
                    <label><span id="posi">ឈ្មោះអ្នកស្នើរសុំ (KH)</span><span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <input type="text" name="request_object[name_kh]" class="form-control" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ភេទ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="request_object[gender]" id="gender" required >
                      <option value=""><< ជ្រើសរើស >></option>
                      <option value="M">ប្រុស</option>
                      <option value="F">ស្រី</option>
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
                        <option value="{{ $value->id }}">
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
                        <option value="{{ $value->id }}">
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
                    <select class="form-control select2" name="request_object[department]" id="department" required>
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($department as $key => $value)
                        <option value="{{ $value->id }}">
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
                        <option value="{{ $value }}">
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
                        <option value="{{ $value }}">
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
                        <option value="{{ $value }}">
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
                      <textarea class="form-control" required rows="3" id="purpose" name="purpose"></textarea>
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
                    ></textarea>
                  </div>
                </div>

                <div class="request_resign_show">
                  <div class="row">
                    <div class="col-sm-2">
                      <label>ស្នើសុំបន្ថែម</label>
                    </div>
                    <div class="col-sm-10 form-group">
                      <textarea class="form-control" rows="3" name="more"></textarea>
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
                  <div class="col-md-10">
                    <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                      <input 
                          type="file"
                          id="file"
                          name="file"
                          value="{{ old('file') }}"
                      >
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>រៀបចំដោយ</label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="user_id" required>
                      @foreach($reviewer as $item)
                        @if($item->id==Auth::id())
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                        @endif
                      @endforeach()
                    </select><br/>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ត្រួតពិនិត្យដោយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="reviewer" required>
                      <option value=""> << ជ្រើសរើស >> </option>
                      @foreach($reviewer as $item)
                        <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
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
                        <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
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
                        <option value="{{ @$item->id }}" @if($item->id == 39) selected @endif>
                          {{ @$item->name }}({{$item->position_name}})
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

              </div>

              <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        name="submit"
                        class="btn btn-success">
                  {{ __('Submit') }}
                </button>
              </div>

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@include('request_create_user.partials.add_more_js')
