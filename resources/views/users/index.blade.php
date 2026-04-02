@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@push('css')
    <style>
        .table td, .table th {
            padding: .5rem;
        }
    </style>
@endpush
@section('plugins.Select2', true)

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <form action="">
                  <div class="row">
                    
                      <div class="col-6">
                          <div class="form-group mb-1">
                              <input type="text" class="form-control" id="" name="keyword" placeholder="លេខកាត គោត្តនាម និងនាម" value="{{ @$_GET['keyword'] }}">
                          </div>
                      </div>

                      <div class="col-6">
                          <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}  mb-1">
                              <select class="form-control select2" name="status">
                                  <option value="" selected><< Status >></option>
                                  <option value="1" @if(@$_GET['status'] == "1") selected @endif >Active</option>
                                  <option value="0" @if(@$_GET['status'] == "0") selected @endif >Inactive</option>
                              </select>
                          </div>
                      </div>

                      <div class="col-6">
                          <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}  mb-1">
                              <select class="form-control select2" name="company_id">
                                  <option value=""><< Company >></option>
                                  @foreach($company as $item)
                                      <option @if(@$_GET['company_id'] == $item->short_name) selected @endif value="{{ $item->short_name }}">{{ $item->name_km }}</option>
                                  @endforeach
                              </select>
                          </div>
                      </div>
                  
                      <div class="col-6">
                          <div class="form-group{{ $errors->has('branch_id') ? ' has-danger' : '' }}  mb-1">
                              <select class="form-control select2" name="branch_id">
                                  <option value=""><< Branch >></option>
                                  @foreach($branch as $item)
                                      <option @if(@$_GET['branch_id'] == $item->short_name) selected @endif value="{{ $item->short_name }}">{{ $item->name_km }}</option>
                                  @endforeach
                              </select>
                          </div>
                      </div>

                  </div>
                  <a href="{{ route('user.index') }}" class="btn btn-sm btn-secondary">
                      <i class="fas fa-times"></i> Reset
                  </a>
                  <button type="submit" class="btn btn-sm btn-info m-2">
                      <i class="fas fa-search"></i> Search
                  </button>
                  <button type="submit" formaction="user/export" class="btn btn-sm btn-success">
                      <i class="fas fa-file-excel"></i> Export
                  </button>
              </form>
          </div>
        <div class="col-md-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title "><strong>Users List</strong></h4>
                  <div class="text-right">
                    <a href="{{ route('password.edit') }}" class="btn btn-sm btn-info">
                      {{ __('Change Your Password') }}
                    </a>
                    @if (auth()->user()->username == 'admin' || auth()->user()->role > 0)
                      <a href="{{ route('user.create') }}" class="btn btn-sm btn-success">
                        {{ __('Create User') }}
                      </a>
                    @endif
                  </div>
              </div>
                <div class="table-responsive" style="padding: 0 10px; ">
                  <table class="table table-striped">
                    <thead class="">
                    <th style="width: 50px">
                        {{ __('ល.រ') }}
                    </th>
                    <th class="text-center" style="min-width: 72px">
                        {{ __('សកម្ម') }}
                    </th>
                    <th class="text-center" style="min-width: 72px">
                        {{ __('ស្ថានភាព') }}
                    </th>
                    <th style="min-width: 150px">
                        {{ __('គោត្តនាម និងនាម') }}
                    </th>
                    <th style="min-width: 150px">
                        {{ __('អ្នកប្រើប្រាស់') }}
                    </th>
                    <th style="min-width: 150px">
                        {{ __('លេខសំគាល់បុគ្គលិក') }}
                    </th>
                    <th style="min-width: 210px;">
                        {{ __('មុខតំណែង') }}
                    </th>
                    <th style="min-width: 210px;">
                        {{ __('សាខា') }}
                    </th>
                    <th style="min-width: 210px;">
                        {{ __('ក្រុមហ៊ុន') }}
                    </th>
                    <th>
                        {{ __('ហត្ថលេខា') }}
                    </th>
                    <th>
                        {{ __('ហត្ថលេខាតូច') }}
                    </th>
                    <th style="min-width: 230px;">
                        {{ __('អ៊ីម៉ែល') }}
                    </th>
                    <th>
                        {{ __('រូបតំណាង') }}
                    </th>
                    </thead>
                    <tbody>
                      @foreach($users as $key => $user)
                          <tr>
                              <td class="text-center">
                                  {{ $key + 1 }}
                              </td>
                              <td class="td-actions text-center">
                                  <!-- check user not admin and not role admin or super admin -->
                                  @if(@admin_action())
                                    <a rel="tooltip" class="btn btn-success btn-xs" href="{{ route('user.edit', $user) }}" data-original-title="" title="">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <!-- set can't delete auth user login and user deleted -->
                                    @if($user->id != auth()->id() && $user->user_status != 0)
                                      <a onclick="return confirm('Are you sure you want to delete this user?')" href="{{URL::route('user_destroy', $user->id)}}" class="btn btn-xs btn-danger" title="Delete the request">
                                        <i class="fa fa-trash"></i>
                                      </a>
                                    @else
                                      <button type="button" class="btn btn-danger btn-xs" disabled title="User inactive">
                                          <i class="fa fa-trash"></i>
                                      </button>
                                    @endif
                                  @else
                                    <button type="button" class="btn btn-default btn-xs" disabled title="You have no permission">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                  @endif
                              </td>
                              <td class="text-center">
                                @if($user->user_status == 0)
                                  <button class="btn btn-xs btn-dark">Inactive</button>
                                @else
                                  <button class="btn btn-xs btn-info">Active</button>
                                @endif
                              </td>
                              <td>
                                {{ $user->name_en }}
                              </td>
                              <td>
                                {{ $user->username }}
                              </td>
                              <td>
                                {{ $user->system_user_id }}
                              </td>
                              <td>
                                {{ $user->positions_name ? $user->positions_name : 'N/A'}}
                              </td>
                              <td>
                                  {{ $user->branch_name ? $user->branch_name : 'N/A' }}
                              </td>
                              <td>
                                {{ $user->company_name ? $user->company_name : 'N/A' }}
                              </td>
                              <td>
                                  @if ($user->signature)
                                    <a href="{{ asset('/'.$user->signature) }}" target="_self">
                                        <img src="{{ asset('/'.$user->signature) }}" alt="signature" style="max-height:40px">
                                    </a>
                                  @else
                                    <span style="font-family: 'Adinda Melia'; color: #0000cc">{{ $user->name }}</span>
                                  @endif
                              </td>
                              <td>
                                  @if ($user->short_signature)
                                      <a href="{{ asset('/'.$user->short_signature) }}" target="_self">
                                          <img src="{{ asset('/'.$user->short_signature) }}" alt="short_signature" style="max-height:40px">
                                      </a>
                                  @else
                                      <span style="font-family: 'Adinda Melia'; color: #0000cc">{{ $user->name }}</span>
                                  @endif
                              </td>
                              <td>
                                  {{ $user->email }}
                              </td>
                              <td>
                                @if ($user->avatar)
                                    <a href="{{ asset('storage/'.$user->avatar) }}" target="_self">
                                        <img src="{{ asset('storage/'.$user->avatar) }}" alt="avatar" style="max-height:40px; border-radius:50%">
                                    </a>
                                @else
                                    <img src="{{ asset('/images/default.png') }}" alt="avatar" style="max-height:40px; border-radius:50%">
                                @endif
                              </td>
                          </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              <div class="d-flex justify-content-center">
                  {{ $users->links() }}
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
@endsection
