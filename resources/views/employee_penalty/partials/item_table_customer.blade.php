<div class="table-responsive">
    <b>២. ចំនួនទឹកប្រាក់ដែលត្រូវស្នើសុំទូទាត់ឲ្យអតិថិជនវិញ</b>
    <table id="sections_customer" class="table-hover table-bordered" style="display: block; overflow-y: auto">
        <thead class="card-header text-center">
            <tr>
                <th style="width: 70px;" rowspan="2">សកម្ម</th>
                <th style="min-width: 120px" rowspan="2">ឈ្មោះអតិថិជន<span style='color: red'>*</span></th>
                <th style="min-width: 120px" rowspan="2">CID<span style='color: red'>*</span></th>
                <th style="min-width: 120px" rowspan="2">ប្រាក់ដើមនៅជំពាក់<span style='color: red'>*</span></th>
                <th style="min-width: 120px" rowspan="2">ទឹកប្រាក់គៃបន្លំ<span style='color: red'>*</span></th>
                <th style="min-width: 120px" colspan="3">ទឹកប្រាក់ត្រូវទូទាត់ក្នុងប្រព័ន្ធ</th>
                <th style="min-width: 120px" colspan="2">ទឹកប្រាក់ស្នើសុំកាត់ចេញ</th>
                <th style="min-width: 120px" rowspan="2">សំគាល់</th>
            </tr>
            <tr>
                <th style="min-width: 120px">ប្រាក់ដើម<span style='color: red'>*</span></th>
                <th style="min-width: 120px">ការប្រាក់<span style='color: red'>*</span></th>
                <th style="min-width: 120px">ប្រាក់សរុប / <br>ស្នើសុំទូទាត់<span style='color: red'>*</span></th>
                <th style="min-width: 120px">ការប្រាក់<span style='color: red'>*</span></th>
                <th style="min-width: 120px">ប្រាក់ពិន័យ<span style='color: red'>*</span></th>
            </tr>
        </thead>

        <tbody>
            <tr class="section_customer">
                <td class="text-center">
                    <button type="button"
                           id="remove_customer"
                           class="remove_customer btn btn-sm btn-danger"
                    >
                       <i class="fa fa-trash"></i>
                   </button>
                </td>
                <td>
                    <input
                        required
                        class="col"
                        type="text"
                        id="cus_name"
                        name="cus_name[]"
                    >
                </td>
                <td>
                    <input
                        required
                        class="col"
                        type="text"
                        id="cid"
                        name="cid[]"
                    >
                </td>
                <td>
                    <input
                        required
                        class="col indebted"
                        type="number"
                        id="indebted"
                        name="indebted[]"
                        value="0"
                    >
                </td>
                <td>
                    <input
                        required
                        class="col fraud"
                        type="number"
                        id="fraud"
                        name="fraud[]"
                        value="0"
                    >
                </td>
                <td>
                    <input
                        required
                        class="col system_rincipal"
                        type="number"
                        id="system_rincipal"
                        name="system_rincipal[]"
                        value="0"
                    >
                </td>
                <td>
                    <input
                        required
                        class="col system_rate"
                        type="number"
                        id="system_rate"
                        name="system_rate[]"
                        value="0"
                    >
                </td>
                <td>
                    <input
                        required
                        class="col system_total"
                        type="number"
                        id="system_total"
                        name="system_total[]"
                        value="0"
                    >
                </td>
                <td>
                    <input
                        required
                        class="col cut_rate"
                        type="number"
                        id="cut_rate"
                        name="cut_rate[]"
                        value="0"
                    >
                </td>
                <td>
                    <input
                        required
                        class="col cut_penalty"
                        type="number"
                        id="cut_penalty"
                        name="cut_penalty[]"
                        value="0"
                    >
                </td>
                <td>
                    <input
                        class="col"
                        type="text"
                        id="remarks"
                        name="remarks[]"
                    >
                </td>
            </tr>

            <tr id="add_more_customer" style="background: #dee2e6">
                <td class="text-center">
                    <button type="button"
                            id="addItem_customer"
                            class="addsection_customer btn btn-sm btn-success"
                    >
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
                <td colspan="2" class="text-right">សរុប៖</td>
                <td>
                    <strong id="total_indebted_text">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_indebted" id="total_indebted">
                </td>
                <td>
                    <strong id="total_fraud_text">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_fraud" id="total_fraud">
                </td>
                <td>
                    <strong id="total_system_rincipal_text">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_system_rincipal" id="total_system_rincipal">
                </td>
                <td>
                    <strong id="total_system_rate_text">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_system_rate" id="total_system_rate">
                </td>
                <td>
                    <strong id="total_system_total_text">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_system_total" id="total_system_total">
                </td>
                <td>
                    <strong id="total_cut_rate_text">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_cut_rate" id="total_cut_rate">
                </td>
                <td>
                    <strong id="total_cut_penalty_text">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_cut_penalty" id="total_cut_penalty">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
