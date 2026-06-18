<?php

namespace App\Support;

class LegalPagesContent
{
    public static function privacyPolicyHtml(): string
    {
        return <<<'HTML'
<div class="legal-document">
<h2>Privacy Policy</h2>
<p><strong>Effective date:</strong> 18 June 2026</p>
<p>Crownmaire Capital ("Crownmaire", "we", "us", or "our") operates the Crownmaire client portal website and mobile application (together, the "Platform"). This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use the Platform.</p>
<p>By registering for an account or using the Platform, you agree to the collection and use of information in accordance with this Privacy Policy.</p>

<h3>1. Information We Collect</h3>
<p><strong>Account and identity information:</strong> name, username, email address, phone number, country, postal address, date of birth or age confirmation, government identification where required for KYC, profile photograph, and referral information.</p>
<p><strong>Financial and transaction information:</strong> deposit and withdrawal records, wallet balances, investment activity, transaction history, payment method details processed through our payment partners, and support ticket communications.</p>
<p><strong>Technical and usage information:</strong> IP address, browser or app type, device identifiers, operating system, access times, pages viewed, and cookies or similar technologies used to maintain sessions and improve security.</p>
<p><strong>Communications:</strong> messages you send to our support team, verification codes, and notification preferences.</p>

<h3>2. How We Use Your Information</h3>
<ul>
<li>To create, verify, and maintain your client account</li>
<li>To process deposits, withdrawals, transfers, and investment-related activity</li>
<li>To comply with KYC, AML, and other legal or regulatory obligations</li>
<li>To provide customer support and respond to inquiries</li>
<li>To send service notifications, security alerts, and account-related messages</li>
<li>To detect, prevent, and investigate fraud, abuse, or unauthorized access</li>
<li>To improve Platform performance, security, and user experience</li>
</ul>

<h3>3. Legal Basis for Processing</h3>
<p>We process personal data where necessary to perform our contract with you, comply with legal obligations, protect legitimate business interests (including fraud prevention and platform security), or with your consent where required.</p>

<h3>4. Sharing of Information</h3>
<p>We do not sell your personal information. We may share information with:</p>
<ul>
<li>Payment processors and banking partners to complete transactions</li>
<li>Identity verification and compliance service providers</li>
<li>Cloud hosting, email, and security vendors that assist in operating the Platform</li>
<li>Law enforcement, regulators, or courts when required by applicable law</li>
</ul>
<p>All third parties are required to protect your information and use it only for the services they provide to us.</p>

<h3>5. Data Security</h3>
<p>We implement administrative, technical, and organizational measures designed to protect your information against unauthorized access, alteration, disclosure, or destruction. No method of transmission over the internet or electronic storage is completely secure; we cannot guarantee absolute security.</p>

<h3>6. Data Retention</h3>
<p>We retain personal information for as long as your account is active and as necessary to fulfill the purposes described in this policy, comply with legal obligations, resolve disputes, and enforce our agreements.</p>

<h3>7. Your Rights</h3>
<p>Depending on your location, you may have the right to access, correct, update, or delete your personal information, restrict or object to certain processing, or withdraw consent where processing is consent-based. To exercise these rights, contact us using the details below.</p>

<h3>8. Cookies and Mobile App Data</h3>
<p>Our website and mobile app may use cookies, local storage, and secure tokens to keep you signed in, remember preferences, and protect accounts. You can control cookies through your browser settings; disabling them may limit Platform functionality.</p>

<h3>9. International Transfers</h3>
<p>Your information may be processed in countries other than your own. Where required, we implement appropriate safeguards for cross-border data transfers.</p>

<h3>10. Children's Privacy</h3>
<p>The Platform is not intended for individuals under 18 years of age. We do not knowingly collect personal information from children. If you believe a child has provided us data, contact us so we can delete it.</p>

<h3>11. Changes to This Policy</h3>
<p>We may update this Privacy Policy from time to time. Updated versions will be posted on this page with a revised effective date. Continued use of the Platform after changes constitutes acceptance of the updated policy.</p>

<h3>12. Contact Us</h3>
<p>If you have questions about this Privacy Policy or our data practices, contact:</p>
<p><strong>Crownmaire Capital</strong><br>
Email: <a href="mailto:Info@crownmaire.com">Info@crownmaire.com</a><br>
Website: <a href="https://crownmairecapital.com">https://crownmairecapital.com</a></p>
</div>
HTML;
    }

