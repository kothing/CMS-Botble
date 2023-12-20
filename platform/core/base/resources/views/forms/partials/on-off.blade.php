<div class="onoffswitch">
    <input type="hidden" name="{{ $name }}" value="0" {!! Html::attributes($attributes) !!}>
    <input type="checkbox" name="{{ $name }}" class="onoffswitch-checkbox" id="{{ $name }}" value="1" @if ($value) checked @endif {!! Html::attributes($attributes) !!}>
    <label class="onoffswitch-label" for="{{ $name }}">
        <span class="onoffswitch-inner"></span>
        <span class="onoffswitch-switch"></span>
    </label>
</div>
