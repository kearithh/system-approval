@extends('adminlte::page', ['activePage' => 'properties owner', 'titlePage' => __('properties owner')])


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
                <a href="{{ route('properties-owner') }}" class="btn btn-sm btn-default">Reset</a>
                <button type="submit" class="btn btn-sm btn-primary m-2">Search</button>
            </form>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><strong>Properties Owner List</strong></h4>
                        <div class="text-right">
                            <button @click="showData()" class="float-right btn btn-sm btn-outline-success">Create</button>
                        </div>
                    </div>
                  </div>
                </div>
                <div class="table-responsive" style="padding: 0 10px">
                    <table class="table table-striped">
                      <thead class="">
                        <th class="text-center" style="width: 70px">ល.រ</th>
                        <th class="text-center">សកម្មភាព</th>
                        <th class="text-nowrap">ឈ្មោះ</th>
                        <th class="text-nowrap">បរិយាយ </th>
                        <th class="text-nowrap">បង្កើតដោយ </th>
                        <th class="text-nowrap">កែប្រែដោយ </th>
                      </thead>
                      <tbody>
                        @foreach(@$propertiesOwner ?? [] as $key => $value)
                          <tr>
                            <td class="text-center">
                              {{ $key + 1 }}
                            </td>
                            <td class="td-actions text-center">
                                <a rel="tooltip" class="btn btn-success btn-xs" @click="showData({{ $value }})" data-original-title="" title="">
                                  <i class="fa fa-pen"></i>
                                </a>
                                <a onclick="return confirm('Are you sure you want to delete this position?')" href="{{ route('properties-owner.destroy', $value->id) }}" class="btn btn-xs btn-danger" title="Delete the request">
                                  <i class="fa fa-trash"></i>
                                </a>
                            </td>
                            <td>
                              {{ @$value->name }}
                            </td>
                            <td>
                              {{ @$value->description }}
                            </td>
                            <td>
                              {{ @$value->userCreated->username }}
                            </td>
                            <td>
                                {{ @$value->userUpdateBy->username }}
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                    {{ @$propertiesOwner->appends($_GET)->links() }}
                  </div>
                <form method="post"
                            action="{{ route('properties-owner-store') }}"
                            enctype="multipart/form-data"
                            autocomplete="off"
                            class="form-horizontal">
                    @csrf
                    @method('post')
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Properties Owner</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form>
                                <input type="hidden" name="id">
                                <div class="form-group">
                                  <label for="recipient-name" class="col-form-label">ឈ្មោះ(Name)</label>
                                 <input
                                    class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                    name="name"
                                    id="name"
                                    type="text"
                                    placeholder="{{ __('Name') }}"
                                    value="{{ old('name') }}"
                                    required="true"
                                    aria-required="true"
                                />
                                </div>
                                <div class="form-group">
                                  <label for="message-text" class="col-form-label">បរិយាយ(Description)</label>
                                  <textarea style="height: 110px;"
                                        class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                        name="description"
                                        id="description"
                                    >
                                    </textarea>
                                </div>
                              </form>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                          </div>
                        </div>
                      </div>
                </form>

            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
    function showData(data = null) {
        console.log(data);
        $('input[name="name"]').val('');
        $('textarea[name="description"]').val('');
        $('input[name="id"]').val('');
        if(data){
            $('input[name="name"]').val(data.name);
            $('textarea[name="description"]').val(data.description);
            $('input[name="id"]').val(data.id);
        }
        $("#exampleModal").modal("show");

    }

</script>
