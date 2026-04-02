<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 150px">ឈ្មោះអតិថិជន<span style='color: red'>*</span></th>
            <th style="min-width: 150px">លេខ CID<span style='color: red'>*</span></th>
            <th style="min-width: 120px">ចំនួនទឹកប្រាក់បើក(រៀល)<span style='color: red'>*</span></th>
            <th style="min-width: 80px">ឈ្មោះភូមិ<span style='color: red'>*</span></th>
            <th style="min-width: 150px">ចម្ងាយផ្លូវ</th>
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
                            required
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="name[]"
                            value="{{ $item->name }}"
                        >
                    </td>
                    <td>
                        <input
                            required
                            class="col-sm-12"
                            type="text"
                            id="cid"
                            name="cid[]"
                            value="{{ $item->cid }}"
                        >
                    </td>
                    <td>
                        <input
                            required
                            class="amount col"
                            min="100"
                            step="100"
                            type="number"
                            id="amount"
                            name="amount[]"
                            value="{{ $item->amount }}"
                        >
                    </td>
                    <td>
                        <input
                            required
                            class="name_v col"
                            type="text"
                            id="name_v"
                            name="name_v[]"
                            value="{{ $item->name_v }}"
                        >
                    </td>
                    <td>
                        <input
                            class="road col"
                            type="text"
                            id="road"
                            name="road[]"
                            value="{{ $item->road }}"
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
                <td colspan="5"></td>
            </tr>
            <tr style="background: #dee2e6">
                <td colspan="3" class="text-right"></td>
                <td>
                    <strong id="totalKHR">0</strong><sup>៛</sup>
                    <input type="hidden" name="total_khr" id="total_khr_input">
                </td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</div>
