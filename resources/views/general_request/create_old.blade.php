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
                  id="requestForm"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('general_request.store') }}"
                  class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('ទម្រង់សំណើទូទៅ') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                <div class="row">
                    <label class="col-sm-2 col-form-label">ប្រភេទសំណើ<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('type') ? ' has-danger' : '' }}">
                          <select class="form-control select2"​​​​​ required name="type" id="type">
                              <option value="1">សំណើខ្ចប់សាច់ប្រាក់</option>
                              <option value="2">សំណើសុំរក្សាសាច់ប្រាក់ទុកលើគោលការណ៍</option>
                              <option value="3">សំណើសុំប្តូរប្រាក់</option>
                              <option value="4">សំណើសុំចំណាយប្រចាំថ្ងៃ</option>
                          </select>
                        </div>
                    </div>
                </div>
              </div>
              <div class="card-body ">
                <div id="packing">
                    @include('general_request.partials.item_packing')
                </div>

                <div id="keep_money">
                    @include('general_request.partials.item_keep_money')
                </div>

                <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('ឯកសារភ្ជាប់') }}</label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                            <input
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
                    <label class="col-sm-2 col-form-label">{{ __('សម្រាប់ក្រុមហ៊ុន') }}</label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
                          <select class="form-control company select2"​​​​​ name="company_id">
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
                          @if(Auth::user()->branch_id == $value->id) selected @endif
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
                          @if($item->id==Auth::id())
                              <option value="{{ $item->id}} " selected="selected">{{ $item->name. ' ('.@$item->position->name_km.')' }}</option>
                          @endif
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
                      ></textarea>
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
                            ></textarea>
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
                            >សាច់ប្រាក់ខ្ចប់នេះ នឹងត្រូវរៀបចំទុកដាក់ក្នុងទូរដែកសុវត្ថភាពដែលស្ថិតនៅក្រោមការគ្រប់គ្រងផ្ទាល់របស់ខ្ញុំបាទ ។ <br>ខ្ញុំបាទប្តេជ្ញាជំរុញឲ្យមន្រ្តីឥណទាន វិលត្រឡប់មកការិយាល័យឲ្យបានទាន់ពេលវេលានាពេលក្រោយៗទៀត ។ </textarea>
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
                  <label class="col-sm-2 col-form-label">{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ៖') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                      <select required class="form-control select2" name="reviewers[]" multiple="multiple">
                        @foreach($reviewer as $item)
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                    <label class="col-sm-2 col-form-label">
                        អនុម័តដោយ
                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                           title="គណនេយ្យហិរញ្ញវត្ថុជាន់ខ្ពស់"
                           data-placement="top">
                        </i>
                    </label>
                    <div class="col-sm-10">
                        <div class="form-group">
                            <select required class="form-control select2 request-by-select2" name="approver_id">
                                <option value=""><<ជ្រើសរើស>></option>
                                @foreach($approver as $item)
                                    <option value="{{ @$item->id }}">
                                        {{ @$item->approver_name }}
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
                        formaction="{{ route('general_request.store')  }}"
                        form="requestForm"
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

@push('js')
<script>
    $.ajax({
          type: "GET",
          url: "{{ route('get-general-request') }}",
          data: {
              _token: "{{ csrf_token() }}",
              request_id: request_id
          },
          success: function(data) {
              $("#modal-body").html(data);
              $(".select2").select2({
                  placeholder: {
                      id: null,
                      text: ' << ជ្រើសរើស >> '
                  }
              });
              $("select").on("select2:select", function (evt) {
                  var element = evt.params.data.element;
                  var $element = $(element);
                  
                  $element.detach();
                  $(this).append($element);
                  $(this).trigger("change");
              });
              $('.datepicker').datepicker({
                  format: 'dd-mm-yyyy',
                  todayHighlight:true,
                  autoclose: true
              });
              $("#upload-file").modal();

          },
          error: function(data) {
              console.log(data)
          }
      });
  </script>
@endpush

@include('general_request.partials.js')
