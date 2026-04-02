<div class="table-responsive">
    <table id="sections" class="table table-hover" style=" overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 250px">ឈ្មោះចំណាយ<span style='color: red'>*</span></th>
            <th style="min-width: 100px">ឯកត្តា<span style='color: red'>*</span></th>
            <th style="min-width: 100px">បរិមាណ<span style='color: red'>*</span></th>
            <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 120px">ថ្លៃឯកត្តា<span style='color: red'>*</span></th>
            <th style="min-width: 140px">ទឹកប្រាក់</th>
            <th style="min-width: 100px">ថ្ងែចំណាយ</th>
            <th style="min-width: 150px">បរិយាយចំណាយ</th>
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
                            required
                            value="{{ $value->name }}"
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
                            step="0.0000001"
                            required
                            value="{{ $value->unit_price }}"
                        >
                    </td>
                    <td>
                        <input
                            disabled="true"
                            class="amount"
                            type="text"
                            id="amount"
                            data-value="{{ $value->qty * $value->unit_price }}"
                            data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'"
                            name="amount[]"
                            value="{{ $value->qty * $value->unit_price }}"
                        >
                    </td>
                    <td>
                        <input
                            class="append-datepicker datepicker col-sm-12"
                            type="text"
                            name="date[]"
                            placeholder="dd-mm-yyyy"
                            autocomplete="off"
                            style="height: 30px;"
                            @if($value->date != null)
                                value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($value->date))->format('d-m-Y'))}}"
                            @endif
                        >
                    </td>
                    <td>
                        <textarea name="desc[]" rows="1">{{ @$value->desc }}</textarea>
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
                <td colspan="5" class="text-right">សរុប៖</td>
                <td>
                    $ <strong id="total">{{ $data->total}}</strong>
                    <input type="hidden" value="{{ $data->total }}" name="total" id="total_input">
                </td>
                <td>
                    <strong id="totalKHR">{{ $data->total_khr }}</strong><sup>៛</sup>
                    <input type="hidden" value="{{ $data->total_khr }}" name="total_khr" id="total_khr_input">
                </td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</div>
