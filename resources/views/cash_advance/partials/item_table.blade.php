<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
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
                        min="1"
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
                        class="amount col-sm-12"
                        type="text"
                        id="amount"
                        data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'"
                        name="amount[]"
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
                    >
                </td>
                <td>
                    <textarea name="desc[]" rows="1"></textarea>
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
                <td colspan="9"></td>
            </tr>
            <tr style="background: #dee2e6">
                <td colspan="5" class="text-right">សរុប៖</td>
                <td>
                    $ <strong id="total"></strong>
                    <input type="hidden" name="total" id="total_input">
                </td>
                <td>
                    <strong id="totalKHR"></strong><sup>៛</sup>
                    <input type="hidden" name="total_khr" id="total_khr_input">
                </td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</div>
