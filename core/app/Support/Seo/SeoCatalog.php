<?php

namespace App\Support\Seo;

use App\Models\Frontend;
use App\Models\JobPost;
use App\Models\Page;

class SeoCatalog
{
    public static function resolve(array $viewData = []): object
    {
        $route = optional(request()->route())->getName() ?: '';
        $pages = self::pages();
        $page  = $pages[$route] ?? self::fallback($viewData);

        if (!empty($viewData['seoContents'])) {
            $override = is_array($viewData['seoContents'])
                ? $viewData['seoContents']
                : (array) $viewData['seoContents'];
            $page['title']       = $override['social_title'] ?? $page['title'];
            $page['description'] = $override['description'] ?? $page['description'];
            $page['keywords']    = $override['keywords'] ?? $page['keywords'];
        }

        if ($route === 'careers.apply' && !empty($viewData['jobPost'])) {
            /** @var JobPost $job */
            $job = $viewData['jobPost'];
            $page['title'] = 'Apply — ' . $job->title . ' | ' . SeoSite::BRAND . ' Careers';
            $page['description'] = 'Apply for the ' . $job->title . ' role at Crownmaire Capital, a quantitative asset management firm.';
            $page['h1'] = 'Apply — ' . $job->title;
            $page['job'] = $job;
        }

        if ($route === 'policy.pages' && !empty($viewData['policy'])) {
            $policy = $viewData['policy'];
            $title  = $policy->data_values->title ?? ($viewData['pageTitle'] ?? 'Legal');
            $page['title'] = $title . ' | ' . SeoSite::BRAND;
            $page['description'] = strLimit(strip_tags((string) ($policy->data_values->details ?? $page['description'])), 155);
            $page['h1'] = $title;
        }

        if ($route === 'pages' && !empty($viewData['pageTitle'])) {
            $page['title'] = $viewData['pageTitle'] . ' | ' . SeoSite::BRAND;
            $page['h1'] = $viewData['pageTitle'];
        }

        $page['route']      = $route;
        $page['canonical']  = $page['canonical'] ?? SeoSite::currentCanonical();
        $page['indexable']  = $page['indexable'] ?? self::isPublicIndexable($route);
        $page['robots']     = $page['indexable'] ? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' : 'noindex, nofollow';
        $page['keywords']   = $page['keywords'] ?? SeoSite::defaultKeywords();
        $page['og_type']    = $page['og_type'] ?? 'website';
        $page['breadcrumbs'] = $page['breadcrumbs'] ?? self::breadcrumbsFor($route, $page);

        $social = SeoSite::socialImage();
        $page['image']        = $page['image'] ?? $social['url'];
        $page['image_width']  = $social['width'];
        $page['image_height'] = $social['height'];

        return (object) $page;
    }

    public static function isPublicIndexable(string $route): bool
    {
        if ($route === '' || $route === 'placeholder.image') {
            return false;
        }

        $blocked = [
            'admin.', 'crm.', 'user.', 'ticket.', 'deposit.', 'ipn',
            'cron', 'seo.beacon', 'ec.', 'emailcampaign',
        ];
        foreach ($blocked as $prefix) {
            if ($route === rtrim($prefix, '.') || str_starts_with($route, $prefix)) {
                return false;
            }
        }

        return isset(self::pages()[$route]) || in_array($route, ['pages', 'blogs', 'blog.details', 'policy.pages'], true);
    }

