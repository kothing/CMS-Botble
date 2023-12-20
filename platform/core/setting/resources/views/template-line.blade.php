<x-core-setting::section
    :title="trans($data['name'])"
    :description="trans($data['description'])"
>
    <div class="table-wrap">
        <table class="table product-list ws-nm">
            <thead>
            <tr>
                <th class="border-none-b">{{ trans('core/setting::setting.template') }}</th>
                <th class="border-none-b"> {{ trans('core/setting::setting.description') }} </th>
                @if ($type !== 'core')
                    <th class="border-none-b text-center"> {{ trans('core/setting::setting.enable') }}</th>
                @else
                    <th>&nbsp;</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach ($data['templates'] as $key => $template)
                <tr>
                    <td>
                        <a class="hover-underline a-detail-template"
                           href="{{ route('setting.email.template.edit', [$type, $module, $key]) }}">
                            {{ trans($template['title']) }}
                        </a>
                    </td>
                    <td>{{ trans($template['description']) }}</td>

                    <td class="text-center template-setting-on-off">
                        @if ($type !== 'core' && Arr::get($template, 'can_off', false))
                            <div class="form-group mb-3">
                                {!! Form::onOff(get_setting_email_status_key($type, $module, $key),
                                    get_setting_email_status($type, $module, $key) == 1,
                                    ['data-key' => 'email-config-status-btn', 'data-change-url' => route('setting.email.status.change')]
                                ) !!}
                            </div>
                        @elseif ($type !== 'core')
                            <div class="form-group mb-3">
                                {!! Form::onOff(get_setting_email_status_key($type, $module, $key), 1,
                                    ['disabled' => true, 'readonly' => true]
                                ) !!}
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-core-setting::section>
