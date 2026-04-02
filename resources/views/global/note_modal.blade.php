
<div class="modal fade" id="note_modal" tabindex="-1" role="dialog" aria-labelledby="note_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <form method="POST" action="{{ $route }}" enctype="multipart/form-data">
                    @csrf
                    <table class="table table-bordered">
                        <tr>
                            <th>ពេលទៅដល់គោលដៅ</th>
                            <th>ពេលត្រលប់មកវិញ</th>
                        </tr>
                        <tr>

                            <td>
                                <div class="form-group">
                                    <input type="date" required name="comment[date_in]" class="form-control datepicker" id="date_in" placeholder="កាលបរិច្ឆេទ*">
                                </div>
                                <div class="form-group">
                                    <input type="time" required name="comment[time_in]" class="form-control" id="time_in" placeholder="ម៉ោង*">
                                </div>
                                <div class="form-group">
                                    <input type="number" min="0" required name="comment[num_in]" class="form-control" id="num_in" placeholder="ចំនួនបុគ្គលិក*">
                                </div>
                                <div class="form-group">
                                    <input type="text" required name="comment[comment_in]" class="form-control" id="comment_in" placeholder="មតិយោបល់*">
                                </div>
                            </td>

                            <td>
                                <div class="form-group">
                                    <input type="date" required name="comment[date_out]" class="form-control" id="date_out" placeholder="កាលបរិច្ឆេទ*">
                                </div>
                                <div class="form-group">
                                    <input type="time" required name="comment[time_out]" class="form-control" id="time_out" placeholder="ម៉ោង*">
                                </div>
                                <div class="form-group">
                                    <input type="number" min="0" required name="comment[num_out]" class="form-control" id="num_out" placeholder="ចំនួនបុគ្គលិក*">
                                </div>
                                <div class="form-group">
                                    <input type="text" required name="comment[comment_out]" class="form-control" id="comment_out" placeholder="មតិយោបល់*">
                                </div>
                            </td>

                        </tr>
                    </table>
                    
                    <button type="submit" class="btn btn-success">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>



