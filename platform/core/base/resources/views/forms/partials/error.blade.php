@if (!empty($errors) && $errors->has($name))
    <div class="text-danger">
        <small>{{ $errors->first($name) }}</small>
    </div>
@endif
