<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 250px">ឈ្មោះបុគ្គលិក<span style='color: red'>*</span></th>
            <th style="min-width: 300px">បរិយាយ<span style='color: red'>*</span></th>
            <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 150px">ទឹកប្រាក់ពិន័យ<span style='color: red'>*</span></th>
            <th style="min-width: 150px">សរុបទឹកប្រាក់<span style='color: red'>*</span></th>
            <th style="min-width: 150px">សំគាល់</th>
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
                        required
                        class="col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                        <option value="">----</option>
                        <option value="KHR" selected>រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input 
                        required 
                        step="0.1"
                        class="amount"
                        type="number"
                        id="amount"
                        name="amount[]"
                    >
                </td>
                <td>
                    <input
                        required
                        class="total_amount"
                        type="number"
                        step="0.1"
                        id="total_amount"
                        name="total_amount[]"
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
                <td colspan="5" class="text-right">សរុប៖</td>
                <td>
                    $ <strong id="total">0</strong>
                    <input type="hidden" name="total" id="total_input">
                </td>
                <td>
                    <strong id="totalKHR">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_khr" id="total_khr_input">
                </td>
            </tr>
        </tbody>
    </table>
</div>
