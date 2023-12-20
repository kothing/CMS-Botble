@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="container">
        <div class="col-md-8 offset-md-2">
            <h2 class="text-center mb-4">{{ trans('core/base::system.updater') }}</h2>

            <div class="updater-box mb-5" dir="ltr">
                <div class="note note-warning">
                    <p>- Please back up your database and script files before upgrading.</p>
                    <p>- You need to activate your license before doing upgrade.</p>
                    <p>- If you don't need this 1-click update, you can disable it in <strong>.env</strong> by adding <strong>CMS_ENABLE_SYSTEM_UPDATER=false</strong></p>
                    <p>- It will override all files in <strong>platform/core</strong>, <strong>platform/packages</strong>, all plugins developed by us in <strong>platform/plugins</strong> and theme developed by us in <strong>platform/themes</strong>.</p>
                </div>

                @if (! empty($latestUpdate))

                    @if (request()->query('no-ajax'))
                        @if($isOutdated)
                            <p class="mb-2 text-success">
                                {{ __('A new version (:version / released on :date) is available to update!', ['version' => $latestUpdate->version, 'date' => $latestUpdate->releasedDate->toDateString()]) }}
                            </p>

                            <div class="note note-info changelog-info">
                                {!! $latestUpdate->changelog !!}
                            </div>
                        @else
                            <p class="mb-2 text-success">{{ __('The system is up-to-date. There are no new versions to update!') }}</p>
                        @endif

                        <form action="{{ route('system.updater') }}?no-ajax=1&update_id={{ $latestUpdate->updateId }}&version={{ $latestUpdate->version }}" method="POST">
                            @csrf
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item mb-2">
                                    <button type="submit" name="step" value="1" class="btn btn-warning btn-update-new-version" data-updating-text="Updating..."><span>Download update files</span></button>
                                </li>
                                <li class="list-group-item mb-2">
                                    <button type="submit" name="step" value="2" class="btn btn-warning btn-update-new-version" data-updating-text="Updating..."><span>Update files and database</span></button>
                                </li>
                                <li class="list-group-item mb-2">
                                    <button type="submit" name="step" value="3" class="btn btn-warning btn-update-new-version" data-updating-text="Updating..."><span>Publish assets</span></button>
                                </li>
                                <li class="list-group-item mb-2">
                                    <button type="submit" name="step" value="4" class="btn btn-warning btn-update-new-version" data-updating-text="Updating..."><span>Cleanup files</span></button>
                                </li>
                            </ol>
                        </form>
                    @else
                        <system-update-component
                            update-url="{{ route('system.updater.post') }}"
                            update-id="{{ $latestUpdate->updateId }}"
                            version="{{ $latestUpdate->version }}"
                            :is-outdated="{{ json_encode($isOutdated) }}"
                        >
                            @if($isOutdated)
                                <p class="mb-2 text-success">
                                    {{ __('A new version (:version / released on :date) is available to update!', ['version' => $latestUpdate->version, 'date' => $latestUpdate->releasedDate->toDateString()]) }}
                                </p>

                                <div class="note note-info changelog-info">
                                    {!! $latestUpdate->changelog !!}
                                </div>
                            @else
                                <p class="mb-2 text-success">{{ __('The system is up-to-date. There are no new versions to update!') }}</p>
                            @endif
                        </system-update-component>
                    @endif
                @else
                    <p class="mb-0 text-success">{{ __('The system is up-to-date. There are no new versions to update!') }}</p>
                @endif
            </div>

            @if (! request()->query('no-ajax'))
                <div class="updater-box shadow-none border-0 p-0" dir="ltr">
                    <p class="note note-warning">If you don't see the update button, please <a href={{ route('system.updater', ['no-ajax' => 1]) }}>click here</a>.</p>
                </div>
            @endif

            @if (isset($isOutdated) && isset($latestUpdate) && ! $isOutdated && $latestUpdate)
                <div class="updater-box bg-transparent shadow-none border-0 p-0" dir="ltr">
                    <div class="mb-2 bold">Latest changelog: released on {{ $latestUpdate->releasedDate->toDateString() }}</div>
                    <pre>{!! trim(strip_tags(str_replace('<li>', '<li>- ', $latestUpdate->changelog))) !!} </pre>
                </div>
            @endif
        </div>
    </div>
@stop
