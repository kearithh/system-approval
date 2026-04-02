@extends('adminlte::page', ['activePage' => 'position', 'titlePage' => __('position')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">

        <div class="col-sm-12">
            <form action="">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group mb-1">
                            <input type="text" class="form-control" id="" name="keyword" placeholder="keyword" value="{{ @$_GET['keyword'] }}">
                        </div>
                    </div>
                </div>
                <a href="{{ route('position.index') }}" class="btn btn-sm btn-default">Reset</a>
                <button type="submit" class="btn btn-sm btn-primary m-2">Search</button>
            </form>
        </div>

        <div class="col-md-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title"><strong>Position List</strong></h4>
                @if (@admin_action())
                  <div class="text-right">
                    <a href="{{ route('position.create') }}" class="btn btn-sm btn-success">Create Position</a>
                  </div>
                @endif
              </div>
                <div class="table-responsive" style="padding: 0 10px">
                  <table class="table table-striped">
                    <thead class="">
                      <th class="text-center" style="width: 70px">ល.រ</th>
                      <th class="text-center">សកម្មភាព</th>
                      <th>ឈ្មោះកាត់</th>
                      <th>ឈ្មោះ(KM)</th>
                      <th>ឈ្មោះ(EN)</th>
                      <th>បរិយាយ </th>
                      <th>លំដាប់ </th>
                    </thead>
                    <tbody>
                      @foreach($positions as $key => $item)
                        <tr>
                          <td class="text-center">
                            {{ $key + 1 }}
                          </td>
                          <td class="td-actions text-center">
                            @if(@admin_action())
                              <a rel="tooltip" class="btn btn-success btn-xs" href="{{ route('position.edit', $item->id) }}" data-original-title="" title="">
                                <i class="fa fa-pen"></i>
                              </a>
                              <a onclick="return confirm('Are you sure you want to delete this position?')" href="{{ route('position.destroy', $item->id) }}" class="btn btn-xs btn-danger" title="Delete the request">
                                <i class="fa fa-trash"></i>
                              </a>
                            @else
                              <button type="button" class="btn btn-default btn-xs" disabled title="You have no permission">
                                <i class="fa fa-pen"></i>
                              </button>
                            @endif
                          </td>
                          <td>
                            {{ $item->short_name }}
                          </td>
                          <td>
                            {{ $item->name_km }}
                          </td>
                          <td>
                            {{ $item->name_en }}
                          </td>
                          <td>
                            {{ $item->desc }}
                          </td>
                          <td>
                            {{ $item->level }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <!-- {!! $positions->links() !!} -->
                  {{ $positions->appends($_GET)->links() }}
                </div>
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
      @if(session('status')==1)
        Swal.fire({
          title: 'Insert Success',
          icon: 'success',
          timer: '2000',
        })
      @elseif(session('status')==2)
        Swal.fire({
          title: 'Update Success',
          icon: 'success',
          timer: '2000',
        })
      @elseif(session('status')==3)
        Swal.fire({
          title: 'Delete Success',
          icon: 'success',
          timer: '2000',
        })
      @endif
  </script>
@endpush
