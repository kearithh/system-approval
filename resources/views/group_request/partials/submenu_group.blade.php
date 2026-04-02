<div class="card card-danger">
    <div class="card-header">
        <h3 class="card-title"><strong>Groups</strong></h3>
    </div>
    <div class="card-body p-0">
        <ul class="nav nav-pills flex-column">
            <li v-for="g in groups" v-if="g.total > 0" class="nav-item" :title="g.id">
                <a 
                    :class="{ 'text-primary' : g.active == 1 }"
                    class="nav-link"
                    :href="g.link">
                    <i class="fas fa-bars"></i>&nbsp;
                    @{{ g.name }}
                    <span v-if="g.total" class="badge {{ @$viewShare['label'] }} right" v-cloak>@{{ g.total }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>
