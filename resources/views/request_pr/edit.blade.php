@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
    {{ route('request_pr.index') }}
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
                        id="requestPR"
                        method="POST"
                        enctype="multipart/form-data"
                        action="{{ route('request_pr.update', $data->id) }}"
                        class="form-horizontal">
                        @csrf
                        <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($data->id) }}">
                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('កែប្រែសំណើបញ្ជាទិញ/Edit Purchase Request​​') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">
                                @include('request_pr.add_more_item_table_edit')
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
                                    <label class="col-sm-3 col-form-label">{{ __('សម្រាប់ក្រុមហ៊ុន') }}</label>
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
                                    <label class="col-sm-3 col-form-label">{{ __('ស្នើដោយ') }}</label>
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
                                    <div class="col-md-3">
                                        <label>ផ្នែក/នាយកដ្ឋាន</label>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <select class="form-control my_select" name="department">
                                            <option value=""> << Select >> </option>
                                            @foreach($department as $key => $value)
                                                @if($value->id == $data->department_id)
                                                    <option value="{{ $value->id }}" selected>{{ $value->name_en }}</option>
                                                @else
                                                    <option value="{{ $value->id }}">{{ $value->name_en }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('Reason for purchase/Project detail:') }}<span style='color: red'>*</span></label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="purpose"
                                                class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                                                name="purpose"
                                                
                                            >@if($data){{ $data->purpose }}@else{{ old('purpose') }}@endif
                                            </textarea>
                                            @if ($errors->has('name'))
                                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('Vendor') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('reason') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="reason"
                                                class="form-control{{ $errors->has('reason') ? ' is-invalid' : '' }}"
                                                name="reason"
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
                                    <label class="col-sm-3 col-form-label">{{ __('Email/Phone') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('ep') ? ' has-danger' : '' }}">
                                    <textarea
                                            id="ep"
                                            class="form-control{{ $errors->has('ep') ? ' is-invalid' : '' }}"
                                            name="ep"
                                    >@if($data){{ $data->ep }}@else{{ old('ep') }}@endif</textarea>
                                            @if ($errors->has('ep'))
                                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('ep') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('FOR') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('remarks') ? ' has-danger' : '' }}">
                                    <textarea
                                            id="remarks"
                                            class="form-control{{ $errors->has('remarks') ? ' is-invalid' : '' }}"
                                            name="remarks"
                                    >@if($data){{ $data->remarks }}@else{{ old('remarks') }}@endif</textarea>
                                            @if ($errors->has('remarks'))
                                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('remarks') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                               <div class="row">
    <label class="col-sm-3 col-form-label">{{ __('Sourcing requirement') }}</label>
    <div class="col-sm-9">
        <div class="form-group">
            <input type="checkbox" id="sourcing_requirement_yes" name="sourcing_requirement_yes" value="1" @if($data->sourcing_requirement_yes) checked @endif>
            <label for="sourcing_requirement_yes">Yes</label><br>
            <input type="hidden" name="sourcing_requirement_no" value="0">
            <input type="checkbox" id="sourcing_requirement_no" name="sourcing_requirement_no" value="0" @if(!$data->sourcing_requirement_no) checked @endif>
            <label for="sourcing_requirement_no">No</label> 
        </div>
    </div>
</div>

<div class="row">
    <label class="col-sm-3 col-form-label">{{ __('Prefer supplier/ Single supplier Requirement') }}</label>
    <div class="col-sm-9">
        <div class="form-group">
            <input type="checkbox" id="prefer_supplier_yes" name="prefer_supplier_yes" value="1" @if($data->prefer_supplier_yes) checked @endif>
            <label for="prefer_supplier_yes">Yes</label><br>
            <input type="hidden" name="prefer_supplier_no" value="0">
            <input type="checkbox" id="prefer_supplier_no" name="prefer_supplier_no" value="0" @if(!$data->prefer_supplier_no) checked @endif>
            <label for="prefer_supplier_no">No</label> 
        </div>
    </div>
</div>

<div class="row">
    <label class="col-sm-3 col-form-label">{{ __('Tender Requirement') }}</label>
    <div class="col-sm-9">
        <div class="form-group">
            <input type="checkbox" id="tender_requirement_yes" name="tender_requirement_yes" value="1" @if($data->tender_requirement_yes) checked @endif>
            <label for="tender_requirement_yes">Yes</label><br>
            <input type="hidden" name="tender_requirement_no" value="0">
            <input type="checkbox" id="tender_requirement_no" name="tender_requirement_no" value="0" @if(!$data->tender_requirement_no) checked @endif>
            <label for="tender_requirement_no">No</label> 
        </div>
    </div>
