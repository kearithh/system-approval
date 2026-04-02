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
                        action="{{ route('return_budget.store') }}"
                        class="form-horizontal">
                        @csrf
                        @method('post')

                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('ទម្រង់សំណើរសុំប្រគល់ថវិកា') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="col-md-2">
                                      <label>សម្រាប់ក្រុមហ៊ុន</label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                      <select class="form-control company select2" name="company_id">
                                        @foreach($company as $key => $value)
                                          @if($value->id==Auth::user()->company_id)
                                            <option value="{{ $value->id}} " selected="selected">{{ $value->name }}</option>
                                          @else
                                            <option value="{{ $value->id}} ">{{ $value->name }}</option>
                                          @endif
                                        @endforeach()
                                      </select><br/>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>កម្មវត្ថុ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <textarea
                                            class="form-control"
                                            name="purpose"
                                            required
                                        >សំណើសុំប្រគល់ថវិកាចំនួន... ដែលទទួលបានពី...</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>បរិយាយ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <textarea
                                            class="form-control"
                                            name="description"
                                            required
                                        >តបតាមកម្មវត្ថុខាងលើ ផ្នែករដ្ឋបាល គោរពស្នើសុំ លោកស្រីនាយិកាប្រតិបត្តិមេត្តាជ្រាបថា៖  ផ្នែករដ្ឋបាល និង ផ្នែកសវនកម្ម...</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>បញ្ជាក់<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <textarea
                                            class="form-control"
                                            name="verify"
                                            required
                                        >ថវិកាខាងលើ ផ្នែករដ្ឋបាល បានប្រគល់ជូនផ្នែករតនាគារ (Treasary) ដើម្បីចូលជាចំណូល ។ </textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>រូបិយប័ណ្ណ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select required class="col-sm-12 currency select2" name="currency" id="">
                                            <option value="">----</option>
                                            <option value="KHR" @if(Auth::user()->branch_id > 1) selected @endif>រៀល</option>
                                            <option value="USD">ដុល្លារ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ទឹកប្រាក់ចំនូន<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <input type="text" class="form-control" required id="budget" name="budget">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 mb-3">
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
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-12 mb-1">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>រៀបចំដោយ</label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <select class="form-control select2" name="user_id" required>
                                                    @foreach($staffs as $key => $value)
                                                        @if($value->id==Auth::id())
                                                            <option value="{{ $value->id }}" selected="selected">{{ $value->name }}</option>
                                                        @endif
                                                    @endforeach()
                                                </select><br/>
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
                                            @foreach($reviewers as $item)
                                                <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                       <label>ទទួលដោយ<span style='color: #ff0000'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control receiver select2" name="receiver" required>
                                            <option value=""><< ជ្រើសរើស >></option>
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
                                        <select class="form-control approver select2" name="approver" required>
                                            <option value=""><< ជ្រើសរើស >></option>
                                            @foreach($approver as $item)
                                                <option value="{{ @$item->id }}" >{{ @$item->name }}({{ $item->position_name }})</option>
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

@include('return_budget.partials.js')
