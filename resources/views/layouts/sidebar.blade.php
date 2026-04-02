<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="{{ asset(config('adminlte.logo_img', 'vendor/adminlte/dist/img/AdminLTELogo.png')) }}" alt="{{config('adminlte.logo_img_alt', 'AdminLTE')}}" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light {{ config('adminlte.classes_brand_text') }}">
                        {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
                    </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->

                <li class="nav-item has-treeview menu-open">
                    <a href="#" class="nav-link @if(request()->segment(3) == 'create') active @endif">
                        <i class="fas fa-plus"></i>
                        <p>
                            Create
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('re.item.create') }}" class="nav-link @if(request()->segment(2) == 'item') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Item</p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('re.department.create') }}" class="nav-link @if(request()->segment(2) == 'department') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Department</p>
                            </a>
                        </li> --}}
                    </ul>
                </li>
                <?php $companyShortName = ['STSK', 'MFI', 'NGO', 'ORD', 'ST', 'MMI', 'MHT', 'TSP'] ?>
                <li class="nav-item has-treeview @if(request()->segment(1) == 'group_request' && in_array(request()->segment(2), $companyShortName)) menu-open @endif">
                    <a href="#" class="nav-link @if (request()->segment(1) == 'group_request' && request()->segment(3) == 'index' ) active @endif">
                        <i class="fa fa-bars"></i>
                        <p>
                            Company
                            <i class="fas fa-angle-left right"></i>

                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/{{ request()->segment(1) }}/STSK/index" class="nav-link @if(request()->segment(2) == 'STSK') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>STSK</p>
                                <span class="badge badge-danger right">1</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/{{ request()->segment(1) }}/MFI/index" class="nav-link @if(request()->segment(2) == 'MFI') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>MFI</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/{{ request()->segment(1) }}/NGO/index" class="nav-link @if(request()->segment(2) == 'NGO') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>NGO</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/{{ request()->segment(1) }}/ORD/index" class="nav-link @if(request()->segment(2) == 'ORD') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>ORD</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/{{ request()->segment(1) }}/ST/index" class="nav-link @if(request()->segment(2) == 'ST') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>ST</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/{{ request()->segment(1) }}/MMI/index" class="nav-link @if(request()->segment(2) == 'MMI') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>MMI</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/{{ request()->segment(1) }}/MHT/index" class="nav-link @if(request()->segment(2) == 'MHT') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>MHT</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/{{ request()->segment(1) }}/TSP/index" class="nav-link @if(request()->segment(2) == 'TSP') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>TSP</p>
                            </a>
                        </li>
                    </ul>
                    <li class="nav-item has-treeview menu-open">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-fw fa-paper-plane"></i>
                        <p>
                            Request Form
                        </p>
                    </a>
                </li>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
