@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        <div {!! $options['wrapperAttrs'] !!}>
            @endif
            @endif

            @if ($showLabel && $options['label'] !== false && $options['label_show'])
                {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
            @endif

            @if ($showField)
                <div class="image-box">
                    <input type="hidden" name="{{ $name }}" value="{{ $options['value'] }}" class="image-data">
                    <input type="file" name="{{ $name }}_input" class="image_input" accept="image/*" style="display: none;">
                    <div class="preview-image-wrapper">
                        <img src="{{ RvMedia::getImageUrl($options['value'], 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ trans('core/base::base.preview_image') }}" class="preview_image" width="150">
                        <a class="btn_remove_image" title="{{ trans('core/base::forms.remove_image') }}">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                    <div class="image-box-actions">
                        <a href="#" class="custom-select-image">
                            {{ trans('core/base::forms.choose_image') }}
                        </a>
                    </div>
                </div>
                @include('core/base::forms.partials.help-block')
            @endif

            @include('core/base::forms.partials.errors')

            @if ($showLabel && $showField)
                @if ($options['wrapper'] !== false)
        </div>
    @endif
@endif
