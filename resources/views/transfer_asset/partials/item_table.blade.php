<div class="table-responsive">
    <table id="sections" class="table table-hover table-bordered" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 250px">ឈ្មោះសម្ភារៈ<span style='color: red'>*</span></th>
            <th style="min-width: 250px">ឈ្មោះ<span style='color: red'>*</span></th>
            <th style="min-width: 250px">តួនាទី<span style='color: red'>*</span></th>
            <th style="min-width: 250px">ការបរិយាយ<span style='color: red'>*</span></th>
            <th style="min-width: 200px">ផ្ទេរពីស្ថាប័ន<span style='color: red'>*</span></th>
            <th style="min-width: 200px">ផ្ទេរទៅស្ថាប័នថ្មី<span style='color: red'>*</span></th>
            <th style="min-width: 200px">ផ្សេងៗ</th>
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
                        class="col-sm-12 form-control"
                        type="text"
                        name="name[]"
                        value=""
                        required
                    >
                </td>
                <td>
                    <input
                        class="col-sm-12 form-control"
                        type="text"
                        name="staff[]"
                        value=""
                        required
                    >
                </td>
                <td>
                    <input
                        class="col form-control"
                        type="text"
                        name="position[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="col form-control"
                        type="text"
                        name="detail[]"
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 form-control" name="from[]">
                        <option value="">----</option>
                        @foreach($company as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select required class="col-sm-12 form-control" name="to[]">
                        <option value="">----</option>
                        @foreach($company as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input
                        class="col form-control"
                        type="text"
                        name="other[]"
                        required
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
                <td colspan="7"></td>
            </tr>
        </tbody>
    </table>
</div>
