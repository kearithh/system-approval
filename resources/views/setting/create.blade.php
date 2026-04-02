@extends('adminlte::page', ['activePage' => 'Setting', 'titlePage' => __('Add Reviewer and Approver')])
@section('plugins.Select2', true)
@section('content')

@include('global.style_default_approve')

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  method="post"
                  action="{{ route('setting-reviewer-approver.store') }}"
                  enctype="multipart/form-data"
                  autocomplete="off"
                  class="form-horizontal">
            @csrf
            @method('post')

            <div class="card ">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">{{ __('Add Reviewer and Approver') }}</h4>
                    <div class="text-right">
                        <a href="{{ route('setting-reviewer-approver.index') }}" class="btn btn-sm btn-success">Back to list</a>
                    </div>
                </div>
              <div class="card-body ">

                <div class="row">
                    <div class="col-md-2">
                        <label>ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" name="company" required>
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
                        <label>នាយកដ្ឋាន<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" name="department" required>
                            @foreach($department as $key => $value)
                                <option value="{{ $value->id }}"
                                        @if(Auth::user()->department_id == $value->id))
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
                    <div class="col-md-2">
                        <label>ប្រភេទ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" id="type" name="type" required>
                            <option value=""><< ជ្រើសរើស >></option>
                            <option value="request">សំណើ</option>
                            <option value="report">របាយការណ៍</option>
                        </select>
                    </div>
                </div>

                <div class="row request">
                    <div class="col-md-2">
                        <label>ប្រភេទសំណើ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2 type_request" name="type_request" required>
                            <option value=""><< ជ្រើសរើស >></option>
                            @foreach($request_type as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->name }} - {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row report">
                    <div class="col-md-2">
                        <label>ប្រភេទរបាយការណ៍<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2 type_report" name="type_report" required>
                            <option value=""><< ជ្រើសរើស >></option>
                            @foreach($tags as $key => $value)
                                <option value="{{ $value->slug }}">
                                    {{ $value->name }} - {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row request">
                    <div class="col-md-2">
                        <label>ក្នុង / ក្រៅ
                            <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                title="ក្នុង=ក្នុងផែនការឬក្នុងRange, ក្រៅ=ក្រៅផែនការឬក្នុងRange"
                                data-placement="top"></i>
                         <span style='color: red'>*</span>
                        </label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2 type_request" name="category" required>
                            <option value=""><< ជ្រើសរើស >></option>
                            <option value="1">ក្នុង</option>
                            <option value="2">ក្រៅ</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label>កំណត់អ្នកត្រួតពិនិត្យ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" name="reviewers[]" multiple required>
                            @foreach($staff as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->reviewer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label>កំណត់អ្នកត្រួតពិនិត្យ(ហត្ថលេខាតូច)</label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" name="reviewers_short[]" multiple>
                            @foreach($staff as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->reviewer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label>កំណត់អ្នកអនុម័ត<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" name="approver" required>
                            <option value=""><< ជ្រើសរើស >></option>
                            @foreach($staff as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->reviewer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <fieldset>
                    <legend>
                        Approver the request
                    </legend>
                    <div class="row">
                        <div class="col-md-2">
                            <label>អនុម័តដោយ<span style='color: red'>*</span></label>
                        </div>
                        <div class="col-md-10 form-group">
                            <select class="form-control select2" name="my_approver" required>
                                <option value=""><< ជ្រើសរើស >></option>
                                @foreach($staff as $key => $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->reviewer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>

              </div>
              <div class="card-footer">
                    <button type="submit" class="btn btn-success">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@include('setting.partials.js')