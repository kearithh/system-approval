@if(Auth::id() == getCEO()->id)
    
    <!------To Approve List Report--------------------------------------------------------------------------->
    <li class="nav-item has-treeview 
        @if(request()->segment(1) == 'to_approve_report' || request()->segment(1) == 'to_approve_group_support') menu-open @endif">
        <a class="nav-link nav-item 
            @if(request()->segment(1) == 'to_approve_report' || request()->segment(1) == 'to_approve_group_support') active @endif" href="#">
            <i class="fa fa-check "></i>
            <p>
                To Approve (Report)
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right" v-if(total_to_approve_report)  v-cloak>@{{ total_to_approve_report }}</span>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'to_approve_report' && @$_GET['company'] == 'ORD') active @endif" href="/to_approve_report?company=ORD&type=report">
                    <i class="far fa-fw fa-circle "></i>
                    <p>
                        ORD
                        <span class="badge badge-info right" v-if="to_approve_report.ORD" v-cloak>@{{ to_approve_report.ORD }}</span>
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'to_approve_report' && @$_GET['company'] == 'ORD2') active @endif" href="/to_approve_report?company=ORD2&type=report">
                    <i class="far fa-fw fa-circle "></i>
                    <p>
                        ORD-II
                        <span class="badge badge-info right" v-if="to_approve_report.ORD2" v-cloak>@{{ to_approve_report.ORD2 }}</span>
                    </p>
                </a>
            </li>
        </ul>
    </li>
@endif
