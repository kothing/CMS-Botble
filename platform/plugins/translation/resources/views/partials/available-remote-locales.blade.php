@if (!empty($locales))
    <div class="table-responsive">
        <table class="table text-start table-striped table-bordered">
            <tbody>
                @foreach($locales as $locale)
                    <tr>
                        <td>{{ $locale['name'] }} - {{ $locale['locale'] }}</td>
                        <td class="text-center" style="white-space: nowrap; width: 1%;"><button class="btn btn-info btn-import-remote-locale" data-url="{{ route('translations.locales.download-remote-locale', $locale['locale']) }}" type="button"><i class="fas fa-cloud-download-alt"></i> {{ trans('plugins/translation::translation.download_locale') }}</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <span class="d-inline-block">{{ trans('core/base::tables.no_data') }}</span>
@endif
