<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 350px">ឈ្មោះអតិថិជន<span style='color: red'>*</span></th>
            <th style="min-width: 300px">ប្រភេទទ្រព្យធានា<span style='color: red'>*</span></th>
            <th style="min-width: 120px">កាលបរិច្ឆេទស្នើសុំដក<span style='color: red'>*</span></th>
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
            <td>
                <input
                    required
                    value="{{ $item->type }}"
                    class="col-sm-12"
                    type="text"
                    id="type"
                    name="type[]"
                >
            </td>
            <td>
                <input
                    required
                    @if($item->date != null)
                        value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($item->date))->format('d-m-Y'))}}"
                    @endif
                    class="col-sm-12 append-datepicker"
                    type="text"
                    id="date"
                    name="date[]"
                    placeholder="dd/mm/yyyy"
                    autocomplete="off"
                >
            </td>
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
            <td colspan="3"></td>
        </tr>
        </tbody>
    </table>
</div>
