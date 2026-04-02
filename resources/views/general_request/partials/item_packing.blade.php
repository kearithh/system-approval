<div class="table-responsive">
    <table id="sections_packing" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 350px">ឈ្មោះ<span style='color: red'>*</span></th>
            <th style="min-width: 120px">ចំនួនប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 300px">ចំនួនទឹកប្រាក់<span style='color: red'>*</span></th>
        </thead>
        
        <tbody>
            <tr class="section_packing">
                <td class="text-center">
                   <button type="button"
                           id="remove_packing"
                           class="remove_packing btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                </td>
                <td>
                    <input
                        required
                        class="col-sm-12 name_packing"
                        type="text"
                        name="name_packing[]"
                    >
                </td>
                <td>
                    <input
                        required
                        class="qty_packing col"
                        value="1"
                        min="1"
                        type="number"
                        name="qty_packing[]"
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency_packing" style="height: 30px" name="currency_packing[]">
                        <option value="">----</option>
                        <option value="KHR" selected >រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input
                        required
                        class="col amount_packing"
                        type="number"
                        name="amount_packing[]"
                        step="0.1"
                        value="0" 
                    >
                </td>
            </tr>

            <tr id="add_more_packing">
                <td class="text-center">
                    <button type="button"
                            id="addItem_packing"
                            class="addsection_packing btn btn-sm btn-success"
                    >
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
                <td colspan="4"></td>
            </tr>
            <tr style="background: #dee2e6">
                <td colspan="3" class="text-right">សរុប៖</td>
                <td>
                    $ <strong id="total_packing">0</strong>
                    <input type="hidden" name="total_packing" id="total_input_packing">
                </td>
                <td>
                    <strong id="totalKHR_packing">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_khr_packing" id="total_khr_input_packing">
                </td>
            </tr>
        </tbody>
    </table>
</div>