</div>

                            {{-- <div class="row">
                            <div class="col-md-3">
                                <label>
                                    ស្នើរដោយ | Requestor by
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ ជាទូទៅជាអ្នកគ្រប់គ្រងផ្ទាល់"
                                       data-placement="top"></i>
                                    <span style='color: red'>*</span> 
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" required name="agree_by">
                                    <option value=""><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $item)
                                        <option value="{{ $item->id }}" @if($item->id == @$agreeBy->user_id) selected @endif>
                                            {{ $item->reviewer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                   អនុម័តដំបូង | Initial Approval 1
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជាប្រធានផ្នែក ឬប្រធាននាយកដ្ឋាន"
                                       data-placement="top"></i>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" name="verify_by_1">
                                    <option value="0"><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $item)
                                        <option value="{{ $item->id }}" @if($item->id == @$verifyBy1->user_id) selected @endif>
                                            {{ $item->reviewer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                អនុម័តដោយ | Non Medical or Medical
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជាប្រធានផ្នែក ឬប្រធាននាយកដ្ឋាន"
                                       data-placement="top"></i>
                                       <span style='color: #ff0000'>*</span>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" name="verify_by_2" required>
                                    <option value=""><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $item)
                                        @if($item->id == 37)
                                            <option value="{{ $item->id }}" @if($item->id == @$verifyBy2->user_id) selected @endif>
                                                <strong>Non-Medical</strong> - {{ $item->reviewer_name }}
                                            </option>
                                        @endif
                                        @if($item->id == 604)
                                            <option value="{{ $item->id }}" @if($item->id == @$verifyBy2->user_id) selected @endif>
                                                <strong>Medical(ORD1)</strong> - {{ $item->reviewer_name }}
                                            </option>
                                        @endif
                                        @if($item->id == 76)
                                            <option value="{{ $item->id }}" @if($item->id == @$verifyBy2->user_id) selected @endif>
                                                <strong>Medical(ORD2)</strong> - {{ $item->reviewer_name }}
                                            </option>
                                        @endif
                                        @if($item->id == 4117)
                                            <option value="{{ $item->id }}" @if($item->id == @$verifyBy2->user_id) selected @endif>
                                                <strong>BD</strong> - {{ $item->reviewer_name }}
                                            </option>
                                        @endif
                                        
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                   អនុម័តដំបូង | Initial Approval 2
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជាប្រធានផ្នែក ឬប្រធាននាយកដ្ឋាន"
                                       data-placement="top"></i>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" name="verify_by_3">
                                    <option value="0"><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $item)
                                        <option value="{{ $item->id }}" @if($item->id == @$verifyBy3->user_id) selected @endif>
                                            {{ $item->reviewer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        
                   
                    
                    
                    
                       {{-- <div class="row">
                            <div class="col-md-3">
                                <label>
                                   ត្រួតពិនិត្យដោយសវនកម្មផ្ទៃក្នុង | Verified By​ internal Audit
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ ជាទូទៅជាមន្រ្តីហិរញ្ញវត្ថុ"
                                       data-placement="top"></i>
                                    <span style='color: red'>*</span>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control select2" required @if(Auth::user()->company_id != 5) required @endif name="reviewer">
                                    <option value=""><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $key => $item)
                                        <option value="{{ $item->id }}" @if($item->id == @$reviewer->user_id) selected @endif>
                                            {{ $item->reviewer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                
                            <div class="row">
                            <div class="col-md-3">
                                <label>
                                    អនុម័តចុងក្រោយដោយ | Final Approved By 1
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជា ប្រធាននាយកប្រតិបត្តិសាម៉ី ជំនួយការប្រធាននាយកប្រតិបត្តិ"
                                       data-placement="top"></i>
                                    <span style='color: #ff0000'>*</span>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" name="final_short">
                                    <option value=""><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $item)
                                        <option value="{{ @$item->id }}" @if($item->id == @$finalShort->user_id) selected @endif>
                                            {{ $item->reviewer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                  
                      
                        
                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                    អនុម័តចុងក្រោយដោយ | Final Approved By 2
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="អនុម័តដោយ Chef Admin សំរាប់សំណើរចំណាយផ្សេងៗដែលមានតម្លៃច្រើនបំផុតត្រឹម 200,000 រៀល ឬ $50"
                                       data-placement="top"></i>
                                    <span style='color: #ff0000'>*</span>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" required name="approver" required>
                                    @foreach($approver as $item)
                                        <option value="{{ @$item->id }}" @if($item->id == @$data->approver()->id) selected @endif>
                                            {{ @$item->name }}({{ @$item->position_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                                {{-- <div class="row">
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
                                </div> --}}

                                {{-- <div class="row">
                                    <label class="col-sm-3 col-form-label">
                                        ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                            title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
                                            data-placement="top"></i>
                                    </label>
                                    <div class="col-sm-9 form-group">
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
                                </div> --}}

                                {{-- <div class="row">
                                    <label class="col-sm-3 col-form-label">
                                        អនុម័តដោយ
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="សម្រាប់ MMI នឹងទៅដល់ President Approver ដោយស្វ័យប្រវត្ត"
                                           data-placement="top">
                                        </i>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="form-group">
                                            <select required class="form-control select2 request-by-select2" name="approver_id" multiple="multiple">
                                                @foreach($approver as $item)
                                                    <option value="{{ @$item->id }}" 
                                                            @if($item->id == @$data->approver()->id) 
                                                                selected 
                                                            @endif
                                                    >
                                                        {{ @$item->name }}({{ $item->position_name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
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
                url : "{{URL::route('request_pr.find_review')}}",
                data:{'company':$value},
                success:function(data){
                  $('.reviewer').empty();
                  $(".reviewer").html(data);
                }
            });
        });
    </script>
@endpush
@include('request_pr.add_more_js')
