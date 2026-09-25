<?php

namespace App\Support;

class CrmPermissionCatalog
{
    public static function all(): array
    {
        return array_merge([
            // Core CRM
            ['group' => 'CRM Core', 'slug' => 'crm.dashboard', 'name' => 'CRM Dashboard', 'description' => 'View CRM overview KPIs'],
            ['group' => 'CRM Core', 'slug' => 'crm.staff', 'name' => 'Manage Staff', 'description' => 'Create and manage CRM team members'],
            ['group' => 'CRM Core', 'slug' => 'crm.roles', 'name' => 'Manage Roles & Permissions', 'description' => 'Assign module access to roles'],
            ['group' => 'CRM Core', 'slug' => 'crm.activity', 'name' => 'Activity Log', 'description' => 'View CRM audit trail'],
            ['group' => 'CRM Core', 'slug' => 'crm.admin_bridge', 'name' => 'Enter Live Admin Desk', 'description' => 'One-click SSO into /admin without separate login'],

            // Portal mirror
            ['group' => 'Portal Oversight', 'slug' => 'portal.investors', 'name' => 'Investors Overview', 'description' => 'View portal investor accounts'],
            ['group' => 'Portal Oversight', 'slug' => 'portal.deposits', 'name' => 'Deposits Overview', 'description' => 'View portal deposits'],
            ['group' => 'Portal Oversight', 'slug' => 'portal.withdrawals', 'name' => 'Withdrawals Overview', 'description' => 'View portal withdrawals'],
            ['group' => 'Portal Oversight', 'slug' => 'portal.tickets', 'name' => 'Support Tickets Overview', 'description' => 'View portal support tickets'],
            ['group' => 'Portal Oversight', 'slug' => 'portal.investments', 'name' => 'Investments Overview', 'description' => 'View strategy investments'],
            ['group' => 'Portal Oversight', 'slug' => 'portal.jobs', 'name' => 'Careers Overview', 'description' => 'View job posts and applications summary'],

            // Manager
            ['group' => 'Management', 'slug' => 'manager.team', 'name' => 'Team Oversight', 'description' => 'Oversee assigned agents and pipelines'],
            ['group' => 'Management', 'slug' => 'manager.pipeline', 'name' => 'Sales Pipeline', 'description' => 'View and manage client pipeline'],
            ['group' => 'Management', 'slug' => 'manager.reports', 'name' => 'Management Reports', 'description' => 'AUM and conversion reports'],

            // Agent
            ['group' => 'Investment Officers', 'slug' => 'agent.clients', 'name' => 'Client Onboarding', 'description' => 'Manage own clients and leads'],
            ['group' => 'Investment Officers', 'slug' => 'agent.referrals', 'name' => 'Referrals', 'description' => 'Track referral prospects'],
            ['group' => 'Investment Officers', 'slug' => 'agent.commissions', 'name' => 'Commissions', 'description' => 'View commission statements'],
            ['group' => 'Investment Officers', 'slug' => 'agent.pitch_decks', 'name' => 'Pitch Decks', 'description' => 'Access CRM pitch materials'],

            ['group' => 'Content', 'slug' => 'crm.pitch_decks.manage', 'name' => 'Upload Pitch Decks', 'description' => 'Upload and manage pitch decks'],

            ['group' => 'Trading Desk', 'slug' => 'trader.positions', 'name' => 'Fund Positions', 'description' => 'Manage where company capital is invested'],
            ['group' => 'Trading Desk', 'slug' => 'trader.funds', 'name' => 'Company Funds Overview', 'description' => 'View allocated vs market value'],

            ['group' => 'Finance', 'slug' => 'finance.entries', 'name' => 'Finance Ledger', 'description' => 'Record income, expense, and P&L entries'],
            ['group' => 'Finance', 'slug' => 'finance.reports', 'name' => 'P&L Reports', 'description' => 'Company profit and loss reports'],
        ], CrmAdminBridge::catalogEntries());
    }

    public static function slugsForPortal(string $portal): array
    {
        $map = [
            'crm' => array_column(self::all(), 'slug'),
            // Manager CRM access is shared; Admin Desk modules (admin.*) + bridge are assigned per manager on Staff.
            'manager' => [
                'crm.dashboard', 'manager.team', 'manager.pipeline', 'manager.reports',
                'agent.clients', 'agent.referrals', 'agent.pitch_decks',
                'portal.investors', 'portal.investments',
            ],
            // Investment Officers all share the same toolkit (leads, commissions, ranking).
            'agent' => [
                'crm.dashboard', 'agent.clients', 'agent.referrals', 'agent.commissions', 'agent.pitch_decks',
            ],
            'trader' => [
                'crm.dashboard', 'trader.positions', 'trader.funds',
            ],
            'finance' => [
                'crm.dashboard', 'finance.entries', 'finance.reports',
                'portal.deposits', 'portal.withdrawals',
            ],
        ];

        return $map[$portal] ?? [];
    }

    public static function adminDeskSlugs(): array
    {
        return array_keys(CrmAdminBridge::permissionMap());
    }
}
