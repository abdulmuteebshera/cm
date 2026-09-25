<?php

namespace App\Support;

class CrmAdminBridge
{
    /**
     * Admin desk permissions assignable from CRM roles.
     * Each maps to admin route-name patterns the staff may use after SSO.
     */
    public static function permissionMap(): array
    {
        return [
            'admin.dashboard' => [
                'name'        => 'Admin Dashboard',
                'description' => 'Access the live /admin dashboard',
                'routes'      => ['admin.dashboard', 'admin.profile*', 'admin.password*', 'admin.notifications*', 'admin.notification.*', 'admin.download.attachment'],
                'menu'        => 'dashboard',
            ],
            'admin.plans' => [
                'name'        => 'Plans & Strategies',
                'description' => 'Time manage, plans, strategy payouts & reports',
                'routes'      => ['admin.time.*', 'admin.plan.*', 'admin.strategy.*'],
                'menu'        => 'plans',
            ],
            'admin.users' => [
                'name'        => 'Manage Users',
                'description' => 'Investor accounts, KYC, notifications',
                'routes'      => ['admin.users.*'],
                'menu'        => 'users',
            ],
            'admin.announcements' => [
                'name'        => 'Announcements',
                'description' => 'Portal announcements',
                'routes'      => ['admin.announcement.*'],
                'menu'        => 'announcements',
            ],
            'admin.job_posts' => [
                'name'        => 'Job Opportunities',
                'description' => 'Create and manage career postings',
                'routes'      => ['admin.job.post.*'],
                'menu'        => 'job_posts',
            ],
            'admin.job_applications' => [
                'name'        => 'Job Applications',
                'description' => 'Review career applicants and résumés',
                'routes'      => ['admin.job.application.*'],
                'menu'        => 'job_applications',
            ],
            'admin.leaderboard' => [
                'name'        => 'Leaderboard',
                'description' => 'Manage investor leaderboard',
                'routes'      => ['admin.leaderboard.*'],
                'menu'        => 'leaderboard',
            ],
            'admin.portfolio_allocation' => [
                'name'        => 'Portfolio Allocation',
                'description' => 'Manage AI / portfolio allocation',
                'routes'      => ['admin.portfolio.allocation.*'],
                'menu'        => 'portfolio_allocation',
            ],
            'admin.promotions' => [
                'name'        => 'Promotional Tools',
                'description' => 'Promotional banners and tools',
                'routes'      => ['admin.promotional.*'],
                'menu'        => 'promotions',
            ],
            'admin.gateways' => [
                'name'        => 'Payment Gateways',
                'description' => 'Automatic and manual gateways',
                'routes'      => ['admin.gateway.*'],
                'menu'        => 'gateways',
            ],
            'admin.deposits' => [
                'name'        => 'Deposits',
                'description' => 'Approve and manage deposits',
                'routes'      => ['admin.deposit.*'],
                'menu'        => 'deposits',
            ],
            'admin.withdrawals' => [
                'name'        => 'Withdrawals',
                'description' => 'Approve and manage withdrawals',
                'routes'      => ['admin.withdraw.*'],
                'menu'        => 'withdrawals',
            ],
            'admin.tickets' => [
                'name'        => 'Support Tickets',
                'description' => 'Reply to investor support tickets',
                'routes'      => ['admin.ticket.*'],
                'menu'        => 'tickets',
            ],
            'admin.reports' => [
                'name'        => 'Reports',
                'description' => 'Transaction, login, invest reports',
                'routes'      => ['admin.report.*', 'admin.invest.report.*'],
                'menu'        => 'reports',
            ],
            'admin.referrals' => [
                'name'        => 'Referral Settings',
                'description' => 'Manage referral configuration',
                'routes'      => ['admin.referrals.*'],
                'menu'        => 'referrals',
            ],
            'admin.ranking' => [
                'name'        => 'User Ranking',
                'description' => 'Investor ranking tiers',
                'routes'      => ['admin.ranking.*'],
                'menu'        => 'ranking',
            ],
            'admin.subscribers' => [
                'name'        => 'Subscribers',
                'description' => 'Newsletter subscribers',
                'routes'      => ['admin.subscriber.*'],
                'menu'        => 'subscribers',
            ],
            'admin.settings' => [
                'name'        => 'System Settings',
                'description' => 'General settings, frontend, SEO (sensitive)',
                'routes'      => ['admin.setting.*', 'admin.frontend.*', 'admin.seo*', 'admin.extension*', 'admin.language*', 'admin.system*', 'admin.cron*', 'admin.maintenance*'],
                'menu'        => 'settings',
            ],
        ];
    }

    public static function catalogEntries(): array
    {
        $entries = [];
        foreach (self::permissionMap() as $slug => $meta) {
            $entries[] = [
                'group'       => 'Admin Desk Modules',
                'slug'        => $slug,
                'name'        => $meta['name'],
                'description' => $meta['description'],
            ];
        }

        return $entries;
    }

    public static function alwaysAllowedRoutes(): array
    {
        return [
            'admin.dashboard',
            'admin.profile',
            'admin.profile.update',
            'admin.password',
            'admin.password.update',
            'admin.notifications',
            'admin.notification.read',
            'admin.notifications.readAll',
            'admin.download.attachment',
            'admin.logout',
        ];
    }

    public static function routeAllowed(?array $bridge, string $routeName): bool
    {
        if (!$bridge) {
            return true; // native admin login — full access
        }

        if (!empty($bridge['full_access'])) {
            return true;
        }

        foreach (self::alwaysAllowedRoutes() as $allowed) {
            if (self::routeMatches($routeName, $allowed)) {
                return true;
            }
        }

        $permissions = $bridge['permissions'] ?? [];
        foreach ($permissions as $slug) {
            $meta = self::permissionMap()[$slug] ?? null;
            if (!$meta) {
                continue;
            }
            foreach ($meta['routes'] as $pattern) {
                if (self::routeMatches($routeName, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function menuAllowed(?array $bridge, string $menuKey): bool
    {
        if (!$bridge || !empty($bridge['full_access'])) {
            return true;
        }

        if ($menuKey === 'dashboard') {
            return true;
        }

        $permissions = $bridge['permissions'] ?? [];
        foreach ($permissions as $slug) {
            $meta = self::permissionMap()[$slug] ?? null;
            if ($meta && ($meta['menu'] ?? null) === $menuKey) {
                return true;
            }
        }

        return false;
    }

    public static function routeMatches(string $routeName, string $pattern): bool
    {
        if (str_ends_with($pattern, '*')) {
            $prefix = rtrim($pattern, '*');
            return str_starts_with($routeName, $prefix);
        }

        return $routeName === $pattern;
    }
}
