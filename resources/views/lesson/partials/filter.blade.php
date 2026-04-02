<form role="form">
    <div class="card-body p-0">
        <div class="row">

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Keyword</label>
                    <?php $keyword = (isset($_GET['keyword']) ? $_GET['keyword'] : null); ?>
                    <input type="text" name="keyword" placeholder="ចំណងជើង" value="{{ $keyword }}" class="form-control">
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Company</label>
                    <select name="company_id" id="" class="form-control select2">
                        <option value="">All</option>
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
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Department</label>
                    <select name="department_id" id="" class="form-control select2">
                        <option value="">All</option>
                        <?php $department_id = (isset($_GET['department_id']) ? $_GET['department_id'] : null); ?>
                        @foreach($department as $key => $value)
                            <option value="{{ $value->id}}"
                                @if($department_id == $value->id))
                                    selected
                                @endif
                            >
                            {{ $value->name_km }}
                          </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputPassword1">Position</label>
                    <select name="position_id" id="" class="form-control select2">
                        <option value="">All</option>
                        <?php $position_id = (isset($_GET['position_id']) ? $_GET['position_id'] : null); ?>
                        @foreach($position as $key => $value)
                            <option value="{{ $value->id}}"
                                @if($position_id == $value->id))
                                    selected
                                @endif
                            >
                            {{ $value->name_km }}
                          </option>
                        @endforeach
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
                <!-- <button type="submit" formaction="{{ $clear_url }}/export" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Export
                </button> -->
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
