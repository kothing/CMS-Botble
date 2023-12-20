@extends('packages/installer::master')

@section('template_title', trans('packages/installer::installer.create_account'))

@section('container')
    <form method="post" action="{{ route('installers.account.save') }}">
        @csrf
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="first_name" class="text-sm font-medium leading-6 text-gray-900">
                    {{ trans('packages/installer::installer.first_name') }}
                </label>
                <div class="relative mt-2 rounded-md shadow-sm">
                    <input type="text" name="first_name" id="first_name" @class(['w-full rounded-md border-0 py-2 px-2 pe-10 ring-1 focus:ring-2 ring-inset sm:text-sm sm:leading-6', 'ring-red-300 focus:ring-red-500' => $errors->has('first_name'), 'ring-gray-300' => ! $errors->has('first_name')]) value="{{ old('first_name') }}" placeholder="{{ trans('packages/installer::installer.first_name') }}">
                    @if ($errors->has('first_name'))
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pe-3">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>
                @error('first_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="last_name" class="text-sm font-medium leading-6 text-gray-900">
                    {{ trans('packages/installer::installer.last_name') }}
                </label>
                <div class="relative mt-2 rounded-md shadow-sm">
                    <input type="text" name="last_name" id="last_name" @class(['w-full rounded-md border-0 py-2 px-2 pe-10 ring-1 focus:ring-2 ring-inset sm:text-sm sm:leading-6', 'ring-red-300 focus:ring-red-500' => $errors->has('last_name'), 'ring-gray-300' => ! $errors->has('last_name')]) value="{{ old('last_name') }}" placeholder="{{ trans('packages/installer::installer.last_name') }}">
                    @if ($errors->has('last_name'))
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pe-3">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>
                @error('last_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="username" class="text-sm font-medium leading-6 text-gray-900">
                    {{ trans('packages/installer::installer.username') }}
                </label>
                <div class="relative mt-2 rounded-md shadow-sm">
                    <input type="text" name="username" id="username" @class(['w-full rounded-md border-0 py-2 px-2 pe-10 ring-1 focus:ring-2 ring-inset sm:text-sm sm:leading-6', 'ring-red-300 focus:ring-red-500' => $errors->has('username'), 'ring-gray-300' => ! $errors->has('username')]) value="{{ old('username') }}" placeholder="{{ trans('packages/installer::installer.username') }}">
                    @if ($errors->has('username'))
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pe-3">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>
                @error('username')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="text-sm font-medium leading-6 text-gray-900">
                    {{ trans('packages/installer::installer.email') }}
                </label>
                <div class="relative mt-2 rounded-md shadow-sm">
                    <input type="email" name="email" id="email" @class(['w-full rounded-md border-0 py-2 px-2 pe-10 ring-1 focus:ring-2 ring-inset sm:text-sm sm:leading-6', 'ring-red-300 focus:ring-red-500' => $errors->has('email'), 'ring-gray-300' => ! $errors->has('email')]) value="{{ old('email') }}" placeholder="{{ trans('packages/installer::installer.email') }}">
                    @if ($errors->has('email'))
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pe-3">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="text-sm font-medium leading-6 text-gray-900">
                    {{ trans('packages/installer::installer.password') }}
                </label>
                <div class="relative mt-2 rounded-md shadow-sm">
                    <input type="password" name="password" id="password" @class(['w-full rounded-md border-0 py-2 px-2 pe-10 ring-1 focus:ring-2 ring-inset sm:text-sm sm:leading-6', 'ring-red-300 focus:ring-red-500' => $errors->has('password'), 'ring-gray-300' => ! $errors->has('password')]) value="{{ old('password') }}" placeholder="{{ trans('packages/installer::installer.password') }}">
                    @if ($errors->has('password'))
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pe-3">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="text-sm font-medium leading-6 text-gray-900">
                    {{ trans('packages/installer::installer.password_confirmation') }}
                </label>
                <div class="relative mt-2 rounded-md shadow-sm">
                    <input type="password" name="password_confirmation" id="password_confirmation" @class(['w-full rounded-md border-0 py-2 px-2 pe-10 ring-1 focus:ring-2 ring-inset sm:text-sm sm:leading-6', 'ring-red-300 focus:ring-red-500' => $errors->has('password_confirmation'), 'ring-gray-300' => ! $errors->has('password_confirmation')]) value="{{ old('password_confirmation') }}" placeholder="{{ trans('packages/installer::installer.password_confirmation') }}">
                    @if ($errors->has('password_confirmation'))
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pe-3">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="text-center mt-10">
            <button type="submit" class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl hover:text-white hover:shadow-2xl focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
                {{ trans('packages/installer::installer.create') }}
                <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
            </button>
        </div>
    </form>

@endsection
