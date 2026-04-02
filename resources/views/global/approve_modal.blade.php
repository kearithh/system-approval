
<!-- Modal -->
<div class="modal fade" id="approve_modal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="approve_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>ការអនុម័តទៅលើសំណើ</b></h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ $route_approve }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputEmail1">ការផ្តល់អនុសាសន៍</label>
                        <textarea name="comment" class="form-control" id="comment" placeholder="អនុសាសន៍របស់គណៈកម្មការ">{{ @$comment }}</textarea>
                    </div>
                    <div class="form-group" style="text-align: right;">
                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>