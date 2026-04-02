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
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button id="back" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</button>
                </div>
                <div class="col-md-12">
                    <form
                        enctype="multipart/form-data"
                        id="requestForm"
                        method="POST"
                        action="{{ route('sale_asset.update', $data->id) }}"
                        class="form-horizontal">
                        @csrf
                        @method('post')

                        <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($data->id) }}">
                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('កែប្រែសំណើរសុំលក់ទ្រព្យសម្បត្តិ') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>កម្មវត្ថុ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <textarea
                                            class="point_textarea form-control"
                                            name="purpose"
                                            required
                                        >{{$data->purpose}}</textarea><br>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        @include('sale_asset.partials.item_table_edit')
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                    <label>ឯកសារភ្ជាប់</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-5 form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                                <input
                                                    type="file"
                                                    id="file"
                                                    name="file[]"
                                                    multiple="multiple"
                                                    value="{{ old('file') }}"
                                                >
                                              </div>

                                            <div class="col-md-7">
                                                @if(@$data->attachment)
                                                    <?php $atts = is_array($data->attachment) ? $data->attachment : json_decode($data->attachment); ?>
                                                    @foreach($atts as $att )
                                                        <a href="{{ asset($att->src) }}" target="_self">View old File: {{ $att->org_name }}</a><br>
                                                    @endforeach
                                                @endif
                                          </div>

                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>សម្រាប់ក្រុមហ៊ុន</label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control company select2" name="company_id">
                                            @foreach($company as $key => $value)
                                                @if($value->id == $data->company_id)
                                                    <option value="{{ $value->id }} " selected="selected">{{ $value->name }}</option>
                                                @else
                                                    <option value="{{ $value->id }} ">{{ $value->name }}</option>
                                                @endif
                                            @endforeach()
                                        </select><br/>
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
                                                        @if($value->id == $data->user_id)
                                                            <option value="{{ $value->id}} " selected="selected">{{ $value->name }}</option>
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

                                            @foreach($data->reviewers() as $item)
                                                <option value="{{ $item->id }}" selected="selected">
                                                    {{ $item->name }}({{ $item->position_name }})
                                                </option>
                                            @endforeach

                                            @foreach($reviewers as $key => $value)
                                                <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>
                                            ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                                            <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                               title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
                                               data-placement="top"></i>
                                        </label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control select2" name="review_short[]" multiple>

                                            @foreach($data->reviewers_short() as $item)
                                                <option value="{{ $item->id }}" selected="selected">
                                                    {{ $item->name }}({{ $item->position_name }})
                                                </option>
                                            @endforeach

                                            @foreach($reviewers_short as $key => $value)
                                                <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
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
                                        </select><br/>
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

@include('sale_asset.partials.add_more_js')
