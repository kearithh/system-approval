@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

@section('content')
  
  @if (@auth()->user()->branch->branch == 1)
      <h2 style="color: red"> User in Branch can't use</h2>
      <?= die() ?>
  @endif

  @include('global.style_default_approve')
        
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  id="requestForm"
                  method="POST"
                  action="{{ route('training.store') }}"
                  enctype="multipart/form-data"
                  class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">សំណើសុំបើកវគ្គបណ្តុះបណ្តាល</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">

                  <div class="row">
                    <div class="col-md-2">
                      <label>ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control company select2" id="company_id" name="company_id">
                        @foreach($company as $key => $value)
                          <option value="{{ $value->id}}"
                                  @if(Auth::user()->company_id == $value->id))
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
                      >សំណើសុំបើកវគ្គបណ្តុះបណ្តាលស្តីពី</textarea>
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
                      ></textarea>
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
                      >ភ្ជាប់ជូនជាមួយឯកសារភ្ជាប់</textarea>
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
                      >ភ្ជាប់ជូនជាមួយឯកសារភ្ជាប់</textarea>
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
                      >តបតាមកម្មវត្ថុនិងមូលហេតុដូចបានជម្រាបជូនខាងលើ ខ្ញុំមានកិត្តិយសសូមជម្រាបជូនលោកស្រីប្រធាននាយិកាប្រតិបត្តិមេត្តាជ្រាបថា៖</textarea>
                    </div>
                  </div>

                  @include('training.partials.item_table')
                  <br>
                  <div class="row">
                    <div class="col-md-2">
                      <label>ឯកសារភ្ជាប់<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10">
                      <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                        <input 
                            required 
                            type="file"
                            id="file"
                            name="file[]"
                            multiple="multiple"
                            value="{{ old('file') }}"
                        >
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
                        @foreach($reviewer as $item)
                          @if($item->id==Auth::id())
                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                          @endif
                        @endforeach()
                      </select><br/>
                    </div>
                  </div>

                  <fieldset>
                    <legend>
                      <button 
                            type="button"
                            value="1"
                            name="check"
                            class="check btn btn-sm btn-info">
                        By default in plan
                      </button>
                      <button 
                            type="button"
                            value="2"
                            name="check"
                            class="check btn btn-sm btn-primary">
                        By default out plan
                      </button>
                      <button
                            type="button"
                            value="1"
                            name="clear"
                            class="clear btn btn-sm btn-secondary">
                        Clear default
                      </button>
                      <div class="row">
                        <input type="hidden" name="" id="my_department" value="{{ Auth::user()->department_id }}">
                        <input type="hidden" name="" id="my_type" value="request">
                        <input type="hidden" name="" id="type_request" value="{{ config('app.type_training') }}">
                        <input type="hidden" name="" id="type_report" value="">
                      </div>
                    </legend>

                    <div class="row">
                      <div class="col-md-2">
                        <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <select class="form-control js-reviewer-multi" name="review_by[]" required multiple>
                          @foreach($reviewer as $item)
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
                        <select class="form-control js-approver" name="approver" required>
                          <option value=""> << ជ្រើសរើស >> </option>
                          @foreach($approver as $item)
                            <option 
                                  value="{{ @$item->id }}" 
                                  @if($item->id == 543) selected @endif
                            >
                                  {{ @$item->name }}({{$item->position_name}})
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </fieldset>

                </div>

                <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        name="submit"
                        class="btn btn-success">
                  Submit
                </button>

              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@include('training.partials.add_more_js')

@include('global.js_default_approve')
