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
                        action="{{ route('send_receive.store') }}"
                        class="form-horizontal">
                        @csrf
                        @method('post')

                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('ទម្រង់សំណើរប្រគល់-ទទួលសម្ភារៈ') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="col-md-12">
                                        @include('send_receive.partials.item_table')
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
                                    <div class="col-md-2">
                                      <label>សម្រាប់ក្រុមហ៊ុន<span style='color: #ff0000'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                      <select class="form-control company select2" name="company_id">
                                        @foreach($company as $key => $value)
                                          @if($value->id==Auth::user()->company_id)
                                            <option value="{{ $value->id }} " selected="selected">{{ $value->name }}</option>
                                          @else
                                            <option value="{{ $value->id }} ">{{ $value->name }}</option>
                                          @endif
                                        @endforeach
                                      </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                      <label>សាខា/ការិយាល័យកណ្តាល<span style='color: #ff0000'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                      <select class="form-control branch select2" name="branch_id">
                                        @foreach($branch as $key => $value)
                                          @if($value->id==Auth::user()->branch_id)
                                            <option value="{{ $value->id }} " selected="selected">{{ $value->name_km }}</option>
                                          @else
                                            <option value="{{ $value->id }} ">{{ $value->name_km }}</option>
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
                                                            <option value="{{ $value->id }} " selected="selected">{{ $value->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 mb-1">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>ប្រគល់ដោយ<span style='color: #ff0000'>*</span></label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <select class="form-control select2" name="sender" required>
                                                    <option value=""> << ជ្រើសរើស >> </option>
                                                    @foreach($staffs as $key => $value)
                                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 mb-1">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>ទទួលដោយ<span style='color: #ff0000'>*</span></label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <select class="form-control select2" name="receiver" required>
                                                    <option value=""> << ជ្រើសរើស >> </option>
                                                    @foreach($staffs as $key => $value)
                                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
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
                                                <option value="{{ @$item->id }}">{{ @$item->name }}({{$item->position_name}})</option>
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

@include('send_receive.partials.add_more_js')
