@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        <div {!! $options['wrapperAttrs'] !!}>
            @endif
            @endif

            @if ($showLabel && $options['label'] !== false && $options['label_show'])
                {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
            @endif

            @if ($showField)
                <div class="form-group mb-3 form-group-no-margin @if ($errors->has($name)) has-error @endif">
                    <div class="multi-choices-widget list-item-checkbox">
                        <ul>
                            @foreach (Arr::get($options, 'choices', []) as $key => $item)
                                <li>
                                    <input type="checkbox"
                                           class="styled"
                                           name="{{ $name }}"
                                           value="{{ $key }}"
                                           id="{{ $name }}-item-{{ $key }}"
                                           @if (in_array($key, Arr::get($options, 'value', []))) checked="checked" @endif>
                                    <label for="{{ $name }}-item-{{ $key }}">{{ $item }}</label>
                                </li>
                            @endforeach
                        </ul>
                        @include('core/base::forms.partials.help-block')
                    </div>
                </div>
            @endif

            @include('core/base::forms.partials.errors')

            @if ($showLabel && $showField)
                @if ($options['wrapper'] !== false)
        </div>
    @endif
@endif