    public static function termsAndConditionsHtml(): string
    {
        return <<<'HTML'
<div class="legal-document">
<h2>Terms and Conditions</h2>
<p><strong>Effective date:</strong> 18 June 2026</p>
<p>These Terms and Conditions ("Terms") govern your access to and use of the Crownmaire Capital client portal website and mobile application (the "Platform"). By creating an account or using the Platform, you agree to these Terms.</p>
<p>If you do not agree, do not use the Platform.</p>

<h3>1. About Crownmaire</h3>
<p>Crownmaire Capital provides an online client portal for account management, investment-related services, wallet transactions, reporting, and support. Product features, availability, and eligibility may vary by jurisdiction and account type.</p>

<h3>2. Eligibility</h3>
<p>You must be at least 18 years old and legally capable of entering into binding agreements. You represent that all registration information is accurate, complete, and current. We may refuse or terminate access if eligibility requirements are not met.</p>

<h3>3. Account Registration and Security</h3>
<ul>
<li>You are responsible for maintaining the confidentiality of your login credentials</li>
<li>You must notify us immediately of any unauthorized access or suspected breach</li>
<li>You are responsible for activity conducted through your account unless caused by our gross negligence</li>
<li>We may require identity verification (KYC) before enabling deposits, withdrawals, or certain features</li>
</ul>

<h3>4. Platform Use</h3>
<p>You agree to use the Platform only for lawful purposes and in accordance with these Terms. You must not:</p>
<ul>
<li>Provide false, misleading, or fraudulent information</li>
<li>Attempt to gain unauthorized access to systems, accounts, or data</li>
<li>Use the Platform for money laundering, fraud, or illegal financial activity</li>
<li>Interfere with Platform security, performance, or other users</li>
<li>Copy, scrape, reverse engineer, or resell Platform content without permission</li>
</ul>

<h3>5. Deposits, Withdrawals, and Wallets</h3>
<p>Deposits and withdrawals are subject to available payment methods, verification status, processing times, fees (if any), minimum and maximum limits, and compliance review. We may delay, reject, or reverse transactions that appear suspicious or violate applicable law or these Terms.</p>
<p>Wallet balances and transaction records displayed on the Platform are provided for account management purposes and should be verified against official statements where applicable.</p>

<h3>6. Investments and Performance</h3>
<p>Any investment products, strategies, performance figures, projections, or historical returns displayed on the Platform are for informational purposes unless otherwise stated in a binding agreement. Past performance does not guarantee future results. All investments involve risk, including possible loss of capital.</p>
<p>You are solely responsible for your investment decisions unless otherwise agreed in writing under a separate client agreement.</p>

<h3>7. Fees and Charges</h3>
<p>Applicable fees, spreads, charges, or deductions will be disclosed on the Platform, in your account dashboard, or in separate product documentation. We may update fee schedules with notice where required.</p>

<h3>8. Intellectual Property</h3>
<p>All Platform content, branding, software, design, and materials are owned by Crownmaire or its licensors and protected by intellectual property laws. You receive a limited, non-exclusive, non-transferable license to access the Platform for personal account use.</p>

<h3>9. Suspension and Termination</h3>
<p>We may suspend or terminate your account if you breach these Terms, fail verification requirements, engage in prohibited conduct, or where required by law or regulatory request. You may request account closure by contacting support, subject to settlement of outstanding obligations.</p>

<h3>10. Disclaimers</h3>
<p>The Platform is provided on an "as is" and "as available" basis. To the fullest extent permitted by law, we disclaim warranties of merchantability, fitness for a particular purpose, and non-infringement. We do not warrant uninterrupted or error-free operation.</p>

<h3>11. Limitation of Liability</h3>
<p>To the maximum extent permitted by law, Crownmaire shall not be liable for indirect, incidental, special, consequential, or punitive damages, or for trading losses arising from your decisions, market conditions, third-party service failures, or events beyond our reasonable control.</p>

<h3>12. Indemnity</h3>
<p>You agree to indemnify and hold harmless Crownmaire, its officers, employees, and partners from claims, losses, and expenses arising from your misuse of the Platform or violation of these Terms.</p>

<h3>13. Privacy</h3>
<p>Your use of the Platform is also governed by our Privacy Policy, which explains how we collect and use personal data.</p>

<h3>14. Changes to These Terms</h3>
<p>We may modify these Terms at any time by posting an updated version on the Platform. Material changes may also be communicated by email or in-app notice. Continued use after the effective date constitutes acceptance.</p>

<h3>15. Governing Law and Disputes</h3>
<p>These Terms are governed by the laws applicable to Crownmaire's operating jurisdiction, without regard to conflict-of-law principles. Disputes shall be resolved through the courts or dispute resolution forum specified in your client agreement, or otherwise in accordance with applicable law.</p>

<h3>16. Contact</h3>
<p>For questions about these Terms, contact:</p>
<p><strong>Crownmaire Capital</strong><br>
Email: <a href="mailto:Info@crownmaire.com">Info@crownmaire.com</a><br>
Website: <a href="https://crownmairecapital.com">https://crownmairecapital.com</a></p>
</div>
HTML;
    }
}
