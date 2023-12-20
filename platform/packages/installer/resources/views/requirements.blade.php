@extends('packages/installer::master')

@section('template_title', trans('packages/installer::installer.requirements.templateTitle'))

@section('container')
    @foreach ($requirements['requirements'] as $type => $requirement)
        <div class="bg-slate-100 rounded-lg overflow-auto mb-6 last:mb-0">
            <div class="border-b flex items-center justify-between bg-blue-100 text-white py-4 px-5">
                <p class="text-base text-slate-900 font-medium leading-6">
                    {{ ucfirst($type) }}
                    @if ($type === 'php')
                        <span>{{ __('version :version required', ['version' => $phpSupportInfo['minimum']]) }}</span>
                    @endif
                </p>
                @if ($type === 'php')
                    <div @class(['text-green-600' => $phpSupportInfo['supported'], 'text-red-600' => ! $phpSupportInfo['supported']])>
                        <span class="font-semibold">{{ $phpSupportInfo['current'] }}</span>
                        <i @class(['fa fa-fw', 'fa-check-circle' => $phpSupportInfo['supported'], 'fa-exclamation-circle' => ! $phpSupportInfo['supported']])></i>
                    </div>
                @endif
            </div>
            <div class="p-5">
                <ul>
                    @foreach ($requirements['requirements'][$type] as $extension => $enabled)
                        <li class="flex justify-between items-center border-b last:border-none pb-3 mb-3 last:pb-0 last:mb-0">
                            <span class="text-gray-800 font-weight-bold">
                                {{ $type !== 'permissions' ? ucfirst($extension) : $extension }}
                            </span>
                            <i @class(['right-2 fa fa-fw', 'text-green-600 fa-check-circle' => $enabled, 'text-red-600 fa-exclamation-circle' => ! $enabled])></i>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endforeach

    @if (! isset($requirements['errors']) && $phpSupportInfo['supported'])
        <div class="text-center mt-10">
            <a
                class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl hover:text-white hover:shadow-2xl focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2"
                href="{{ URL::signedRoute('installers.environment', [], \Carbon\Carbon::now()->addMinutes(30)) }}"
            >
                {{ trans('packages/installer::installer.permissions.next') }}
                <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
            </a>
        </div>
    @endif
@endsection
