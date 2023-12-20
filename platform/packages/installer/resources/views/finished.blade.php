@extends('packages/installer::master')

@section('template_title', trans('packages/installer::installer.final.templateTitle'))

@section('container')
    <div class="max-w-2xl mx-auto">
        <div class="py-6 md:py-40">
            <p class="text-center text-slate-600 text-xl">
                <svg class="h-12 w-12 text-green-400 w-full mx-auto" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                {{ trans('packages/installer::installer.install_success') }}
            </p>
        </div>
    </div>

    <div class="text-center mt-10">
        <a href="{{ route('access.login') }}" class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl hover:text-white hover:shadow-2xl focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
            {{ trans('packages/installer::installer.final.exit') }}
            <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
        </a>
    </div>
@endsection
