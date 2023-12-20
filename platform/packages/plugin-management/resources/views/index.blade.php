@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div id="plugin-list">
        @if (config('packages.plugin-management.general.enable_marketplace_feature') && auth()->user()->hasPermission('plugins.marketplace'))
            <div class="mb-3">
                <a class="btn btn-info" href="{{ route('plugins.marketplace') }}">
                    <i class="fa fa-plus me-1"></i> {{ trans('packages/plugin-management::plugin.plugins_add_new') }}
                </a>
            </div>
        @endif

        <div class="clearfix app-grid--blank-slate row">
            @foreach ($list as $plugin)
                <div class="app-card-item col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="app-item app-{{ $plugin->path }}">
                        <div class="app-icon" @if ($plugin->image) style="background-image: url('{{ $plugin->image }}');" @endif>
                            @if (! $plugin->image)
                                <span class="display-5 font-white">
                                    <i class="fa fa-puzzle-piece"></i>
                                </span>
                            @endif
                        </div>
                        <div class="app-details">
                            <h4 class="app-name">{{ $plugin->name }}</h4>
                        </div>
                        <div class="app-footer">
                            <div class="app-description" title="{{ $plugin->description }}">{{ $plugin->description }}</div>
                            @if (! config('packages.plugin-management.general.hide_plugin_author', false))
                                <div class="app-author">{{ trans('packages/plugin-management::plugin.author') }}: <a href="{{ $plugin->url }}" target="_blank">{{ $plugin->author }}</a></div>
                            @endif
                            <div class="app-version">{{ trans('packages/plugin-management::plugin.version') }}: {{ $plugin->version }}</div>
                            <div class="app-actions">
                                @if (auth()->user()->hasPermission('plugins.edit'))
                                    <button class="btn @if ($plugin->status) btn-warning @else btn-info @endif btn-trigger-change-status" data-plugin="{{ $plugin->path }}" data-status="{{ $plugin->status }}">
                                        @if ($plugin->status)
                                            {{ trans('packages/plugin-management::plugin.deactivate') }}
                                        @else
                                            {{ trans('packages/plugin-management::plugin.activate') }}
                                        @endif
                                    </button>
                                @endif

                                <button class="btn btn-success btn-trigger-update-plugin" style="display: none;" data-name="{{ $plugin->path }}" data-check-update="{{ $plugin->id ?? 'plugin-' . $plugin->path }}" data-version="{{ $plugin->version }}">{{ trans('packages/plugin-management::plugin.update') }}</button>

                                @if (auth()->user()->hasPermission('plugins.remove'))
                                    <button class="btn btn-link text-danger text-decoration-none btn-trigger-remove-plugin" data-plugin="{{ $plugin->path }}">{{ trans('packages/plugin-management::plugin.remove') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <x-core-base::modal
        id="remove-plugin-modal"
        :title="trans('packages/plugin-management::plugin.remove_plugin')"
        type="danger"
        button-id="confirm-remove-plugin-button"
        :button-label="trans('packages/plugin-management::plugin.remove_plugin_confirm_yes')"
    >
        {!! trans('packages/plugin-management::plugin.remove_plugin_confirm_message') !!}
    </x-core-base::modal>

    <x-core-base::modal
        id="confirm-install-plugin-modal"
        :title="trans('packages/plugin-management::plugin.install_plugin')"
        button-id="confirm-install-plugin-button"
        :button-label="trans('packages/plugin-management::plugin.install')"
    >
        <input type="hidden" name="plugin_name" value="">
        <input type="hidden" name="ids" value="">
        <p id="requirement-message"></p>
    </x-core-base::modal>
@endsection
