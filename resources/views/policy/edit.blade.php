@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

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
              action="{{ route('policy.update', $data->id) }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Edit Policy / SOP</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control company select2" name="company_id">
                      @foreach($company as $key => $value)
                        <option value="{{ $value->id }}"
                                @if($data->company_id == $value->id))
                                  selected
                                @endif
                        >{{ $value->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>នាយកដ្ឋាន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="department_id">
                      @foreach($department as $key => $value)
                        <option value="{{ $value->id }}"
                                @if($data->department_id == $value->id))
                                  selected
                                @endif
                        >{{ $value->name_km }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>លេខកែសម្រួល<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                      <input
                              type="text"
                              class="form-control"
                              name="number_edit"
                              value="{{ @$data->number_edit }}"
                              required
                        >
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ថ្ងៃសុពលភាព<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                      <input
                          type="text"
                          class="datepicker form-control "
                          name="validity_date"
                          required
                          data-inputmask-inputformat="dd-mm-yyyy"
                          placeholder="dd-mm-yyyy"
                          autocomplete="off"
                          value="{{(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->validity_date))->format('d-m-Y'))}}"
                      >
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>បរិយាយការកែសម្រួល<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                      <textarea class="form-control" required rows="3" name="description">{{ @$data->description }}</textarea>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>លេខយោង<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                      <input
                              type="text"
                              class="form-control"
                              name="footnote"
                              required
                              value="{{ @$data->footnote }}"
                        >
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ឯកសារភ្ជាប់</label>
                  </div>
                  <div class="col-md-10">
                    <div class="row">

                      <div class="col-md-5 form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                        <div id="validate"></div>
                        <input
                            type="file"
                            id="file"
                            name="file[]"
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
                    <label>រៀបចំដោយ</label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="user_id" required>
                      @foreach($reviewer as $item)
                        @if($item->id == $data->user_id)
                          <option value="{{ $item->id }}" selected="selected">{{ $item->reviewer_name }}</option>
                        @endif
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

                      @foreach($reviewer_short as $key => $value)
                        <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
                      @endforeach

                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ត្រួតពិនិត្យដោយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="review_by[]" required multiple>

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

                <div class="row">
                  <div class="col-md-2">
                    <label>អនុម័តដោយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="approve_by" required >
                      @foreach($approver as $item)
                          <option value="{{ @$item->id }}" @if($item->id == @$data->approver()->id) selected @endif>
                            {{ @$item->name }}({{ $item->position_name }})
                          </option>
                      @endforeach
                    </select>
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

@include('custom_letter.partials.js')
