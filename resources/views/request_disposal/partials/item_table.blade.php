
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
            <th style="min-width: 300px">ឈ្មោះ</th>
            <th style="min-width: 200px">កូដ</th>
            <th style="width: 70px">ចំនួន</th>
            <th style="min-width: 70px">កាលបរិច្ឆេទទិញ</th>
            <th style="min-width: 70px">កាលបរិច្ឆេទខូច</th>
            <th style="min-width: 100px">កន្លែង</th>
        </tr>
        </thead>
        <tbody>

        @if($requestItem==null || count($requestItem) == 0)
            <tr class="section">
                <td class="text-center">
                    <div>
                        <button type="button"
                                id="remove"
                                class="remove btn btn-sm btn-danger"
                        >
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
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
                        class="col-sm-12"
                        type="text"
                        id="code"
                        name="code[]"
                        required
                    >
                </td>
                <td>
                    <input
                        readonly
                        class="col qty text-center"
                        type="text"
                        id="qty"
                        name="qty[]"
                        value="1"
                        required
                    >
                </td>
                <td>
                    <input
                        class="col"
                        min="1"
                        type="date"
                        id="purchase_date"
                        name="purchase_date[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="col"
                        type="date"
                        id="broken_date"
                        name="broken_date[]"
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
                        required
                    >
                </td>
            </tr>
        @else
            @foreach($requestItem as $key => $value)
                <tr class="section">
                    <td class="text-center">
                        <div>
                            <button type="button"
                                    id="remove"
                                    class="remove btn btn-sm btn-danger"
                            >
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
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
                            id="code"
                            name="code[]"
                            value="{{$value->code}}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            readonly
                            class="col qty text-center"
                            type="text"
                            id="qty"
                            name="qty[]"
                            value="1"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col"
                            min="1"
                            type="date"
                            id="purchase_date"
                            name="purchase_date[]"
                            value="{{$value->purchase_date->format('Y-m-d')}}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col"
                            type="date"
                            id="broken_date"
                            name="broken_date[]"
                            value="{{$value->broken_date->format('Y-m-d')}}"
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
        @endif

        <tr id="add_more">
            <td class="text-center">
                <button type="button"
                        id="addItem"
                        class="addsection addValue btn btn-sm btn-success"
                >
                    <i class="fa fa-plus"></i>
                </button>
            </td>
            <td colspan="6"></td>
        </tr>
        </tbody>
    </table>
</div>
