<ul>
    @if(is_array($item['dependencies']))
        @foreach($item['dependencies'] as $dependencyName => $dependencyVersion)
            <li>{{ $dependencyName }} : <span class="label ld-version-tag">{{ $dependencyVersion }}</span></li>
        @endforeach
    @else
        <li><span class="label label-primary">{{ $item['dependencies'] }}</span></li>
    @endif
</ul>