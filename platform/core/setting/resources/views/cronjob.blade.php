@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core-setting::section
        :title="trans('core/setting::setting.cronjob.name')"
        :description="trans('core/setting::setting.cronjob.description')"
    >
        <div class="input-group mb-3 cronjob">
            <input type="text" id="command" class="form-control" value="{{ $command }}" data-copied="{{ trans('core/setting::setting.cronjob.setup.copied') }}">
            <button class="input-group-text px-3" id="copy-command">{{ trans('core/setting::setting.cronjob.copy_button') }}</button>
        </div>

        <div class="mt-2">
            @if(! $lastRunAt)
                <div class="alert alert-info" type="info">
                    {{ trans('core/setting::setting.cronjob.is_not_ready') }}
                </div>
            @elseif(\Carbon\Carbon::now()->diffInMinutes($lastRunAt) <= 10)
                <div class="alert alert-success" type="success">
                    <p class="mb-2">{{ trans('core/setting::setting.cronjob.is_working') }}</p>
                    <p class="text-xs mb-0">{!! trans('core/setting::setting.cronjob.last_checked', ['time' => "<span class='fw-semibold'>{$lastRunAt->diffForHumans()}</span>"]) !!}</p>
                </div>
            @else
                <div class="alert alert-danger" type="danger">
                    {{ trans('core/setting::setting.cronjob.is_not_working') }}
                </div>
            @endif
        </div>

        <div class="mt-4 pt-4 border-top">
            <h3 class="fs-5">{{ trans('core/setting::setting.cronjob.setup.name') }}</h3>
            <ul class="text-sm ps-3">
                <li style="list-style-type: decimal;">{{ trans('core/setting::setting.cronjob.setup.connect_to_server') }}</li>
                <li style="list-style-type: decimal;">{{ trans('core/setting::setting.cronjob.setup.open_crontab') }}</li>
                <li style="list-style-type: decimal;">{{ trans('core/setting::setting.cronjob.setup.add_cronjob') }}</li>
                <li style="list-style-type: decimal;">{{ trans('core/setting::setting.cronjob.setup.done') }}</li>
            </ul>

            <p class="border-top pt-4 text-sm mt-2">{!! trans('core/setting::setting.cronjob.setup.learn_more', ['documentation' => '<a href="https://laravel.com/docs/8.x/scheduling" target="_blank" class="hover:underline text-primary-500">' . trans('core/setting::setting.cronjob.setup.documentation') . '</a>.']) !!}</p>
        </div>
    </x-core-setting::section>
@endsection
