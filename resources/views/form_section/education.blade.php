<form id="form_expense">
    <br>

    <div class="col-sm-12">
        <div id="sections">
            <div class="section border clearfix btn-secondary" >
                <p>
                    <a href="#" class="btn btn-xs btn-danger remove float-right">
                        <i class="fa fa-trash"></i>
                    </a>
                </p>
                <?= Form::text([
                    "label" => "ឈ្មោះ",
                    "name" => "penalty[name]",
                    "value" => old('name'),
                ]); ?>

                <?= Form::number([
                    "label" => "សរុប($)",
                    "name" => "penalty[amount]",
                    "value" => old('amount'),
                ]); ?>

                <?= Form::textarea([
                    "label" => "ហេតុផល",
                    "name" => "penalty[reason]",
                    "value" => old('reason'),
                ]); ?>

            </div>
        </div>
    </div>
    <a href="#" class="addsection btn btn-xs btn-success">
        <i class="fa fa-plus"></i>
        Add more
    </a>
</form>

