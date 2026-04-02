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
          <a href="{{ url('request_dispose') }}" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</a>
        </div>
        <div class="col-md-12">
          <form
              id="requestForm"
              method="POST"
              action="{{ route('request_dispose.update', $requestForm->id) }}"
              class="form-horizontal">
            @csrf
            <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($requestForm->id) }}">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Edit Dispose') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                  @include('request_disposal.partials.item_table')
                  <?= Form::textarea([
                      "label" => "បរិយាយ",
                      "name" => "desc",
                      "value" => $requestForm->desc,
                      "required" => true,
                      "aria-required" => true,
                      "rows" => 7
                  ]); ?>

                  @if($requestForm->is_penalty == 1)
                    <div class="row">
                      <div class="col-md-2">
                        <label>សំណង</label>
                      </div>
                      <div class="col-md-10">
                        <select class="form-control"​​​​​ name="is_penalty" id="is_penalty">
                          <option value="0">No</option>
                          <option value="1" selected="selected">Yes</option>
                        </select>
                        <br/>
                      </div>
                    </div>
                    <div class="penalty">
                        <?= Form::text([
                            "label" => "ឈ្មោះ",
                            "name" => "penalty[name]",
                            "value" => json_decode($requestForm->penalty)->name,
                        ]); ?>

                        <?= Form::number([
                            "label" => "សរុប($)",
                            "name" => "penalty[amount]",
                            "value" => json_decode($requestForm->penalty)->amount,
                        ]); ?>
                    </div>
                    <div class="no_penalty" style="display: none">
                        <?= Form::textarea([
                            "label" => "ហេតុផល",
                            "name" => "penalty[reason]",
                            "value" => json_decode($requestForm->penalty)->reason,

                        ]); ?>
                    </div>
                  @else
                    <div class="row">
                      <div class="col-md-2">
                        <label>សំណង</label>
                      </div>
                      <div class="col-md-10">
                        <select class="form-control"​​​​​ name="is_penalty" id="is_penalty">
                          <option value="0" selected="selected">No</option>
                          <option value="1">Yes</option>
                        </select>
                        <br/>
                      </div>
                    </div>
                    <div class="penalty" style="display: none">
                        <?= Form::text([
                            "label" => "ឈ្មោះ",
                            "name" => "penalty[name]",
                            "value" => json_decode($requestForm->penalty)->name,
                        ]); ?>

                        <?= Form::number([
                            "label" => "សរុប($)",
                            "name" => "penalty[amount]",
                            "value" => json_decode($requestForm->penalty)->amount,
                        ]); ?>
                    </div>
                    <div class="no_penalty">
                        <?= Form::textarea([
                            "label" => "ហេតុផល",
                            "name" => "penalty[reason]",
                            "value" => json_decode($requestForm->penalty)->reason,

                        ]); ?>
                    </div>
                  @endif

                  <div class="row">
                    <div class="col-md-2">
                      <label>ពិនិត្យដោយ</label>
                    </div>
                    <div class="col-md-10">
                      <select class="form-control select2"​​​​​ name="review_by[]">
                        @foreach($staffs as $key => $value)
                          @if($value->id == $requestForm->review_by)
                            <option value="{{ $value->id}}" selected="selected">{{ $value->name }}</option>
                          @else
                            <option value="{{ $value->id}}">{{ $value->name }}</option>
                          @endif
                        @endforeach()
                      </select>
                    </div>
                  </div>
              </div>
                <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        name="submit"
                        form="requestForm"
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

  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
  <script>
      @if(session('status'))
      Swal.fire({
          title: 'Success',
          icon: 'success',
          timer: '2000',
      })
      @endif
  </script>

  <script>
      $(".position-select2").select2({
          // tags: true
      });
      $(".request-by-select2").select2();

      $('.datepicker').datepicker({
          format: 'dd-mm-yyyy'
      });
      $('.datepicker').inputmask();

      $(".review_by_select2").select2({
          tags: true
      });

      $("#is_penalty").on('change', function () {
          var penalty = $(this).val();
          console.log(penalty);
          if(penalty == 1) {
              $('.penalty').attr('style', 'display:block');
              $('.no_penalty').attr('style', 'display:none')
          } else {
              $('.penalty').attr('style', 'display:none');
              $('.no_penalty').attr('style', 'display:block')
          }
      });

@endpush
@include('request_disposal.partials.add_more_js')

