@if (!empty($menu) && $menu->id)
    <input type="hidden" name="deleted_nodes">
    <textarea name="menu_nodes" id="nestable-output" class="form-control hidden"></textarea>
    <div class="row widget-menu">
        <div class="col-md-4">
            <div class="panel-group" id="accordion">

                @php do_action(MENU_ACTION_SIDEBAR_OPTIONS) @endphp

                <div class="widget meta-boxes">
                    <a data-bs-toggle="collapse" data-parent="#accordion" href="#collapseCustomLink">
                        <h4 class="widget-title">
                            <span>{{ trans('packages/menu::menu.add_link') }}</span>
                            <i class="fa fa-angle-down narrow-icon"></i>
                        </h4>
                    </a>
                    <div id="collapseCustomLink" class="panel-collapse collapse">
                        <div class="widget-body">
                            <div class="box-links-for-menu">
                                <div id="external_link" class="the-box">
                                    <div class="node-content" id="menu-node-create-form">
                                        {!! app(Botble\Base\Forms\FormBuilder::class)->create(Botble\Menu\Forms\MenuNodeForm::class)->renderForm([], false, true, false) !!}

                                        <div class="form-group mb-3">
                                            <div class="text-end add-button">
                                                <div class="btn-group">
                                                    <a href="#" class="btn-add-to-menu btn btn-primary">
                                                        <span class="text">
                                                            <i class="fa fa-plus"></i> {{ trans('packages/menu::menu.add_to_menu') }}
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('packages/menu::menu.structure') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="dd nestable-menu" id="nestable" data-depth="0">
                        {!!
                             Menu::generateMenu([
                                'slug'   => $menu->slug,
                                'view'   => 'packages/menu::partials.menu',
                                'theme'  => false,
                                'active' => false,
                             ])
                        !!}
                    </div>
                </div>
            </div>

            @if (defined('THEME_MODULE_SCREEN_NAME'))
                <div class="widget meta-boxes">
                    <div class="widget-title">
                        <h4>
                            <span>{{ trans('packages/menu::menu.menu_settings') }}</span>
                        </h4>
                    </div>
                    <div class="widget-body" style="min-height: 0">
                        <div class="row">
                            <div class="col-md-4">
                                <p><i>{{ trans('packages/menu::menu.display_location') }}</i></p>
                            </div>
                            <div class="col-md-8">
                                @foreach (Menu::getMenuLocations() as $location => $description)
                                    <div>
                                        <input type="checkbox" @if (in_array($location, $locations)) checked @endif  name="locations[]" value="{{ $location }}" id="menu_location_{{ $location }}">
                                        <label for="menu_location_{{ $location }}">{{ $description }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
