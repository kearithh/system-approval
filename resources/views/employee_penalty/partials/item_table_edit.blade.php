<div class="table-responsive">
    <b>១. ចំនួនទឹកប្រាក់ដែលទទួលបានពីបុគ្គលិក</b>
    <table id="sections" class="table table-bordered table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 500px">បរិយាយ<span style='color: red'>*</span></th>
            <th style="min-width: 150px">ចំនួនទឹកប្រាក់<span style='color: red'>*</span></th>
            <th style="min-width: 200px">ផ្សេងៗ</th>
        </thead>
        <tbody>
            @foreach($data->items as $item)
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
                            id="desc"
                            name="desc[]"
                            value="{{$item->desc}}"
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
                            value="{{$item->total}}"
                        >
                    </td>
                    <td>
                        <input
                            class="col"
                            type="text"
                            id="other"
                            name="other[]"
                            value="{{$item->other}}"
                        >
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
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>
</div>
