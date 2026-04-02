<div class="row">
    <div class="col-sm-2 col-form-label">
        <label class="col-form-label">កម្មវត្ថុ<span style='color: red'>*</span></label>
    </div> 
    <div class="col-sm-10">
        <div class="table-responsive">
            <table class="table table-hover" style="display: block; overflow-y: auto">
                <thead class="card-header ">
                    <th>ការប្រាក់ហួសកាលកំណត់(៛)<span style='color: red'>*</span></th>
                    <th>ប្រភេទកម្ចី<span style='color: red'>*</span></th>
                    <th>អតិថិជនឈ្មោះ<span style='color: red'>*</span></th>
                    <th>ភេទ<span style='color: red'>*</span></th>
                    <th>CID<span style='color: red'>*</span></th>
                    <th>ថ្ងៃបើកប្រាក់<span style='color: red'>*</span></th>
                    <th>ចំនួនថ្ងៃយឺត(ថ្ងៃ)<span style='color: red'>*</span></th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input 
                                style="height: 30px; width: 150px;"
                                class="col-sm-12" 
                                type="number"
                                name="subject_obj[interest_past]"
                                required
                            >
                        </td>
                        <td>
                            <select required style="height: 30px; width: 150px;" name="subject_obj[type_loan]">
                                <option value="">----</option>
                                <option value="1">អតិថិជនយឺតយ៉ាវ (Loan Default)</option>
                                <option value="2">កម្ចីលុបចេញពីបញ្ជី (Write Off)</option>
                            </select>
                        </td>
                        <td>
                            <input 
                                style="height: 30px; width: 150px;"
                                type="text"
                                name="subject_obj[customer_name]"
                                required
                            >
                        </td>
                        <td>
                            <select required style="height: 30px; width: 70px;" name="subject_obj[gender]">
                                <option value="">----</option>
                                <option value="M">ប្រុស</option>
                                <option value="F">ស្រី</option>
                            </select>
                        </td>
                        <td>
                            <input
                                style="width: 120px;" 
                                type="text"
                                name="subject_obj[cid]"
                                required
                            >
                        </td>
                        <td>
                            <input 
                                class="datepicker" 
                                type="text"
                                name="subject_obj[date_open]"
                                data-inputmask-inputformat="dd-mm-yyyy"
                                placeholder="dd-mm-yyyy"
                                autocomplete="off"
                                style="height: 30px; width: 120px;" 
                                required
                            >
                        </td>
                        <td>
                            <input
                                class="col-sm-12" 
                                type="number"
                                name="subject_obj[number_day_late]"
                                required
                            >
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
