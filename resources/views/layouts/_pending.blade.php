@if (auth()->id() != getCEO()->id)
    <li class="nav-item has-treeview @if(request()->segment(1) == 'pending') menu-open @endif">
        <a class="nav-link nav-item @if(request()->segment(1) == 'pending') active @endif" href="#">
            <i class="fa fa-bars "></i>
            <p>
                Pending List
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-warning right" v-if="total_pending" v-cloak>@{{ total_pending }}</span>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'pending' && @$_GET['company'] == 'ORD') active @endif" href="/pending?company=ORD">
                    <i class="far fa-fw fa-circle "></i>
                    <p>
                        ORD
                        <span class="badge badge-warning right" v-if="(pending.ORD)" v-cloak>@{{ pending.ORD }}</span>
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'pending' && @$_GET['company'] == 'ORD2') active @endif" href="/pending?company=ORD2">
                    <i class="far fa-fw fa-circle "></i>
                    <p>
                        ORD-II
                        <span class="badge badge-warning right" v-if="(pending.ORD2)" v-cloak>@{{ pending.ORD2 }}</span>
                    </p>
                </a>
            </li>
        </ul>
    </li>
@endif
