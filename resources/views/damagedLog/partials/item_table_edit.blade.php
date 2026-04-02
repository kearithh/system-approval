
@push('css')
    <style>
        .table td {
            padding: 0.1em;
        }
        .datepicker {
            padding: 1px;
            border-radius: 0;;
        }
    </style>
@endpush
<div class="table-responsive">
    <table id="sections" class="table table-hover" style="width: 100%; overflow-y: auto">
        <thead>
            <tr>
                <th style="width: 70px">សកម្ម</th>
                <th style="min-width: 300px">ឈ្មោះទ្រព្យសម្បត្តិ<span style='color: red'>*</span></th>
                <th style="min-width: 180px">ឈ្មោះអ្នកប្រើប្រាស់<span style='color: red'>*</span></th>
                <th style="min-width: 200px">លេខកូដ<span style='color: red'>*</span></th>
                <th style="width: 120px">ចំនួន<span style='color: red'>*</span></th>
                <th style="min-width: 100px">ឯកត្តា<span style='color: red'>*</span></th>
                <th style="min-width: 150px">កាលបរិច្ឆេទទិញ</th>
                <th style="min-width: 150px">កាលបរិច្ឆេទខូច<span style='color: red'>*</span></th>
                <th style="min-width: 100px">ទីកន្លែង<span style='color: red'>*</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach($damagedItem as $key => $value)
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
                            value="{{$value->name}}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="staff"
                            name="staff[]"
                            value="{{$value->staff}}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="code"
                            name="code[]"
                            value="{{$value->code}}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col number"
                            type="number"
                            id="number"
                            name="number[]"
                            value="{{$value->number}}"
                            min="0"
                            step="0.005"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col unit"
                            type="text"
                            id="unit"
                            name="unit[]"
                            value="{{$value->unit}}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col append-datepicker"
                            min="1"
                            type="text"
                            id="purchase_date"
                            name="purchase_date[]"
                            autocomplete="off"
                            @if($value->purchase_date != null)
                                value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($value->purchase_date))->format('d-m-Y'))}}"
                            @endif
                            placeholder="dd-mm-yyyy"
                        >
                    </td>
                    <td>
                        <input
                            class="col append-datepicker"
                            type="text"
                            id="broken_date"
                            name="broken_date[]"
                            value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($value->broken_date))->format('d-m-Y'))}}"
                            placeholder="dd-mm-yyyy"
                            autocomplete="off"
                            required
                        >
                    </td>
                    <td>
                        <input
                            style="width: 100%"
                            class="location"
                            type="text"
                            id="location"
                            name="location[]"
                            value="{{$value->location}}"
                            required
                        >
                    </td>
                </tr>
            @endforeach()

            <tr id="add_more">
                <td class="text-center">
                    <button type="button"
                            id="addItem"
                            class="addsection addValue btn btn-sm btn-success"
                    >
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
                <td colspan="8"></td>
            </tr>
        </tbody>
    </table>
</div>
