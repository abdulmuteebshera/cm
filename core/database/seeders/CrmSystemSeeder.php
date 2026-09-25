<?php

namespace Database\Seeders;

use App\Models\Crm\CrmPermission;
use App\Models\Crm\CrmRole;
use App\Models\Crm\CrmStaff;
use App\Support\CrmPermissionCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CrmSystemSeeder extends Seeder
{
    public function run(): void
    {
        foreach (CrmPermissionCatalog::all() as $row) {
            CrmPermission::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name'        => $row['name'],
                    'group'       => $row['group'],
                    'description' => $row['description'],
                ]
            );
        }

        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'portal' => 'crm',
                'description' => 'Full CRM control and portal oversight',
                'is_system' => 1,
                'permissions' => array_column(CrmPermissionCatalog::all(), 'slug'),
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'portal' => 'manager',
                'description' => 'Team and pipeline oversight. Admin tasks (Allocation, Announcements, etc.) are assigned per manager on Staff.',
                'is_system' => 1,
                'permissions' => CrmPermissionCatalog::slugsForPortal('manager'),
            ],
            [
                'name' => 'Investment Officer',
                'slug' => 'agent',
                'portal' => 'agent',
                'description' => 'Shared Investment Officer toolkit: leads, referrals, commissions, goals & ranking',
                'is_system' => 1,
                'permissions' => CrmPermissionCatalog::slugsForPortal('agent'),
            ],
            [
                'name' => 'Trader',
                'slug' => 'trader',
                'portal' => 'trader',
                'description' => 'Company funds and market positions',
                'is_system' => 1,
                'permissions' => CrmPermissionCatalog::slugsForPortal('trader'),
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'portal' => 'finance',
                'description' => 'Company finance and P&L',
                'is_system' => 1,
                'permissions' => CrmPermissionCatalog::slugsForPortal('finance'),
            ],
        ];

        foreach ($roles as $data) {
            $role = CrmRole::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name'        => $data['name'],
                    'portal'      => $data['portal'],
                    'description' => $data['description'],
                    'is_system'   => $data['is_system'],
                    'status'      => 1,
                ]
            );

            $permissionIds = CrmPermission::whereIn('slug', $data['permissions'])->pluck('id');
            $role->permissions()->sync($permissionIds);
        }

        $superRole = CrmRole::where('slug', 'super-admin')->first();

        CrmStaff::updateOrCreate(
            ['email' => 'info@crownmaire.com'],
            [
                'name'         => 'CRM Super Admin',
                'password'     => Hash::make('CMCapital123!@#'),
                'crm_role_id'  => $superRole?->id,
                'is_super'     => 1,
                'status'       => 1,
                'department'   => 'Executive',
                'title'        => 'Super Administrator',
                'employee_code'=> 'CRM-001',
            ]
        );
    }
}
