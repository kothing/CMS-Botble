<?php

namespace Botble\Member\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\SendsPasswordResetEmails;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        SeoHelper::setTitle(trans('plugins/member::member.forgot_password'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.member.auth.passwords.email')) {
            return Theme::scope('member.auth.passwords.email')->render();
        }

        return view('plugins/member::auth.passwords.email');
    }

    public function broker()
    {
        return Password::broker('members');
    }
}
