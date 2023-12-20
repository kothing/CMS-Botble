<div class="ui-select-wrapper form-group {{ Arr::get($selectAttributes, 'wrapper_class') ?: '' }}">
    @php
        Arr::set($selectAttributes, 'class', Arr::get($selectAttributes, 'class') . ' ui-select');
    @endphp
    {!! Form::select($name, $list ?? $choices, $selected, $selectAttributes, $optionsAttributes, $optgroupsAttributes) !!}
    <svg class="svg-next-icon svg-next-icon-size-16">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 16l-4-4h8l-4 4zm0-12L6 8h8l-4-4z"></path></svg>
    </svg>
</div>
