<div class="table-responsive">
    <table id="sections_keep" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 350px">ប្រភេទ<span style='color: red'>*</span></th>
            <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 120px">សាច់ប្រាក់អតិបរិមាដែលអាចរក្សារទុកបាន<span style='color: red'>*</span></th>
            <th style="min-width: 120px">សាច់ប្រាក់ជាក់ស្ដែង<span style='color: red'>*</span></th>
            <th style="min-width: 120px">សាច់ប្រាក់លើស<span style='color: red'>*</span></th>
        </thead>
        
        <tbody>
            <tr class="section_keep">
                <td class="text-center">
                   <button type="button"
                           id="remove_keep"
                           class="remove_keep btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                </td>
                <td>
                    <input
                        required
                        class="col-sm-12 name_keep"
                        type="text"
                        name="name_keep[]"
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency_keep" style="height: 30px" name="currency_keep[]">
                        <option value="">----</option>
                        <option value="KHR" selected >រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input
                        required
                        class="qty_keep col"
                        value="0"
                        min="1"
                        type="number"
                        name="qty_keep[]"
                    >
                </td>
                <td>
                    <input
                        required
                        class="col amount_keep"
                        type="number"
                        name="amount_keep[]"
                        step="0.1"
                        value="0" 
                    >
                </td>
                <td>
                    <input
                        required
                        class="qty_keep col"
                        value="0"
                        min="0"
                        type="number"
                        id="qty_keep"
                        name="qty_keep[]"
                    >
                </td>
            </tr>

            <tr id="add_more_keep">
                <td class="text-center">
                    <button type="button"
                            id="addItem_keep"
                            class="addsection_keep btn btn-sm btn-success"
                    >
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
                <td colspan="5"></td>
            </tr>
            <tr style="background: #dee2e6">
                <td colspan="4" class="text-right">សរុប៖</td>
                <td>
                    $ <strong id="total_keep">0</strong>
                    <input type="hidden" name="total_keep" id="total_input_keep">
                </td>
                <td>
                    <strong id="totalKHR_keep">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_khr_keep" id="total_khr_input_keep">
                </td>
            </tr>
        </tbody>
    </table>
</div>
