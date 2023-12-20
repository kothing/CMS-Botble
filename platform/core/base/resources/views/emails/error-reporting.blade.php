<div style="max-width: 100%; word-break: break-all; padding: 30px;">
    <p><strong>URL: </strong><a href="{{ $url }}">{{ $url }}</a></p>
    <p><strong>File: </strong> {{ $ex->getFile() }}:{{ $ex->getLine() }}</p>
    <p><strong>Error: </strong>{!! trim($error) !!}</p>
</div>