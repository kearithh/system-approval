<?php
    $label = '';
    if(basename(Request::url())=='toapprove') {
        $label = 'badge-info';
    }elseif(basename(Request::url())=='reject') {
        $label = 'badge-danger';
    }elseif(basename(Request::url())=='approved') {
        $label = 'badge-success';
    } elseif(basename(Request::url())=='pending'){
        $label = 'badge-warning';
    }
?>

@if(@$approved)
    <div class="col-md-4 col-lg-3">
        <div class="card card-danger request-type" id="approved_department">
            <div class="card-header">
                <h3 class="card-title"><strong>Departments</strong></h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column" >
                    {{-- @foreach($department_approved as $value)
                        <li class="nav-item">
                            <a class="nav-link"
                               @if(@$_GET['depart']==$value->id) style="color: #007bff;" @endif
                               href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'] . '&type=' . @$_GET['type'] . '&depart='. $value->id}}"
                            >
                                <i class="fas fa-bars"></i> {{$value->name_en}}
                                @if(@$memo > 0)
                                    <span class="badge {{ $label }} right">{{ @$memo }}</span>
                                @endif
                                <span v-if="requestType.memo" class="badge {{ $label }} right" v-cloak>@{{ requestType.memo }}</span>
                            </a>
                        </li>
                    @endforeach --}}

                    <li class="nav-item" v-for="dp in departments">
                        <a v-if="dp.active === 1" class="nav-link text-primary" :href="dp.link">
                            <i class="fas fa-bars"></i>&nbsp; @{{ dp.name_en }}
                            <span v-if="dp.total" class="badge {{ @$viewShare['label'] }} right" v-cloak>@{{ dp.total }}</span>
                        </a>
                        <a v-if="dp.active === 0" class="nav-link" :href="dp.link">
                            <i class="fas fa-bars"></i>&nbsp; @{{ dp.name_en }}
                            <span v-if="dp.total" class="badge {{ @$viewShare['label'] }} right" v-cloak>@{{ dp.total }}</span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
@endif
