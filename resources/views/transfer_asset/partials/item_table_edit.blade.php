<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 250px">ឈ្មោះសម្ភារៈ<span style='color: red'>*</span></th>
            <th style="min-width: 250px">ឈ្មោះ<span style='color: red'>*</span></th>
            <th style="min-width: 250px">តួនាទី<span style='color: red'>*</span></th>
            <th style="min-width: 250px">ការបរិយាយ<span style='color: red'>*</span></th>
            <th style="min-width: 200px">ផ្ទេរពីស្ថាប័ន<span style='color: red'>*</span></th>
            <th style="min-width: 200px">ផ្ទេរទៅស្ថាប័នថ្មី<span style='color: red'>*</span></th>
            <th style="min-width: 200px">ផ្សេងៗ</th>
        </thead>
        <tbody>
            @foreach($data->items as $key => $value)
                <tr class="section">
                    <td class="text-center">
                       <button type="button"
                               id="remove"
                               class="remove btn btn-sm btn-danger"
                       >
                           <i class="fa fa-trash"></i>
                       </button>
                    </td>
                    <td>
                        <input
                            class="col-sm-12 form-control"
                            type="text"
                            name="name[]"
                            value="{{$value->name}}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col-sm-12 form-control"
                            type="text"
                            name="staff[]"
                            value="{{$value->staff}}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col form-control"
                            type="text"
                            name="position[]"
                            value="{{$value->position}}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col form-control"
                            type="text"
                            name="detail[]"
                            value="{{$value->detail}}"
                            required
                        >
                    </td>
                    <td>
                        <select required class="col-sm-12 form-control" name="from[]">
                            <option value="">----</option>
                            @foreach($company as $key => $val)
                                <option value="{{ $val->id }}" @if($val->id == $value->from) selected @endif > {{ $val->name }} </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select required class="col-sm-12 form-control" name="to[]">
                            <option value="">----</option>
                            @foreach($company as $key => $val)
                                <option value="{{ $val->id }}" @if($val->id == $value->from) selected @endif > {{ $val->name }} </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="col form-control"
                            type="text"
                            name="other[]"
                            value="{{$value->other}}"
                            required
                        >
                    </td>
                </tr>
            @endforeach

            <tr id="add_more" style="background: #dee2e6">
                <td class="text-center">
                    <button type="button"
                            id="addItem"
                            class="addsection btn btn-sm btn-success"
                    >
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
                <td colspan="7"></td>
            </tr>
        </tbody>
    </table>
</div>
