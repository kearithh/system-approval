@extends('adminlte::page', ['activePage' => 'request', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)
@section('plugins.Sweetalert2', true)

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
              <a href="{{ route('request.index') }}" class="btn btn-primary btn-sm" style="margin-top: -35px"> Back</a>
          </div>
        <div class="col-md-12">
          <form
                  id="requestForm"
                  method="post"
                  action="{{ route('request.store') }}"
                  enctype="multipart/form-data"
                  autocomplete="off"
                  class="form-horizontal">
            @csrf
            @method('post')

            <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($requestForm->id) }}">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('ទម្រង់សំណើរ') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                {{--<div class="row">--}}
                  {{--<div class="col-md-12 text-right">--}}
                      {{--<a href="{{ route('request.index') }}" class="btn btn-sm btn-primary">{{ __('ថយក្រោយ') }}</a>--}}
                  {{--</div>--}}
                {{--</div>--}}

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ស្នើដោយ') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('user_id') ? ' has-danger' : '' }}">
                      <select required class="form-control request-by-select2" name="user_id" disabled>
                        @foreach($staffs as $item)
                          <option
                                  @if($item->id == $requestForm->user_id)
                                    selected
                                  @elseif ($item->id == Auth::id())
                                    selected
                                  @endif
                          value="{{ $item->id }}">{{ $item->name }}</option>
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
                  <label class="col-sm-2 col-form-label">{{ __('តាមរយះ') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                      <select required class="form-control position-select2" name="position_id" disabled>
                        @foreach($positions as $item)
                          <option

                                  @if($item->id == $requestForm->position_id)
                                  selected
                                  @elseif ($item->id == Auth::id())
                                  selected
                                  @endif

                                  value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('position_id'))
                        <span
                                id="name-error"
                                class="error text-danger"
                                for="input-name">
                          {{ $errors->first('position_id') }}
                        </span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('កម្មវត្ថុ') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                      <textarea
                              disabled
                              class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                              name="purpose"
                              required="true"
                              aria-required="true"
                      >@if($requestForm){{ $requestForm->purpose }} @else {{ old('purpose') }} @endif</textarea>
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                  <div class="row">
                      <label class="col-sm-2 col-form-label">{{ __('មូលហេតុ') }}</label>
                      <div class="col-sm-7">
                          <div class="form-group{{ $errors->has('reason') ? ' has-danger' : '' }}">
                      <textarea
                              id="purpose"
                              class="form-control{{ $errors->has('reason') ? ' is-invalid' : '' }}"
                              name="reason"
                              required
                              disabled
                      >@if($requestForm){{ $requestForm->reason }}@else{{ old('reason') }}@endif</textarea>
                              @if ($errors->has('reason'))
                                  <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('reason') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>


                  <div class="row">
                  <div class="col-md-12">
                    {{--<button type="button"--}}
                            {{--id="addItem"--}}
                            {{--class="btn btn-sm btn-primary"--}}
                            {{--data-toggle="modal"--}}
                            {{--data-target="#bd-example-modal-lg">--}}
                      {{--{{ __('Add Item') }}--}}
                    {{--</button>--}}
                    <p></p>
                        <div class="table-responsive">
                          <table class="table table-hover">
                            <thead class="card-header ">
                            <th style="width: 70px">ល.រ</th>
                            <th>ឈ្មោះ</th>
                            <th>បរិយាយ</th>
                            <th style="width: 50px">បរិមាណ</th>
                            <th style="width: 100px">តម្លៃរាយ($)</th>
                            <th style="width: 100px">សរុប($)</th>
                            </thead>
                            <tbody>
                            <?php $total = 0; ?>
                            @foreach($requestForm->items as $key => $item)
                              <tr>
                                <td class="td-actions">
                                  {{ $key + 1 }}
                                </td>
                                <td>
                                  {{ $item->name }}
                                </td>
                                <td>
                                  {{ $item->desc }}
                                </td>
                                <td>
                                  {{ $item->qty }}
                                </td>
                                <td>
                                  $ {{ number_format($item->unit_price, 2) }}
                                </td>
                                <td>
                                  $ {{ number_format($item->qty * $item->unit_price, 2) }}
                                </td>
                              </tr>
                              <?php $total += $item->qty * $item->unit_price ?>
                            @endforeach

                            <tr>
                              <td colspan="5" class="text-right">សរុប</td>
                              <td>
                                <strong> $ {{ number_format($total, 2) }}</strong>
                              </td>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                   </div>
                </div>
                  <div class="row">
                      <div class="col-sm-12">
                          {{ request_status($requestForm) }}
                          <a class="btn btn-xs btn-default" href="{{ route('request.generate_pdf', $requestForm->id) }}">
                              <i class="fa fa-file-pdf"></i>
                              PDF
                          </a>
                      </div>
                  </div>
              </div>
              <div class="card-footer">
                  @if ($requestForm->status < 0 || $requestForm->status == 100 || $requestForm->created_by == \Illuminate\Support\Facades\Auth::id())
                      <button id="btn_approve" type="button" class="btn btn-success" disabled>{{ __('Approve') }}</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button id="btn_reject" type="button" class="btn btn-danger" disabled>{{ __('Reject') }}</button>
                  @else
                      <button id="btn_approve" type="button" class="btn btn-success">{{ __('Approve') }}</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button id="btn_reject" type="button" class="btn btn-danger">{{ __('Reject') }}</button>
                  @endif
              </div>
            </div>
          </form>





          {{----------------------------------}}


        </div>
      </div>
    </div>
  </div>
@endsection
@push('js')
  <script>


      $(".position-select2").select2({
          tags: true
      });
      $(".request-by-select2").select2();


      $('#addItem').on('click', function (e) {
          e.preventDefault();

          var requestParam = $('#requestForm').serialize()
          $.ajax({
              type: "POST",
              url: "{{ action('RequestFormController@storeAjax') }}",
              data: {
                  _token: "{{ csrf_token() }}",
                 request_param: requestParam
              },
              dataType: "json",
              success: function(data) {

                  $('.request_token').val(data.request_token);
                  console.log(data.request_token)
                  //var obj = jQuery.parseJSON(data); if the dataType is not specified as json uncomment this
                  // do what ever you want with the server response
              },
              error: function(data) {
                  console.log(data)
                  // alert('error handling here');
              }
          });

      })

      $('#qty, #unit_price').on('change keyup', function (e) {
          var qty = $('#qty').val();
          var unit_price = $('#unit_price').val();

          var amount = parseFloat(qty * unit_price).toFixed(2);
          $('#amount').val('$ '+ amount)
      });

      $(document).on('click', '#btn_approve', function(e) {
          swal.fire({
              type: 'warning',
              icon: 'warning',
              title: 'សូមបញ្ចក់ម្ដងទៀត',
              showCancelButton: true,
              cancelButtonText: 'ចាកចេញ',
              confirmButtonText: 'បញ្ជូន',
              showLoaderOnConfirm: true,
          }).then((result) => {
              if (result.value) {
                  $.ajax({
                      type: "POST",
                      url: "{{ action('RequestFormController@approveAjax') }}",
                      data: {
                          _token: "{{ csrf_token() }}",
                          request_id: "{{ $requestForm->id }}"
                      },
                      dataType: "json",
                      success: function(data) {
                          if (data.status) {
                              swal.fire({
                                  type: 'success',
                                  title: 'The request was approved',
                                  timer: 1500
                              })
                              location.replace("{{ route('request.index') }}")
                          }
                          console.log(data.request_token)
                      },
                      error: function(data) {
                          console.log(data)
                      }
                  });
              }
          })
      });

      $(document).on('click', '#btn_reject', function(e) {
          swal.fire({
              title: 'សូមបញ្ចូលមូលហេតុខាងក្រោម',
              input: 'text',
              inputPlaceholder: 'Your comment',
              showCancelButton: true,
              cancelButtonText: 'ចាកចេញ',
              confirmButtonText: 'បញ្ជូន',
              showLoaderOnConfirm: true,
              inputValidator: (value) => {
                  if (!value) {
                      return 'Comment is required!'
                  }
              },
              allowOutsideClick: false
          }).then((result) => {
              if (result.value) {
                  if (result.value) {
                      $.ajax({
                          type: "POST",
                          url: "{{ action('RequestFormController@rejectAjax') }}",
                          data: {
                              _token: "{{ csrf_token() }}",
                              request_id: "{{ $requestForm->id }}",
                              comment: result.value
                          },
                          dataType: "json",
                          success: function(data) {
                              if (data.status) {
                                  swal.fire({
                                      type: 'success',
                                      title: 'The request was rejected',
                                      timer: 1500
                                  })
                                  location.replace("{{ route('request.index') }}")
                              }
                              console.log(data.request_token)
                          },
                          error: function(data) {
                              console.log(data)
                          }
                      });
                  }
              }
          })
      });


  </script>
@endpush