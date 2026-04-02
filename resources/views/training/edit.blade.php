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
              action="{{ route('training.update', $data->id) }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">កែប្រែសំណើសុំបើកវគ្គបណ្តុះបណ្តាល</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">

                  <div class="row">
                    <div class="col-md-2">
                      <label>ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
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
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>កម្មវត្ថុ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <textarea
                          class=" form-control"
                          name="subject"
                          required
                      >{{ $data->subject }}</textarea>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>គោលបំណង<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <textarea
                          class="point_textarea form-control"
                          name="purpose"
                          required
                      >{{ $data->purpose }}</textarea>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>សមាសភាពចូលរួម<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <textarea
                          class="form-control"
                          name="participating"
                          required
                      >{{ $data->participating }}</textarea>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>កម្មវិធីបណ្តុះបណ្តាល<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <textarea
                          class="form-control"
                          name="components"
                          required
                      >{{ $data->components }}</textarea>
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
                      >{{ $data->description }}</textarea>
                    </div>
                  </div>

                  @include('training.partials.item_table_edit')

                  <br>
                  <div class="row">
                    <div class="col-md-2">
                      <label>ឯកសារភ្ជាប់</label>
                    </div>
                    <div class="col-md-10 form-group">
                      <div class="row">
                        <div class="col-md-5">
                          <input
                              type="file"
                              id="file"
                              name="file[]"
                              multiple="multiple"
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
                      <label>ធ្វើឡើងនៅថ្ងៃទី(ខ្មែរ)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <input
                            type="text"
                            id="khmer_date"
                            class="form-control"
                            name="khmer_date"
                            value="{{$data->khmer_date}}"
                            required="required"
                            placeholder="ឧទាហរណ៍ៈ រាជធានីភ្នំពេញ ថ្ងៃចន្ទ ១៤កើត ខែចេត្រ ឆ្នាំកុរ ឯកស័ក ពុទ្ធសករាជ ២៥៦៣"
                      >
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>រៀបចំដោយ</label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control select2" name="user_id" required>
                        @foreach($reviewer as $key => $value)
                          @if($value->id == $data->user_id)
                            <option value="{{ $value->id}}" selected="selected">{{ $value->reviewer_name }}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control select2" name="review_by[]" required multiple>

                        @foreach($data->reviewers() as $item)
                            <option value="{{ $item->id }}" selected="selected">
                              {{ $item->name }}({{ $item->position_name }})
                            </option>
                        @endforeach

                        @foreach($reviewer as $key => $value)
                            @if(!in_array($value->id, $data->reviewers()->pluck('id')->toArray()))
                              <option value="{{ $value->id }}">{{  $value->reviewer_name }}</option>
                            @endif
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
                          <option value="{{ @$item->id }}" 
                            @if($item->id == @$data->approver()->id) selected @endif
                          >
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

@include('training.partials.add_more_js')

