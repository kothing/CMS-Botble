<div class="row widget-wrapper mb-3">
    @foreach($widgets as $widget)
        {{ $widget->render() }}
    @endforeach
</div>
