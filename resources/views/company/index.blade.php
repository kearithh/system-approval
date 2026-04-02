@extends('adminlte::page', ['activePage' => 'department', 'titlePage' => __('department')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title "><strong>Company List</strong></h4>
            </div>
              <div class="table-responsive" style="padding: 0 10px">
                <table class="table table-striped">
                  <thead class="">
                  <th class="text-center" style="width: 70px">ល.រ</th>
                  <th>ឈ្មោះកាត់(KM)</th>
                  <th>ឈ្មោះកាត់(EN)</th>
                  <th>ឈ្មោះពេញ</th>
                  </thead>
                  <tbody>
                    @foreach($companys as $key => $item)
                      <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->short_name_km }}</td>
                        <td>{{ $item->short_name_en }}</td>
                        <td>{{ $item->name }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                {!! $companys->links() !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
