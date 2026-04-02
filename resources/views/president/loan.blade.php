@extends('adminlte::page', ['activePage' => 'request', 'titlePage' => __('Request')])

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }
        .table td, .table th {
            padding: .75rem .3rem;
        }
    </style>
@stop

@section('content')
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                  @include('president.partials.navigation')
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-lg-2">
                    @include('president.partials.nav_type')
                </div>
                <div class="col-md-9 col-lg-10">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title "><strong>Loan Approval List</strong></h4>
                        </div>
                        <div class="table-responsive" style="padding: 0 10px">
                            <table class="table table-striped table-hover">
                                <thead class="">
                                    <tr>
                                        <th style="min-width: 50px">ល.រ</th>
                                        <th class="" style="min-width: 100px">សកម្មភាព</th>
                                        <th style="min-width: 100px;">ស្ថានភាព</th>
                                        <th style="min-width: 200px">កាលបរិច្ឆេទស្នើរ</th>
                                        <th style="min-width: 200px">កាលបរិច្ឆេទកែសម្រួល</th>
                                        <th style="min-width: 100px;">ស្នើរដោយ</th>
                                        <th style="min-width: 215px">ពិនិត្យដោយ</th>
                                        <th style="min-width: 245px;">អនុម័តដោយ</th>
                                        <th style="min-width: 245px;">ចម្លងជូន</th>
                                        <th style="min-width: 100px;">ឈ្មោះសាខា</th>
                                        <th style="min-width: 200px;">ឈ្មោះមន្រ្តីឥណទាន</th>
                                        <th style="min-width: 150px;">ឈ្មោះអ្នកខ្ចី</th>
                                        <th style="min-width: 150px;">ឈ្មោះអ្នករួមខ្ចី</th>
                                        <th style="min-width: 100px; text-align: center;">ទំហំឥណទាន(រៀល)</th>
                                        <th style="min-width: 150px; text-align: center;">រយៈពេលខ្ចី <br> (ខែ/សប្តាហ៍)</th>
                                        <th style="min-width: 150px; text-align: center;">អត្រាការប្រាក់(%)</th>
                                        <th style="min-width: 150px; text-align: center;">សេវារដ្ឋបាល(%)</th>

                                        @if(@$_GET['company']=='MFI')
                                            <th style="min-width: 150px; text-align: center;">សេវារៀបចំឥណទាន(%)</th>
                                            <th style="min-width: 150px; text-align: center;">សេវាត្រួតពិនិត្យឥណទាន(%)</th>
                                            <th style="min-width: 150px; text-align: center;">សេវាប្រមូលឥណទាន(%)</th>
                                        @endif

                                        <th style="min-width: 300px;">របៀបសង</th>
                                        <th style="min-width: 200px;">របៀបអនុម័ត</th>
                                        <th style="min-width: 200px;">ប្រភេទឥណទាន</th>
                                        <th style="min-width: 150px">ឯកសារភ្ជាប់</th>
                                        <th style="min-width: 200px;">តំណភ្ជាប់ទីតាំង(Map)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($data->count())
                                        <?php $i = 1; ?>
                                        @foreach($data as $key => $item)
                                            <tr>
                                                <td> {{ $i++  }}</td>
                                                <td class="td-actions">
                                                    @include('global.list_action', ['uri' => 'loan', 'object' => $item])
                                                </td>
                                                <td>{{ request_status($item) }}</td>
                                                <td>{{ created_at($item->created_at) }}</td>
                                                <td>{{ $item->resubmit ? created_at($item->resubmit) : 'N/A' }}</td>
                                                <td>{{ $item->requester_name }}</td>
                                                <td>
                                                    {{ @reviewer_position(\App\Loan::reviewerNames($item->id)) }}
                                                </td>
                                                <td>
                                                    {{ @approver_position(\App\Loan::approverName($item->id)) }}
                                                </td>
                                                <td>
                                                    {{ @reviewer_position(\App\Loan::ccNames($item->id)) }}
                                                </td>
                                                <td>{{ $item->branch_name }}</td>
                                                <td>{{ $item->credit }}</td>
                                                <td>{{ $item->borrower }}</td>
                                                <td>
                                                    <?php
                                                        $part = json_decode($item->participants);
                                                    ?>
                                                        {{ $part ? implode(", ",$part) : 'N/A' }}
                                                </td>
                                                <td class="text-right">{{ number_format($item->money) }}៛</td>
                                                <td class="text-center">
                                                    {{ $item->times }}
                                                    @if($item->type_time == 1)
                                                        ខែ
                                                    @else
                                                        សប្តាហ៍
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->interest }}%</td>
                                                
                                                <td class="text-center">
                                                    {{ $item->service ? @$item->service : 0}}%
                                                </td>

                                                <?php $obj = json_decode(@$item->service_object); ?>
                                                @if(@$_GET['company']=='MFI')
                                                    <td class="text-center">
                                                        {{ @$obj->arrangement ? @$obj->arrangement : 0 }}%
                                                    </td>
                                                    <td class="text-center">
                                                        {{ @$obj->check ? @$obj->check : 0 }}%
                                                    </td>
                                                    <td class="text-center">
                                                        {{ @$obj->collection ? @$obj->collection : 0 }}%
                                                    </td>
                                                @endif

                                                <td>

                                                    @if($item->types == 1) 
                                                        សងការប្រាក់ និងប្រាក់ដើមរាល់ ១សប្តាហ៍ម្តង
                                                    @elseif($item->types == 2) 
                                                        សងការប្រាក់ និងប្រាក់ដើមរាល់ ២សប្តាហ៍ម្តង
                                                    @elseif($item->types == 3) 
                                                        សងការប្រាក់ និងប្រាក់ដើមរាល់ខែ
                                                    @elseif($item->types == 4) 
                                                        សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៤ខែម្តង
                                                    @elseif($item->types == 5) 
                                                        សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៦ខែម្តង
                                                    @elseif($item->types == 6) 
                                                        សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៨ខែម្តង
                                                    @elseif($item->types == 7) 
                                                        សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ១២ខែម្តង
                                                    @elseif($item->types == 8) 
                                                        សងការប្រាក់ និងប្រាក់ដើមរាល់ ៤ខែម្តង
                                                    @elseif($item->types == 9) 
                                                        សងការប្រាក់ និងប្រាក់ដើមរាល់ ៦ខែម្តង
                                                    @elseif($item->types == 10) 
                                                        សងការប្រាក់ និងប្រាក់ដើមរាល់ ៨ខែម្តង
                                                    @elseif($item->types == 11) 
                                                        សងការប្រាក់ និងប្រាក់ដើមរាល់ ១២ខែម្តង
                                                    @endif

                                                </td>
                                                <td>
                                                    @if($item->principle == 1)
                                                        អនុម័តតាមគោលការណ៍
                                                    @else($item->principle == 0)
                                                        អនុម័តខុសគោលការណ៍
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->type_loan == 1)
                                                        ឥណទានថ្មី
                                                    @elseif ($item->type_loan == 3)
                                                        ឥណទានចាស់
                                                    @elseif ($item->type_loan == 2)
                                                        ឥណទានរៀបចំឡើងវិញ
                                                    @elseif ($item->type_loan == 4)
                                                        ឥណទានរៀបចំឡើងវិញលើកទី១
                                                    @elseif ($item->type_loan == 5)
                                                        ឥណទានរៀបចំឡើងវិញលើកទី២
                                                    @elseif ($item->type_loan == 6)
                                                        ឥណទានរៀបចំឡើងវិញលើកទី៣
                                                    @elseif ($item->type_loan == 7)
                                                        ឥណទានរៀបចំឡើងវិញលើកទី៤
                                                    @elseif ($item->type_loan == 8)
                                                        ឥណទានរៀបចំឡើងវិញលើកទី៥
                                                    @elseif ($item->type_loan == 9)
                                                        ឥណទានរៀបចំឡើងវិញលើកទី៦
                                                    @elseif ($item->type_loan == 10)
                                                        ឥណទានរៀបចំឡើងវិញលើកទី៧
                                                    @elseif ($item->type_loan == 11)
                                                        ឥណទានរៀបចំឡើងវិញលើកទី៨
                                                    @elseif ($item->type_loan == 12)
                                                        ឥណទានរៀបចំឡើងវិញលើកទី៩
                                                    @elseif ($item->type_loan == 13)
                                                        ឥណទានរៀបចំឡើងវិញលើកទី១០
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->attachment)
                                                        <?php $atts = is_array($item->attachment) ? $item->attachment : json_decode($item->attachment); ?>
                                                        @foreach($atts as $att )
                                                            <a href="{{ asset($att->src) }}" target="_self">{{ $att->org_name }}</a><br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td class="text-left">
                                                    <?php 
                                                        $gps_object = @json_decode(@$item->gps_object) ?: []; 
                                                    ?>
                                                    @forelse(@$gps_object as $key => $item)
                                                        <a href="{{ $item->link }}">{{ $key + 1 }}. {{ $item->name }}</a><br>
                                                    @empty
                                                        N/A
                                                    @endforelse
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @if(@$_GET['company']=='MFI')
                                            <tr>
                                                <td colspan="25">Record Not Found!</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="22">Record Not Found!</td>
                                            </tr>
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- {!! $data->render() !!} -->
                    {{ $data->appends($_GET)->links() }}
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" id="token" value="{{ csrf_token() }}">
@endsection

@section('js')
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {

            $(".preview").on('click', function () {
                localStorage.previous = window.location.href ;
            });

            var token = $("#token").val();
            $('.sidebar-mini').addClass("sidebar-collapse");

            $(".btn-delete").on('click', function () {
                var id = $(this).data('item-id');
                var formAction = $(this).attr('action');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: formAction,
                            method:"post",
                            type: "json",
                            data: {
                                "id": id,
                                "_token": token,
                            },
                            success:function (data) {
                                if (data.success == 1) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                    );
                                    window.location.reload();
                                } else {
                                    console.log(data.msg);
                                    Swal.fire(
                                        {
                                            title: 'Delete failed !',
                                            text: "Please refresh your browser!",
                                            icon: 'warning',
                                        }
                                    )
                                }
                            },
                            fail:function (err) {
                                console.log(err);
                            }
                        });
                    }
                })
            })
        });
    </script>
@endsection
