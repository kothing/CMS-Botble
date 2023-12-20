@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="bs-callout bs-callout-primary">
                <p>{{ trans('core/base::system.report_description') }}:</p>
                <button id="btn-report" class="btn btn-info btn-sm">{{ trans('core/base::system.get_system_report') }}</button>

                <div id="report-wrapper">
                    <textarea name="txt-report" id="txt-report" class="col-sm-12" rows="10" spellcheck="false" onfocus="this.select()">
                        ### {{ trans('core/base::system.system_environment') }}

                        - {{ trans('core/base::system.cms_version') }}: {{ get_cms_version() }}
                        - {{ trans('core/base::system.framework_version') }}: {{ $systemEnv['version'] }}
                        - {{ trans('core/base::system.timezone') }}: {{ $systemEnv['timezone'] }}
                        - {{ trans('core/base::system.debug_mode_off') }}: {!! ! $systemEnv['debug_mode'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.storage_dir_writable') }}: {!! $systemEnv['storage_dir_writable'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.cache_dir_writable') }}: {!! $systemEnv['cache_dir_writable'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.app_size') }}: {{ $systemEnv['app_size'] }}

                        ### {{ trans('core/base::system.server_environment') }}

                        - {{ trans('core/base::system.php_version') }}: {{ $serverEnv['version'] . (!$matchPHPRequirement ? '(' . trans('core/base::system.php_version_error', ['version' => $requiredPhpVersion]) . ')' : '') }}
                        - {{ trans('core/base::system.memory_limit') }}: {!! $serverEnv['memory_limit'] ?: '&mdash;' !!}
                        - {{ trans('core/base::system.max_execution_time') }}: {!! $serverEnv['max_execution_time'] ?: '&mdash;' !!}
                        - {{ trans('core/base::system.server_software') }}: {{ $serverEnv['server_software'] }}
                        - {{ trans('core/base::system.server_os') }}: {{ $serverEnv['server_os'] }}
                        - {{ trans('core/base::system.database') }}: {{ $serverEnv['database_connection_name'] }}
                        - {{ trans('core/base::system.ssl_installed') }}: {!! $serverEnv['ssl_installed'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.cache_driver') }}: {{ $serverEnv['cache_driver'] }}
                        - {{ trans('core/base::system.queue_connection') }}: {{ $serverEnv['queue_connection'] }}
                        - {{ trans('core/base::system.session_driver') }}: {{ $serverEnv['session_driver'] }}
                        - {{ trans('core/base::system.allow_url_fopen_enabled') }}: {!! $serverEnv['allow_url_fopen_enabled'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.mbstring_ext') }}: {!! $serverEnv['mbstring'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.openssl_ext') }}: {!! $serverEnv['openssl'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.pdo_ext') }}: {!! $serverEnv['pdo'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.curl_ext') }}: {!! $serverEnv['curl'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.exif_ext') }}: {!! $serverEnv['exif'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.file_info_ext') }}: {!! $serverEnv['fileinfo'] ? '&#10004;' : '&#10008;' !!}
                        - {{ trans('core/base::system.tokenizer_ext') }}: {!! $serverEnv['tokenizer']  ? '&#10004;' : '&#10008;'!!}
                        - {{ trans('core/base::system.imagick_or_gd_ext') }}: {!! $serverEnv['imagick_or_gd']  ? '&#10004;' : '&#10008;'!!}
                        - {{ trans('core/base::system.zip') }}: {!! $serverEnv['zip']  ? '&#10004;' : '&#10008;'!!}
                        - {{ trans('core/base::system.iconv') }}: {!! $serverEnv['iconv']  ? '&#10004;' : '&#10008;'!!}

                        ### {{ trans('core/base::system.installed_packages') }}

                        @foreach($packages as $package)
                            - {{ $package['name'] }} : {{ $package['version'] }}
                        @endforeach
                    </textarea>
                    <button id="copy-report" class="btn btn-info btn-sm">{{ trans('core/base::system.copy_report') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row"> <!-- Main Row -->

        <div class="col-sm-8"> <!-- Package & Dependency column -->
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('core/base::system.installed_packages') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    {!! $infoTable->renderTable() !!}
                </div>
            </div>
        </div> <!-- / Package & Dependency column -->

        <div class="col-sm-4"> <!-- Server Environment column -->
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('core/base::system.system_environment') }}</span>
                    </h4>
                </div>

                <ul class="list-group">
                    <li class="list-group-item">{{ trans('core/base::system.cms_version') }}: {{ get_cms_version() }}</li>
                    <li class="list-group-item">{{ trans('core/base::system.framework_version') }}: {{ $systemEnv['version'] }}</li>
                    <li class="list-group-item">{{ trans('core/base::system.timezone') }}: {{ $systemEnv['timezone'] }}</li>
                    <li class="list-group-item">{{ trans('core/base::system.debug_mode_off') }}: {!! ! $systemEnv['debug_mode'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.storage_dir_writable') }}: {!! $systemEnv['storage_dir_writable'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.cache_dir_writable') }}: {!! $systemEnv['cache_dir_writable'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.app_size') }}: {{ $systemEnv['app_size'] }}</li>
                </ul>
            </div>

            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('core/base::system.server_environment') }}</span>
                    </h4>
                </div>

                <ul class="list-group">
                    <li class="list-group-item">{{ trans('core/base::system.php_version') }}: {{ $serverEnv['version'] }} @if ($matchPHPRequirement) <span class="fas fa-check"></span> @else <span class="fas fa-times"></span> <span class="text-danger">({{ trans('core/base::system.php_version_error', ['version' => $requiredPhpVersion]) }})</span> @endif</li>
                    <li class="list-group-item">{{ trans('core/base::system.memory_limit') }}: {!! $serverEnv['memory_limit'] ?: '&mdash;' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.max_execution_time') }}: {!! $serverEnv['max_execution_time'] ?: '&mdash;' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.server_software') }}: {{ $serverEnv['server_software'] }}</li>
                    <li class="list-group-item">{{ trans('core/base::system.server_os') }}: {{ $serverEnv['server_os'] }}</li>
                    <li class="list-group-item">{{ trans('core/base::system.database') }}: {{ $serverEnv['database_connection_name'] }}</li>
                    <li class="list-group-item">{{ trans('core/base::system.ssl_installed') }}: {!! $serverEnv['ssl_installed'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.cache_driver') }}: {{ $serverEnv['cache_driver'] }}</li>
                    <li class="list-group-item">{{ trans('core/base::system.session_driver') }}: {{ $serverEnv['session_driver'] }}</li>
                    <li class="list-group-item">{{ trans('core/base::system.queue_connection') }}: {{ $serverEnv['queue_connection'] }}</li>
                    <li class="list-group-item">{{ trans('core/base::system.allow_url_fopen_enabled') }}: {!! $serverEnv['allow_url_fopen_enabled'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.openssl_ext') }}: {!! $serverEnv['openssl'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.mbstring_ext') }}: {!! $serverEnv['mbstring'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.pdo_ext') }}: {!! $serverEnv['pdo'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.curl_ext') }}: {!! $serverEnv['curl'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.exif_ext') }}: {!! $serverEnv['exif'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.file_info_ext') }}: {!! $serverEnv['fileinfo'] ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>' !!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.tokenizer_ext') }}: {!! $serverEnv['tokenizer']  ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>'!!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.imagick_or_gd_ext') }}: {!! $serverEnv['imagick_or_gd']  ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>'!!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.zip') }}: {!! $serverEnv['zip']  ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>'!!}</li>
                    <li class="list-group-item">{{ trans('core/base::system.iconv') }}: {!! $serverEnv['zip']  ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times"></span>'!!}</li>
                </ul>
            </div>
        </div> <!-- / Server Environment column -->

    </div> <!-- / Main Row -->
@stop
