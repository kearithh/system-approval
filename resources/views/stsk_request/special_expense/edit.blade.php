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
                    <a href="{{ route('request.index') }}" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</a>
                </div>
                <div class="col-md-12">
                    <form
                        id="requestForm"
                        method="POST"
                        action="{{ route('request.update', $data->id) }}"
                        class="form-horizontal">
                        @csrf
                        <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($data->id) }}">
                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('Edit Special Expense') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">
                                @include('request.add_more_item_table_edit')
                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('សម្រាប់ក្រុមហ៊ុន') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
                                          <select class="form-control company select2"​​​​​ name="company_id">
                                            @foreach($company as $key => $value)
                                                <option value="{{ $value->id}}"
                                                        @if($data->company_id == $value->id))
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
                                    <label class="col-sm-3 col-form-label">{{ __('ស្នើដោយ') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('user_id') ? ' has-danger' : '' }}">
                                            <select required class="form-control select2 request-by-select2" name="user_id">
                                                @foreach($requester as $item)
                                                    <option
                                                        @if($item->id == $data->user_id)
                                                            selected
                                                        @endif
                                                        value="{{ $item->id }}">{{ $item->name. ' ('.@$item->position->name_km.')' }}</option>
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
                                    
                                    <label class="col-sm-3 col-form-label">{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ៖') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                                            <select required class="form-control reviewer select2" name="reviewer_id[]" multiple="multiple">
                                                @foreach($reviewer as $key => $value)
                                                    @if(in_array($value->id, $data->reviewers()->pluck('id')->toArray()))
                                                        <option value="{{ $value->id}} " selected="selected">{{  $value->reviewer_name }}</option>
                                                    @else
                                                        <option value="{{ $value->id}} ">{{  $value->reviewer_name }}</option>
                                                    @endif
                                                @endforeach()
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('កម្មវត្ថុ') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                      <textarea
                          id="purpose"
                          class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                          name="purpose"
                          required
                      >@if($data){{ $data->purpose }}@else{{ old('purpose') }}@endif</textarea>
                                            @if ($errors->has('name'))
                                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('មូលហេតុ') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('reason') ? ' has-danger' : '' }}">
                      <textarea
                          id="reason"
                          class="form-control{{ $errors->has('reason') ? ' is-invalid' : '' }}"
                          name="reason"
                      >@if($data){{ $data->reason }}@else{{ old('reason') }}@endif</textarea>
                                            @if ($errors->has('reason'))
                                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('reason') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button
                                    type="submit"
                                    value="1"
                                    name="submit"
                                    class="btn btn-success">
                                    {{ __('Update') }}
                                </button>

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
        @if(session('status'))
            Swal.fire({
                title: 'Success',
                icon: 'success',
                timer: '2000',
            })
        @endif

        
        $(".company").change(function(){
            $value=$(this).val();
            //alert($value);
            $.ajax({
                type : 'get',
                url : "{{URL::route('request.find_review')}}",
                data:{'company':$value},
                success:function(data){ 
                  $('.reviewer').empty();
                  $(".reviewer").html(data);
                }
            });
        });
    </script>
@endpush
@include('request.add_more_js')
