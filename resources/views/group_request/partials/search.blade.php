

<div class="card card-success">
    <div class="card-header">
        <i class="fas fa-search"></i>
            Search and Filter
        <div class="card-tools">
            <button type="button" class="btn btn-sm" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-sm" data-card-widget="remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form role="form">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-sm-3">
                        <?php $type = isset($_GET['type']) ? $_GET['type'] : null; ?>
                        <div class="form-group">
                            <label for="company">Company</label>
                            <select name="company" id="company" class="form-control select2">
                                <option value="all">All</option>
                                @foreach($companies as $key => $item)
                                    @if (@$_GET['company'] == $item->id)
                                        <option selected value="{{ $item->id }}">{{ $item->name }}</option>
                                    @elseif (@$companyId == $item->id)
                                        <option selected value="{{ $item->id }}">{{ $item->name }}</option>
                                    @else
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="department">នាយកដ្ឋាន</label>
                            <select class="form-control select2 department_id_search" id="department_id" name="department_id">
                                <option value="{{ null }}"> << ជ្រើសរើស >> </option>
                                @foreach($departments as $key => $value)
                                    @if($value->id == @$_GET['department_id'])
                                        <option value="{{ $value->id}}" selected="selected">{{ @$value->name_km }}</option>
                                    @elseif ($value->id == @$departmentId)
                                        <option value="{{ $value->id}}" selected="selected">{{ @$value->name_km }}</option>
                                    @else
                                        <option value="{{ $value->id}}">{{ @$value->name_km }}</option>
                                    @endif
                                @endforeach()
                            </select><br/>
                        </div>
                    </div>


                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="exampleInputPassword1">Type</label>
                            <select name="type" id="" class="form-control select2">
                                <option value="{{ null }}"> << ជ្រើសរើស>> </option>
                                <option @if (@$getParamTags == 'daily') selected @endif value="daily">Daily</option>
                                <option @if (@$getParamTags == 'weekly') selected @endif value="weekly">Weekly</option>
                                <option @if (@$getParamTags == 'monthly') selected @endif value="monthly">Monthly</option>
                                <option @if (@$getParamTags == 'quarterly') selected @endif value="quarterly">Quarterly</option>
                                <option @if (@$getParamTags == 'yearly') selected @endif value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Status</label>
                            <select name="status" id="" class="form-control select2">
{{--                                @if ($getParamStatus)--}}
                                    <option value="all">All</option>
                                    <option value="draft" @if ($getParamStatus == config('app.draft') || $getParamStatus == null) selected @endif >Draft</option>
                                    <option value="pending" @if ($getParamStatus == config('app.pending')) selected @endif >Pending</option>
                                    <option value="approved" @if ($getParamStatus == config('app.approved')) selected @endif >Approved</option>
                                    <option value="rejected" @if ($getParamStatus == config('app.rejected')) selected @endif >Rejected</option>
{{--                                @endif--}}
                            </select>
                        </div>
                    </div>

{{--                    <div class="col-sm-3">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="exampleInputPassword1">Post Date From</label>--}}
{{--                            <div class="input-group">--}}
{{--                                <div class="input-group-prepend">--}}
{{--                                      <span class="input-group-text">--}}
{{--                                        <i class="far fa-calendar-alt"></i>--}}
{{--                                      </span>--}}
{{--                                </div>--}}
{{--                                <input--}}
{{--                                    value="{{ $getParamPostDateFrom }}"--}}
{{--                                    name="post_date_from"--}}
{{--                                    type="text"--}}
{{--                                    class="form-control float-right datepicker"--}}
{{--                                    id="post_date_from"--}}
{{--                                    autocomplete="off"--}}
{{--                                >--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-sm-3">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="exampleInputPassword1">Post Date To</label>--}}
{{--                            <div class="input-group">--}}
{{--                                <div class="input-group-prepend">--}}
{{--                                    <span class="input-group-text">--}}
{{--                                        <i class="far fa-calendar-alt"></i>--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                                <input--}}
{{--                                    value="{{ $getParamPostDateTo }}"--}}

{{--                                    name="post_date_to"--}}
{{--                                    type="text"--}}
{{--                                    class="form-control float-right datepicker"--}}
{{--                                    id="post_date_to"--}}
{{--                                    autocomplete="off"--}}
{{--                                >--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <a href="{{ isset($clear_url) ? $clear_url : '/group_request/create' }}" class="btn btn-sm btn-secondary">Clear</a>
                        <button type="submit" class="btn btn-sm btn-success">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


{{--@push('js')--}}
{{--    <script>--}}
{{--        $('.datepicker').datepicker({--}}
{{--            format: 'dd-mm-yyyy'--}}
{{--        });--}}
{{--    </script>--}}

{{--@endpush--}}
