<!-- Button trigger modal -->
{{--<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#comment_modal">--}}
{{--    Launch demo modal--}}
{{--</button>--}}

<!-- Modal -->
<div class="modal fade" id="comment_modal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="comment_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>ផ្តល់យោបល់ឲ្យកែប្រែ និងធ្វើម្តងទៀត</b></h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ $route }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputEmail1">Comment <span class="text-danger">*</span></label>
                        <textarea required name="comment" class="form-control" id="comment" placeholder="Comment"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputFile">File input</label>
                        <input type="file" id="file" name="file">
                    </div>
                    <div class="form-group" style="text-align: right;">
                        <button type="submit" class="btn btn-success btn-sm">Submit</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{--$('#comment_modal').modal('show'); --}}


