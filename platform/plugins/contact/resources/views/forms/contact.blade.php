{!! Form::open(['route' => 'public.send.contact', 'method' => 'POST', 'class' => 'contact-form']) !!}
    <div class="contact-form-row">
        {!! apply_filters('pre_contact_form', null) !!}

        <div class="contact-column-6">
            <div class="contact-form-group">
                <label for="contact_name" class="contact-label required">{{ __('Name') }}</label>
                <input type="text" class="contact-form-input" name="name" value="{{ old('name') }}" id="contact_name"
                       placeholder="{{ __('Name') }}">
            </div>
        </div>
        <div class="contact-column-6">
            <div class="contact-form-group">
                <label for="contact_email" class="contact-label required">{{ __('Email') }}</label>
                <input type="email" class="contact-form-input" name="email" value="{{ old('email') }}" id="contact_email"
                       placeholder="{{ __('Email') }}">
            </div>
        </div>
    </div>
    <div class="contact-form-row">
        <div class="contact-column-6">
            <div class="contact-form-group">
                <label for="contact_address" class="contact-label">{{ __('Address') }}</label>
                <input type="text" class="contact-form-input" name="address" value="{{ old('address') }}" id="contact_address"
                       placeholder="{{ __('Address') }}">
            </div>
        </div>
        <div class="contact-column-6">
            <div class="contact-form-group">
                <label for="contact_phone" class="contact-label">{{ __('Phone') }}</label>
                <input type="text" class="contact-form-input" name="phone" value="{{ old('phone') }}" id="contact_phone"
                       placeholder="{{ __('Phone') }}">
            </div>
        </div>
    </div>
    <div class="contact-form-row">
        <div class="contact-column-12">
            <div class="contact-form-group">
                <label for="contact_subject" class="contact-label">{{ __('Subject') }}</label>
                <input type="text" class="contact-form-input" name="subject" value="{{ old('subject') }}" id="contact_subject"
                       placeholder="{{ __('Subject') }}">
            </div>
        </div>
    </div>
    <div class="contact-form-row">
        <div class="contact-column-12">
            <div class="contact-form-group">
                <label for="contact_content" class="contact-label required">{{ __('Message') }}</label>
                <textarea name="content" id="contact_content" class="contact-form-input" rows="5" placeholder="{{ __('Message') }}">{{ old('content') }}</textarea>
            </div>
        </div>
    </div>

    @if (is_plugin_active('captcha'))
        @if (Captcha::isEnabled())
            <div class="contact-form-row">
                <div class="contact-column-12">
                    <div class="contact-form-group">
                        {!! Captcha::display() !!}
                    </div>
                </div>
            </div>
        @endif

        @if (setting('enable_math_captcha_for_contact_form', 0))
            <div class="contact-form-group">
                <label for="math-group" class="contact-label required">{{ app('math-captcha')->label() }}</label>
                {!! app('math-captcha')->input(['class' => 'contact-form-input', 'id' => 'math-group']) !!}
            </div>
        @endif
    @endif

    {!! apply_filters('after_contact_form', null) !!}

    <div class="contact-form-group"><p>{!! BaseHelper::clean(__('The field with (<span style="color:#FF0000;">*</span>) is required.')) !!}</p></div>

    <div class="contact-form-group">
        <button type="submit" class="contact-button">{{ __('Send') }}</button>
    </div>

    <div class="contact-form-group">
        <div class="contact-message contact-success-message" style="display: none"></div>
        <div class="contact-message contact-error-message" style="display: none"></div>
    </div>

{!! Form::close() !!}
