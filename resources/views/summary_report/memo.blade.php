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
              <a class="btn btn-xs bg-success" href="/summary_report/memo? status={{ config('app.approve_status_approve') }}" style="font-size: 0.85rem;">
                Total Approved
                <span class="badge badge-light">{{ @$totalApproved }}</span>
              </a>
              <a class="btn btn-xs bg-orange" href="/summary_report/memo? status={{ config('app.approve_status_draft') }}" style="font-size: 0.85rem;">
                Total Pending
                <span class="badge badge-light">{{ @$totalPending }}</span>
              </a>
              <a class="btn btn-xs bg-danger" href="/summary_report/memo? status={{ config('app.approve_status_reject') }}" style="font-size: 0.85rem;">
                Total Commented
                <span class="badge badge-light">{{ @$totalCommented }}</span>
              </a>
              <a class="btn btn-xs bg-secondary" href="/summary_report/memo? status={{ config('app.approve_status_delete') }}" style="font-size: 0.85rem;">
                Total Deleted
                <span class="badge badge-light">{{ @$totalDeleted }}</span>
              </a>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @include('global.search_request',['clear_url' => 'memo', 'branch_request' => 'hidden', 'department_request' => '', 'company_request' => []])
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
              <h4 class="card-title "><strong>Memo List</strong></h4>
              <div class="col-sm-12 text-right">
                <button class="btn btn-outline-secondary btn-sm">Total: {{ $total }} Records</button>
              </div>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table table-striped table-hover">
                <thead class="">
                  <th>ល.រ</th>
                  <th style="min-width: 100px;">សកម្ម</th>
                  <th style="min-width: 200px;">និរាករណ៍</th>
                  <th>ស្ថានភាព</th>
                  <th style="min-width: 152px;">ក្រុមហ៊ុន</th>
                  <th style="min-width: 152px;">នាយកដ្ឋាន</th>
                  <th style="min-width: 152px;">ស្នើសុំដោយ</th>
                  <th style="min-width: 152px;">អនុសរណៈ</th>
                  <th style="min-width: 250px">ចំណងជើង</th>
                  <th style="min-width: 100px">កាលបរិច្ឆេទស្នើរ</th>
                  <th style="min-width: 100px">ថ្ងៃសុពលភាព</th>
                </thead>
                <?php 
                  $can_abrogation = @Auth()->user()->action_object->can_abrogation;
                  $can_deabrogation = @Auth()->user()->action_object->can_deabrogation;
                ?>

                <tbody>
                  @if($data->count())
                    <?php $i = 1; ?>
                    @foreach($data as $key => $item)
                      <tr title="Request ID: {{$item->id}}" 
                        @if($item->abrogation_status) style="opacity: 50%;" @endif
                      >
                        <td> {{ $i++ }}</td>
                        <td>
                          <a  href="{{ route('request_memo.show', $item->id) }}" 
                              class="preview btn btn-xs btn-info" 
                              title="View the request">
                              <i class="fa fa-eye"></i>
                          </a>
                          @if($item->status == config('app.approve_status_approve'))
                            @if(!$item->abrogation_status && @$can_abrogation)
                              <button action="/request_memo/{{ $item->id }}/abrogation"
                                abrogation_status="1"
                                method="POST"
                                class="btn-abrogation btn btn-xs btn-danger" 
                                title="និរាករណ៍ទៅលើ Memo">
                                <i class="fa fa-window-close"></i>
                              </button>
                            @elseif($item->abrogation_status && @$can_deabrogation)
                              <button action="/request_memo/{{ $item->id }}/abrogation"
                                abrogation_status="0"
                                method="POST"
                                class="btn-abrogation btn btn-xs btn-danger" 
                                title="ដកនិរាករណ៍ទៅលើ Memo">
                                <i class="fa fa-window-close"></i>
                              </button>
                            @else
                              <button
                                class="btn btn-xs btn-danger" 
                                title="Memo Active"
                                disabled>
                                <i class="fa fa-window-close"></i>
                              </button>
                            @endif
                          @else
                            <button
                              class="btn btn-xs btn-danger" 
                              title="Memo Pending"
                              disabled>
                              <i class="fa fa-window-close"></i>
                            </button>
                          @endif
                        </td>
                        <td>
                          {{ @$item->abrogation_desc == 'true'? '' : @$item->abrogation_desc }}
                        </td>
                        <td>
                          @if($item->deleted_at)
                            <button class="btn btn-xs bg-dark">Deleted</button>
                          @else
                            {{ request_status($item) }}
                          @endif
                        </td>
                        <td>
                          {{ $item->company_name }}
                        </td>
                        <td>
                          {{ $item->department_name }}
                        </td>
                        <td>
                          {{ $item->requester_name }}
                        </td>
                        <td>
                          {{ $item->types }}
                        </td>
                        <td>
                          {{ $item->title_km }}
                        </td>
                        <td>
                          {{ created_at($item->created_at) }}
                        </td>
                        <td>
                          {{ created_at($item->start_date) }}
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="11">Record Not Found!</td>
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
@endsection

@push('js')
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
  <script>
    $(document).ready(function() {
        $(".preview").on('click', function () {
            localStorage.previous = window.location.href ;
        });

        $(".btn-abrogation").on('click', function () {
            var formAction = $(this).attr('action');
            var abrogation_status = $(this).attr('abrogation_status');
            if (abrogation_status == 1) {
              var text_title = "និរាករណ៍?";
              var text_desc = "សេចក្តីបញ្ជាក់...";
              var type_input = "textarea";
            } else {
              var text_title = "ដកនិរាករណ៍?";
              var text_desc = "";
              var type_input = "";
            }
            Swal.fire({
                title: text_title,
                text: text_desc,
                icon: "warning",
                showCancelButton: true,
                input: type_input,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: "ទេ",
                confirmButtonText: "យល់ព្រម",
                inputValidator: (value) => {
                  if (!value) return "សូមបំពេញសេចក្តីបញ្ជាក់"
                  else return null
                }
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: formAction,
                        method:"post",
                        type: "json",
                        data: {
                            "_token": "{{ @csrf_token() }}",
                            "abrogation_status": abrogation_status,
                            "comment": result.value,
                        },
                        success:function (data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: "Success!",
                                    text: "The memo has been abrogation.",
                                    icon: "success",
                                    timer: "5000",
                                })
                                window.location.reload();
                            } else {
                                console.log(data.msg);
                                Swal.fire(
                                    {
                                        title: "Abrogation failed !",
                                        text: "Please refresh your browser!",
                                        icon: "warning",
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

    });

  </script>
@endpush