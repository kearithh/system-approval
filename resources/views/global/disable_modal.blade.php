
<form method="POST" action="{{ $route_disable }}" enctype="multipart/form-data">
    @csrf
    <!-- Modal -->
    <div class="modal fade" id="disable_modal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="disable_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>បដិសេធសំណើ</b></h5>
                </div>
                <div class="modal-body">
                    <p style="color: red">*ចំណាំ៖ នៅពេលបដិសេធ អ្នកបង្កើតសំណើនឹងមិនអាចកែប្រែ ឬធ្វើសំណើនោះឡើងវិញបានទេ!!!</p>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Comment of reject <span class="text-danger">*</span></label>
                        <textarea required name="comment" class="form-control" id="comment" placeholder="Comment"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputFile">File input</label>
                        <input type="file" id="file" name="file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Submit</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</form>