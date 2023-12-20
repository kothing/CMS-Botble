@foreach(RvMedia::getConfig('libraries.javascript', []) as $js)
    <script src="{{ asset($js) }}" type="text/javascript"></script>
@endforeach
