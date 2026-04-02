@extends('adminlte::page', ['activePage' => 'request', 'titlePage' => __('Request')])

@section('plugins.Select2', true)

@section('css')
  <style>
    .table td {
      vertical-align: middle;
    }
  </style>
@stop

@section('content')
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-md-12">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <i class="fas fa-search"></i>
                <strong>Search and Filter</strong>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @include('summary_report.partials.filter_report',['clear_url' => 'report'])
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 col-lg-12">
          <div class="card">
            <div class="card-header card-header-primary">
              <i class="fas fa-list"></i>
              <strong>List of report</strong>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive" style="padding: 0 10px">
                <table class="table table-striped table-bordered table-hover">
                  <thead class="">
                    <th style="min-width: 50px;">NO.</th>
                    <th style="min-width: 150px;">Company</th>
                    <th style="min-width: 180px;">Department</th>
                    <th style="min-width: 80px;">Type</th>
                    <th style="min-width: 280px;">Report Name</th>
                    <th style="min-width: 80px;">Report</th>
                    <th style="min-width: 80px;">Submited</th>
                    <th style="min-width: 80px;">Not</th>
                  </thead>
                  <tbody>

                    @php
                        $i = 1;
                        $_total_report = 0;
                        $_total_submit = 0;
                        $_total_not_submit = 0;
                    @endphp

                    @foreach($data as $key => $value)

                      @php
                        $_total_report += @$value->daily;
                        $_total_submit += @$value->submited;
                        $_total_not_submit += @$value->daily - @$value->submited;
                      @endphp

                      <tr title="{{$value->id}}">
                        <td> {{ $i++ }}</td>
                        <td>{{@$value->company_name}}</td>
                        <td>{{@$value->department_name}}</td>
                        <td>{{@$value->tags}}</td>
                        <td>{{@$value->name}}</td>
                        <td>{{@$value->daily}}</td>
                        <td>{{@$value->submited}}</td>
                        <td>{{@$value->daily - @$value->submited}}</td>   
                      </tr> 
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="5" class="text-right">Total:</th>
                      <th class="text-primary">{{@$_total_report}}</th>
                      <th class="text-success">{{@$_total_submit}}</th>
                      <th class="text-danger">{{@$_total_not_submit}}</th>
                    </tr>
                  </tfoot>
                </table>

              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
  <script>
    $(document).ready(function(){
      //var va = ($('#sort_review').text());

      $(".select2").select2({
          // tags: true
      });

      $('.mydatepicker').datepicker({
          format: 'dd-mm-yyyy',
          todayHighlight:true,
          autoclose: true
      });


      $( "#company").on( "change", function( event ) {
          $("#department").empty();
          $("#department").html('<select required class="form-control"><option value=""> << ជ្រើសរើស >> </option></select>');
          var company = $(this).val();
          // alert(company);
          $.ajax({
              type: "GET",
              url: "{{ route('re.get-company-by-department') }}",
              data: {
                  _token: "{{ csrf_token() }}",
                  company_id: company
              },
              success: function(data) {
                  $("#department").html(data);
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