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
                  action="{{ route('request_dispose.store') }}"
                  class="form-horizontal">
            @csrf
            {{--@method('post')--}}

            <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($requestForm->id) }}">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('ទម្រង់សំណើរសុំកាត់សម្ភារៈខូចខាត') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">

{{--                  <div class="row">--}}
{{--                      <div class="col-md-12">--}}
{{--                          <button type="button"--}}
{{--                                  id="addItem"--}}
{{--                                  class="btn btn-sm btn-success"--}}
{{--                                  data-toggle="modal"--}}
{{--                                  data-target="#bd-example-modal-lg">--}}
{{--                              {{ __('Create Item') }}--}}
{{--                          </button>--}}
{{--                          <p></p>--}}
{{--                          @include('request_disposal.item_table')--}}
{{--                      </div>--}}
{{--                  </div>--}}

                  @include('request_disposal.partials.item_table')
{{--                  <hr>--}}

                  <?= Form::textarea([
                      "label" => "បរិយាយ",
                      "name" => "desc",
                      "value" => old('desc'),
                      "required" => true,
                      "aria-required" => true,
                      "rows" => 7
                  ]); ?>

                  <?= Form::select([
                      "label" => "សំណង",
                      "name" => "is_penalty",
                      "id" => "is_penalty",
                      "option" => [
//                          [
//                              "label" => "---",
//                              "value" => "0",
//                          ],
                          [
                              "label" => "No",
                              "value" => "0",
                          ],
                          [
                              "label" => "Yes",
                              "value" => "1",
                          ]
                      ]
                  ]); ?>
                  <div class="penalty" style="display: none">
                      <?= Form::text([
                          "label" => "ឈ្មោះ",
                          "name" => "penalty[name]",
                          "value" => old('name'),
                      ]); ?>

                      <?= Form::number([
                          "label" => "សរុប($)",
                          "name" => "penalty[amount]",
                          "value" => old('amount'),
                      ]); ?>
                  </div>
                  <div class="no_penalty">
                      <?= Form::textarea([
                          "label" => "ហេតុផល",
                          "name" => "penalty[reason]",
                          "value" => old('reason'),
                      ]); ?>
                  </div>


                  <div class="row">
                    <div class="col-md-2">
                      <label>ពិនិត្យដោយ</label>
                    </div>
                    <div class="col-md-10">
                      <select class="form-control select2"​​​​​ name="review_by[]" multiple>
                        @foreach($staffs as $key => $value)
                        <option value="{{ $value->id}} " >{{ $value->name }}</option>
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
                        formaction="{{ route('request_dispose.store') }}"
                        form="requestForm"
                        class="btn btn-success">
                  {{ __('Submit') }}
                </button>

              </div>
            </div>
          </form>

          {{----------------------------------}}
        <!-- Modal -->
{{--          @include('request_disposal.modal')--}}
        </div>
      </div>
    </div>
  </div>
@endsection
@push('js')
  <script>


      $(".review_by_select2").select2({
          // tags: true
      });

      // $(".position-select2").select2({
          // tags: true
      // });
      // $(".request-by-select2").select2();


      {{--$('#addItem').on('click', function (e) {--}}
      {{--    e.preventDefault();--}}

      {{--    var requestParam = $('#requestForm').serialize()--}}
      {{--    $.ajax({--}}
      {{--        type: "POST",--}}
      {{--        url: "{{ action('RequestFormController@storeAjax') }}",--}}
      {{--        data: {--}}
      {{--            _token: "{{ csrf_token() }}",--}}
      {{--           request_param: requestParam--}}
      {{--        },--}}
      {{--        dataType: "json",--}}
      {{--        success: function(data) {--}}

      {{--            $('.request_token').val(data.request_token);--}}
      {{--            console.log(data.request_token)--}}
      {{--        },--}}
      {{--        error: function(data) {--}}
      {{--            console.log(data)--}}
      {{--        }--}}
      {{--    });--}}

      {{--})--}}

      // $('#qty, #unit_price, #vat').on('change keyup', function (e) {
      //     var qty = $('#qty').val();
      //     var unit_price = $('#unit_price').val();
      //     var vat = $('#vat').val();
      //
      //     var amount = parseFloat(qty * unit_price).toFixed(2);
      //     var total = parseFloat(parseFloat(amount) + parseFloat((vat *amount)/100)).toFixed(2);
      //     $('#amount').val('$ '+ total)
      // });

      // function formRequestCancel() {
      //     $('#purpose').val(' ');
      //     $('#requestForm').submit()
      // }

      $('.datepicker').datepicker({
          format: 'dd-mm-yyyy'
      });
      $('.datepicker').inputmask();


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

      // Edit
      {{--$(".edit_id").on('click', function () {--}}
          {{--var token = $(this).data('token');--}}
          {{--$.ajax({--}}
              {{--type: "POST",--}}
              {{--url: "{{ action('RequestItemController@editAjax') }}",--}}
              {{--data: {--}}
                  {{--_token: "{{ csrf_token() }}",--}}
                  {{--token: token--}}
              {{--},--}}
              {{--dataType: "json",--}}
              {{--success: function(data) {--}}
                  {{--console.log(data.request_token)--}}
                  {{----}}
                  {{--$('#name').val(data.name);--}}
                  {{--$('#desc').val(data.desc);--}}
                  {{--$('#qty').val(data.qty);--}}
                  {{--$('#unit_price').val(data.unit_price);--}}
                  {{----}}
              {{--},--}}
              {{--error: function(data) {--}}
                  {{--console.log(data)--}}
              {{--}--}}
          {{--});--}}
      {{--})--}}
  </script>
@endpush
@include('request_disposal.partials.add_more_js')
