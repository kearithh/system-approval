<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
        <th style="width: 70px;">សកម្ម</th>
        <th style="min-width: 300px">បរិយាយមុខទំនិញ<br> Product Name / Description</th>
        <th style="min-width: 120px">ចំនួនកម្មង់<br>Order Qty<span style='color: red'>*</span></th>
        <th style="min-width: 120px">ចំនួនប្រគល់<br>Deliverd Qty<span style='color: red'>*</span></th>
        <th style="min-width: 150px">Note</th>
        </thead>
        <tbody>
        @foreach($data->items as $key => $item)
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
                    value="{{ $item->name }}"
                    required
                    class="col-sm-12"
                    type="text"
                    id="name"
                    name="name[]"
                >
            </td>
            {{-- <td>
                <input
                    value="{{ $item->desc }}"
                    class="col-sm-12"
                    type="text"
                    id="desc"
                    name="desc[]"
                >
            </td> --}}
            <td>
                <input
                    value="{{ $item->qty }}"
                    required
                    class="qty col"
                    min="0.5"
                    step="0.5"
                    type="number"
                    id="qty"
                    name="qty[]"
                >
            </td>
            <td>
                <input
                    value="{{ $item->dqty }}"
                    required
                    class="dqty col"
                    min="0.5"
                    step="0.5"
                    type="number"
                    id="dqty"
                    name="dqty[]"
                >
            </td>
            
            <td>
                <input
                    value="{{ $item->other }}"
                    class="col"
                    type="text"
                    id="other"
                    name="other[]"
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
            <td colspan="6"></td>
        </tr>
        
        </tbody>
    </table>
    <hr style="width:100%;text-align:left;margin-left:0">
</div>
