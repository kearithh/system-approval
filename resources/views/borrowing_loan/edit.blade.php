@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

@include('borrowing_loan.partials.style')

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
              action="{{ route('borrowing_loan.update', $data->id) }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">កែប្រែសំណើទទួលប្រាក់កម្ចី</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body">

                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-4 form-group">
                    <select class="form-control select2" name="company_id" required>
                      @foreach($company as $key => $value)
                        <option
                          value="{{ $value->id}}"
                          @if(Auth::user()->company_id == $value->id)) selected @endif
                        >
                          {{ $value->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-2">
                    <label>សាខា<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-4 form-group">
                    <select class="form-control select2" name="branch_id" required>
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($branch as $key => $value)
                        <option
                          value="{{ $value->id }}"
                          @if(Auth::user()->branch_id == $value->id) selected @endif
                        >
                          {{ $value->name_km }} ({{ $value->short_name }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <fieldset>
                  <legend>
                    <strong>ភាគីកូនបំណុល</strong>
                  </legend>
                  
                  <div class="row">
                    <div class="col-md-2">
                      <label>ងារ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <select class="form-control select2" name="debtor_obj[title]" required>
                        <option value=""><< ជ្រើសរើស >></option>
                        @foreach(config('app.customer_title') as $key => $value)
                          <option value="{{ $value }}" @if($value == @$data->debtor_obj->title)) selected @endif >
                            {{ $value }}
                          </option>
                        @endforeach
                      </select>

                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ឈ្មោះ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="បុគ្គលិក" name="debtor_obj[name]" value="{{ @$data->debtor_obj->name }}" required>
                    </div>

                    <div class="col-md-2">
                      <label>អត្តសញ្ញាណប័ណ្ណ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="អត្តសញ្ញាណប័ណ្ណ" name="debtor_obj[nid]" value="{{ @$data->debtor_obj->nid }}" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>តួនាទី<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="តួនាទី" name="debtor_obj[position]" value="{{ @$data->debtor_obj->position }}" required>
                    </div>

                    <div class="col-md-2">
                      <label>លេខទូរស័ព្ទ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="លេខទូរស័ព្ទ" name="debtor_obj[phone]" value="{{ @$data->debtor_obj->phone }}" required>
                    </div>
                  </div>
                </fieldset>

                <fieldset>
                  <legend>
                    <strong>ភាគីម្ចាស់បំណុល</strong>
                  </legend>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ងារ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <select class="form-control select2" name="creditor_obj[title]" required>
                        <option value=""><< ជ្រើសរើស >></option>
                        @foreach(config('app.customer_title') as $key => $value)
                          <option value="{{ $value }}" @if($value == @$data->creditor_obj->title) selected @endif >
                            {{ $value }}
                          </option>
                        @endforeach
                    </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ឈ្មោះ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="អតិថិជន" name="creditor_obj[name]" value="{{ @$data->creditor_obj->name }}" required>
                    </div>

                    <div class="col-md-2">
                      <label>អត្តសញ្ញាណប័ណ្ណ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="អត្តសញ្ញាណប័ណ្ណ" name="creditor_obj[nid]" value="{{ @$data->creditor_obj->nid }}" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>អាសយដ្ឋានបច្ចុប្បន្ន</label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="លេខផ្ទះ" name="creditor_obj[home]" value="{{ @$data->creditor_obj->home }}">
                    </div>

                    <div class="col-md-2">
                      <label>ផ្លូវលេខ</label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="លេខផ្លូវ" name="creditor_obj[street]" value="{{ @$data->creditor_obj->street }}">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ភូមិ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="ភូមិ" name="creditor_obj[village]" value="{{ @$data->creditor_obj->village }}" required>
                    </div>

                    <div class="col-md-2">
                      <label>សង្កាត់/ឃុំ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="សង្កាត់/ឃុំ" name="creditor_obj[commune]" value="{{ @$data->creditor_obj->commune }}" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ខណ្ឌ/ស្រុក<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="ខណ្ឌ/ស្រុក" name="creditor_obj[district]" value="{{ @$data->creditor_obj->district }}" required>
                    </div>

                    <div class="col-md-2">
                      <label>ក្រុង/ខេត្ត<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="ក្រុង/ខេត្ត" name="creditor_obj[province]" value="{{ @$data->creditor_obj->province }}" required>
                    </div>
                  </div>
                </fieldset>

                <fieldset>
                  <legend>
                    <strong>លក្ខខណ្ឌរួម</strong>
                  </legend>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ប្រភេទក្រដាសប្រេាក់<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <select class="form-control select2" name="currency" required>
                        <option value=""><< ជ្រើសរើស >></option>
                        <option @if($data->currency == 'KHR') selected @endif value="KHR">KHR</option>
                        <option @if($data->currency == 'USD') selected @endif value="USD">USD</option>
                    </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ទឹកប្រាក់ខ្ចី(ជាលេខ)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control money" placeholder="ទឹកប្រាក់ខ្ចី" name="amount_number" value="{{ @$data->amount_number }}" required>
                    </div>

                    <div class="col-md-2">
                      <label>ទឹកប្រាក់ខ្ចី(ជាអក្សរ)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="ទឹកប្រាក់ខ្ចី" name="amount_text" value="{{ @$data->amount_text }}" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>រយៈពេលខ្ចី<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="number" class="form-control" placeholder="គិតជាខែ" min="1" name="period" value="{{ @$data->period }}" required>
                    </div>

                    <div class="col-md-2">
                      <label>គិតចាប់ពី<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input 
                          type="text" 
                          class="form-control datepicker" 
                          required
                          data-inputmask-inputformat="dd-mm-yyyy"
                          placeholder="dd-mm-yyyy"
                          autocomplete="off"
                          name="from"
                          value="{{ $data->from->format('d-m-Y') }}" 
                      >
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ដល់<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input 
                          type="text" 
                          class="form-control datepicker" 
                          required
                          data-inputmask-inputformat="dd-mm-yyyy"
                          placeholder="dd-mm-yyyy"
                          autocomplete="off"
                          name="to"
                          value="{{ $data->to->format('d-m-Y') }}" 
                      >
                    </div>

                    <div class="col-md-2">
                      <label>ការប្រាក់ប្រចាំឆ្នាំ(%)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="number" class="form-control" placeholder="%" step="0.1" min="0" name="interest" value="{{ @$data->interest }}" required>
                    </div>
                  </div>
                </fieldset>
                <fieldset>
                  <legend>
                    <strong>មធ្យោបាយនៃការផ្ទេរប្រាក់(កូនបំណុល)</strong>
                  </legend>
                  <div class="row">
                    <div class="col-md-2">
                      <label>អាសយដ្ឋានផ្ទះលេខ</label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="លេខផ្ទះ" name="debtor_transfer[home]" value="{{ @$data->debtor_transfer->home }}">
                    </div>

                    <div class="col-md-2">
                      <label>ផ្លូវលេខ</label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="លេខផ្លូវ" name="debtor_transfer[street]" value="{{ @$data->debtor_transfer->street }}" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ភូមិ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="ភូមិ" name="debtor_transfer[village]" value="{{ @$data->debtor_transfer->village }}" required>
                    </div>

                    <div class="col-md-2">
                      <label>សង្កាត់/ឃុំ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="សង្កាត់/ឃុំ" name="debtor_transfer[commune]" value="{{ @$data->debtor_transfer->commune }}" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ខណ្ឌ/ស្រុក<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="ខណ្ឌ/ស្រុក" name="debtor_transfer[district]" value="{{ @$data->debtor_transfer->district }}" required>
                    </div>

                    <div class="col-md-2">
                      <label>ក្រុង/ខេត្ត<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="ក្រុង/ខេត្ត" name="debtor_transfer[province]" value="{{ @$data->debtor_transfer->province }}" required>
                    </div>
                  </div>
                </fieldset>
                <fieldset>
                  <legend>
                    <strong>មធ្យោបាយនៃការផ្ទេរប្រាក់(ម្ចាស់បំណុល)</strong>
                  </legend>
                  <div class="row">
                    <div class="col-md-2">
                      <label>ធនាគារ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="ឈ្មោះធនាគា" name="creditor_transfer[bank]" value="{{ @$data->creditor_transfer->bank }}" required>
                    </div>

                    <div class="col-md-2">
                      <label>ឈ្មោះគណនី<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="ឈ្មោះគណនី" name="creditor_transfer[acc_name]" value="{{ @$data->creditor_transfer->acc_name }}" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>លេខគណនី<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" class="form-control" placeholder="លេខគណនី" name="creditor_transfer[acc_number]" value="{{ @$data->creditor_transfer->acc_number }}" required>
                    </div>
                  </div>
                </fieldset>

                <div class="row">
                  <div class="col-md-2">
                    <label>កំណត់សម្គាល់</label>
                  </div>
                  <div class="col-md-10 form-group">
                    <textarea class="form-control" rows="5" name="remark"> {{ @$data->remark }} </textarea>
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
                            accept=".pdf" 
                            type="file"
                            id="file"
                            name="file[]"
                            multiple="multiple"
                            value="{{ old('file') }}"
                        >
                      </div>

                      <div class="col-md-7">
                        @if(@$data->attachments)
                          <?php $atts = is_array($data->attachments) ? $data->attachments : json_decode($data->attachments); ?>
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
                      @foreach($staff as $item)
                        @if($item->id == $data->created_by)
                          <option value="{{ $item->id }}" selected="selected">{{ $item->reviewer_name }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ត្រួតពិនិត្យដោយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="reviewers[]" required multiple>

                      @foreach($data->reviewers() as $item)
                          <option value="{{ $item->id }}" selected="selected">
                            {{ $item->name }} ({{ $item->position_name }})
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
                    <label>អនុម័តដោយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control" name="approve_by" id="approve_by" required >
                      @foreach($approver as $item)
                        <option 
                            value="{{ @$item->id }}"
                            data-position_short="{{ $item->position_short_name }}"
                            @if($item->id == @$data->approver()->id) selected @endif>{{ @$item->approver_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

              </div>

              <div class="card-footer">
                @if ($data->status == config('app.approve_status_reject'))
                  <button
                      @if ($data->created_by != \Illuminate\Support\Facades\Auth::id())
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
                      @if ($data->created_by != \Illuminate\Support\Facades\Auth::id() || $data->status == config('app.approve_status_approve'))
                          disabled
                          title="Only requester that able to edit the request and record is pending"
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

@include('borrowing_loan.partials.js')
