{{--@extends('layouts.group_request', ['activePage' => 'group_request', 'titlePage' => __('User Management')])--}}
@extends('adminlte::page', ['activePage' => @$_GET['company'], 'titlePage' => __('Dashboard')])

@section('plugins.Select2', true)
@section('plugins.Pace', true)
@push('css')
<style>
    .table td, .table th {
        padding: .75rem .3rem;
    }
    .small-label {
        margin-top: 5px;
        font-size: 12px;
        opacity: 0.7;
    }
</style>
@endpush

@section('content')
{{--  <div class="content">--}}
    <div class="container-fluid">
      <!-- /.row -->
      <div class="row">
        <div class="col-sm-12">
            @include('group_request.partials.create_form')
        </div>
      </div>
        <div class="row">
            <div class="col-sm-12">
                @include('group_request.partials.search')
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @include('group_request.partials.list')
            </div>
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
    </div>
@include('group_request.partials.modal')

@endsection

@push('js')
  <script>
      $( document ).ready(function() {
          $('.upload-file').click(function() {
              var request_id = $(this).data('id');
              $.ajax({
                  type: "GET",
                  url: "{{ route('re.get-request-form') }}",
                  data: {
                      _token: "{{ csrf_token() }}",
                      request_id: request_id
                  },
                  success: function(data) {
                      $("#modal-body").html(data);
                      $(".select2").select2({
                          placeholder: {
                              id: null,
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
                      $('.datepicker').datepicker({
                          format: 'dd-mm-yyyy',
                          todayHighlight:true,
                          autoclose: true
                      });
                      
                      $("#upload-file").modal();

                      $("#file").on( "change", function( event ) {
                        // allow file 15M
                        if(this.value && this.files[0].size > 15500000){
                          alert("File is too big!");
                          this.value = "";
                        };
                      });

                      //unable changed company and department
                    //   $( ".company_id").on( "change", function( event ) {
                    //       $(".department_box").html('<select required class="form-control"><option value=""> << ជ្រើសរើស >> </option></select>');
                    //       var company_id = $(this).val();
                    //       //alert();
                    //       $.ajax({
                    //           type: "GET",
                    //           url: "{{ route('re.get-company-by-department') }}",
                    //           data: {
                    //               _token: "{{ csrf_token() }}",
                    //               company_id: company_id
                    //           },
                    //           success: function(data) {
                    //               $(".department_box").html(data);
                    //               $(".select2").select2({
                    //                   placeholder: {
                    //                       id: null, // the value of the option
                    //                       text: ' << ជ្រើសរើស >> '
                    //                   }
                    //               });
                    //           },
                    //           error: function(data) {
                    //               console.log(data)
                    //           }
                    //       });

                    //       //get approver by company
                    //       $.ajax({
                    //           type: "GET",
                    //           url: "{{ route('re.get-approver-by-company') }}",
                    //           data: {
                    //               _token: "{{ csrf_token() }}",
                    //               company_id: company_id
                    //           },
                    //           success: function(data) {
                    //               $(".approver_box").html(data);
                    //               $(".select2").select2({
                    //                   placeholder: {
                    //                       id: null, // the value of the option
                    //                       text: ' << ជ្រើសរើស >> '
                    //                   }
                    //               });
                    //               console.log(data);
                    //           },
                    //           error: function(data) {
                    //               console.log(data)
                    //           }
                    //       });
                          
                    //   });
                  },
                  error: function(data) {
                      console.log(data)
                  }
              });

          });

          $('.edit-template').click(function() {
              var request_id = $(this).data('id');
              $.ajax({
                  type: "GET",
                  url: "{{ route('re.get-edit-template-form') }}",
                  data: {
                      _token: "{{ csrf_token() }}",
                      request_id: request_id
                  },
                  success: function(data) {
                      $("#modal-body").html(data);
                      $(".select2").select2({
                          placeholder: {
                              id: null,
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
                      $('.datepicker').datepicker({
                          format: 'dd-mm-yyyy',
                          todayHighlight:true,
                          autoclose: true
                      });
                      $("#upload-file").modal();
                      $( ".company_id").on( "change", function( event ) {
                          $(".department_box").html('<select required class="form-control"><option value=""> << ជ្រើសរើស >> </option></select>');
                          var company_id = $(this).val();
                          //alert();
                          $.ajax({
                              type: "GET",
                              url: "{{ route('re.get-company-by-department') }}",
                              data: {
                                  _token: "{{ csrf_token() }}",
                                  company_id: company_id
                              },
                              success: function(data) {
                                  $(".department_box").html(data);
                                  $(".select2").select2({
                                      placeholder: {
                                          id: null, // the value of the option
                                          text: ' << ជ្រើសរើស >> '
                                      }
                                  });
                              },
                              error: function(data) {
                                  console.log(data)
                              }
                          });

                      });
                  },
                  error: function(data) {
                      console.log(data)
                  }
              });

          });

          $('.delete-template').click(function() {
              var id = $(this).data('id');
              var formAction = $(this).attr('action');

              Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, delete it!'
              }).then((result) => {
                  if (result.value) {
                      $.ajax({
                          url: formAction,
                          method:"post",
                          type: "json",
                          data: {
                              "id": id,
                              "_token": "{{ csrf_token() }}",
                          },
                          success:function (data) {
                              if (data.status == 4) {
                                  Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                  );
                                  window.location.reload();
                              } else {
                                  console.log(data.msg);
                                  Swal.fire(
                                      {
                                          title: 'Delete failed !',
                                          text: "Please refresh your browser!",
                                          icon: 'warning',
                                      }
                                  )
                              }
                          },
                          fail:function (err) {
                              console.log(err);
                          }
                      });

                  }
              })

          });

          $('.datepicker').datepicker({
              format: 'dd-mm-yyyy',
              todayHighlight:true,
              autoclose: true
          });

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

          // re.get-company-by-department
          $( ".company_id").on( "change", function( event ) {
              $(".department_box").html('<select required class="form-control"><option value=""> << ជ្រើសរើស >> </option></select>');
              var company_id = $(this).val();

              $.ajax({
                  type: "GET",
                  url: "{{ route('re.get-company-by-department') }}",
                  data: {
                      _token: "{{ csrf_token() }}",
                      company_id: company_id
                  },
                  success: function(data) {
                      $(".department_box").html(data);
                      $(".select2").select2({
                          placeholder: {
                              id: null, // the value of the option
                              text: ' << ជ្រើសរើស >> '
                          }
                      });
                  },
                  error: function(data) {
                      console.log(data)
                  }
              });
          });

          $( "#company").on( "change", function( event ) {
              $(".department_id_search").html('<select required class="form-control"><option value=""> << ជ្រើសរើស >> </option></select>');
              var company_id = $(this).val();
              $.ajax({
                  type: "GET",
                  url: "{{ route('re.get-company-by-department') }}",
                  data: {
                      _token: "{{ csrf_token() }}",
                      company_id: company_id
                  },
                  success: function(data) {
                      $(".department_id_search").html(data);
                      $(".select2").select2({
                          placeholder: {
                              id: null, // the value of the option
                              text: ' << ជ្រើសរើស >> '
                          }
                      });
                  },
                  error: function(data) {
                      console.log(data)
                  }
              });
          });
      });
  </script>
@endpush
