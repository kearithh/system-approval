<li class="nav-item has-treeview  @if(request()->segment(1) == 'disable') menu-open @endif">
    <a class="nav-link nav-item @if(request()->segment(1) == 'disable') active @endif" href="#">
        <i class="fa fa-ban "></i>
        <p>
            Rejected List
            <i class="fas fa-angle-left right"></i>
            <span class="badge badge-secondary right" v-if="total_disabled" v-cloak>@{{ total_disabled }}</span>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class=" nav-item ">
            <a class="nav-link @if(request()->segment(1) == 'disable' && @$_GET['company'] == 'ORD') active @endif" href="/disable?company=ORD">
                <i class="far fa-fw fa-circle "></i>
                <p>
                    ORD
                    <span class="badge badge-secondary right" v-if="disabled.ORD" v-cloak>@{{ disabled.ORD }}</span>
                </p>
            </a>
        </li>
        <li class=" nav-item ">
            <a class="nav-link @if(request()->segment(1) == 'disable' && @$_GET['company'] == 'ORD2') active @endif" href="/disable?company=ORD2">
                <i class="far fa-fw fa-circle "></i>
                <p>
                    ORD-II
                    <span class="badge badge-secondary right" v-if="disabled.ORD2" v-cloak>@{{ disabled.ORD2 }}</span>
                </p>
            </a>
        </li>
    </ul>
</li>
