<div>
    <form role="form">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="company">Company</label>
                        <select name="company" id="company" class="form-control select2">
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
                        <label for="deprtment">Department</label>
                        <select name="department" id="department" class="form-control select2">
                            <option value="{{ null }}"> << ជ្រើសរើស >> </option>
                            @foreach($departments as $key => $value)
                                @if($value->id == @$_GET['department'])
                                    <option value="{{ $value->id}}" selected="selected">{{ @$value->name_en }}</option>
                                @elseif ($value->id == @$departmentId)
                                    <option value="{{ $value->id}}" selected="selected">{{ @$value->name_en }}</option>
                                @else
                                    <option value="{{ $value->id}}">{{ @$value->name_en }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="exampleInputPassword1">Type</label>
                        <select name="type" id="" class="form-control select2">
                            <option @if (@$_GET['type'] == 'daily') selected @endif value="daily">Daily</option>
                            <option @if (@$_GET['type'] == 'weekly') selected @endif value="weekly">Weekly</option>
                            <option @if (@$_GET['type'] == 'monthly') selected @endif value="monthly">Monthly</option>
                            <option @if (@$_GET['type'] == 'quarterly') selected @endif value="quarterly">Quarterly</option>
                            <option @if (@$_GET['type'] == 'yearly') selected @endif value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="exampleInputPassword1">Date</label>
                            <input  style="background-color: white;" 
                                    placeholder="dd-mm-yyyy" 
                                    readonly 
                                    value="{{ isset($_GET['date']) ? $_GET['date'] : (\Carbon\Carbon::now()->format('d-m-Y')) }}" 
                                    name="date"
                                    type="text"
                                    class="form-control float-right mydatepicker"
                                    id="date"
                                    autocomplete="off"
                                    data-date-end-date="0d"
                            >
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
    </form>
</div>

