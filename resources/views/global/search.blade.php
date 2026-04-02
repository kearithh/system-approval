<form role="form">
    <div class="card-body p-0">
        <div class="row">
            <div class="col-sm-3">
                <?php $type = isset($_GET['type']) ? $_GET['type'] : null; ?>
                <div class="form-group">
                    <label for="exampleInputPassword1">Type</label>
                    <select name="type" id="" class="form-control">
                        {{--<option @if ($type == 1) selected @endif value="1">All</option>--}}
                        <option @if ($type == 2) selected @endif value="2">Your Request</option>
                        <option @if ($type == 3 || \Illuminate\Support\Facades\Auth::user()->position->level == config('app.position_level_president')) selected @endif value="3">Your Approval</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputEmail1">Status</label>
                    <select name="status" id="" class="form-control">
                        <?php $status = (isset($_GET['status']) ? $_GET['status'] : null); ?>
                        <option value="0">All</option>
                        <option value="2" @if ($status == 2) selected @endif >Approved</option>
                        <option value="1" @if ($status == 1) selected @endif >Pending</option>
                        <option value="3" @if ($status == 3) selected @endif >Commented</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputPassword1">Post Date From</label>
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
                    <label for="exampleInputPassword1">Post Date To</label>
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
        </div>
        <div class="row">
            <div class="col-sm-12">
                <a href="{{ isset($clear_url) ? $clear_url : '/' }}" class="btn btn-sm btn-secondary">Clear</a>
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
@push('js')
    <script>
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy'
        });
    </script>

@endpush
