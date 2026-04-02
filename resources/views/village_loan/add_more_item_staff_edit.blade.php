<div class="table-responsive">
    <table id="sections_staff" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 300px">ឈ្មោះបុគ្គលិក<span style='color: red'>*</span></th>
            <th style="min-width: 300px">តួនាទី<span style='color: red'>*</span></th>
            <th style="min-width: 300px">សមាសភាព<span style='color: red'>*</span></th>
        </thead>
        <tbody>
            <?php $staffs = json_decode($data->staff_obj) ?>
            @foreach($staffs as $key => $staff)

                <tr class="section_staff">
                    <td class="text-center">
                       <button type="button"
                               id="remove_staff"
                               class="remove_staff btn btn-sm btn-danger"
                       >
                           <i class="fa fa-trash"></i>
                       </button>
                    </td>
                    <td>
                        <input
                            required
                            class="col-sm-12"
                            type="text"
                            id="name_staff"
                            name="name_staff[]"
                            value="{{ $staff->name_staff }}"
                        >
                    </td>
                    <td>
                        <input
                            required
                            class="col-sm-12"
                            type="text"
                            id="position_staff"
                            name="position_staff[]"
                             value="{{ $staff->position_staff }}"
                        >
                    </td>
                    <td>
                        <input
                            required
                            class="col-sm-12"
                            type="text"
                            id="composition"
                            name="composition[]"
                            value="{{ $staff->composition }}"
                        >
                    </td>
                </tr>
            @endforeach
            <tr id="add_more_staff">
                <td class="text-center">
                    <button type="button"
                            id="addItemStaff"
                            class="addsectionsStaff btn btn-sm btn-success"
                    >
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
</div>
