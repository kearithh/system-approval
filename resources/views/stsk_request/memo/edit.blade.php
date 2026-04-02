@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@push('css')
    <style>
        .dropdown-fontname li {
            width: 200px;
            padding-left: 15px;
        }
    </style>

@endpush


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
                    <a href="{{ route('request_memo.index') }}" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</a>
                </div>
                <div class="col-md-12">
                    <form
                        method="POST"
                        action="{{ route('request_memo.update', $memo->id) }}"
                        class="form-horizontal">
                        @csrf
                        @method('POST')

                        <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($memo->id) }}">
                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('Edit Memo') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">
                                {{--<div class="row">--}}
                                {{--<div class="col-md-12 text-right">--}}
                                {{--<a href="{{ route('request.index') }}" class="btn btn-sm btn-primary">{{ __('ថយក្រោយ') }}</a>--}}
                                {{--</div>--}}
                                {{--</div>--}}


                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('លេខរៀង') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('no') ? ' has-danger' : '' }}">
                                            <input
                                                type="number"
                                                id="no"
                                                class="form-control{{ $errors->has('no') ? ' is-invalid' : '' }}"
                                                name="no"

                                                value="{{ old('no', $memo->no) }}"
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('ចំណងជើង(KM)') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('title_km') ? ' has-danger' : '' }}">
                                            <input
                                                type="text"
                                                id="title_km"
                                                class="form-control{{ $errors->has('title_km') ? ' is-invalid' : '' }}"
                                                name="title_km"
                                                required
                                                value="{{ old('title_km',$memo->title_km) }}"
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('ចំណងជើង(EN)') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('title_en') ? ' has-danger' : '' }}">
                                            <input
                                                type="text"
                                                id="title_en"
                                                class="form-control{{ $errors->has('title_ne') ? ' is-invalid' : '' }}"
                                                name="title_en"
                                                value="{{ old('title_en', $memo->title_en) }}"
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('ចាប់ផ្ដើមអនុវត្ត') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('title_en') ? ' has-danger' : '' }}">
                                            <input
                                                type="text"
                                                id="start_date"
                                                class="datepicker form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}"
                                                name="start_date"
                                                required
                                                value="{{ old('start_date', $memo->start_date->format('d/m/Y')) }}"
                                                data-inputmask-alias="datetime"
                                                data-inputmask-inputformat="dd/mm/yyyy"
                                                data-mask="" im-insert="true">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('បរិយាយ') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('desc') ? ' has-danger' : '' }}">
                      <textarea
                          id="desc"
                          class="desc_textarea form-control{{ $errors->has('desc') ? ' is-invalid' : '' }}"
                          name="desc"

                      >@if($memo){{ $memo->desc }}@else{{ old('desc') }}@endif</textarea>
                                            @if ($errors->has('desc'))
                                                <span id="desc" class="error text-danger" for="desc">{{ $errors->first('desc') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>








                                <div class="row point"​>
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០១') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                      <textarea
                          id="point1"
                          class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                          name="point[]"
                          required
                      >@if(isset($point[0])){{ $point[0] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point1" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row point @if(!isset($point[1])) hidden @endif"​>
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០២') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                      <textarea
                          id="point2"
                          class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                          name="point[]"
                      >@if(isset($point[1])){{ $point[1] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point2" class="error text-danger" for="point2">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row point @if(!isset($point[2])) hidden @endif"​>
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៣') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                      <textarea
                          id="point3"
                          class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                          name="point[]"
                      >@if(isset($point[2])){{ $point[2] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point3" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[3])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៤') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                      <textarea
                          id="point4"
                          class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                          name="point[]"
                      >@if(isset($point[3])){{ $point[3] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point4" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[4])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៥') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                      <textarea
                          id="point5"
                          class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                          name="point[]"
                      >@if(isset($point[4])){{ $point[4] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point4" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[5])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៦') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                      <textarea
                          id="point6"
                          class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                          name="point[]"
                      >@if(isset($point[5])){{ $point[5] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point4" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[6])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៧') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                      <textarea
                          id="point7"
                          class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                          name="point[]"
                      >@if(isset($point[6])){{ $point[6] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point4" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[7])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៨') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                      <textarea
                          id="point7"
                          class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                          name="point[]"
                      >@if(isset($point[7])){{ $point[7] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point7" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[8])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៩') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                      <textarea
                          id="point9"
                          class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                          name="point[]"
                      >@if(isset($point[8])){{ $point[8] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point7" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[9])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១០') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                      <textarea
                          id="point10"
                          class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                          name="point[]"
                      >@if(isset($point[9])){{ $point[9] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point10" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button"
                                                id="addPoint"
                                                class="btn btn-sm btn-success">
                                            <i class="fa fa-plus"></i>
                                            {{--                              {{ __('Add') }}--}}
                                        </button>
                                    </div>
                                </div>



                                <hr>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">{{ __('ចម្លងជូន') }}</label>
                                        <div class="col-sm-10">
                                            <div class="form-group{{ $errors->has('practise_point') ? ' has-danger' : '' }}">
                                                <input
                                                    type="number"
                                                    min="1"
                                                    max="10"
                                                    id="practise_point"
                                                    class="form-control{{ $errors->has('practise_point') ? ' is-invalid' : '' }}"
                                                    name="practise_point"
                                                    value="{{ old('practise_point', $memo->practise_point) }}"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-2 form-group">
                                          <label>សម្រាប់ក្រុមហ៊ុន</label>
                                        </div>
                                        <div class="col-md-10">
                                          <select class="form-control select2"​​​​​ name="company_id">
                                            @foreach($company as $key => $value)
                                                <option value="{{ $value->id}}"
                                                        @if($memo->company_id == $value->id))
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
                                @include('request_memo.partials.reviewer_section_edit', ['approvals' => $approvals])
                                <div>

                                <!-- khmer date -->
                                <div class="col-sm-12">

                                    <div class="row">
                                        <div class="col-md-2">
                                          <label>ថ្ងៃស្នើរជាខ្មែរ</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input
                                                  type="text"
                                                  id="khmer_date"
                                                  class="form-control{{ $errors->has('khmer_date') ? ' is-invalid' : '' }}"
                                                  name="khmer_date"
                                                  placeholder="ឧទាហរណ៍ៈ ថ្ងៃចន្ទ ១៤កើត ខែចេត្រ ឆ្នាំកុរ ឯកស័ក ពុទ្ធសករាជ ២៥៦៣"
                                                  required
                                                  value="{{ old('khmer_date', $memo->khmer_date) }}"
                                            >
                                        </div>
                                    </div>
                                </div><br>

                              </div>

                            </div>
                            <div class="card-footer">
                                @include('global.resubmit_btn')
                                <button
                                    title="Update the Request"
                                    type="submit"
                                    value="1"
                                    name="submit"
                                    class="btn btn-success">
                                    {{ __('Update') }}
                                </button>

                            </div>
                        </div>
                    </form>

                {{----------------------------------}}
                <!-- Modal -->
                    {{--          @include('request.modal')--}}
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

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            todayHighlight:true
        });

        $(".select2").select2({
            tags: true
        });
        $(".request-by-select2").select2();


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

        {{--$('#qty, #unit_price, #vat').on('change keyup', function (e) {--}}
        {{--    var qty = $('#qty').val();--}}
        {{--    var unit_price = $('#unit_price').val();--}}
        {{--    var vat = $('#vat').val();--}}

        {{--    var amount = parseFloat(qty * unit_price).toFixed(2);--}}
        {{--    var total = parseFloat(parseFloat(amount) + parseFloat((vat *amount)/100)).toFixed(2);--}}
        {{--    $('#amount').val('$ '+ total)--}}
        {{--});--}}

        {{--function formRequestCancel() {--}}
        {{--    $('#purpose').val(' ');--}}
        {{--    $('#requestForm').submit()--}}
        {{--}--}}

        $('.point_textarea, .desc_textarea').summernote({
            fontNames: [
                // "Roboto",
                // "Arial",
                // "Arial Black",
                // "Comic Sans MS",
                // "Courier New",
                // "Helvetica Neue",
                // "Helvetica",
                // "Impact",
                // "Lucida Grande",
                // "Tahoma",
                // "Times New Roman",
                // "Verdana",
                "Khmer OS Content",
                "Khmer OS Muol Light"
            ],
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                // ['font', ['strikethrough', 'superscript', 'subscript']],
                // ['fontsize', ['fontsize']],
                // ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                // ['height', ['height']]
                ['fontname', ['fontname']],
            ]
        });

        $('#addPoint').on('click', function () {
            var pointBox = $('div.point').each(function() {

                var hiddenBox = $(this).hasClass('hidden');
                if (hiddenBox) {
                    $(this).removeClass('hidden');
                    return false

                }
            });
        });
        $('#start_date').inputmask()


    </script>
@endpush
