
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
        <thead class="card-header ">
            <tr>
                <th style="width: 70px">សកម្ម</th>
                <th style="min-width: 300px">តួនាទី<span style='color: red'>*</span></th>
                <th style="min-width: 300px">វគ្គបណ្តុះបណ្តាល<span style='color: red'>*</span></th>
                <th style="min-width: 230px; text-align: center;" colspan="2">កាលបរិច្ឆេទ<span style='color: red'>*</span></th>
                <th style="min-width: 150px; text-align: center;" colspan="2">ម៉ោង</th>
                <th style="min-width: 100px">ចំនួនថ្ងៃ</th>
                <th style="min-width: 200px">ទីតាំងបណ្តុះបណ្តាល<span style='color: red'>*</span></th>
            </tr>
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
                    <textarea rows="1" 
                        class="col-sm-12"
                        id="position"
                        name="position[]"
                        required
                    ></textarea>
                </td>
                <td>
                    <textarea rows="1" 
                        class="col-sm-12"
                        id="course"
                        name="course[]"
                        required
                    ></textarea>
                </td>
                <td>
                    <input
                        class="col append-datepicker"
                        min="1"
                        type="text"
                        id="from_date"
                        name="from_date[]"
                        placeholder="dd-mm-yyyy"
                        autocomplete="off"
                        required
                    >
                </td>
                <td>
                    <input
                        class="col append-datepicker"
                        type="text"
                        id="to_date"
                        name="to_date[]"
                        placeholder="dd-mm-yyyy"
                        autocomplete="off"
                    >
                </td>
                <td>
                    <input
                        class="col"
                        type="time"
                        id="from_time"
                        name="from_time[]"
                    >
                </td>
                <td>
                    <input
                        class="col"
                        type="time"
                        id="to_time"
                        name="to_time[]"
                    >
                </td>
                <td>
                    <input 
                        min="0" 
                        step="0.5" 
                        class="col text-center"
                        type="number"
                        id="number"
                        name="number[]"
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
