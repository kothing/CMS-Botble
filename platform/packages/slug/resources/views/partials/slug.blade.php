<input type="hidden" name="model" value="{{ $class = get_class($object) }}">

@if (empty($object))
    <div class="form-group mb-3 @if ($errors->has('slug')) has-error @endif">
        {!! Form::permalink('slug', old('slug'), 0, $prefix, [], true, $class) !!}
        {!! Form::error('slug', $errors) !!}
    </div>
@else
    <div class="form-group mb-3 @if ($errors->has('slug')) has-error @endif">
        {!! Form::permalink('slug', $object->slug, $object->slug_id, $prefix, SlugHelper::canPreview($class) && $object->status != \Botble\Base\Enums\BaseStatusEnum::PUBLISHED, [], true, $class) !!}
        {!! Form::error('slug', $errors) !!}
    </div>
@endif
