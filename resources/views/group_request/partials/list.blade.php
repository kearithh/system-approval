@if (@$data)
    <div class="card">
        <div class="card-header card-header-primary">
            <i class="fa fa-bars"></i>
            {{ __('បញ្ជី Upload របាយការណ៍') }}
            <div class="card-tools">
                <button type="button" class="btn btn-sm" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-sm" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="table-responsive" style="padding: 0 10px">
            <table class="table table-striped">
                <thead class="">
                <th style="min-width: 50px">ល.រ</th>
                <th style="min-width: 500px;">{{ __('ឈ្មោះរបាយការណ៍') }}</th>
                <th style="min-width: 115px; text-align: center;">{{ __('សកម្ម') }}</th>

                </thead>
                <tbody>
                <?php $i = 1; ?>
                @foreach($data as $key => $item)
                    <tr title="Template id: {{ @$item->id }}">
                        <td class="text-center"> {{ $i++ }}</td>
                        <td>
                            @if($item->status == 2)
                                <a href="" style="color: inherit">
                                    <p class="mb-0" style="white-space: nowrap">{{ @$item->name }}</p>
                                </a>
                                <p class="mb-0  small-label">
                                    Status: 
                                    <button 
                                        class="btn btn-xs bg-success" 
                                        style="line-height: 1; padding: 0px; font-size: 11px;" 
                                        title="done" type="button">
                                        &nbsp; done &nbsp;&nbsp;
                                    </button>&emsp;&emsp;
                                    Creator: 
                                    <span class="text-primary">{{ $item->user_name }}</span>&emsp;&emsp;
                                    Approve By: 
                                    <span class="text-success">{{ @$item->approver->name }}</span>
                                </p>
                            @else
                                <p class="mb-0" style="white-space: nowrap">{{ @$item->name }}</p>
                                <p class="mb-0 small-label">
                                    Status: 
                                    <button 
                                        class="btn btn-xs bg-secondary" 
                                        style="line-height: 1; padding: 0px; font-size: 11px;" 
                                        title="draft" 
                                        type="button">
                                        &nbsp; draft &nbsp;
                                    </button>&emsp;&emsp;
                                    Creator: 
                                    <span class="text-primary">{{ $item->user_name }}</span>
                                    &emsp;&emsp;
                                    <!-- Approve By: 
                                    <span class="text-success">{{ @$item->approver->name }}</span> -->
                                    &emsp;&emsp;
                                    Can Upload: 
                                    <span class="text-success">All Staff</span>
                                    <span style="width: 70px; float:right;">{{ type(@$item->tags) }}</span>
                                    <span style="float: right">Type:</span>
                                </p>
                            @endif
                        </td>
                        <td class="td-actions text-center" style="vertical-align: middle">
                            <button
                                class="btn btn-xs btn-success upload-file"
                                title="Upload attachment"
                                data-id="{{ $item->id }}"
                            >
                                <i class="fa fa-upload"></i>
                            </button>

                            <?php $can_delete = json_decode(Auth()->user()->manage_template_report); ?>

                            @if(Auth()->id() == @$item->user_id)

                                @if( $can_delete && in_array(@$item->company_id, $can_delete) )   
                                    <button
                                        class="btn btn-xs btn-info edit-template"
                                        title="Edit template"
                                        data-id="{{ $item->id }}"
                                    >
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <button
                                        class="btn btn-xs btn-danger delete-template"
                                        title="Delete template"
                                        data-id="{{ $item->id }}"
                                        action="/group_request/template/{{ $item->id }}/delete"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @else
                                    <button
                                        class="btn btn-xs btn-info edit-template"
                                        title="Edit template"
                                        data-id="{{ $item->id }}"
                                    >
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <button
                                        disabled
                                        class="btn btn-xs btn-danger "
                                        title="Delete template"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endif

                            @else
                                    
                                @if( $can_delete && in_array(@$item->company_id, $can_delete) )  
                                    <button
                                        class="btn btn-xs btn-info edit-template"
                                        title="Edit template"
                                        data-id="{{ $item->id }}"
                                    >
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <button
                                        class="btn btn-xs btn-danger delete-template"
                                        title="Delete template"
                                        data-id="{{ $item->id }}"
                                        action="/group_request/template/{{ $item->id }}/delete"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @else
                                    <button 
                                        disabled 
                                        class="btn btn-xs btn-info"
                                        title="Edit template"
                                    >
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <button
                                        disabled
                                        class="btn btn-xs btn-danger "
                                        title="Delete template"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </b>
                                @endif

                            @endif

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{--                {!! $data->render() !!}--}}

@else
    <div class="card">
        <div class="card-header card-header-primary">
            <h4 class="card-title ">{{ __('Please Select Type') }}</h4>
        </div>
        <div class="table-responsive" style="padding: 0 10px">
        </div>
    </div>
@endif
