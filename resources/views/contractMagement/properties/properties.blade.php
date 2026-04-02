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
                <a href="{{ route('properties') }}" class="btn btn-sm btn-default">Reset</a>
                <button type="submit" class="btn btn-sm btn-primary m-2">Search</button>
            </form>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><strong>Properties List</strong></h4>
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
                        <th class="text-nowrap">សកម្មភាព</th>
                        <th class="text-nowrap">ឯកសារ</th>
                        <th class="text-nowrap">ឈ្មោះ</th>
                        <th class="text-nowrap">ម្ចាស់(Properties Owner)</th>
                        <th class="text-nowrap">ប្រភេទ</th>
                        <th class="text-nowrap">បរិយាយ </th>
                        <th class="text-nowrap">បង្កើតដោយ </th>
                        <th class="text-nowrap">កែប្រែដោយ </th>
                      </thead>
                      <tbody>
                        @foreach(@$properties ?? [] as $key => $value)
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
                                <a onclick="return confirm('Are you sure you want to delete this position?')" href="{{ route('properties.destroy', $value->id) }}" class="btn btn-xs btn-danger" title="Delete the request">
                                  <i class="fa fa-trash"></i>
                                </a>
                            </td>
                            <td>
                                <a href="{{url("/uploads/$obj->attachfile")}}" target="_blank" class="open_link_file">
                                    {{ $obj->attachfile }}
                                </a>
                            </td>
                            <td>
                              {{ @$obj->name }}
                            </td>
                            <td>
                                {{ @$value->proOwner->name }}
                            </td>
                            <td>
                                {{ @Constants::PROPERTIES_TYPE[@$obj->type] }}
                              </td>
                            <td>
                              {{ @$obj->description }}
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
                    {{ @$properties->appends($_GET)->links() }}
                  </div>
                <form method="post"
                            action="{{ route('properties-store') }}"
                            enctype="multipart/form-data"
                            autocomplete="off"
                            class="form-horizontal">
                    @csrf
                    @method('post')
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Properties</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form>
                                <input type="hidden" name="id">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class=" col-form-label">{{ __('ម្ចាស់(Properties Owner)') }}<span style='color: red'>*</span></label>
                                        <div class="form-group{{ $errors->has('properties_owner_id') ? ' has-danger' : '' }}">
                                        <select class="form-control position-select2" name="properties_owner_id" required @click="getOwner()">
                                            <option value=""><< Please select >></option>
                                            @foreach(@$propertiestOwner as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('properties_owner_id'))
                                            <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('properties_owner_id') }}</span>
                                        @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="recipient-name" class="col-form-label">ឈ្មោះ(Name)<span style='color: red'>*</span></label>
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
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-form-label">{{ __('ប្រភេទ') }}<span style='color: red'>*</span></label>
                                        <div class="form-group{{ $errors->has('type') ? ' has-danger' : '' }}">
                                        <select class="form-control" name="type"
                                            required="true"
                                            aria-required="true"
                                        >

                                        <option value=""> << Please select >> </option>
                                        @foreach (Constants::PROPERTIES_TYPE as $key => $value)
                                          <option value="{{ $key }}"> {{ $value }} </option>
                                        @endforeach

                                        </select>
                                        @if ($errors->has('type'))
                                            <span id="name-type" class="error text-danger" for="input-name">{{ $errors->first('type') }}</span>
                                        @endif
                                        </div>
                                        <label class="col-form-label" for="input-password-confirmation">{{ __('ឯកសារ') }}</label>
                                        <div class="col-sm-6">
                                          <div class="form-group form-file-upload {{ $errors->has('attachfile') ? ' has-danger' : '' }}">
                                            <input name="attachfile" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                                            <label name="attachfile"></label>
                                          </div>
                                          @if ($errors->has('attachfile'))
                                            <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('attachfile') }}</span>
                                          @endif
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
                                {{-- <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-form-label" for="input-password-confirmation">{{ __('ឯកសារ') }}</label>
                                        <div class="col-sm-6">
                                          <div class="form-group form-file-upload {{ $errors->has('attachfile') ? ' has-danger' : '' }}">
                                            <input name="attachfile" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                                          </div>
                                          @if ($errors->has('attachfile'))
                                            <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('attachfile') }}</span>
                                          @endif
                                        </div>
                                    </div>

                                </div> --}}

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

        $('input[name="name"]').val('');
        $('textarea[name="description"]').val('');
        $('input[name="id"]').val('');
        $('select[name="type"]').val('');
        $('select[name="properties_owner_id"]').val('');
        if(data){
            var obj = JSON.parse(data.data);
            $('input[name="name"]').val(obj.name);
            $('textarea[name="description"]').val(obj.description);
            $('select[name="type"]').val(obj.type);
            $('input[name="id"]').val(data.id);
            $('select[name="properties_owner_id"]').val(data.properties_owner_id);
        }
        $("#exampleModal").modal("show");

    }
</script>
