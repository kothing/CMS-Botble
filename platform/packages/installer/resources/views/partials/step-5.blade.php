<li class="relative md:flex md:flex-1">
            <span class="flex items-center px-6 py-4 text-sm font-medium">
                    @if ($currentStep == 5)
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 border-indigo-600 group-hover:border-indigo-400">
                            <span class="text-gray-500 group-hover:text-gray-900">05</span>
                        </span>
                    <span class="ms-4 text-sm font-medium text-indigo-600">{{ trans('packages/installer::installer.final.title') }}</span>

                @elseif ($currentStep > 5)
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-600 group-hover:bg-indigo-800">
                            <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                              <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clip-rule="evenodd" />
                            </svg>
                        </span>

                    <span class="ms-4 text-sm font-medium text-indigo-600">{{ trans('packages/installer::installer.final.title') }}</span>

                @else
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 border-gray-300 group-hover:border-gray-400">
                            <span class="text-gray-500 group-hover:text-gray-900">05</span>
                        </span>
                    <span class="ms-4 text-sm font-medium text-gray-900">{{ trans('packages/installer::installer.final.title') }}</span>
                @endif
            </span>
</li>
