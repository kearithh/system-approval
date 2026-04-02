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
    
    @include('global.style_default_approve')

    <div class="content">
        @if (@auth()->user()->branch->branch == 1)
            <h2 style="color: red"> User in Branch can't use</h2>
            <?= die() ?>
        @endif
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <form
                        enctype="multipart/form-data"
                        id="requestForm"
                        method="POST"
                        action="{{ route('transfer_asset.store') }}"
                        class="form-horizontal">
                        @csrf
                        @method('post')

                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('ទម្រង់សំណើសុំផ្ទេរសម្ភារៈ') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="col-md-12">
                                        @include('transfer_asset.partials.item_table')
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ឯកសារភ្ជាប់</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                            <input 
                                                multiple="" 
                                                type="file"
                                                id="file"
                                                class="{{ $errors->has('file') ? ' is-invalid' : '' }}"
                                                name="file[]"
                                                value="{{ old('file') }}"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                      <label>សម្រាប់ក្រុមហ៊ុន<span style='color: #ff0000'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                      <select class="form-control company select2" id="company_id" name="company_id">
                                        @foreach($company as $key => $value)
                                          @if($value->id==Auth::user()->company_id)
                                            <option value="{{ $value->id}} " selected="selected">{{ $value->name }}</option>
                                          @else
                                            <option value="{{ $value->id}} ">{{ $value->name }}</option>
                                          @endif
                                        @endforeach
                                      </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-12 mb-1">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>រៀបចំដោយ<span style='color: #ff0000'>*</span></label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <select class="form-control select2" name="user_id" required>
                                                    @foreach($staffs as $key => $value)
                                                        @if($value->id==Auth::id())
                                                            <option value="{{ $value->id}} " selected="selected">{{ $value->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
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
                                            <input type="hidden" name="" id="type_request" value="{{ config('app.type_transfer_asset') }}">
                                            <input type="hidden" name="" id="type_report" value="">
                                        </div>
                                    </legend>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ<span style='color: red'>*</span></label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <select class="form-control js-reviewer-multi" name="reviewers[]" required multiple>
                                                @foreach($reviewers as $item)
                                                    <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">
                                              ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                                              <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                                   title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
                                                   data-placement="top"></i>
                                        </label>
                                        <div class="col-sm-10 form-group">
                                            <select class="form-control js-short-multi" name="review_short[]" multiple>
                                                @foreach($reviewers as $item)
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
                                            <select class="form-control js-approver" name="approver" required>
                                                @foreach($approver as $item)
                                                    <option value="{{ @$item->id }}" @if($item->id == 11) selected @endif>{{ @$item->name }}({{$item->position_name}})</option>
                                                @endforeach
                                            </select><br/>
                                        </div>
                                    </div>

                                </fieldset>

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

@include('transfer_asset.partials.js')

@include('global.js_default_approve')
