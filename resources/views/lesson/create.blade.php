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

  @include('global.style_default_approve')

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
              method="POST"
              action="{{ route('lesson.store') }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Lesson</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control company select2" id="company_id" name="company_id">
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
                        >
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ឯកសារភ្ជាប់<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                      <input
                          type="file"
                          id="file"
                          name="file"
                          value="{{ old('file') }}"
                          required
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
                      @foreach($staffs as $item)
                        @if($item->id == Auth::id())
                          <option value="{{ $item->id }}">{{ $item->staff_name }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់នាយកដ្ឋាន</label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" id="departments" name="departments[]" multiple>
                      @foreach($department as $key => $value)
                        <option value="{{ $value->id }}">
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
                        <option value="{{ $value->id }}">
                          {{ $value->name_km }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

              </div>

              <div class="card-footer">
                <button
                    @if(@!auth()->user()->action_object->can_lesson) disabled @endif
                    type="submit"
                    value="1"
                    name="submit"
                    class="btn btn-success"
                >
                  Submit
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