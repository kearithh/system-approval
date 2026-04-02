<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 300px">ឈ្មោះសម្ភារៈ<span style='color: red'>*</span></th>
            <th style="min-width: 200px">លេខកូដ</th>
            <th style="min-width: 120px">ឯកត្តា<span style='color: red'>*</span></th>
            <th style="min-width: 120px">បរិមាណ<span style='color: red'>*</span></th>
            <th style="min-width: 300px">ផ្សេងៗ</th>
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
                        id="code"
                        name="code[]"
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
                    <input
                        class="col"
                        type="text"
                        id="other"
                        name="other[]"
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
                <td colspan="2"></td>
                <td class="text-right">សរុប៖</td>
                <td class="text-center">
                    <strong id="total"></strong>
                    <input type="hidden" name="total" id="total_input">
                </td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</div>
