@if (!empty($options))
    @php $id = Str::slug($name) . '-'. time(); @endphp
    <div class="widget meta-boxes">
        <a data-bs-toggle="collapse" data-parent="#accordion" href="#{{ $id }}">
            <h4 class="widget-title">
                <span>{{ $name }}</span>
                <i class="fa fa-angle-down narrow-icon"></i>
            </h4>
        </a>
        <div id="{{ $id }}" class="panel-collapse collapse @if (Str::slug($name) == 'pages') show @endif">
            <div class="widget-body">
                <div class="box-links-for-menu">
                    <div class="the-box">
                        {!! $options !!}
                        <div class="text-end">
                            <div class="btn-group btn-group-devided">
                                <a href="#" class="btn-add-to-menu btn btn-primary">
                                    <span class="text"><i class="fa fa-plus"></i> {{ trans('packages/menu::menu.add_to_menu') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
