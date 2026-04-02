@extends('adminlte::page', ['activePage' => 'department', 'titlePage' => __('department')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title "><strong>Department List</strong></h4>
              </div>
                <div class="table-responsive" style="padding: 0 10px">
                  <table class="table table-striped">
                    <thead class="">
                      <th class="text-center" style="width: 70px">ល.រ</th>
                      <th>ឈ្មោះកាត់</th>
                      <th>ឈ្មោះ(KM)</th>
                      <th>ឈ្មោះ(EN)</th>
                      <th>បរិយាយ</th>
                    </thead>
                    <tbody>
                      @foreach($departments as $key => $item)
                        <tr>
                          <td class="text-center">{{ $key + 1 }}</td>
                          <td>{{ $item->short_name }}</td>
                          <td>{{ $item->name_km }}</td>
                          <td>{{ $item->name_en }}</td>
                          <td>{{ $item->desc }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  {!! $departments->links() !!}
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
