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
              @include('request_memo.partials.filter', ['clear_url' => 'public_memo'])
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
              <h4 class="card-title"><strong>Memo List</strong></h4>
              <div class="col-sm-12 text-right">
                <button class="btn btn-outline-secondary btn-sm">Total: {{ $total }} Records</button>
              </div>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table table-striped">
                <thead class="">
                <th>ល.រ</th>
                <th>មើល</th>
                <th>ស្ថានភាព</th>
                <th style="min-width: 152px;">ក្រុមហ៊ុន</th>
                <th style="min-width: 152px;">ស្នើសំុដោយ</th>
                <th style="min-width: 152px;">អនុសរណៈ</th>
                <th style="min-width: 250px">ចំណងជើង</th>
                <th style="min-width: 100px">ថ្ងៃស្នើ</th>
                </thead>
                <tbody>
                  @if($data->count())
                    <?php $i = 1; ?>
                    @foreach($data as $key => $item)
                      <tr title="Request ID: {{$item->id}}"
                        @if($item->abrogation_status) style="opacity: 50%; background-color: whitesmoke;" @endif
                      >
                        <td> {{ $i++  }}</td>
                        <td>
                          <a  href="{{ route('request_memo.show', $item->id) }}" 
                              class="preview btn btn-xs btn-info" 
                              title="View the request">
                              <i class="fa fa-eye"></i>
                          </a>
                        </td>
                        <td>
                          @if($item->deleted_at)
                            <button class="btn btn-xs bg-secondary">Deleted</button>
                          @else
                            {{ request_status($item) }}
                          @endif
                        </td>
                        <td>
                          {{ $item->company_name }}
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
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="8">Record Not Found!</td>
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

@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $(".preview").on('click', function () {
                localStorage.previous = window.location.href ;
            });
            $(".select2").select2({
                // tags: true
            });
        });
    </script>
@endsection