<form role="form">
    <div class="card-body p-0">
        <div class="row">

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Date From</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                        </div>
                        <input
                                value="{{ isset($_GET['post_date_from']) ? $_GET['post_date_from'] : '' }}"
                                name="post_date_from"
                                type="text"
                                class="form-control float-right datepicker"
                                id="post_date_from"
                                autocomplete="off"
                        >
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Date To</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                        </div>
                        <input
                                value="{{ isset($_GET['post_date_to']) ? $_GET['post_date_to'] : '' }}"
                                name="post_date_to"
                                type="text"
                                class="form-control float-right datepicker"
                                id="post_date_to"
                                autocomplete="off"
                        >
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Company</label>
                    <select name="company_id" id="" class="form-control select2">
                        <option value="%">All</option>
                        <?php $company_id = (isset($_GET['company_id']) ? $_GET['company_id'] : null); ?>
                        @foreach($company as $key => $value)
                            @if(@!in_array($value->id, @$company_request))
                                <option value="{{ $value->id}}"
                                    @if($company_id == $value->id))
                                        selected
                                    @endif
                                >
                                    {{ $value->name }}
                                </option>
                            @endif
                        @endforeach()
                    </select>
                </div>
            </div>

            <div class="col-sm-3" {{ @$branch_request }} >
                <div class="form-group">
                    <label for="">Branch</label>
                    <select name="branch_id" id="" class="form-control select2">
                        <option value="%">All</option>
                        <?php $branch_id = (isset($_GET['branch_id']) ? $_GET['branch_id'] : null); ?>
                        @foreach($branch as $key => $value)
                            <option value="{{ $value->id}}"
                                @if($branch_id == $value->id))
                                    selected
                                @endif
                            >
                            {{ $value->name_km }} ({{ @$value->short_name }})
                          </option>
                        @endforeach()
                    </select>
                </div>
            </div>

            <div class="col-sm-3" {{ @$department_request }}>
                <div class="form-group">
                    <label for="">Department</label>
                    <select name="department_id" id="" class="form-control select2">
                        <option value="%">All</option>
                        <?php $department_id = (isset($_GET['department_id']) ? $_GET['department_id'] : null); ?>
                        @foreach($department as $key => $value)
                            <option value="{{ $value->id}}"
                                @if($department_id == $value->id))
                                    selected
                                @endif
                            >
                            {{ $value->name_km }}
                          </option>
                        @endforeach()
                    </select>
                </div>
            </div>

            <!-- <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputPassword1">Position</label>
                    <select name="position_id" id="" class="form-control select2">
                        <option value="%">All</option>
                        <?php $position_id = (isset($_GET['position_id']) ? $_GET['position_id'] : null); ?>
                        @foreach($position as $key => $value)
                            <option value="{{ $value->id}}"
                                @if($position_id == $value->id))
                                    selected
                                @endif
                            >
                            {{ $value->name_km }}
                          </option>
                        @endforeach()
                    </select>
                </div>
            </div> -->

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Status</label>
                    <select name="status" id="" class="form-control select2">
                        <?php $status = (isset($_GET['status']) ? $_GET['status'] : null); ?>
                        <option value="%">All</option>
                        <option value="{{ config('app.approve_status_approve') }}" @if ($status == config('app.approve_status_approve') ) selected @endif >Approved</option>
                        <option value="{{ config('app.approve_status_draft') }}" @if ($status == config('app.approve_status_draft') ) selected @endif >Pending</option>
                        <option value="{{ config('app.approve_status_reject') }}" @if ($status == config('app.approve_status_reject') ) selected @endif >Commented </option>
                        <option value="{{ config('app.approve_status_disable') }}" @if ($status == config('app.approve_status_disable') ) selected @endif >Rejected</option>
                        <option value="{{ config('app.approve_status_delete') }}" @if ($status == config('app.approve_status_delete') ) selected @endif >Deleted</option>
                    </select>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-12">
                <a href="{{ isset($clear_url) ? $clear_url : '/' }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
                <button type="submit" class="btn btn-sm btn-info">
                    <i class="fas fa-search"></i> Search
                </button>
                <button type="submit" formaction="{{ $clear_url }}/export" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Export
                </button>
            </div>
        </div>

    </div>
    <!-- /.card-body -->
</form>

@section('js')

    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.datepicker').datepicker({
              format: 'dd-mm-yyyy',
              todayHighlight:true,
              autoclose: true
            });

            $(".select2").select2({
              // tags: true
            });
        });
    </script>

@endsection
