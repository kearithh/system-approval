<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 300px">ឈ្មោះសម្ភារៈ<span style='color: red'>*</span></th>
            <th style="min-width: 200px">លេខកូដ</th>
            <th style="min-width: 120px">ឯកត្តា<span style='color: red'>*</span></th>
            <th style="min-width: 120px">បរិមាណ<span style='color: red'>*</span></th>
            <th style="min-width: 300px">ផ្សេងៗ</th>
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
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="name[]"
                            value="{{ $value->name }}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col"
                            type="text"
                            id="code"
                            name="code[]"
                            value="{{ $value->code }}"
                        >
                    </td>
                    <td>
                        <input
                            class="col"
                            type="text"
                            id="unit"
                            name="unit[]"
                            value="{{ $value->unit }}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="qty col"
                            min="1"
                            type="number"
                            id="qty"
                            name="qty[]"
                            value="{{ $value->qty }}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col"
                            type="text"
                            id="other"
                            name="other[]"
                            value="{{ $value->others }}"
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
                <td colspan="2"></td>
                <td class="text-right">សរុប៖</td>
                <td>
                    <strong id="total">{{$data->total_item}}</strong>
                    <input type="hidden" name="total" value="{{$data->total_item}}" id="total_input">
                </td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>
</div>
