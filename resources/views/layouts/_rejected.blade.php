<li class="nav-item has-treeview  @if(request()->segment(1) == 'reject') menu-open @endif">
    <a class="nav-link nav-item @if(request()->segment(1) == 'reject') active @endif" href="#">
        <i class="fa fa-times "></i>
        <p>
            Commented List
            <i class="fas fa-angle-left right"></i>
            <span class="badge badge-danger right" v-if="total_rejected" v-cloak>@{{ total_rejected }}</span>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class=" nav-item ">
            <a class="nav-link @if(request()->segment(1) == 'reject' && @$_GET['company'] == 'ORD') active @endif" href="/reject?company=ORD">
                <i class="far fa-fw fa-circle "></i>
                <p>
                    ORD
                    <span class="badge badge-danger right" v-if="rejected.ORD" v-cloak>@{{ rejected.ORD }}</span>
                </p>
            </a>
        </li>
        <li class=" nav-item ">
            <a class="nav-link @if(request()->segment(1) == 'reject' && @$_GET['company'] == 'ORD2') active @endif" href="/reject?company=ORD2">
                <i class="far fa-fw fa-circle "></i>
                <p>
                    ORD-II
                    <span class="badge badge-danger right" v-if="rejected.ORD2" v-cloak>@{{ rejected.ORD2 }}</span>
                </p>
            </a>
        </li>
    </ul>
</li>
