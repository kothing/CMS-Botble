@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="widget meta-boxes">
        <div class="widget-title">
            <h4>&nbsp; {{ trans('plugins/translation::translation.locales') }}</h4>
        </div>
        <div class="widget-body box-translation">
            <div class="row">
                <div class="col-md-5">
                    <div class="main-form">
                        <div class="form-wrap">
                            <form class="add-locale-form" action="{{ route('translations.locales') }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="locale" class="control-label">{{ trans('plugins/translation::translation.locale') }}</label>
                                    {!! Form::customSelect('locale', ['' => trans('plugins/translation::translation.select_locale')] + collect($locales)->map(fn($item, $key) => $item . ' - ' . $key)->all(), null, ['class' => 'select-search-full']) !!}
                                </div>
                                <p class="submit">
                                    <button class="btn btn-primary" type="submit">{{ trans('plugins/translation::translation.add_new_locale') }}</button>
                                </p>
                            </form>
                        </div>

                        <br>
                        <div class="widget meta-boxes">
                            <div class="widget-title px-0">
                                <h4>{{ trans('plugins/translation::translation.import_available_locale') }}</h4>
                            </div>
                            <div class="widget-body px-0">
                                <div id="available-remote-locales" data-url="{{ route('translations.locales.available-remote-locales') }}">
                                    @include('core/base::elements.loading')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="table-responsive">
                        <table class="table table-hover table-language table-header-color" style="background: #f1f1f1;">
                            <thead>
                            <tr>
                                <th class="text-start"><span>{{ trans('plugins/translation::translation.name') }}</span></th>
                                <th class="text-center"><span>{{ trans('plugins/translation::translation.locale') }}</span></th>
                                <th class="text-center"><span>{{ trans('plugins/translation::translation.is_default') }}</span></th>
                                <th class="text-center"><span>{{ trans('plugins/translation::translation.actions') }}</span></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($existingLocales as $item)
                                    @include('plugins/translation::partials.locale-item', compact('item'))
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    @include('core/table::partials.modal-item', [
        'type'        => 'danger',
        'name'        => 'modal-confirm-delete',
        'title'       => trans('core/base::tables.confirm_delete'),
        'content'     => trans('plugins/translation::translation.confirm_delete_message', ['lang_path' => lang_path()]),
        'action_name' => trans('core/base::tables.delete'),
        'action_button_attributes' => [
            'class' => 'delete-crud-entry',
        ],
    ])

    <x-core-base::modal
        class="modal-confirm-import-locale"
        :title="trans('plugins/translation::translation.import_available_locale_confirmation')"
        type="info"
        button-class="button-confirm-import-locale"
        :button-label="trans('plugins/translation::translation.download_locale')"
    >
        <div class="text-break">{!! BaseHelper::clean(trans('plugins/translation::translation.import_available_locale_confirmation_content', ['lang_path' => Html::tag('strong', lang_path())->toHtml()])) !!}</div>
    </x-core-base::modal>
@stop
