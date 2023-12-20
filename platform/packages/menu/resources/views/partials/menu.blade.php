<ol class="dd-list">
    @foreach ($menu_nodes->loadMissing('metadata') as $key => $row)
       @include('packages/menu::partials.node')
    @endforeach
</ol>