    public static function pages(): array
    {
        $brand = SeoSite::BRAND;

        return [
            'home' => [
                'title'       => $brand . ' | Quantitative Asset Management Firm',
                'description' => SeoSite::defaultDescription(),
                'h1'          => 'Quantitative Fintech-Driven Algorithmic Asset Management',
                'keywords'    => SeoSite::defaultKeywords(),
                'schemas'     => ['organization', 'website', 'webpage', 'financial', 'faq', 'breadcrumb'],
            ],
            'about' => [
                'title'       => 'About ' . $brand . ' | Investment Management Firm',
                'description' => 'Crownmaire Capital is a quantitative investment management firm operating in New York and Dubai. Learn how we manage capital through research, algorithms, and institutional reporting.',
                'h1'          => 'Redefining Asset Management Through Precision and Technology',
                'keywords'    => array_merge(SeoSite::defaultKeywords(), ['about Crownmaire Capital', 'investment management firm New York', 'Dubai asset manager']),
                'schemas'     => ['organization', 'website', 'webpage', 'financial', 'about', 'breadcrumb'],
            ],
            'strategies' => [
                'title'       => 'Investment Strategies | ' . $brand,
                'description' => 'Explore Crownmaire Capital invitation-only quantitative, multi-asset, and capital-preservation programs designed for qualified investors.',
                'h1'          => 'Structured programs built for quantitative performance',
                'keywords'    => array_merge(SeoSite::defaultKeywords(), ['investment strategies', 'quantitative strategies', 'multi-asset programs']),
                'schemas'     => ['organization', 'website', 'webpage', 'financial', 'breadcrumb'],
            ],
            'plan' => [
                'title'       => 'Investment Programs | ' . $brand,
                'description' => 'Review Crownmaire Capital structured investment programs and request access as a qualified participant.',
                'h1'          => 'Investment Programs',
                'keywords'    => array_merge(SeoSite::defaultKeywords(), ['investment programs', 'private investment access']),
                'schemas'     => ['organization', 'website', 'webpage', 'financial', 'breadcrumb'],
            ],
            'markets' => [
                'title'       => 'Markets & Insights | ' . $brand,
                'description' => 'Follow global market indices and financial news curated for Crownmaire Capital investors and institutional counterparties.',
                'h1'          => 'Global market intelligence',
                'keywords'    => array_merge(SeoSite::defaultKeywords(), ['market insights', 'global markets', 'financial news']),
                'schemas'     => ['organization', 'website', 'webpage', 'breadcrumb'],
            ],
            'insights' => [
                'title'       => 'Markets & Insights | ' . $brand,
                'description' => 'Follow global market indices and financial news curated for Crownmaire Capital investors.',
                'h1'          => 'Markets & Insights',
                'keywords'    => SeoSite::defaultKeywords(),
                'schemas'     => ['organization', 'website', 'webpage', 'breadcrumb'],
            ],
            'platform' => [
                'title'       => 'Investor Platform | ' . $brand . ' Member Portal',
                'description' => 'The Crownmaire Capital portal gives members institutional reporting, performance analytics, and secure capital oversight.',
                'h1'          => 'An institutional-grade portal built for Crownmaire members',
                'keywords'    => array_merge(SeoSite::defaultKeywords(), ['investor portal', 'investment reporting platform']),
                'schemas'     => ['organization', 'website', 'webpage', 'software', 'breadcrumb'],
            ],
            'careers' => [
                'title'       => 'Careers | ' . $brand,
                'description' => 'Join Crownmaire Capital quantitative research, technology, and investment operations teams in New York and Dubai.',
                'h1'          => 'Build the Future of Quantitative Asset Management',
                'keywords'    => array_merge(SeoSite::defaultKeywords(), ['asset management careers', 'quant jobs New York', 'fintech careers Dubai']),
                'schemas'     => ['organization', 'website', 'webpage', 'breadcrumb'],
            ],
            'careers.apply' => [
                'title'       => 'Apply for a Role | ' . $brand,
                'description' => 'Submit your application to join Crownmaire Capital.',
                'h1'          => 'Apply',
                'keywords'    => ['Crownmaire careers', 'job application'],
                'schemas'     => ['organization', 'website', 'webpage', 'job', 'breadcrumb'],
            ],
            'contact' => [
                'title'       => 'Contact ' . $brand . ' | Request an Invitation',
                'description' => 'Contact Crownmaire Capital in New York or Dubai to request an invitation, private consultation, or institutional partnership.',
                'h1'          => 'We welcome serious inquiries from eligible investors and institutions',
                'keywords'    => array_merge(SeoSite::defaultKeywords(), ['contact Crownmaire Capital', 'request investment invitation']),
                'schemas'     => ['organization', 'website', 'webpage', 'contact', 'breadcrumb'],
            ],
            'cookie.policy' => [
                'title'       => 'Cookie Policy | ' . $brand,
                'description' => 'How Crownmaire Capital uses cookies and similar technologies on crownmairecapital.com.',
                'h1'          => 'Cookie Policy',
                'keywords'    => ['cookie policy', 'Crownmaire Capital privacy'],
                'schemas'     => ['organization', 'website', 'webpage', 'breadcrumb'],
            ],
            'policy.pages' => [
                'title'       => 'Legal | ' . $brand,
                'description' => 'Legal policies for Crownmaire Capital clients and website visitors.',
                'h1'          => 'Legal',
                'keywords'    => ['privacy policy', 'terms and conditions', 'Crownmaire Capital'],
                'schemas'     => ['organization', 'website', 'webpage', 'breadcrumb'],
            ],
            'blogs' => [
                'title'       => 'Insights | ' . $brand,
                'description' => 'Commentary and insights from Crownmaire Capital.',
                'h1'          => 'Insights',
                'keywords'    => SeoSite::defaultKeywords(),
                'schemas'     => ['organization', 'website', 'webpage', 'breadcrumb'],
            ],
        ];
    }

