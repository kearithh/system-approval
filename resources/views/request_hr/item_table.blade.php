<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 300px">បរិយាយ<span style='color: red'>*</span></th>
            <th style="min-width: 350px">គោលបំណង</th>
            <th style="min-width: 120px">ឯកត្តា<span style='color: red'>*</span></th>
            <th style="min-width: 120px">បរិមាណ<span style='color: red'>*</span></th>
            <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 120px">ថ្លៃឯកត្តា<span style='color: red'>*</span></th>
            <th style="min-width: 150px">ទឹកប្រាក់</th>
            <th style="min-width: 150px">ថ្ងៃទិញចុងក្រោយ</th>
            <th style="min-width: 150px">ចំនួននៅសល់<span style='color: red'>*</span></th>
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
                        class="col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="col-sm-12"
                        type="text"
                        id="purpose"
                        name="purpose[]"
                    >
                </td>
                <td>
                    <input
                        class="col"
                        type="text"
                        id="unit"
                        name="unit[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="qty col"
                        min="0.01"
                        step="0.01"
                        type="number"
                        id="qty"
                        value="1"
                        name="qty[]"
                        required
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
                        class="col unit_price"
                        type="number"
                        id="unit_price"
                        name="unit_price[]"
                        step="0.0000001"
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
                    >
                </td>
                <td>
                    <input
                        class="col-sm-12 append-datepicker"
                        type="text"
                        id="last_purchase_date"
                        name="last_purchase_date[]"
                        placeholder="dd/mm/yyyy"
                        autocomplete="off"
                    >
                </td>
                <td>
                    <input
                        required
                        class="col-sm-12"
                        type="number"
                        id="remain_qty"
                        name="remain_qty[]"
                    >
                </td>
            </tr>


            <tr id="add_more" style="background: #dee2e6">
                <td class="text-center">
                    <button type="button"
                            id="addItem"
                            class="addsection btn btn-sm btn-success"
                    >
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
                <td colspan="6" class="text-right">សរុប៖</td>
                <td>
                    $ <strong id="total"></strong>
                    <input type="hidden" name="total" id="total_input">
                </td>
                <td>
                    <strong id="totalKHR"></strong><sup>៛</sup>
                    <input type="hidden" name="total_khr" id="total_khr_input">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
