@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
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
        <div class="col-md-12">
          <form
                  id="employee_penalty"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('employee_penalty.store') }}"
                  class="form-horizontal">
            @csrf

            <input type="hidden" class="request_token" name="request_type" value="{{ config('app.type_penalty') }}">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">សំណើសុំទទួលប្រាក់ពិន័យបុគ្គលិក</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">

                  <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('សម្រាប់ក្រុមហ៊ុន') }}</label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
                          <select class="form-control company select2" name="company_id">
                            @foreach($company as $key => $value)
                                <option value="{{ $value->id}}"
                                        @if(Auth::user()->company_id == $value->id))
                                            selected
                                        @endif
                                >
                                    {{ $value->name }}
                                </option>
                            @endforeach()
                          </select><br/>
                            @if ($errors->has('company_id'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('company_id') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('កម្មវត្ថុ') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('subject') ? ' has-danger' : '' }}">
                      <textarea
                              id="subject"
                              class="form-control{{ $errors->has('subject') ? ' is-invalid' : '' }}"
                              name="subject"
                              required
                      ></textarea>
                    </div>
                  </div>
                </div>

                <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('គោលបំណង') }}<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                    <textarea
                            id="purpose"
                            class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                            name="purpose"
                            required
                    ></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('ឈ្មោះបុគ្គលិក') }}<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                            <input type="text" class="form-control" name="staff" value="{{ old('staff') }}" required>
                        </div>
                    </div>
                </div>

                  @include('employee_penalty.partials.item_table')

                  @include('employee_penalty.partials.item_table_customer')
                  
                <br>
                <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('ឯកសារភ្ជាប់') }}</label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                            <input
                                type="file"
                                id="file"
                                class="{{ $errors->has('file') ? ' is-invalid' : '' }}"
                                name="file"
                                value="{{ old('file') }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('កំណត់សម្គាល់') }}</label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('remark') ? ' has-danger' : '' }}">
                    <textarea
                            id="remark"
                            class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"
                            name="remark"
                    ></textarea>

                        </div>
                    </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ស្នើដោយ') }}</label>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('user_id') ? ' has-danger' : '' }}">
                      <select required class="form-control select2 request-by-select2" name="user_id">
                        @foreach($requester as $item)
                          @if($item->id==Auth::id())
                              <option value="{{ $item->id}} " selected="selected">{{ $item->name. ' ('.@$item->position->name_km.')' }}</option>
                          @endif
                        @endforeach
                      </select>
                      @if ($errors->has('user_id'))
                        <span
                                id="name-error"
                                class="error text-danger"
                                for="input-name">
                          {{ $errors->first('user_id') }}
                        </span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">
                    {{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ៖') }}<span style='color: red'>*</span>
                  </label>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                      <select required class="form-control reviewer select2" name="reviewer_id[]" multiple="multiple">
                        @foreach($reviewer as $item)
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">
                    ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                         title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
                         data-placement="top"></i>
                  </label>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                      <select class="form-control reviewer select2" name="reviewer_short[]" multiple="multiple">
                        @foreach($reviewer as $item)
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                    <label class="col-sm-2 col-form-label">អនុម័តដោយ<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                        <div class="form-group">
                            <select required class="form-control select2 request-by-select2" name="approver_id">
                                @foreach($approver as $item)
                                    <option value="{{ @$item->id }}" @if($item->id == 11) selected @endif>
                                        {{ @$item->name }}({{$item->position_name}})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

              </div>
              <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        name="submit"
                        formaction="{{ route('employee_penalty.store')  }}"
                        form="employee_penalty"
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

@include('employee_penalty.partials.js')
