<li class="nav-item has-treeview  @if(request()->segment(1) == 'toapprove') menu-open @endif">
    <a class="nav-link nav-item @if(request()->segment(1) == 'toapprove') active @endif" href="#">
        <i class="fa fa-check "></i>
        <p>
            To Approve List
            <i class="fas fa-angle-left right"></i>
            <span class="badge badge-info  right" v-if="total_to_approve" v-cloak>@{{ total_to_approve }}</span>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class=" nav-item ">
            <a class="nav-link @if(request()->segment(1) == 'toapprove' && @$_GET['company'] == 'ORD') active @endif" href="/toapprove?company=ORD">
                <i class="far fa-fw fa-circle "></i>
                <p>
                    ORD
                    <span class="badge badge-info right" v-if="to_approve.ORD" v-cloak>@{{ to_approve.ORD }}</span>
                </p>
            </a>
        </li>
        <li class=" nav-item ">
            <a class="nav-link @if(request()->segment(1) == 'toapprove' && @$_GET['company'] == 'ORD2') active @endif" href="/toapprove?company=ORD2">
                <i class="far fa-fw fa-circle "></i>
                <p>
                    ORD-II
                    <span class="badge badge-info right" v-if="to_approve.ORD2" v-cloak>@{{ to_approve.ORD2 }}</span>
                </p>
            </a>
        </li>
    </ul>
</li>
