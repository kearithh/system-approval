@extends('adminlte::page', ['activePage' => 'position', 'titlePage' => __('position')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title"><strong>Setting Group Support List</strong></h4>
              </div>
                <div class="table-responsive" style="padding: 0 10px">
                  <table class="table table-striped">
                    <thead class="">
                      <th class="text-center" style="width: 70px">ល.រ</th>
                      <th>សកម្មភាព</th>
                      <th>ប្រភេទ</th>
                      <th>ឈ្មោះ</th>
                    </thead>
                    <tbody>
                      @foreach($data as $key => $item)
                        <tr>
                          <td class="text-center">
                            {{ $key + 1 }}
                          </td>
                          <td class="td-actions">
                            @if(@admin_action())
                              <a rel="tooltip" class="btn btn-success btn-xs" href="{{ route('setting_group_support.edit', $item->id) }}" data-original-title="" title="">
                                <i class="fa fa-pen"></i>
                              </a>
                            @else
                              <button type="button" class="btn btn-default btn-xs" disabled title="You have no permission">
                                <i class="fa fa-pen"></i>
                              </button>
                            @endif
                          </td>
                          <td>
                            Group Support
                          </td>
                          <td>
                            {{ $item->staff_name }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
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
