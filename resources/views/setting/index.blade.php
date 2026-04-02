@extends('adminlte::page', ['activePage' => 'setting-reviewer-approver', 'titlePage' => __('Manage Approver')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">

        <div class="col-sm-12">
              <form action="">
                  <div class="row">
                    
                      <div class="col-4">
                          <div class="form-group mb-1">
                              <select class="form-control select2" name="company_id">
                                  <option value=""><< Company >></option>
                                  @foreach($company as $item)
                                      <option @if(@$_GET['company_id'] == $item->id) selected @endif value="{{ $item->id }}">
                                          {{ $item->name }}
                                      </option>
                                  @endforeach
                              </select>
                          </div>
                      </div>
                  
                      <div class="col-4">
                          <div class="form-group mb-1">
                              <select class="form-control select2" name="department_id">
                                  <option value=""><< Department >></option>
                                  @foreach($department as $item)
                                      <option @if(@$_GET['department_id'] == $item->id) selected @endif value="{{ $item->id }}">
                                          {{ $item->name_km }}
                                      </option>
                                  @endforeach
                              </select>
                          </div>
                      </div>

                      <div class="col-4">
                          <div class="form-group mb-1">
                              <select class="form-control select2" name="type">
                                  <option value=""><< Type >></option>
                                  <option value="request" @if(@$_GET['type'] == "request") selected @endif >Request</option>
                                  <option value="report" @if(@$_GET['type'] == "report") selected @endif >Report</option>
                              </select>
                          </div>
                      </div>

                  </div>

                  <a href="{{ route('setting-reviewer-approver.index') }}" class="btn btn-sm btn-secondary">
                      <i class="fas fa-times"></i> Reset
                  </a>
                  <button type="submit" class="btn btn-sm btn-info m-2">
                      <i class="fas fa-search"></i> Search
                  </button>
              </form>
          </div>

        <div class="col-md-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title"><strong>Reviewer and Approver List</strong></h4>
                @if(@admin_action())
                  <div class="text-right">
                    <a href="{{ route('setting-reviewer-approver.create') }}" class="btn btn-sm btn-success">Add more</a>
                  </div>
                @endif
              </div>
                <div class="table-responsive" style="padding: 0 10px">
                  <table class="table table-striped table-hover">
                    <thead class="">
                      <th style="width: 70px">
                        ល.រ
                      </th>
                      <th>
                        សកម្មភាព
                      </th>
                      <th>
                        ក្រុមហ៊ុន
                      </th>
                      <th>
                        នាយកដ្ឋាន
                      </th>
                      <th>
                        សំណើ / របាយការណ៍
                      </th>
                      <th>
                        ប្រភេទ
                      </th>
                      <th>
                        គោលការណ៍
                      </th>
                      <th>
                        អ្នកត្រួតពិនិត្យ
                      </th>
                      <th>
                        អ្នកត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                      </th>
                      <th>
                        អ្នកអនុម័ត
                      </th>
                    </thead>
                    <tbody>
                      @if($settings->count())
                        @foreach($settings as $key => $item)
                          <tr>
                            <td>
                              {{ $key + 1 }}
                            </td>
                            <td class="td-actions">
                              @if(@admin_action())
                                <a rel="tooltip" class="btn btn-success btn-xs" href="{{ route('setting-reviewer-approver.edit', $item->id) }}" data-original-title="" title="">
                                  <i class="fa fa-pen"></i>
                                </a>
                                <button
                                    action="{{ route('setting-reviewer-approver.destroy', $item->id) }}" 
                                    class="btn btn-xs btn-danger btn-delete" 
                                    data-item-id="{{ $item->id }}"
                                    title="Delete the request"
                                >
                                  <i class="fa fa-trash"></i>
                                </button>
                              @else
                                <button type="button" class="btn btn-default btn-xs" disabled title="You have no permission">
                                  <i class="fa fa-pen"></i>
                                </button>
                              @endif
                            </td>
                            <td>
                              {{ $item->company->name ?? '' }}
                            </td>
                            <td>
                              {{ $item->department->name_km }}
                            </td>
                            <td>
                              {{ $item->type }}
                            </td>
                            @if(@$item->type == 'report')
                                <td>
                                    {{ $item->type_report }}
                                </td>
                                <td>
                                    N/A
                                </td>
                            @else 
                                <td>
                                    {{ @$request_type->where('id', @$item->type_request)->first()->name }}
                                </td>
                                <td>
                                    @if($item->category == 1)
                                        ក្នុង
                                    @else
                                        ក្រៅ
                                     @endif
                                </td>
                            @endif
                            <td>
                              <?php $reviewers = @\App\SettingReviewerApprover::reviewerName(@$item->reviewers) ?>
                              @if(@$reviewers)
                                @foreach($reviewers as $key => $value)
                                    <p>{{@$value->name}}</p>
                                @endforeach
                              @endif
                            </td>
                            <td>
                              <?php $reviewers_short = @\App\SettingReviewerApprover::reviewerShortName(@$item->reviewers_short) ?>
                              @if(@$reviewers_short)
                                @foreach($reviewers_short as $key => $value)
                                    <p>{{@$value->name}}</p>
                                @endforeach
                              @endif
                            </td>
                            <td>
                              {{ $item->approverName->name }}
                            </td>
                          </tr>
                        @endforeach
                      @else
                        <tr>
                          <td colspan="10">Record Not Found!</td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                  <!-- {!! $settings->links() !!} -->
                  {{ $settings->appends($_GET)->links() }}
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

      $(document).ready(function () {

          $(".btn-delete").on('click', function () {
              var id = $(this).data('item-id');
              var formAction = $(this).attr('action');

              Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, delete it!'
              }).then((result) => {
                  if (result.value) {
                      $.ajax({
                          url: formAction,
                          method:"post",
                          type: "json",
                          data: {
                              "id": id,
                              "_token": "{{ csrf_token() }}"
                          },
                          success:function (data) {
                              if (data.success == 1) {
                                  Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                  );
                                  window.location.reload();
                              } else {
                                  console.log(data.msg);
                                  Swal.fire(
                                      {
                                          title: 'Delete failed !',
                                          text: "Please refresh your browser!",
                                          icon: 'warning',
                                      }
                                  )
                              }
                          },
                          fail:function (err) {
                              console.log(err);
                          }
                      });

                  }
              })
          })
      });
    </script>

@endpush
