@php
    $customCaptcha = loadCustomCaptcha('100%', 48, '#e8edf6');
    $googleCaptcha = loadReCaptcha();
@endphp

@if ($googleCaptcha)
    <div class="cm-form-group cm-form-captcha">
        <label>@lang('Captcha')</label>
        <div class="cm-captcha-widget">
            @php echo $googleCaptcha @endphp
        </div>
        <div id="g-recaptcha-error"></div>
    </div>
@endphp

@if ($customCaptcha)
    <div class="cm-form-group cm-form-captcha">
        <label for="contact_captcha">@lang('Captcha')</label>
        <div class="cm-captcha-visual">
            @php echo $customCaptcha @endphp
        </div>
        <input
            type="text"
            id="contact_captcha"
            name="captcha"
            class="cm-form-control"
            placeholder="@lang('Enter captcha')"
            required
        >
    </div>
@endphp

@if ($googleCaptcha)
    @push('script')
        <script>
            (function ($) {
                'use strict';
                $('.verify-gcaptcha').on('submit', function () {
                    var response = grecaptcha.getResponse();
                    if (response.length === 0) {
                        var el = document.getElementById('g-recaptcha-error');
                        if (el) {
                            el.innerHTML = '<span class="cm-captcha-error">@lang("Captcha field is required.")</span>';
                        }
                        return false;
                    }
                    return true;
                });
            })(jQuery);
        </script>
    @endpush
@endphp
