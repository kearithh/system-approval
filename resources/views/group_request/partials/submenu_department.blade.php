
{{--{{ dd($viewShare['company_departments']) }}--}}
<div class="card card-danger">
    <div class="card-header">
        <h3 class="card-title"><strong>Departments</strong></h3>
    </div>
    <div class="card-body p-0">
        <ul class="nav nav-pills flex-column">

{{--        @if (@$viewShare['company_departments'])--}}
{{--            @foreach($viewShare['company_departments'] as $item)--}}
{{--                <li class="nav-item">--}}
{{--                    <a--}}
{{--                        class="nav-link @if(@$_GET['department'] == $item->short_name) text-primary @endif"--}}
{{--                        href="{{ URL::current().'?'.appendQueryString('department', $item->short_name)}}">--}}
{{--                        <i class="fas fa-bars"></i>&nbsp;--}}
{{--                        {{ str_replace('Department', '', $item->name_en) }}--}}
{{--                        @if(@$item->total)--}}
{{--                            <span class="badge {{ @$viewShare['label'] }} right">{{ $item->total }}</span>--}}
{{--                        @endif--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @endforeach--}}
{{--        @endif--}}

            <li class="nav-item" v-for="cd in company_departments">
                <a v-if="cd.active === 1" class="nav-link text-primary" :href="departmentLink">
                    <i class="fas fa-bars"></i>&nbsp; @{{ cd.name_en }}
                    <span v-if="cd.total" class="badge {{ @$viewShare['label'] }} right">@{{ cd.total }}</span>
                </a>

                <a v-else="cd.active === 0" class="nav-link" :href="cd.link">
                    <i class="fas fa-bars"></i>&nbsp; @{{ cd.name_en }}
                    <span v-if="cd.total" class="badge {{ @$viewShare['label'] }} right">@{{ cd.total }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>
