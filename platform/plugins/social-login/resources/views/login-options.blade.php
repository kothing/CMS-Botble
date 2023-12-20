@if (SocialService::hasAnyProviderEnable())
    <div class="login-options">
        <br>
        <p style="font-size: 14px">{{ __('Login with social networks') }}</p>
        <ul class="social-icons">
            @foreach (SocialService::getProviderKeys() as $item)
                @if (SocialService::getProviderEnabled($item))
                    <li>
                        <a class="{{ $item }}" data-bs-toggle="tooltip" data-bs-original-title="{{ $item }}"
                           href="{{ route('auth.social', isset($params) ? array_merge([$item], $params) : $item) }}"></a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endif
