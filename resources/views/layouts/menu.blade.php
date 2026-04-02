{{--{{ dd($viewShare) }}--}}
<nav class="mt-2" id="main_menu">
    <ul class="nav nav-pills nav-sidebar flex-column {{config('adminlte.classes_sidebar_nav', '')}}" data-widget="treeview" role="menu" @if(config('adminlte.sidebar_nav_animation_speed') != 300) data-animation-speed="{{config('adminlte.sidebar_nav_animation_speed')}}" @endif @if(!config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif>
        <li class=" nav-item ">
            <a class="nav-link @if(request()->segment(1) == 'dashboard') active @endif " href="{{ route('dashboard') }}">
                <i class="fas fa-fw fa-home"></i>
                <p> Dashboard </p>
            </a>
        </li>
        <!------Create ------------------------------------------------------------------------------------>
        @include('layouts._create')

        <!------Pending List ------------------------------------------------------------------------------>
        @include('layouts._pending')

        <!------To Approve List --------------------------------------------------------------------------->
        @include('layouts._to_approve')

        <!------To Approve List Report--------------------------------------------------------------------------->
        @include('layouts._to_approve_report')

        <!------Comment List ----------------------------------------------------------------------------->
        @include('layouts._rejected')

        <!------Rejected List ----------------------------------------------------------------------------->
        @include('layouts._disabled')

        <!------Approved List ----------------------------------------------------------------------------->
        @include('layouts._approved')

        <!------Summary Report ----------------------------------------------------------------------------->
        @include('layouts._summary_report')

        <!------Setting ----------------------------------------------------------------------------->
        @include('layouts._setting')

        <li class=" nav-item">
            <a class="nav-link @if(request()->segment(1) == 'public_memo') active @endif " href="{{ route('public_memo') }}">
                <i class="fas fa-gavel"></i>
                <p> View Memo </p>
            </a>
        </li>

        <li class=" nav-item">
            <a class="nav-link @if(request()->segment(1) == 'public_policy') active @endif " href="{{ route('public_policy') }}">
                <i class="fas fa-book"></i>
                <p> View Policy / SOP </p>
            </a>
        </li>

        <li class=" nav-item">
            <a class="nav-link @if(request()->segment(1) == 'public_lesson') active @endif " href="{{ route('public_lesson') }}">
                <i class="fas fa-book-open"></i>
                <p> View Lesson </p>
            </a>
        </li>

        <li class=" nav-item ">
            <a class="nav-link @if(request()->segment(1) == 'setting-reviewer-approver') active @endif" href="/setting-reviewer-approver">
                <i class="fas fa-fw fa-cogs "></i>
                <p>
                    Manage Approver
                </p>
            </a>
        </li>

        <li class=" nav-item">
            <a class="nav-link @if(request()->segment(1) == 'user-guide') active @endif " href="{{ route('user.guide') }}">
                <i class="fas fa-book"></i>
                <p> របៀបប្រើប្រាស់ប្រព័ន្ធ </p>
            </a>
        </li>
        @include('layouts._contract_manament')
        <li class=" nav-item">
            <a class="nav-link @if(request()->segment(1) == 'task-dateline-tracking') active @endif " href="{{ route('task-dateline-tracking') }}">
                <i class="fas fa-book"></i>
                <p>Task Dateline Tracking </p>
            </a>
        </li>

    </ul>
</nav>
