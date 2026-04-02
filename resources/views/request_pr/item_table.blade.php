<div class="table-responsive">
    <table class="table table-hover">
        <thead class="card-header ">
        <th style="width: 70px">សកម្មភាព</th>
        <th>ឈ្មោះ</th>
        <th>បរិយាយ</th>
        <th style="min-width: 50px">បរិមាណ</th>
        <th style="min-width: 110px">តម្លៃរាយ($)</th>
        <th style="min-width: 70px">VAT</th>
        <th style="min-width: 100px">សរុប($)</th>
        <th style="max-width: 100px">ផ្សេងៗ</th>
        </thead>
        <tbody>
        <?php $total = 0; ?>
        @foreach($requestPR->items as $item)
            <tr>
                <td class="td-actions">
                    <form id="item_form" action="{{ route('request_items_pr.destroy', $item->id) }}" method="POST">
                        @csrf
                        <button
                                form="item_form"
                                type="button"
                                class="btn btn-danger btn-xs"
                                data-original-title=""
                                title=""
                                onclick="confirm('{{ __("Are you sure?") }}') ? $('#item_form').submit() : alert(23)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
                <td>
                    {{ $item->name }}
                </td>
                <td>
                    {{ $item->desc }}
                </td>
                <td>
                    {{ $item->qty }}
                </td>
                {{-- <td>
                    $ {{ number_format($item->unit_price, 2) }}
                </td>
                <td>
                    {{ $item->vat }}%
                </td>
                <td>
                    $ {{ number_format($item->qty + $item->unit_price, 2) }}
                </td> --}}
               <td>
                   {{ $item->remark }}
               </td>
            </tr>
            <?php $total += $item->qty + $item->qty ?>
        @endforeach

        <tr>
            <td colspan="6" class="text-right">សរុប</td>
            <td>
                {{-- <strong> $ {{ number_format($total, 2) }}</strong> --}}
            </td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>
