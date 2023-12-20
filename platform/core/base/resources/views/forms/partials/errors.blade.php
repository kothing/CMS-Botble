@if ($showError && isset($errors))
    @foreach ($errors->get($nameKey) as $err)
        <div {{ $options['errorAttrs'] }}>{{ $err }}</div>
    @endforeach
@endif