    public static function fallback(array $viewData = []): array
    {
        $pageTitle = $viewData['pageTitle'] ?? 'Page';
        $route     = optional(request()->route())->getName() ?: '';

        return [
            'title'       => SeoSite::BRAND . ( $pageTitle ? ' | ' . $pageTitle : ''),
            'description' => SeoSite::defaultDescription(),
            'h1'          => $pageTitle,
            'keywords'    => SeoSite::defaultKeywords(),
            'schemas'     => self::isPublicIndexable($route)
                ? ['organization', 'website', 'webpage']
                : [],
            'indexable'   => self::isPublicIndexable($route),
        ];
    }

    public static function homeFaqs(): array
    {
        return [
            [
                'q' => 'What is Crownmaire Capital?',
                'a' => 'Crownmaire Capital is a quantitative asset management firm that manages private, invitation-only investment programs for qualified investors. The firm combines data science, algorithmic execution, and institutional reporting across global markets, with offices in New York and Dubai.',
            ],
            [
                'q' => 'How does Crownmaire manage capital across its strategies?',
                'a' => 'Crownmaire deploys proprietary quantitative and algorithmic trading strategies informed by data science, machine learning, and real-time execution systems. These strategies operate across multiple global markets, including foreign exchange, indices, commodities, and equities, under defined risk and exposure parameters.',
            ],
            [
                'q' => 'Who can invest with Crownmaire Capital?',
                'a' => 'Access is private and invitation-only. Crownmaire works with a select group of qualified high-net-worth individuals and institutional counterparties through structured contractual arrangements, subject to KYC, AML, and eligibility review.',
            ],
            [
                'q' => 'What type of performance framework does Crownmaire follow?',
                'a' => "Crownmaire's private investment programs are structured around predefined distribution frameworks derived from overall trading performance and internal capital allocation policies. Performance outcomes vary based on market conditions, strategy allocation, and participation structure.\n\nHistorical performance information is shared privately with participants.",
            ],
            [
                'q' => 'How is capital managed and protected?',
                'a' => "Capital is managed under strict internal risk and governance frameworks. Crownmaire applies exposure controls, drawdown limits, and reserve management practices designed to prioritize capital preservation.\n\nAll participation is subject to contractual agreements, risk disclosures, and internal compliance procedures, including KYC and AML standards.",
            ],
            [
                'q' => 'What are the liquidity and withdrawal terms?',
                'a' => "Liquidity terms are defined contractually. Participants may request distributions or capital withdrawals in accordance with their applicable agreement, subject to notice periods and prevailing liquidity conditions.\n\nCrownmaire maintains structured withdrawal and close-out policies to ensure operational stability.",
            ],
            [
                'q' => 'Where does Crownmaire Capital operate?',
                'a' => 'Crownmaire Capital is registered and operating across the United States and the United Arab Emirates, with offices at 100 Wall Street Ct, New York, NY 10005 and 2402 Al-Manara Tower, Business Bay, Dubai. The firm serves qualified investors with a global market mandate.',
            ],
            [
                'q' => 'What distinguishes Crownmaire Capital from other investment managers?',
                'a' => "Crownmaire is built as a technology-driven, quantitatively focused investment manager with a disciplined, private operating model.\n\nRather than mass-market products, the firm operates selective investment programs emphasizing structured execution, transparency, and long-term alignment with participants.",
            ],
        ];
    }

