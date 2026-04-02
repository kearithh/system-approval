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
                    required
                    class="col-sm-12"
                    type="text"
                    id="name"
                    name="name[]"
                >
            </td>
            <td>
                <input
                    class="col-sm-12"
                    type="text"
                    id="desc"
                    name="desc[]"
                >
            </td>
            <td>
                <input
                    required
                    class="qty col"
                    value="1"
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
                    <option value="KHR" @if(Auth::user()->branch_id > 1) selected @endif>រៀល</option>
                    <option value="USD">ដុល្លារ</option>
                </select>
            </td>
            <td>
                <input
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
                    required
                    class="col vat"
                    type="number"
                    id="vat"
                    name="vat[]"
                    value="0"
                    step="0.01"
                >
            </td>
            <td>
                <input
                    disabled="true"
                    class="amount"
                    type="text"
                    id="amount"
                    data-value="0"
                    data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'"
                    name="amount[]"
                >
            </td>
            <td>
                <input
                    class="col"
                    type="text"
                    id="other"
                    name="other[]"
                >
            </td>
        </tr>


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
                $ <strong id="total">0</strong>
                <input type="hidden" name="total" id="total_input">
            </td>
            <td>
                <strong id="totalKHR">0</strong><sup>៛</sup>
                <input type="hidden" name="total_khr" id="total_khr_input">
            </td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>
