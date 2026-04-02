@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title "><strong>Branch List</strong></h4>
                @if(@admin_action())
                  <div class="text-right">
                    <a href="{{ route('branch.create') }}" class="btn btn-sm btn-success">Create Branch</a>
                  </div>
                @endif
              </div>
                <div class="table-responsive" style="padding: 0 10px">
                  <table class="table table-striped">
                    <thead class="">
                      <th class="text-center">ល.រ</th>
                      <th class="text-center">សកម្មភាព</th>
                      <th style="min-width: 150px">កូដ</th>
                      <th style="min-width: 150px">ឈ្មោះកាត់</th>
                      <th style="min-width: 150px">ឈ្មោះ(KH)</th>
                      <th>ឈ្មោះ(EN)</th>
                      <th>ក្រុមហ៊ុន</th>
                    </thead>
                    <tbody>
                      @foreach($branchs as $key => $branch)
                        <tr>
                          <td class="text-center">
                              {{ $key + 1 }}
                          </td>
                          <td class="td-actions text-center">
                            @if(@admin_action())
                              <a rel="tooltip" class="btn btn-success btn-xs" href="{{ route('branch.edit', $branch) }}" data-original-title="" title="">
                                <i class="fa fa-pen"></i>
                              </a>
                              <a onclick="return confirm('Are you sure you want to delete this branch?')" href="{{ route('branch.destroy', $branch) }}" class="btn btn-xs btn-danger" title="Delete the request">
                                <i class="fa fa-trash"></i>
                              </a>
                            @else
                              <button type="button" class="btn btn-default btn-xs" disabled title="You have no permission">
                                <i class="fa fa-pen"></i>
                              </button>
                            @endif
                          </td>
                          <td>
                            {{ $branch->code }}
                          </td>
                          <td>
                            {{ $branch->short_name }}
                          </td>
                          <td>
                            {{ $branch->name_km }}
                          </td>
                          <td>
                            {{ $branch->name_en }}
                          </td>
                          <td>
                            {{ $branch->company ? $branch->company->name : 'N/A' }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  {!! $branchs->links() !!}
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
