<div @class(['mb-3', 'col-md-' . $columns => $columns])>
    <div class="rp-card bg-white h-100">
        <div class="rp-card-header">
            <h5 class="p-2">{{ $label }}</h5>
        </div>
        <div class="rp-card-content equal-height">
            {!! $table !!}
        </div>
    </div>
</div>
