@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
    {{ route('general_request.create') }}
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
                        id="requestForm"
                        method="POST"
                        enctype="multipart/form-data"
                        action="{{ route('general_request.update', $data->id) }}"
                        class="form-horizontal">
                        @csrf
                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('Edit General Request') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">ប្រភេទសំណើ<span style='color: red'>*</span></label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('type') ? ' has-danger' : '' }}">
                                            <select class="form-control select2"​​​​​ required name="type">
                                                <option value="1" @if ($data->type == 1)) selected @endif >
                                                    សំណើខ្ចប់សាច់ប្រាក់
                                                </option>
                                                <option value="2" @if ($data->type == 2)) selected @endif >
                                                    សំណើសុំប្តូរប្រាក់
                                                </option>
                                                <option value="3" @if ($data->type == 3)) selected @endif >
                                                    សំណើសុំចំណាយប្រចាំថ្ងៃ
                                                </option>
                                                <option value="4" @if ($data->type == 4)) selected @endif >
                                                    សំណើសុំរក្សាសាច់ប្រាក់ទុកលើគោលការណ៍
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-body ">
                                @include('general_request.partials.item_packing_edit')

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('ឯកសារភ្ជាប់') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                            <input
                                                type="file"
                                                id="file"
                                                class="{{ $errors->has('file') ? ' is-invalid' : '' }}"
                                                name="file"
                                                value="{{ old('file', $data->attachment) }}"
                                            >
                                            &emsp;&emsp;
                                            @if(@$data->attachment)
                                                <a href="{{ asset('/'.@$data->attachment) }}" target="_self">View old File</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('សម្រាប់ក្រុមហ៊ុន') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
                                            <select class="form-control company select2"​​​​​ name="company_id">
                                                @foreach($company as $key => $value)
                                                    <option value="{{ $value->id}}"
                                                            @if ($data->company_id == $value->id)) selected @endif
                                                    >
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach()
                                            </select><br/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-2">
                                        <label>សាខា<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-sm-10 form-group">
                                        <select class="form-control select2" required name="branch_id">
                                            <option value=""><< ជ្រើសរើស >></option>
                                            @foreach($branch as $key => $value)
                                                <option
                                                    value="{{ $value->id}}"
                                                    @if ($data->branch_id == $value->id)) selected @endif
                                                >
                                                  {{ $value->name_km }}
                                                </option>
                                            @endforeach()
                                        </select><br/>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('ស្នើដោយ') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('user_id') ? ' has-danger' : '' }}">
                                            <select required class="form-control select2 request-by-select2" name="user_id">
                                                @foreach($requester as $item)
                                                    <option
                                                        @if($item->id == $data->user_id)
                                                            selected
                                                        @endif
                                                        value="{{ $item->id }}">{{ $item->name. ' ('.@$item->position->name_km.')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('កម្មវត្ថុ') }}<span style='color: red'>*</span></label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="purpose"
                                                class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                                                name="purpose"
                                                required
                                            >{{ $data->purpose }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('មូលហេតុ') }}<span style='color: red'>*</span></label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('reason') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="reason"
                                                class="form-control{{ $errors->has('reason') ? ' is-invalid' : '' }}"
                                                name="reason"
                                                required
                                            >{{ $data->reason }}
                                            </textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('បរិយាយ') }}<span style='color: red'>*</span></label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('desc') ? ' has-danger' : '' }}">
                                            <textarea
                                                    rows="4" 
                                                    id="desc"
                                                    class="form-control desc_textarea"
                                                    name="desc"
                                                    required
                                            >{!! $data->desc !!}</textarea>
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
                                            >{{ $data->remark }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ៖') }}<span style='color: red'>*</span></label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                                            <select required class="form-control select2" name="reviewers[]" multiple="multiple">

                                                @foreach($data->reviewers() as $item)
                                                    <option value="{{ $item->id}}" selected="selected">
                                                      {{ $item->name }}({{$item->position_name}})
                                                    </option>
                                                @endforeach()

                                                @foreach($reviewer as $key => $value)
                                                    @if(!in_array($value->id, $data->reviewers()->pluck('id')->toArray()))
                                                      <option value="{{ $value->id}}">{{  $value->reviewer_name }}</option>
                                                    @endif
                                                @endforeach()

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">
                                        អនុម័តដោយ
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="សម្រាប់ MMI នឹងទៅដល់ President Approver ដោយស្វ័យប្រវត្ត"
                                           data-placement="top">
                                        </i>
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="form-group">
                                            <select required class="form-control select2 request-by-select2" name="approver_id">
                                                @foreach($approver as $item)
                                                    <option value="{{ @$item->id }}" 
                                                            @if($item->id == @$data->approver()->id) 
                                                                selected 
                                                            @endif
                                                    >
                                                        {{ @$item->approver_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
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
                                      ​​  name="submit"
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
@push('js')
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script>
        $( "#back" ).on( "click", function( event ) {
            if(localStorage.previous){
                window.location.href = localStorage.previous;
                window.localStorage.removeItem('previous');
            }
            else{
                alert("Can't previous");
            }
        });
    </script>
@endpush
@include('general_request.partials.js')
