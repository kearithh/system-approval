@extends('adminlte::page', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

@section('plugins.Pace', true)
@push('css')
<style>
    .table td, .table th {
        padding: .75rem .3rem;
    }
</style>
@endpush

@section('content')
  <div class="content">
    <div class="container-fluid">
      <!-- /.row -->
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title ">{{ __('Memo Request List') }}</h4>
              </div>
              <div class="table-responsive" style="padding: 0 10px">
                <table class="table table-striped">
                  <thead class="">
                  <th style="min-width: 30px">ល.រ</th>
                  <th class="" style="min-width: 120px">
                    {{ __('សកម្ម') }}
                  </th>
                  <th style="min-width: 100px">
                    {{ __('ស្ថានភាព') }}
                  </th>
                  <th class="text-center" style="min-width: 100px;">
                    {{ __('អនុសរណៈ') }}
                  </th>
                  <th style="min-width: 250px">
                    {{ __('ចំណងជើង') }}
                  </th>
                  <th style="min-width: 100px;">
                    {{ __('ថ្ងៃអនុវត្ត') }}
                  </th>
                  <th style="min-width: 180px">
                      {{ __('ស្នើដោយ') }}
                  </th>
                  <th style="min-width: 300px">
                    {{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ') }}
                  </th>
                  <th style="min-width: 180px">
                    {{ __('ថ្ងៃស្នើ') }}
                  </th>
                  </thead>
                  <tbody>
                  <?php $i = 1; ?>
                  @foreach($data as $key => $item)
                    <tr>
                      <td> {{ $i++  }}</td>
                      <td class="td-actions">

                          @include('global.list_action', ['uri' => 'request_memo', 'object' => $item, 'type' => config('app.type_memo')])

                      </td>
                      <td>
                        {{ memo_status($item) }}
                      </td>
                      <td class="text-center">
                        {{ $item->types }}
                      </td>
                      <td>
                        {{ $item->title_km }}
                      </td>
                      <td>
                        {{ start_date($item->start_date) }}
                      </td>
                      <td>
                        {{ $item->requester_name }}
                      </td>
                      <td>
                        {{ reviewer_position(\App\RequestMemo::reviewerName($item->id)) }}
                      </td>
                      <td>
                        {{ created_at($item->created_at) }}
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            {!! $data->render() !!}

        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script>
        @if(session('status'))
        Swal.fire({
            title: 'Success',
            icon: 'success',
            timer: '2000',
        });
        @endif
    </script>
    <script>
    $(document).ready(function() {

        // $('#post_date_from').datetimepicker();
        // $('#post_date_to').datetimepicker();

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy'
        });

        $(".btn-delete").on('click', function () {
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
                            "_token": "{{ @csrf_token() }}",
                        },
                        success:function (data) {
                            if (data.status == 1) {
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


    //Datemask dd/mm/yyyy
    $('#start_date').inputmask()
    $('#end_date').inputmask()
  </script>
@endpush
