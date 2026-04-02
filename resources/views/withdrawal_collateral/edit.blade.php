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
                <div class="col-sm-12 text-right">
                    <button id="back" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</button>
                </div>
                <div class="col-md-12">
                    <form
                        id="WithdrawalCollateral"
                        method="POST"
                        enctype="multipart/form-data"
                        action="{{ route('withdrawal_collateral.update', $data->id) }}"
                        class="form-horizontal">
                        @csrf
                        <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($data->id) }}">
                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('កែប្រែសំណើដកឯកសារទ្រព្យធានារបស់អតិថិជនខុសគោលការណ៍') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">
                                @include('withdrawal_collateral.add_more_item_table_edit')
                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('ឯកសារភ្ជាប់') }}</label>
                                    <div class="col-sm-9">
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
                                    <label class="col-sm-3 col-form-label">{{ __('សម្រាប់ក្រុមហ៊ុន') }}<span style='color: red'>*</span></label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
                                            <select class="form-control company select2" name="company_id">
                                                @foreach($company as $key => $value)
                                                    <option value="{{ $value->id }}"
                                                            @if($data->company_id == $value->id))
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
                                    <div class="col-md-3">
                                        <label>សាខា<span style='color: red'>*</span></label>
                                    </div>

                                    <div class="col-md-9 form-group">
                                        <select class="form-control select2" required name="branch">
                                            <option value=""><< ជ្រើសរើស >></option>
                                            @foreach($branch as $key => $value)
                                                <option
                                                    value="{{ $value->id }}"
                                                    @if($data->branch_id == $value->id) selected @endif
                                                >
                                                    {{ $value->name_km }} ({{ $value->short_name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('ស្នើដោយ') }}<span style='color: red'>*</span></label>
                                    <div class="col-sm-9">
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
                                            >@if($data){{ $data->purpose }}@else{{ old('purpose') }}@endif
                                            </textarea>
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
                                            >@if($data){{ $data->reason }}@else{{ old('reason') }}@endif
                                            </textarea>
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
                                    >@if($data){{ $data->remark }}@else{{ old('remark') }}@endif</textarea>
                                            @if ($errors->has('remark'))
                                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('remark') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ៖') }}<span style='color: red'>*</span></label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                                            <select required class="form-control reviewer select2" name="reviewer_id[]" multiple="multiple">

                                                @foreach($data->reviewers() as $item)
                                                    <option value="{{ $item->id }}" selected="selected">
                                                      {{ $item->name }}({{ $item->position_name }})
                                                    </option>
                                                @endforeach

                                                @foreach($reviewer as $key => $value)
                                                    <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-3 col-form-label">
                                        អនុម័តដោយ<span style='color: red'>*</span>
                                    </label>
                                    <div class="col-sm-9">
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
                                        id="submit"
                                        type="submit"
                                        value="1"
                                        name="resubmit"
                                        class="btn btn-info">
                                        {{ __('Re-Submit') }}
                                    </button>
                                @elseif ($data->status == config('app.approve_status_approve'))
                                    <button
                                        disabled
                                        id="submit"
                                        type="button"
                                        value="1"
                                        name="submit"
                                        class="btn btn-danger">
                                        Re-Submit
                                    </button>
                                @else
                                    <button
                                        @if ($data->user_id != \Illuminate\Support\Facades\Auth::id())
                                            disabled
                                            title="Only requester that able to edit the request"
                                        @endif
                                        id="submit"
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

        $(".company").change(function(){
            $value=$(this).val();
            //alert($value);
            $.ajax({
                type : 'get',
                url : "{{URL::route('withdrawal_collateral.find_review')}}",
                data:{'company':$value},
                success:function(data){
                  $('.reviewer').empty();
                  $(".reviewer").html(data);
                }
            });
        });
    </script>
@endpush
@include('withdrawal_collateral.add_more_js')
