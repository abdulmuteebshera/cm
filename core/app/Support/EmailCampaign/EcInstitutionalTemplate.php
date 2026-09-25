<?php

namespace App\Support\EmailCampaign;

class EcInstitutionalTemplate
{
    public const SLUG_NAME = 'Crownmaire — Institutional Invitation';

    public static function siteUrl(): string
    {
        return 'https://crownmairecapital.com';
    }

    public static function logoUrl(): string
    {
        return self::siteUrl() . '/assets/images/logoIcon/logo_2.png';
    }

    public static function subject(): string
    {
        return 'Crownmaire Capital | Quantitative Asset Management for Qualified Investors';
    }

    public static function bodyHtml(): string
    {
        $logo = self::logoUrl();
        $site = self::siteUrl();

        return <<<HTML
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<title>Crownmaire Capital</title>
<!--[if mso]><style type="text/css">body,table,td{font-family:Arial,Helvetica,sans-serif!important;}</style><![endif]-->
</head>
<body style="margin:0;padding:0;width:100%!important;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;background-color:#dfe8f4;">
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#dfe8f4;">
Structured yield. Quant-driven performance. Private by design. — Crownmaire Capital
</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#dfe8f4;">
<tr>
<td align="center" style="padding:40px 16px;">
<table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0" style="max-width:640px;width:100%;">

<tr>
<td style="background-color:#030712;padding:36px 40px 32px;text-align:center;border-radius:16px 16px 0 0;">
<img src="{$logo}" width="220" alt="Crownmaire Capital" style="display:block;margin:0 auto 24px;border:0;outline:none;text-decoration:none;max-width:220px;height:auto;">
<div style="height:1px;width:72px;background-color:#1bb0ce;margin:0 auto 20px;"></div>
<p style="margin:0 0 12px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:11px;font-weight:600;letter-spacing:0.28em;text-transform:uppercase;color:#1bb0ce;">Private Investment Programs</p>
<h1 style="margin:0 0 16px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:26px;line-height:1.35;font-weight:600;color:#ffffff;">
Quantitative Fintech-Driven Algorithmic Asset Management
</h1>
<p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#94a3b8;font-style:italic;">
Structured yield.&nbsp;&nbsp;Quant-driven performance.&nbsp;&nbsp;Private by design.
</p>
</td>
</tr>

<tr>
<td style="background-color:#ffffff;padding:40px 40px 8px;">
<p style="margin:0 0 20px;font-family:Georgia,'Times New Roman',Times,serif;font-size:17px;line-height:1.5;color:#0c1222;">Dear {{name}},</p>
<p style="margin:0 0 20px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:16px;line-height:1.75;color:#4a5d78;">
Crownmaire Capital is a modern investment management firm deploying quantitative and algorithmic trading strategies across global markets. We operate at the intersection of finance, technology, and disciplined governance—built on precision, powered by data, and refined through active risk management.
</p>
<p style="margin:0 0 28px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;color:#0033ad;">What we do</p>
</td>
</tr>

<tr>
<td style="background-color:#ffffff;padding:0 40px 8px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top" width="50%" style="padding:0 12px 24px 0;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#edf2f9;border-radius:12px;border:1px solid rgba(0,51,173,0.08);">
<tr><td style="padding:22px 20px;">
<p style="margin:0 0 8px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:700;color:#0c1222;">Quantitative Precision</p>
<p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;line-height:1.65;color:#4a5d78;">Data science, machine learning, and real-time algorithmic execution. Proprietary quantitative models designed to identify market inefficiencies and generate risk-adjusted returns across multiple asset classes.</p>
</td></tr>
</table>
</td>
<td valign="top" width="50%" style="padding:0 0 24px 12px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#edf2f9;border-radius:12px;border:1px solid rgba(0,51,173,0.08);">
<tr><td style="padding:22px 20px;">
<p style="margin:0 0 8px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:700;color:#0c1222;">Multi-Asset Structured Yield</p>
<p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;line-height:1.65;color:#4a5d78;">Diversified exposure across currencies, indices, commodities, futures, and select equities—structured to balance opportunity with risk across varying market conditions.</p>
</td></tr>
</table>
</td>
</tr>
<tr>
<td valign="top" width="50%" style="padding:0 12px 24px 0;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#edf2f9;border-radius:12px;border:1px solid rgba(0,51,173,0.08);">
<tr><td style="padding:22px 20px;">
<p style="margin:0 0 8px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:700;color:#0c1222;">Fintech Infrastructure</p>
<p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;line-height:1.65;color:#4a5d78;">Proprietary trading systems and automation frameworks enable scalable execution with institutional precision—plus secure access to reporting, performance summaries, and capital activity.</p>
</td></tr>
</table>
</td>
<td valign="top" width="50%" style="padding:0 0 24px 12px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#edf2f9;border-radius:12px;border:1px solid rgba(0,51,173,0.08);">
<tr><td style="padding:22px 20px;">
<p style="margin:0 0 8px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:700;color:#0c1222;">Investor-First Philosophy</p>
<p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;line-height:1.65;color:#4a5d78;">Access is private and invitation-only. We work with a select group of qualified participants through structured arrangements emphasizing transparency, disciplined execution, and long-term alignment.</p>
</td></tr>
</table>
</td>
</tr>
</table>
</td>
</tr>

<tr>
<td style="background-color:#ffffff;padding:8px 40px 32px;">
<p style="margin:0 0 14px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#0033ad;">Global market allocation framework</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
<tr style="background-color:#0c1222;">
<td colspan="2" style="padding:14px 18px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:12px;font-weight:600;color:#ffffff;letter-spacing:0.06em;">MULTI-ASSET EXPOSURE · 5 MARKETS</td>
</tr>
<tr style="background-color:#ffffff;"><td style="padding:12px 18px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;color:#0c1222;border-bottom:1px solid #eef2f7;">Commodities</td><td align="right" style="padding:12px 18px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;color:#0033ad;border-bottom:1px solid #eef2f7;">33%</td></tr>
<tr style="background-color:#f8fafc;"><td style="padding:12px 18px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;color:#0c1222;border-bottom:1px solid #eef2f7;">Forex</td><td align="right" style="padding:12px 18px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;color:#0033ad;border-bottom:1px solid #eef2f7;">20%</td></tr>
<tr style="background-color:#ffffff;"><td style="padding:12px 18px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;color:#0c1222;border-bottom:1px solid #eef2f7;">Indices</td><td align="right" style="padding:12px 18px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;color:#0033ad;border-bottom:1px solid #eef2f7;">18%</td></tr>
<tr style="background-color:#f8fafc;"><td style="padding:12px 18px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;color:#0c1222;">Crypto</td><td align="right" style="padding:12px 18px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;color:#0033ad;">14%</td></tr>
</table>
</td>
</tr>

<tr>
<td style="background-color:#ffffff;padding:0 40px 32px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#0c1222;border-radius:12px;">
<tr><td style="padding:28px 28px 20px;">
<p style="margin:0 0 8px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:18px;font-weight:600;color:#ffffff;">Institutional-grade visibility</p>
<p style="margin:0 0 16px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;line-height:1.65;color:#94a3b8;">Secure, real-time visibility into capital activity, performance charts, and portfolio allocations—designed for transparency and informed oversight.</p>
<p style="margin:0 0 6px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;color:#e2e8f0;">&#10003;&nbsp; Real-time performance analytics</p>
<p style="margin:0 0 6px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;color:#e2e8f0;">&#10003;&nbsp; Portfolio allocation visibility</p>
<p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;color:#e2e8f0;">&#10003;&nbsp; Capital activity reporting</p>
</td></tr>
</table>
</td>
</tr>

<tr>
<td style="background-color:#ffffff;padding:0 40px 36px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-left:3px solid #1bb0ce;background-color:#f0f7ff;">
<tr><td style="padding:20px 22px;">
<p style="margin:0 0 8px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:15px;font-weight:700;color:#0c1222;">Reserved for qualified investors</p>
<p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;line-height:1.7;color:#4a5d78;">Participation in Crownmaire's private investment programs is limited to qualified individuals and entities in order to preserve performance discipline, service quality, and operational integrity. Capital is managed under strict risk and governance frameworks, including KYC and AML standards.</p>
</td></tr>
</table>
</td>
</tr>

<tr>
<td style="background-color:#ffffff;padding:0 40px 40px;text-align:center;">
<p style="margin:0 0 8px;font-family:Georgia,'Times New Roman',Times,serif;font-size:20px;line-height:1.4;color:#0c1222;">Access the future of smart investment strategies</p>
<p style="margin:0 0 24px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#4a5d78;">Connect with our team to learn how Crownmaire's quantitative programs align with your capital objectives.</p>
<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
<tr>
<td style="border-radius:8px;background-color:#0033ad;">
<a href="{$site}" target="_blank" style="display:inline-block;padding:16px 36px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:700;letter-spacing:0.04em;color:#ffffff;text-decoration:none;">Visit crownmairecapital.com</a>
</td>
</tr>
</table>
<p style="margin:28px 0 0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#4a5d78;text-align:left;">
With distinguished regards,<br>
<strong style="color:#0c1222;">Crownmaire Capital</strong><br>
<span style="font-size:13px;color:#7b8ba8;">Investor Relations</span>
</p>
</td>
</tr>

<tr>
<td style="background-color:#e8edf6;padding:28px 40px;border-radius:0 0 16px 16px;border-top:1px solid #dde4ef;">
<p style="margin:0 0 12px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:11px;line-height:1.65;color:#7b8ba8;text-align:center;">
<strong style="color:#4a5d78;">Crownmaire Capital</strong><br>
Quantitative Fintech-Driven Algorithmic Asset Management and Multi-Asset Investment Firm<br>
<a href="{$site}" style="color:#0033ad;text-decoration:none;">crownmairecapital.com</a>
</p>
<p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:10px;line-height:1.6;color:#94a3b8;text-align:center;">
This communication is intended solely for {{email}}. It does not constitute an offer, solicitation, or recommendation to invest. All programs involve risk, including possible loss of principal; past performance is not indicative of future results. Participation is subject to qualification, contractual terms, and applicable law.
</p>
</td>
</tr>

</table>
</td>
</tr>
</table>
</body>
</html>
HTML;
    }

