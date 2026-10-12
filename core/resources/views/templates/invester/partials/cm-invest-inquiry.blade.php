@php
    $inquiryUser = auth()->user();
    $selectedReason = old('subject', 'Investment');
    $showOffices = $showOffices ?? false;
@endphp

<section class="cm-section cm-section--inquiry" id="inquiry">
    <div class="cm-container">
        <div class="cm-contact-layout">
            <div class="cm-contact-info cm-reveal">
                <span class="cm-section__tag">@lang('Investment inquiry')</span>
                <h2 class="cm-split__title">@lang('Tell us you want to invest')</h2>
                <p class="cm-contact-info__text">@lang('Send your name, phone, and approximate capital range. Crownmaire reviews the inquiry and contacts you about private quantitative programs. This form is for new investment inquiries, not member account support.')</p>
                <ul class="cm-inquiry-steps">
                    <li><strong>01</strong> @lang('You send the inquiry')</li>
                    <li><strong>02</strong> @lang('We review fit and eligibility')</li>
                    <li><strong>03</strong> @lang('We contact you by phone or email')</li>
                </ul>
                @if ($showOffices)
                    <ul class="cm-contact-details">
                        <li>
                            <i class="las la-phone"></i>
                            <div>
                                <span>@lang('Phone')</span>
                                <a href="tel:+19175006476">+1 917 500 6476</a>
                            </div>
                        </li>
                        <li>
                            <i class="las la-envelope"></i>
                            <div>
                                <span>@lang('Email')</span>
                                <a href="mailto:Info@crownmaire.com">Info@crownmaire.com</a>
                            </div>
                        </li>
                        <li>
                            <i class="las la-map-marker"></i>
                            <div>
                                <span>@lang('New York')</span>
                                <p>100 Wall Street Ct, New York, NY 10005</p>
                            </div>
                        </li>
                        <li>
                            <i class="las la-map-marker"></i>
                            <div>
                                <span>@lang('Dubai')</span>
                                <p>2402 Al-Manara Tower, Business Bay, Dubai</p>
                            </div>
                        </li>
                    </ul>
                @else
                    <p class="cm-inquiry-direct">
                        @lang('Prefer to call?')
                        <a href="tel:+19175006476">+1 917 500 6476</a>
                        <span>@lang('or')</span>
                        <a href="mailto:Info@crownmaire.com">Info@crownmaire.com</a>
                    </p>
                @endif
            </div>

            <div class="cm-contact-form-wrap cm-reveal cm-reveal--delay">
                <form action="{{ route('contact') }}" class="cm-contact-form" method="post">
                    @csrf
                    <div class="cm-hp" hidden aria-hidden="true">
                        <label for="cm_leave_blank">Leave blank</label>
                        <input type="text" id="cm_leave_blank" name="cm_leave_blank" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="cm-form-row cm-form-row--2">
                        <div class="cm-form-group">
                            <label for="contact_name">@lang('Name')</label>
                            <input type="text" id="contact_name" name="name" class="cm-form-control" value="{{ old('name', @$inquiryUser->fullname) }}" @if ($inquiryUser) readonly @endif required>
                        </div>
                        <div class="cm-form-group">
                            <label for="contact_company">@lang('Company') <span>@lang('optional')</span></label>
                            <input type="text" id="contact_company" name="company_name" class="cm-form-control" value="{{ old('company_name') }}">
                        </div>
                    </div>
                    <div class="cm-form-row cm-form-row--2">
                        <div class="cm-form-group">
                            <label for="contact_email">@lang('Email')</label>
                            <input type="email" id="contact_email" name="email" class="cm-form-control" value="{{ old('email', @$inquiryUser->email) }}" @if ($inquiryUser) readonly @endif required>
                        </div>
                        <div class="cm-form-group">
                            <label for="contact_phone">@lang('Phone')</label>
                            <input type="tel" id="contact_phone" name="phone" class="cm-form-control" value="{{ old('phone') }}" placeholder="+1 917 000 0000" required>
                        </div>
                    </div>
                    <div class="cm-form-row cm-form-row--2">
                        <div class="cm-form-group">
                            <label for="contact_reason">@lang('Reason')</label>
                            <select id="contact_reason" name="subject" class="cm-form-control" required>
                                @foreach (\App\Models\SupportTicket::inquiryReasons() as $reason)
                                    <option value="{{ $reason }}" @selected($selectedReason === $reason)>@lang($reason)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="cm-form-group">
                            <label for="contact_capital">@lang('Approximate capital')</label>
                            <select id="contact_capital" name="capital_range" class="cm-form-control" required>
                                <option value="" disabled @selected(!old('capital_range'))>@lang('Select a range')</option>
                                @foreach (\App\Models\SupportTicket::capitalRanges() as $range)
                                    <option value="{{ $range }}" @selected(old('capital_range') === $range)>@lang($range)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="cm-form-group">
                        <label for="contact_message">@lang('What do you want to discuss?')</label>
                        <textarea id="contact_message" name="message" class="cm-form-control" rows="4" placeholder="@lang('For example: I want to discuss a private investment program.')" required>{{ old('message') }}</textarea>
                    </div>
                    <div class="cm-form-footer">
                        <button type="submit" class="cm-btn cm-btn--accent cm-form-footer__submit">
                            <i class="las la-paper-plane"></i> @lang('Send investment inquiry')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
