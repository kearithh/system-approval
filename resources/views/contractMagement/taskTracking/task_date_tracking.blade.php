@extends('adminlte::page', ['activePage' => 'Task Dateline Tracking', 'titlePage' => __('Task Dateline Tracking')])


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
                <a href="{{ route('task-dateline-tracking') }}" class="btn btn-sm btn-default">Reset</a>
                <button type="submit" class="btn btn-sm btn-primary m-2">Search</button>
            </form>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><strong>Task Dateline Tracking List</strong></h4>
                        <div class="text-right">
                            <button @click="importData()" class="float-right btn btn-sm btn-outline-success">Import</button>
                        </div>
                    </div>
                  </div>
                </div>
                <div class="table-responsive" style="padding: 0 10px">
                    <table class="table table-striped">
                      <thead class="">
                        <th class="text-center" style="width: 70px">ល.រ</th>
                        <th class="text-nowrap">សកម្មភាព</th>
                        <th class="text-nowrap">ថ្ងៃផុតកំណត់</th>
                        <th class="text-nowrap">បរិយាយ </th>
                        <th class="text-nowrap">លេខតេឡេក្រាម </th>
                        <th class="text-nowrap">បង្កើតដោយ </th>
                        <th class="text-nowrap">កែប្រែដោយ </th>
                      </thead>
                      <tbody>
                        @foreach(@$data ?? [] as $key => $value)
                        <?php
                          $obj = json_decode(@$value->data);
                        ?>
                          <tr>
                            <td class="text-center">
                              {{ $key + 1 }}
                            </td>
                            <td class="td-actions text-center">
                                <a rel="tooltip" class="btn btn-success btn-xs" @click="showData({{ $value }})" data-original-title="" title="">
                                  <i class="fa fa-pen"></i>
                                </a>
                                <a onclick="return confirm('Are you sure you want to delete this position?')" href="{{ route('task-dateline-tracking-destroy', $value->id) }}" class="btn btn-xs btn-danger" title="Delete the request">
                                  <i class="fa fa-trash"></i>
                                </a>
                            </td>
                            <td class="text-nowrap">{{ @$obj->due_date }}</td>
                            <td>
                              {{ @$obj->description }}
                            </td>
                            <td>
                                {{ @$obj->is_id_telegram }}
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
                    {{ @$data->appends($_GET)->links() }}
                  </div>
                <form method="post"
                            action="{{ route('task-dateline-tracking-store') }}"
                            enctype="multipart/form-data"
                            autocomplete="off"
                            class="form-horizontal">
                    @csrf
                    @method('post')
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Task Dateline Tracking</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form>
                                <input type="hidden" name="id">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class=" col-form-label">{{ __('ថ្ងៃផុតកំណត់') }}<span style='color: red'>*</span></label>
                                        <div class="form-group{{ $errors->has('due_date') ? ' has-danger' : '' }}">
                                            <input
                                                type="text"
                                                id="due_date"
                                                class="datepicker form-control {{ $errors->has('due_date') ? ' is-invalid' : '' }}"
                                                name="due_date"
                                                required
                                                value="{{ old('due_date', \Carbon\Carbon::now()->format('d-m-Y')) }}"
                                                data-inputmask-inputformat="dd-mm-yyyy"
                                                placeholder="dd-mm-yyyy"
                                                autocomplete="off"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="recipient-name" class="col-form-label">{{ __('TelegrameId') }}</label>
                                           <input
                                              class="form-control"
                                              name="is_id_telegram"
                                              id="is_id_telegram"
                                              type="text"
                                              placeholder="{{ __('Id telegram') }}"
                                              value="{{ old('is_id_telegram') }}"

                                          />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="message-text" class="col-form-label">បរិយាយ(Description)</label>
                                            <textarea style="height: 110px;"
                                                  class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                                  name="description"
                                                  id="description"
                                              >
                                              </textarea>
                                          </div>
                                    </div>
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
                <form method="post"
                            action="{{ route('task-dateline-tracking-import') }}"
                            enctype="multipart/form-data"
                            autocomplete="off"
                            class="form-horizontal">
                    @csrf
                    @method('post')
                    <div class="modal fade" id="exampleModal-import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-ml" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Task Dateline Tracking</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form>
                                <input type="hidden" name="id">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-form-label" for="input-password-confirmation">{{ __('ឯកសារ') }}</label>
                                        <div class="col-sm-6">
                                          <div class="form-group form-file-upload {{ $errors->has('staff_file') ? ' has-danger' : '' }}">
                                            <input name="staff_file" type="file" class="" style="z-index: 1; opacity: 1; height: 28px"
                                            required
                                            >
                                            <label name="staff_file"></label>
                                          </div>
                                          @if ($errors->has('staff_file'))
                                            <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('staff_file') }}</span>
                                          @endif
                                        </div>
                                    </div>
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
        $('textarea[name="description"]').val('');
        $('input[name="id"]').val('');
        $('input[name="due_date"]').val('');
        $('input[name="is_id_telegram"]').val('');
        $('input[name="attachfile"]').val('');
        if(data){
            var obj = JSON.parse(data.data);
            console.log(obj.attachfile);
            $('textarea[name="description"]').val(obj.description);
            $('input[name="id"]').val(data.id);
            $('input[name="due_date"]').val(obj.due_date);
             $('input[name="is_id_telegram"]').val(obj.is_id_telegram);
        }
        $("#exampleModal").modal("show");

    }
    function importData(){
        $("#exampleModal-import").modal("show");
    }
</script>
