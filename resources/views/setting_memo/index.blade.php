@extends('adminlte::page', ['activePage' => 'position', 'titlePage' => __('position')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title"><strong>Setting List</strong></h4>
                @if (@admin_action())
                  <!-- <div class="text-right">
                    <a href="{{ route('setting_memo.create') }}" class="btn btn-sm btn-success">
                      Create Setting Memo
                    </a>
                  </div> -->
                @endif
              </div>
                <div class="table-responsive" style="padding: 0 10px">
                  <table class="table table-striped">
                    <thead class="">
                      <th class="text-center" style="width: 70px">ល.រ</th>
                      <th>សកម្មភាព</th>
                      <th>ក្រុមហ៊ុន</th>
                      <th>លេខលំដាប់ </th>
                    </thead>
                    <tbody>
                      @foreach($data as $key => $item)
                        <tr>
                          <td class="text-center">
                            {{ $key + 1 }}
                          </td>
                          <td class="td-actions">
                            @if(@admin_action())
                              <a rel="tooltip" class="btn btn-success btn-xs" href="{{ route('setting_memo.edit', $item->id) }}" data-original-title="" title="">
                                <i class="fa fa-pen"></i>
                              </a>
                              <!-- <a onclick="return confirm('Are you sure you want to delete this setting_memo?')" href="{{ route('setting_memo.destroy', $item->id) }}" class="btn btn-xs btn-danger" title="Delete the request">
                                <i class="fa fa-trash"></i>
                              </a> -->
                            @else
                              <button type="button" class="btn btn-default btn-xs" disabled title="You have no permission">
                                <i class="fa fa-pen"></i>
                              </button>
                            @endif
                          </td>
                          <td>
                            {{ $item->company_name }}
                          </td>
                          <td>
                            {{ $item->no }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  {{ $data->appends($_GET)->links() }}
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
