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
  
  @include('global.style_default_approve')

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  id="withdrawalCollateral"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('withdrawal_collateral.store') }}"
                  class="form-horizontal">
            @csrf
            {{--@method('post')--}}

            <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($withdrawalCollateral->id) }}">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('សំណើដកឯកសារទ្រព្យធានារបស់អតិថិជនខុសគោលការណ៍') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                  @include('withdrawal_collateral.add_more_item_table')
                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('ឯកសារភ្ជាប់') }}</label>
                    <div class="col-sm-9">
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
                    <label class="col-sm-3 col-form-label">សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
                          <select class="form-control select2" id="company_id" name="company_id">
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
                            @if ($errors->has('company_id'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('company_id') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <label>សាខា<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-9 form-group">
                    <select class="form-control select2" required name="branch">
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($branch as $key => $value)
                        <option
                          value="{{ $value->id }}"
                          @if(Auth::user()->branch_id == $value->id) selected @endif
                        >
                          {{ $value->name_km }} ({{ $value->short_name }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-3 col-form-label">ស្នើដោយ<span style='color: red'>*</span></label>
                  <div class="col-sm-9">
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
                  <label class="col-sm-3 col-form-label">{{ __('កម្មវត្ថុ') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-9">
                    <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                      <textarea
                              id="purpose"
                              class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                              name="purpose"
                              required
                      >ស្នើសុំដកឯសារទ្រព្យធានាខុគោលការណ៍ឲ្យអតិថិនបង់ផ្តាច់នៅថ្ងៃទី................ </textarea>
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('គោលបំណង') }}<span style='color: red'>*</span></label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('reason') ? ' has-danger' : '' }}">
                    <textarea
                            id="reason"
                            class="form-control{{ $errors->has('reason') ? ' is-invalid' : '' }}"
                            name="reason"
                            required
                    >ដោយសារ........................................</textarea>
                            @if ($errors->has('reason'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('reason') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('កំណត់សម្គាល់') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('remark') ? ' has-danger' : '' }}">
                    <textarea
                            id="remark"
                            class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"
                            name="remark"
                    >@if($withdrawalCollateral){{ $withdrawalCollateral->remark }}@else{{ old('remark') }}@endif</textarea>
                            @if ($errors->has('remark'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('remark') }}</span>
                            @endif
                        </div>
                    </div>
                </div>


                <fieldset>
                    <legend>
                        <button 
                            type="button"
                            value="1"
                            name="check"
                            class="check btn btn-sm btn-info">
                            By default
                        </button>
                        <button
                            type="button"
                            value="1"
                            name="clear"
                            class="clear btn btn-sm btn-secondary">
                            Clear default
                        </button>
                        <div class="row">
                            <input type="hidden" name="" id="my_department" value="{{ Auth::user()->department_id }}">
                            <input type="hidden" name="" id="my_type" value="request">
                            <input type="hidden" name="" id="type_request" value="{{ config('app.type_special_expense') }}">
                            <input type="hidden" name="" id="type_report" value="">
                        </div>
                    </legend>

                    <div class="row">
                      <label class="col-sm-3 col-form-label">{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ៖') }}<span style='color: red'>*</span></label>
                      <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                          <select required class="form-control js-reviewer-multi" name="reviewer_id[]" multiple="multiple">
                            @foreach($reviewer as $item)
                              <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-3 col-form-label">
                            អនុម័តដោយ <span style='color: red'>*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <select required class="form-control js-approver" name="approver_id">
                                    <option value=""><<ជ្រើសរើស>></option>
                                    @foreach($approver as $item)
                                        <option 
                                            value="{{ @$item->id }}"
                                        >
                                            {{ @$item->approver_name }}
                                      </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </fieldset>

              </div>
              <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        name="submit"
                        formaction="{{ route('withdrawal_collateral.store')  }}"
                        form="withdrawalCollateral"
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

@include('withdrawal_collateral.add_more_js')

@include('global.js_default_approve')
