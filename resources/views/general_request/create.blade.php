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
                        <div class="form-group">
                          <select class="form-control" required name="type" id="type">
                              @foreach(config('app.branch_request') as $key => $value)
                                  <option value="{{ $key }}">{{ $value }}</option>
                              @endforeach
                          </select>
                        </div>
                    </div>
                </div>
              </div>

              <div id="type_request">
               
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
  $(document).ready(function(){
    checkType();

    $('#type').on('change', function () {
      checkType();
    });

    function checkType() {
      type = $('#type').val();
      $.ajax({
          type: "GET",
          url: "{{ route('get-general-request') }}",
          data: {
              _token: "{{ csrf_token() }}",
              type: type
          },
          success: function(data) {
              $("#type_request").empty();
              $("#type_request").html(data);



                $('.desc_textarea').summernote({
                  fontNames: [
                      "Khmer OS Content",
                      "Khmer OS Muol Light"
                  ],
                  toolbar: [
                      // [groupName, [list of button]]
                      ['style', ['bold', 'italic', 'underline', 'clear']],
                      // ['font', ['strikethrough', 'superscript', 'subscript']],
                      // ['fontsize', ['fontsize']],
                      // ['color', ['color']],
                      ['para', ['ul', 'paragraph']],
                      // ['height', ['height']]
                      ['fontname', ['fontname']],
                  ]
              });

              $('.note-popover').attr('hidden',true);

              $(".select2").select2({
                  placeholder: {
                      id: null, // the value of the option
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

          },
          error: function(data) {
              console.log(data)
          }
      });

    }
  });  
  </script>
@endpush

@include('general_request.partials.js')


