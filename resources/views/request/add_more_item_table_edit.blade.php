<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
        <th style="width: 70px;">សកម្ម</th>
        <th style="min-width: 350px">ឈ្មោះ<span style='color: red'>*</span></th>
        <th style="min-width: 300px">បរិយាយ</th>
        <th style="min-width: 120px">បរិមាណ<span style='color: red'>*</span></th>
        <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
        <th style="min-width: 150px">ថ្លៃឯកត្តា<span style='color: red'>*</span></th>
        <th style="min-width: 120px">ពន្ធ(%)<span style='color: red'>*</span></th>
        <th style="min-width: 150px">ទឹកប្រាក់</th>
        <th style="min-width: 150px">ផ្សេងៗ</th>
        </thead>
        <tbody>
        @foreach($data->items as $key => $item)
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
                    value="{{ $item->name }}"
                    required
                    class="col-sm-12"
                    type="text"
                    id="name"
                    name="name[]"
                >
            </td>
            <td>
                <input
                    value="{{ $item->desc }}"
                    class="col-sm-12"
                    type="text"
                    id="desc"
                    name="desc[]"
                >
            </td>
            <td>
                <input
                    value="{{ $item->qty }}"
                    required
                    class="qty col"
                    min="0.5"
                    step="0.5"
                    type="number"
                    id="qty"
                    name="qty[]"
                >
            </td>
            <td>
                <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                    <option value="">----</option>
                    <option value="KHR" @if($item->currency=='KHR') selected @endif>រៀល</option>
                    <option value="USD" @if($item->currency=='USD') selected @endif>ដុល្លារ</option>
                </select>
            </td>
            <td>
                <input
                    value="{{ $item->unit_price }}"
                    required
                    class="col unit_price"
                    type="number"
                    id="unit_price"
                    name="unit_price[]"
                    step="0.0001"
{{--                    data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'"--}}

                >
            </td>
            <td>
                <input
                    value="{{ $item->vat ? $item->vat : 0 }}"
                    class="col vat"
                    type="number"
                    id="vat"
                    name="vat[]"
                    step="0.01"
                >
            </td>
            <td>
                <input
                    value="{{ $item->amount }}"
                    readonly="true"
                    class="amount"
                    type="text"
                    id="amount"
                    data-value="{{ $item->amount }}"
                    data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'"
                    name="amount[]"
                >
            </td>
            <td>
                <input
                    value="{{ $item->other }}"
                    class="col"
                    type="text"
                    id="other"
                    name="other[]"
                >
            </td>
        </tr>
        @endforeach

        <tr id="add_more">
            <td class="text-center">
                <button type="button"
                        id="addItem"
                        class="addsection btn btn-sm btn-success"
                >
                    <i class="fa fa-plus"></i>
                </button>
            </td>
            <td colspan="6"></td>
        </tr>
        <tr style="background: #dee2e6">
            <td colspan="6" class="text-right">សរុប៖</td>
            <td>
                $ <strong id="total">{{ $data->total_amount_usd }}</strong>
                <input type="hidden" value="{{ $data->total_amount_usd }}" name="total" id="total_input">
            </td>
            <td>
                <strong id="totalKHR">{{ $data->total_amount_khr }}</strong><sup>៛</sup>
                <input type="hidden" value="{{ $data->total_amount_khr }}" name="total_khr" id="total_khr_input">
            </td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>
