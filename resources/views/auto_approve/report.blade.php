@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('content')
  @if ( @admin_action() || (auth()->id() == 28) )
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
              id="requestForm"
              method="POST"
              action="{{ route('approve_report.store') }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title"><strong>Auto Approve Report</strong></h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body">

                <div class="row">
                  <div class="col-md-2">
                    <label>អ្នកអនុញ្ញាត<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="user_id" required>
                      <option value=""><< ជ្រើសរើស >></option>
                      @if(@admin_action())
                        @foreach($staff as $item)
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                        @endforeach
                      @else
                        @foreach($staff as $item)
                          @if(auth()->id() == @$item->id)
                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                          @endif
                        @endforeach
                      @endif
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control company select2" name="company_id">
                      <option value="all"><< All >></option>
                      @foreach($company as $key => $value)
                        <option value="{{ $value->id}}">{{ $value->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ចាប់ពី<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                      <input
                          type="text"
                          class="datepicker form-control "
                          name="start_date"
                          required
                          data-inputmask-inputformat="dd-mm-yyyy"
                          placeholder="dd-mm-yyyy"
                          autocomplete="off"
                      >
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ដល់<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                      <input
                          type="text"
                          class="datepicker form-control "
                          name="end_date"
                          required
                          data-inputmask-inputformat="dd-mm-yyyy"
                          placeholder="dd-mm-yyyy"
                          autocomplete="off"
                      >
                    </div>
                  </div>
                </div>

              </div>

              <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        id="submit"
                        name="submit"
                        class="btn btn-success">
                  Generate
                </button>
                <span id="divMsg" style="display:none;"> Please wait... </span>
              </div>
              
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  @else
  <h2 style="color: red"> User can't use</h2>
  @endif
@endsection

@include('auto_approve.partials.js')
