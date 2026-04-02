<?php
    $label = '';
    if(basename(Request::url())=='toapprove') {
        $label = 'badge-info';
    }elseif(basename(Request::url())=='reject') {
        $label = 'badge-danger';
    }elseif(basename(Request::url())=='disable') {
        $label = 'badge-secondary';
    }elseif(basename(Request::url())=='approved') {
        $label = 'badge-success';
    } elseif(basename(Request::url())=='pending'){
        $label = 'badge-warning';
    }
?>
<div class="card card-danger request-type">
    <div class="card-header">
        <h3 class="card-title"><strong>Type Requests</strong></h3>
    </div>
    <div class="card-body p-0">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='Memo') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=Memo'}}"
                >
                    <i class="fas fa-gavel"></i> Memo
                    @if(@$memo > 0)
                        <span class="badge {{ $label }} right">{{ @$memo }}</span>
                    @endif
                    <span v-if="requestType.memo" class="badge {{ $label }} right" v-cloak>@{{ requestType.memo }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='CustomLetter') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=CustomLetter'}}"
                >
                    <i class="fas fa-file-alt"></i> Custom Letter
                    @if(@$custom_letter > 0)
                        <span class="badge {{ $label }} right">{{ @$custom_letter }}</span>
                    @endif
                    <span v-if="requestType.custom_letter" class="badge {{ $label }} right" v-cloak>@{{ requestType.custom_letter }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='Letter') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=Letter'}}"
                >
                    <i class="fas fa-file"></i> Letter
                    @if(@$hr_request > 0)
                        <span class="badge {{ $label }} right">{{ @$hr_request }}</span>
                    @endif
                    <span v-if="requestType.hr_request" class="badge {{ $label }} right" v-cloak>@{{ requestType.hr_request }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='Resign') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=Resign'}}"
                >
                    <i class="fas fa-envelope"></i> Resign Letter
                    @if(@$resign > 0)
                        <span class="badge {{ $label }} right">{{ @$resign }}</span>
                    @endif
                    <span v-if="requestType.resign" class="badge {{ $label }} right" v-cloak>@{{ requestType.resign }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='RequestLastDay') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=RequestLastDay'}}"
                >
                    <i class="far fa-calendar-check"></i> Request Last Day
                    @if(@$resign_last_day > 0)
                        <span class="badge {{ $label }} right">{{ @$resign_last_day }}</span>
                    @endif
                    <span v-if="requestType.resign_last_day" class="badge {{ $label }} right" v-cloak>@{{ requestType.resign_last_day }}</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='Special') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=Special'}}"
                >
                    <i class="fas fa-star"></i> Special Expense
                    @if(@$special > 0)
                        <span class="badge {{ $label }} right">{{ @$special }}</span>
                    @endif
                    <span v-if="requestType.special" class="badge {{ $label }} right" v-cloak>@{{ requestType.special }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='GRN') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=GRN'}}"
                >
                    <i class="fas fa-star"></i> GRN
                    @if(@$grn > 0)
                        <span class="badge {{ $label }} right">{{ @$grn }}</span>
                    @endif
                    <span v-if="requestType.grn" class="badge {{ $label }} right" v-cloak>@{{ requestType.grn }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='Po_Request') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=Po_Request'}}"
                >
                    <i class="fas fa-star"></i> PO Request
                    @if(@$po_request > 0)
                        <span class="badge {{ $label }} right">{{ @$po_request }}</span>
                    @endif
                    <span v-if="requestType.po_request" class="badge {{ $label }} right" v-cloak>@{{ requestType.po_request }}</span>
                </a>
            </li>

           <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='Pr_Request') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=Pr_Request'}}"
                >
                    <i class="fas fa-star"></i> PR Request
                    @if(@$pr_request > 0)
                        <span class="badge {{ $label }} right">{{ @$pr_request }}</span>
                    @endif
                    <span v-if="requestType.pr_request" class="badge {{ $label }} right" v-cloak>@{{ requestType.pr_request }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='General') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=General'}}"
                >
                    <i class="fas fa-money-check-alt"></i> General Expense
                    @if(@$general > 0)
                        <span class="badge {{ $label }} right">{{ @$general }}</span>
                    @endif
                    <span v-if="requestType.general" class="badge {{ $label }} right" v-cloak>@{{ requestType.general }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='DamagedLog') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=DamagedLog'}}"
                >
                    <i class="fas fa-exclamation-circle"></i> Damaged Asset
                    @if(@$damagedlog > 0)
                        <span class="badge {{ $label }} right">{{ @$damagedlog }}</span>
                    @endif
                    <span v-if="requestType.damagedlog" class="badge {{ $label }} right" v-cloak>@{{ requestType.damagedlog }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='Disposal') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=Disposal'}}"
                >
                    <i class="fas fa-minus-circle"></i> Disposal Asset
                    @if(@$disposal > 0)
                        <span class="badge {{ $label }} right">{{ @$disposal }}</span>
                    @endif
                    <span v-if="requestType.disposal" class="badge {{ $label }} right" v-cloak>@{{ requestType.disposal }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='TransferAsset') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=TransferAsset'}}"
                >
                    <i class="fas fa-random"></i> Transfer Asset
                    @if(@$transfer_asset > 0)
                        <span class="badge {{ $label }} right">{{ @$transfer_asset }}</span>
                    @endif
                    <span v-if="requestType.transfer_asset" class="badge {{ $label }} right" v-cloak>@{{ requestType.transfer_asset }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='SaleAsset') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=SaleAsset'}}"
                >
                    <i class="fas fa-file-invoice-dollar"></i> Sale Asset
                    @if(@$sale_asset > 0)
                        <span class="badge {{ $label }} right">{{ @$sale_asset }}</span>
                    @endif
                    <span v-if="requestType.sale_asset" class="badge {{ $label }} right" v-cloak>@{{ requestType.sale_asset }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='CashAdvance') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=CashAdvance'}}"
                >
                    <i class="fas fa-money-bill-alt"></i> Cash Advance
                    @if(@$cash_advance > 0)
                        <span class="badge {{ $label }} right">{{ @$cash_advance }}</span>
                    @endif
                    <span v-if="requestType.cash_advance" class="badge {{ $label }} right" v-cloak>@{{ requestType.cash_advance }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='RequestOT') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=RequestOT'}}"
                >
                    <i class="fas fa-clipboard"></i> Request OT
                    @if(@$request_ot > 0)
                        <span class="badge {{ $label }} right">{{ @$request_ot }}</span>
                    @endif
                    <span v-if="requestType.request_ot" class="badge {{ $label }} right" v-cloak>@{{ requestType.request_ot }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='Training') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=Training'}}"
                >
                    <i class="fas fa-radiation-alt"></i> Training
                    @if(@$training > 0)
                        <span class="badge {{ $label }} right">{{ @$training }}</span>
                    @endif
                    <span v-if="requestType.training" class="badge {{ $label }} right" v-cloak>@{{ requestType.training }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='RequestUser') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=RequestUser'}}"
                >
                    <i class="fas fa-user-plus"></i> Request User
                    @if(@$request_user > 0)
                        <span class="badge {{ $label }} right">{{ @$request_user }}</span>
                    @endif
                    <span v-if="requestType.request_user" class="badge {{ $label }} right" v-cloak>@{{ requestType.request_user }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='RequestDisableUser') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=RequestDisableUser'}}"
                >
                    <i class="fas fa-user-lock"></i> Request Disable User
                    @if(@$request_disable_user > 0)
                        <span class="badge {{ $label }} right">{{ @$request_disable_user }}</span>
                    @endif
                    <span v-if="requestType.request_disable_user" class="badge {{ $label }} right" v-cloak>@{{ requestType.request_disable_user }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='Setting') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=Setting'}}"
                >
                    <i class="fas fa-cogs"></i> Setting Reviewer & Approver
                    @if(@$setting > 0)
                        <span class="badge {{ $label }} right">{{ @$setting }}</span>
                    @endif
                    <span v-if="requestType.setting" class="badge {{ $label }} right" v-cloak>@{{ requestType.setting }}</span>
                </a>
            </li>

            <li class="nav-item" @if (getCEO()->id == auth()->id() && request()->segment(1) == 'toapprove') hidden @endif>
                <a class="nav-link"
                   @if(@$_GET['type']=='report') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=report'}}"
                >
                    <i class="fa fa-bars"></i> Report
                    @if(@$viewShare['total_report_by_company'] > 0)
                        <span class="badge {{ @$viewShare['label'] }} right"> {{ @$viewShare['total_report_by_company'] }}</span>
                    @endif
                    <span v-if="requestType.report" class="badge {{ $label }} right" v-cloak>@{{ requestType.report }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   @if(@$_GET['type']=='Policy') style="color: #007bff;" @endif
                   href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=Policy'}}"
                >
                    <i class="fas fa-book"></i> SOP/Policy
                    @if(@$policy > 0)
                        <span class="badge {{ $label }} right">{{ @$policy }}</span>
                    @endif
                    <span v-if="requestType.policy" class="badge {{ $label }} right" v-cloak>@{{ requestType.policy }}</span>
                </a>
            </li>

        </ul>
    </div>
</div>
