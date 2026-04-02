<form role="form">
    <div class="card-body p-0">
        <div class="row">
            <div class="col-sm-3">
                <?php $status = isset($_GET['status']) ? $_GET['status'] : null; ?>
                <div class="form-group">
                    <label for="exampleInputEmail1">Status</label>
                    <select name="status" id="" class="form-control">
                        <option @if ($status == 1) selected @endif value="1">All</option>
                        <option @if ($status == 2) selected @endif value="2">Approved</option>
                        <option @if ($status == 3) selected @endif value="3">Pending</option>
                        <option @if ($status == 4) selected @endif value="4">Rejected</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <?php $type = isset($_GET['type']) ? $_GET['type'] : null; ?>
                <div class="form-group">
                    <label for="exampleInputPassword1">Type</label>
                    <select name="type" id="" class="form-control">
                        {{--<option @if ($type == 1) selected @endif value="1">All</option>--}}
                        <option @if ($type == 2) selected @endif value="2">Your Request</option>
                        <option @if ($type == 3) selected @endif value="3">Your Approval</option>
                    </select>
                </div>
            </div>
{{--            <div class="col-sm-3">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="exampleInputPassword1">Post Date From</label>--}}
{{--                    <div class="input-group">--}}
{{--                        <div class="input-group-prepend">--}}
{{--                      <span class="input-group-text">--}}
{{--                        <i class="far fa-calendar-alt"></i>--}}
{{--                      </span>--}}
{{--                        </div>--}}
{{--                        <input--}}
{{--                            value="{{ isset($_GET['post_date_from']) ? $_GET['post_date_from'] : '' }}"--}}
{{--                            name="post_date_from"--}}
{{--                            type="text"--}}
{{--                            class="form-control float-right datepicker"--}}
{{--                            id="post_date_from"--}}
{{--                            autocomplete="off"--}}
{{--                        >--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-sm-3">--}}
{{--                <div class="form-group">--}}
{{--                    <label for="exampleInputPassword1">Post Date To</label>--}}
{{--                    <div class="input-group">--}}
{{--                        <div class="input-group-prepend">--}}
{{--                      <span class="input-group-text">--}}
{{--                        <i class="far fa-calendar-alt"></i>--}}
{{--                      </span>--}}
{{--                        </div>--}}
{{--                        <input--}}
{{--                            value="{{ isset($_GET['post_date_to']) ? $_GET['post_date_to'] : '' }}"--}}

{{--                            name="post_date_to"--}}
{{--                            type="text"--}}
{{--                            class="form-control float-right datepicker"--}}
{{--                            id="post_date_to"--}}
{{--                            autocomplete="off"--}}
{{--                        >--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-sm btn-success">Search</button>
                <a href="/report/request" class="btn btn-sm btn-secondary">Clear</a>
            </div>
        </div>
    </div>
    <!-- /.card-body -->
</form>
