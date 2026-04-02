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
              <h4 class="card-title"><strong>Filter</strong></h4>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @include('lesson.partials.filter', ['clear_url' => 'public_lesson'])
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
              <h4 class="card-title"><strong>Lesson List</strong></h4>
              <div class="col-sm-12 text-right">
                <button class="btn btn-outline-secondary btn-sm">Total: {{ $total }} Records</button>
              </div>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table table-striped">
                <thead class="">
                  <th>ល.រ</th>
                  <th>សកម្ម</th>
                  <th style="min-width: 152px;">ក្រុមហ៊ុន</th>
                  <th style="min-width: 152px;">ស្នើសំុដោយ</th>
                  <th style="min-width: 152px;">ចំណងជើងមេរៀន</th>
                  <th style="min-width: 100px">ថ្ងៃស្នើ</th>
                </thead>
                <tbody>
                  @if($data->count())
                    <?php $i = 1; ?>
                    @foreach($data as $key => $item)
                      <tr title="Request ID: {{$item->id}}">
                        <td> {{ $i++ }}</td>
                        <td>
                          <a  href="{{ route('lesson.show', $item->id) }}" 
                              class="preview btn btn-xs btn-info" 
                              title="View the request">
                              <i class="fa fa-eye"></i>
                          </a>
                          @if($item->created_by == auth()->id())
                            <a  href="{{ route('lesson.edit', $item->id) }}" 
                                class="preview btn btn-xs btn-success" 
                                title="View the request">
                                <i class="fa fa-pen"></i>
                            </a>
                            <button 
                                class="btn-delete btn btn-xs btn-danger"
                                action="/lesson/{{ $item->id }}/delete"
                                data-item-id="{{ $item->id }}"
                                title="View the request">
                                <i class="fa fa-trash"></i>
                            </button>
                          @else
                            <button disabled 
                                class="preview btn btn-xs btn-success" 
                                title="View the request">
                                <i class="fa fa-pen"></i>
                            </button>
                            <button disabled 
                                class="preview btn btn-xs btn-danger" 
                                title="View the request">
                                <i class="fa fa-trash"></i>
                            </button>
                          @endif
                        </td>
                        <td>
                          {{ $item->company_name }}
                        </td>
                        <td>
                          {{ $item->requester_name }}
                        </td>
                        <td>
                          {{ $item->title }}
                        </td>
                        <td>
                          {{ created_at($item->created_at) }}
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="6">Record Not Found!</td>
                    </tr>
                  @endif
                </tbody>
              </table>
              {{ $data->appends($_GET)->links() }}
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  <input type="hidden" name="token" id="token" value="{{ csrf_token() }}">
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $(".preview").on('click', function () {
                localStorage.previous = window.location.href ;
            });
        });
    </script>

    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {

            $(".preview").on('click', function () {
                localStorage.previous = window.location.href ;
            });

            $('.sidebar-mini').addClass("sidebar-collapse");
            var token = $("#token").val();

            $(".btn-delete").on('click', function () {
                var id = $(this).data('item-id');
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
                                "_token": token,
                            },
                            success:function (data) {
                                if (data.success == 1) {
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
            })
        });
    </script>

@endpush