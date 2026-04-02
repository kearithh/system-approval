<li class="nav-item has-treeview  @if(request()->segment(1) == 'approved') menu-open @endif">
    <a class="nav-link nav-item @if(request()->segment(1) == 'approved') active @endif" href="#">
        <i class="fa fa-check-circle "></i>
        <p>
            Approved List
            <i class="fas fa-angle-left right"></i>
            <span class="badge badge-success  right" v-if="total_approved" v-cloak>@{{ total_approved }}</span>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class=" nav-item ">
            <a class="nav-link @if(request()->segment(1) == 'approved' && @$_GET['company'] == 'ORD') active @endif" href="/approved?company=ORD">
                <i class="far fa-fw fa-circle "></i>
                <p>
                    ORD
                    <span class="badge badge-success right" v-if="(approved.ORD)" v-cloak>@{{ approved.ORD }}</span>
                </p>
            </a>
        </li>
        <li class=" nav-item ">
            <a class="nav-link @if(request()->segment(1) == 'approved' && @$_GET['company'] == 'ORD2') active @endif" href="/approved?company=ORD2">
                <i class="far fa-fw fa-circle "></i>
                <p>
                    ORD-II
                    <span class="badge badge-success right" v-if="(approved.ORD2)" v-cloak>@{{ approved.ORD2 }}</span>
                </p>
            </a>
        </li>
    </ul>
</li>
