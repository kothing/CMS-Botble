<div class="form-group mb-3 @if ($errors->has('slug')) has-error @endif">
    {!! Form::permalink('slug', $object->slug, $object->slug_id, $prefix, false, [], false, get_class($object)) !!}
    {!! Form::error('slug', $errors) !!}
</div>
