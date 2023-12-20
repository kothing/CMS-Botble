<a href="#" class="btn-trigger-show-seo-detail" v-pre>{{ trans('packages/seo-helper::seo-helper.edit_seo_meta') }}</a>
<div class="seo-preview" v-pre>
    <p class="default-seo-description @if (!empty($object->id)) hidden @endif">{{ trans('packages/seo-helper::seo-helper.default_description') }}</p>
    <div class="existed-seo-meta @if (empty($object->id)) hidden @endif">
        <span class="page-title-seo">
             {!! BaseHelper::clean($meta['seo_title'] ?? (!empty($object->id) ? $object->name ?? $object->title : null)) !!}
        </span>

        <div class="page-url-seo ws-nm">
            <p>{{ !empty($object->id) && $object->url ? $object->url : '-' }}</p>
        </div>

        <div class="ws-nm">
            <span style="color: #70757a;">{{ !empty($object->id) && $object->created_at ? $object->created_at->format('M d, Y') : Carbon\Carbon::now()->format('M d, Y') }} - </span>
            <span class="page-description-seo">
                {{ strip_tags((string)$meta['seo_description'] ?? (!empty($object->id) ? $object->description : (!empty($object->id) && $object->content ? Str::limit($object->content, 250) : old('seo_meta.seo_description')))) }}
            </span>
        </div>
    </div>
</div>


<div class="seo-edit-section hidden" v-pre>
    <hr>
    <div class="form-group mb-3">
        <label for="seo_title" class="control-label">{{ trans('packages/seo-helper::seo-helper.seo_title') }}</label>
        {!! Form::text('seo_meta[seo_title]', $meta['seo_title'] ?? old('seo_meta.seo_title'), ['class' => 'form-control', 'id' => 'seo_title', 'placeholder' => trans('packages/seo-helper::seo-helper.seo_title'), 'data-counter' => 120]) !!}
    </div>
    <div class="form-group mb-3">
        <label for="seo_description" class="control-label">{{ trans('packages/seo-helper::seo-helper.seo_description') }}</label>
        {!! Form::textarea('seo_meta[seo_description]', strip_tags((string)$meta['seo_description']) ?? old('seo_meta.seo_description'), ['class' => 'form-control', 'rows' => 3, 'id' => 'seo_description', 'placeholder' => trans('packages/seo-helper::seo-helper.seo_description'), 'data-counter' => 160]) !!}
    </div>
</div>
