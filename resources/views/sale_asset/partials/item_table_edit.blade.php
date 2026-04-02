<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 300px">ឈ្មោះក្រុមហ៊ុន/សាខា<span style='color: red'>*</span></th>
            <th style="min-width: 300px">ឈ្មោះទ្រព្យសម្បត្តិ<span style='color: red'>*</span></th>
            <th style="min-width: 200px">លេខកូដ</th>
            <th style="min-width: 120px">ឯកត្តា<span style='color: red'>*</span></th>
            <th style="min-width: 120px">បរិមាណ<span style='color: red'>*</span></th>
            <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 120px">ថ្លៃឯកត្តា<span style='color: red'>*</span></th>
            <th style="min-width: 150px">ទឹកប្រាក់</th>
            <th style="min-width: 200px">អ្នកទិញ<span style='color: red'>*</span></th>
            <th style="min-width: 300px">ផ្សេងៗ<span style='color: red'>*</span></th>
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
                            id="branch"
                            name="branch[]"
                            value="{{ $value->branch }}"
                            required
                        >
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
                        <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                            <option value="">----</option>
                            <option value="KHR" @if($value->currency=='KHR') selected @endif>រៀល</option>
                            <option value="USD" @if($value->currency=='USD') selected @endif>ដុល្លារ</option>
                        </select>
                    </td>
                    <td>
                        <input
                            class="col unit_price"
                            type="number"
                            id="unit_price"
                            name="unit_price[]"
                            step="0.0001"
                            value="{{ $value->unit_price }}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            disabled="true"
                            class="amount"
                            type="text"
                            id="amount"
                            data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'"
                            name="amount[]"
                            value="{{ $value->qty * $value->unit_price }}"
                        >
                    </td>
                    <td>
                        <input
                            required 
                            class="col"
                            type="text"
                            id="customer"
                            name="customer[]"
                            value="{{ $value->customer }}"
                        >
                    </td>
                    <td>
                        <input
                            required 
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
                <td>
                    $ <strong id="totalUSD">{{$data->total_usd}}</strong>
                    <input type="hidden" name="total_usd" value="{{$data->total_usd}}" id="total_usd_input">
                </td>
                <td>
                    <strong id="totalKHR">{{$data->total_khr}}</strong><sup>៛</sup>
                    <input type="hidden" name="total_khr" value="{{$data->total_khr}}" id="total_khr_input">
                </td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>
</div>
