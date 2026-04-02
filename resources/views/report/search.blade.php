<form role="form">
    <div class="card-body p-0">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputEmail1">Status</label>
                    <select name="status" id="" class="form-control">
                        <?php $status = (isset($_GET['status']) ? $_GET['status'] : null); ?>
                        <option value="1">All</option>
                        <option value="2" @if ($status == 2) selected @endif >Approved</option>
                        <option value="3" @if ($status == 3) selected @endif >Pending</option>
                        <option value="4" @if ($status == 4) selected @endif >Rejected</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputEmail1">Type</label>
                    <select name="type" id="" class="form-control">
                        <?php $type = (isset($_GET['type']) ? $_GET['type'] : null); ?>
                        <option value="2" @if ($type == 2) selected @endif >My Request</option>
                        <option value="3" @if ($type == 3) selected @endif>My Review</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputPassword1">Start date</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                        </div>
                        <input
                                value=""
                                name="start_date"
                                type="text"
                                class="form-control float-right"
                                id="start_date"
                                data-inputmask-alias="datetime"
                                data-inputmask-inputformat="dd/mm/yy"
                                data-mask="" im-insert="true">
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputPassword1">End date</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                        </div>
                        <input
                                name="end_date"
                                type="text"
                                class="form-control float-right"
                                id="end_date"
                                data-inputmask-alias="datetime"
                                data-inputmask-inputformat="dd/mm/yy"
                                data-mask="" im-insert="true">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <a href="/report/request" class="btn btn-sm btn-secondary">Clear</a>
                <button type="submit" class="btn btn-sm btn-success">Search</button>
                {{--<button type="submit" formaction="..." class="btn btn-sm btn-primary">Export</button>--}}
            </div>
        </div>









        {{--<div class="input-group">--}}
            {{--<div class="input-group-prepend">--}}
                {{--<span class="input-group-text"><i class="far fa-calendar-alt"></i></span>--}}
            {{--</div>--}}
            {{--<input--}}
                    {{--value=""--}}
                    {{--id="datemask"--}}
                    {{--type="text"--}}
                    {{--class="form-control"--}}
                    {{--data-inputmask-alias="datetime"--}}
                    {{--data-inputmask-inputformat="dd/mm/yy"--}}
                    {{--data-mask="" im-insert="true">--}}
        {{--</div>--}}

    </div>
    <!-- /.card-body -->
</form>
