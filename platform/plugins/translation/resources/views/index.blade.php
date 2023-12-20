@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="widget meta-boxes">
        <div class="widget-title">
            <h4>&nbsp; {{ trans('plugins/translation::translation.translations') }}</h4>
        </div>
        <div class="widget-body box-translation" v-pre>
            @if (empty($group))
                {!! Form::open(['route' => 'translations.import', 'class' => 'form-inline', 'role' => 'form']) !!}
                    {!! Form::customSelect('replace', [
                        0 => trans('plugins/translation::translation.append_translation'),
                        1 => trans('plugins/translation::translation.replace_translation')
                    ], null, ['wrapper_class' => 'd-inline-block mb-0']) !!}

                    <button type="submit" class="btn btn-primary button-import-groups">{{ trans('plugins/translation::translation.import_group') }}</button>
                {!! Form::close() !!}
                <br>
            @endif
            @if (!empty($group))
                <form method="POST" action="{{ route('translations.group.publish', compact('group')) }}" class="form-inline" role="form">
                    @csrf
                    <button type="submit" class="btn btn-info button-publish-groups">{{ trans('plugins/translation::translation.publish_translations') }}</button>
                    <a href="{{ route('translations.index') }}" class="btn btn-secondary translation-back">{{ trans('plugins/translation::translation.back') }}</a>
                </form>
                <div class="note note-warning">{{ trans('plugins/translation::translation.export_warning', ['lang_path' => lang_path()]) }}</div>

                {!! apply_filters('translation_other_translation_header', null) !!}
            @endif
            {!! Form::open(['role' => 'form']) !!}
                {!! Form::customSelect('group', $groups, $group, ['class' => 'group-select select-search-full']) !!}
            {!! Form::close() !!}
            @if (!empty($group))
                <hr>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            @foreach($locales as $locale)
                                <th>{{ $locale }}</th>
                            @endforeach
                            {!! apply_filters('translation_other_translation_table_header', null) !!}
                        </tr>
                        </thead>
                            <tbody>
                            @foreach ($translations as $key => $translation)
                                <tr id="{{ $key }}">
                                    @foreach($locales as $locale)
                                        @php $item = $translation[$locale] ?? null @endphp
                                        <td class="text-start">
                                            <a href="#edit" class="editable status-{{ $item ? $item->status : 0 }} locale-{{ $locale }}"
                                               data-locale="{{ $locale }}" data-name="{{ $locale . '|' . $key }}"
                                               data-type="textarea" data-pk="{{ $item ? $item->id : 0 }}" data-url="{{ $editUrl }}"
                                               data-title="{{ trans('plugins/translation::translation.edit_title') }}">{!! ($item ? htmlentities($item->value, ENT_QUOTES, 'UTF-8', false) : '') !!}</a>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            {!! apply_filters('translation_other_translation_table_body', null) !!}
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-info">{{ trans('plugins/translation::translation.choose_group_msg') }}</p>
            @endif
        </div>
        <div class="clearfix"></div>
    </div>
    @if (!empty($group))
        <x-core-base::modal
            id="confirm-publish-modal"
            :title="trans('plugins/translation::translation.publish_translations')"
            type="warning"
            button-id="button-confirm-publish-groups"
            :button-label="trans('core/base::base.yes')"
        >
            {!! trans('plugins/translation::translation.confirm_publish_group', ['group' => $group]) !!}
        </x-core-base::modal>
    @endif
@stop
