<div class="card card-danger">
    <div class="card-header">
        <h3 class="card-title"><strong>Tags</strong></h3>
    </div>
    <div class="card-body p-0">
        <ul class="nav nav-pills flex-column">
{{--            @if (@$viewShare['setting_tags'])--}}
{{--                @foreach($viewShare['setting_tags'] as $item)--}}
{{--                    <li class="nav-item">--}}
{{--                        <a--}}
{{--                            class="nav-link @if(@$_GET['tags'] == $item->name) text-primary @endif"--}}
{{--                            href="{{ URL::current().'?'.appendQueryString('tags', $item->name)}}">--}}
{{--                            <i class="fas fa-bars"></i>&nbsp;--}}
{{--                            {{ $item->name }}--}}
{{--                            @if($item->total)--}}
{{--                                    <span class="badge {{ @$viewShare['label'] }} right">{{ $item->total }}</span>--}}
{{--                            @endif--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                @endforeach--}}
{{--            @endif--}}

                <li v-for="t in tags" class="nav-item">
                    <a
                        :class="{ 'text-primary' : t.active == 1 }"
                        class="nav-link"
                        :href="t.link">
                        <i class="fas fa-bars"></i>&nbsp;
                        @{{ t.name }}
                        <span v-if="t.total" class="badge {{ @$viewShare['label'] }} right" v-cloak>@{{ t.total }}</span>
                    </a>
                </li>
        </ul>
    </div>
</div>