    public static function bodyText(): string
    {
        return <<<'TEXT'
CROWNMAIRE CAPITAL
Structured yield. Quant-driven performance. Private by design.

Dear {{name}},

Crownmaire Capital is a modern investment management firm deploying quantitative and algorithmic trading strategies across global markets. We operate at the intersection of finance, technology, and disciplined governance. Built on precision, powered by data, and refined through active risk management.

QUANTITATIVE PRECISION ACROSS GLOBAL MARKETS
Our investment approach is rooted in data science, machine learning, and real-time algorithmic execution. Decisions are informed by proprietary quantitative models designed to identify market inefficiencies and generate risk-adjusted returns across multiple asset classes.

MULTI-ASSET EXPOSURE WITH STRUCTURED YIELD
Crownmaire manages diversified exposure across currencies, indices, commodities, futures, and select equities. Portfolios are structured to balance opportunity with risk, allowing flexibility and adaptability across varying market conditions.

FINTECH INFRASTRUCTURE BUILT FOR PERFORMANCE
Our proprietary trading systems and automation frameworks enable scalable execution with institutional precision. Members receive secure access to reporting, performance summaries, and capital activity through a protected, institutional-grade platform.

EXCLUSIVE, INVESTOR-FIRST PHILOSOPHY
Access to Crownmaire's investment programs is private and invitation-only. We work with a select group of qualified participants through structured investment arrangements, emphasizing transparency, disciplined execution, and long-term alignment.

GLOBAL ALLOCATION FRAMEWORK (5 MARKETS)
Commodities 33% | Forex 20% | Indices 18% | Crypto 14%

INSTITUTIONAL-GRADE DASHBOARD
- Real-time performance analytics
- Portfolio allocation visibility
- Capital activity reporting

Crownmaire is reserved for qualified investors. Capital is managed under strict internal risk and governance frameworks, including KYC and AML standards.

Access the future of smart investment strategies.
Connect with our team: https://crownmairecapital.com

With distinguished regards,
Crownmaire Capital
Investor Relations

---
Intended for {{email}} only. Not an offer or solicitation. Investing involves risk.
TEXT;
    }
}