    public static function sitemapEntries(): array
    {
        $entries = [
            ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.9'],
            ['loc' => route('strategies'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => route('plan'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => route('markets'), 'changefreq' => 'hourly', 'priority' => '0.8'],
            ['loc' => route('platform'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('careers'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('contact'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('cookie.policy'), 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        try {
            $jobs = JobPost::active()->orderByDesc('id')->get();
            foreach ($jobs as $job) {
                $entries[] = [
                    'loc'        => route('careers.apply', $job->id),
                    'changefreq' => 'weekly',
                    'priority'   => '0.6',
                ];
            }
        } catch (\Throwable $e) {
            // jobs table may be missing on a fresh install
        }

        $template = activeTemplateName();
        $policies = Frontend::where('data_keys', 'policy_pages.element')
            ->when($template, fn ($q) => $q->where('template_name', $template))
            ->orderBy('id')
            ->get();
        foreach ($policies as $policy) {
            $title = $policy->data_values->title ?? 'policy';
            $entries[] = [
                'loc'        => route('policy.pages', [slug($title), $policy->id]),
                'changefreq' => 'yearly',
                'priority'   => '0.4',
            ];
        }

        $reserved = ['about', 'platform', 'insights', 'markets', 'strategies', 'careers', 'contact', 'plan', 'blogs', 'faqs'];
        $pages = Page::where('tempname', activeTemplate())
            ->where('is_default', 0)
            ->get();
        foreach ($pages as $page) {
            $slug = trim((string) $page->slug, '/');
            if ($slug === '' || in_array($slug, $reserved, true)) {
                continue;
            }
            $entries[] = [
                'loc'        => route('pages', $page->slug),
                'changefreq' => 'monthly',
                'priority'   => '0.5',
            ];
        }

        $unique = [];
        foreach ($entries as $entry) {
            $unique[$entry['loc']] = $entry;
        }

        return array_values($unique);
    }

    public static function breadcrumbsFor(string $route, array $page): array
    {
        $crumbs = [
            ['name' => 'Home', 'url' => route('home')],
        ];

        $map = [
            'about'          => 'About',
            'strategies'     => 'Strategies',
            'plan'           => 'Programs',
            'markets'        => 'Markets',
            'platform'       => 'Platform',
            'careers'        => 'Careers',
            'careers.apply'  => 'Careers',
            'contact'        => 'Contact',
            'cookie.policy'  => 'Cookie Policy',
            'policy.pages'   => $page['h1'] ?? 'Legal',
        ];

        if ($route === 'home' || !isset($map[$route])) {
            if ($route === 'home') {
                return [['name' => 'Home', 'url' => route('home')]];
            }
            if (!empty($page['h1'])) {
                $crumbs[] = ['name' => $page['h1'], 'url' => SeoSite::currentCanonical()];
            }
            return $crumbs;
        }

        if ($route === 'careers.apply') {
            $crumbs[] = ['name' => 'Careers', 'url' => route('careers')];
            $crumbs[] = ['name' => $page['h1'] ?? 'Apply', 'url' => SeoSite::currentCanonical()];
            return $crumbs;
        }

        $crumbs[] = ['name' => $map[$route], 'url' => SeoSite::currentCanonical()];
        return $crumbs;
    }

    public static function jsonLd(object $page): array
    {
        $graph = [];
        $schemas = $page->schemas ?? [];
        $orgId = SeoSite::publicBase() . '/#organization';
        $siteId = SeoSite::publicBase() . '/#website';
        $pageId = $page->canonical . '#webpage';

        if (in_array('organization', $schemas, true) || in_array('financial', $schemas, true)) {
            $graph[] = [
                '@type' => 'Organization',
                '@id'   => $orgId,
                'name'  => SeoSite::BRAND,
                'legalName' => SeoSite::LEGAL_NAME,
                'url'   => SeoSite::canonicalForPath('/'),
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => SeoSite::logoUrl(),
                ],
                'email' => SeoSite::EMAIL,
                'telephone' => SeoSite::PHONE,
                'sameAs' => [
                    'https://' . SeoSite::ALIAS_HOST,
                    'https://' . SeoSite::PRIMARY_HOST,
                ],
                'address' => SeoSite::offices(),
                'areaServed' => [
                    ['@type' => 'Country', 'name' => 'United States'],
                    ['@type' => 'Country', 'name' => 'United Arab Emirates'],
                ],
            ];
        }

        if (in_array('financial', $schemas, true)) {
            $graph[] = [
                '@type' => 'FinancialService',
                'name'  => SeoSite::BRAND,
                'url'   => SeoSite::canonicalForPath('/'),
                'image' => SeoSite::logoUrl(),
                'email' => SeoSite::EMAIL,
                'telephone' => SeoSite::PHONE,
                'description' => SeoSite::defaultDescription(),
                'priceRange' => 'Invitation-only',
                'currenciesAccepted' => 'USD',
                'areaServed' => ['US', 'AE'],
                'address' => SeoSite::offices()[0],
                'parentOrganization' => ['@id' => $orgId],
            ];
        }

        if (in_array('website', $schemas, true)) {
            $graph[] = [
                '@type' => 'WebSite',
                '@id'   => $siteId,
                'url'   => SeoSite::canonicalForPath('/'),
                'name'  => SeoSite::BRAND,
                'publisher' => ['@id' => $orgId],
                'inLanguage' => 'en-US',
            ];
        }

        if (in_array('webpage', $schemas, true)) {
            $webPage = [
                '@type' => in_array('about', $schemas, true) ? ['WebPage', 'AboutPage'] : (in_array('contact', $schemas, true) ? ['WebPage', 'ContactPage'] : 'WebPage'),
                '@id'   => $pageId,
                'url'   => $page->canonical,
                'name'  => $page->title,
                'description' => $page->description,
                'isPartOf' => ['@id' => $siteId],
                'about' => ['@id' => $orgId],
                'inLanguage' => 'en-US',
            ];
            if (!empty($page->image)) {
                $webPage['primaryImageOfPage'] = $page->image;
            }
            $graph[] = $webPage;
        }

        if (in_array('software', $schemas, true)) {
            $graph[] = [
                '@type' => 'SoftwareApplication',
                'name'  => SeoSite::BRAND . ' Investor Platform',
                'applicationCategory' => 'FinanceApplication',
                'operatingSystem' => 'Web',
                'url' => SeoSite::canonicalForPath('/platform'),
                'offers' => [
                    '@type' => 'Offer',
                    'availability' => 'https://schema.org/PrivateAccess',
                    'price' => '0',
                    'priceCurrency' => 'USD',
                ],
                'provider' => ['@id' => $orgId],
            ];
        }

        if (in_array('faq', $schemas, true)) {
            $entities = [];
            foreach (self::homeFaqs() as $faq) {
                $entities[] = [
                    '@type' => 'Question',
                    'name'  => $faq['q'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => $faq['a'],
                    ],
                ];
            }
            $graph[] = [
                '@type' => 'FAQPage',
                'mainEntity' => $entities,
            ];
        }

        if (in_array('breadcrumb', $schemas, true) && !empty($page->breadcrumbs)) {
            $items = [];
            foreach ($page->breadcrumbs as $i => $crumb) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['url'],
                ];
            }
            $graph[] = [
                '@type' => 'BreadcrumbList',
                'itemListElement' => $items,
            ];
        }

        if (in_array('contact', $schemas, true)) {
            $graph[] = [
                '@type' => 'ContactPage',
                'url'   => $page->canonical,
                'name'  => $page->title,
                'mainEntity' => ['@id' => $orgId],
            ];
        }

        if (in_array('job', $schemas, true) && !empty($page->job)) {
            $job = $page->job;
            $graph[] = [
                '@type' => 'JobPosting',
                'title' => $job->title,
                'description' => strip_tags((string) ($job->description ?: $job->summary)),
                'datePosted' => optional($job->created_at)->toAtomString(),
                'employmentType' => $job->employment_type ?: 'FULL_TIME',
                'hiringOrganization' => [
                    '@type' => 'Organization',
                    'name'  => SeoSite::BRAND,
                    'sameAs' => SeoSite::canonicalForPath('/'),
                    'logo'  => SeoSite::logoUrl(),
                ],
                'jobLocation' => [
                    '@type' => 'Place',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => $job->location ?: 'New York',
                        'addressCountry'  => 'US',
                    ],
                ],
                'industry' => 'Asset Management',
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph'   => $graph,
        ];
    }

    public static function auditChecklist(): array
    {
        return [
            ['item' => 'Primary domain canonical', 'status' => 'Crownmairecapital.com is the indexed host; crownmaire.com 301s to it.'],
            ['item' => 'Unique title & meta description', 'status' => 'Every public marketing page has a dedicated title, description, and keyword set.'],
            ['item' => 'Open Graph & Twitter cards', 'status' => 'Social titles, descriptions, and images are emitted on all indexable pages.'],
            ['item' => 'XML sitemap', 'status' => url('/sitemap.xml')],
            ['item' => 'Robots.txt', 'status' => url('/robots.txt')],
            ['item' => 'Structured data', 'status' => 'Organization, FinancialService, WebSite, WebPage, FAQPage, BreadcrumbList, JobPosting, ContactPage.'],
            ['item' => 'Internal linking', 'status' => 'Header, footer, and in-page CTAs connect Home, About, Strategies, Markets, Platform, Careers, Contact, and legal pages.'],
            ['item' => 'Local SEO signals', 'status' => 'New York and Dubai NAP (name, address, phone) on Contact, About, footer, and schema.'],
            ['item' => 'Index control', 'status' => 'Admin, CRM, member portal, tickets, and login routes are noindex.'],
            ['item' => 'HTTPS & trailing slashes', 'status' => 'Apache removes trailing slashes; SSL follows General Setting force_ssl.'],
        ];
    }
}
