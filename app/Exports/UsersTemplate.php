<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersTemplate implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'name',
            'email',
            'password',
            'level',
            'divisi',
            'garage',
            'system_access',
        ];
    }

    public function array(): array
    {
        return [
            [
                '-- INSTRUKSI --',
                '-- WAJIB DIISI --',
                '-- OPSIONAL (kosongkan jika tidak ada) --',
                '-- OPSIONAL: admin/ceo/cfo/manager/spv/staff/headstore/kasir/sales/mekanik/pr_user (default: staff) --',
                '-- OPSIONAL: Divisi sesuai sistem --',
                '-- OPSIONAL: Garage sesuai sistem --',
                '-- WAJIB: dashboard,user_management,pr,spk_management (pisahkan dengan koma) --',
            ],
            [
                'John Admin',
                'john.admin@company.com',
                'MySecretPassword123!',
                'admin',
                'HCGA',
                'GARAGE-01',
                'dashboard,user_management,pr,spk_management',
            ],
            [
                'Jane Staff (minimal)',
                'jane.staff@company.com',
                '',
                '',
                '',
                '',
                'dashboard,pr',
            ],
            [
                'Mike Manager',
                'mike.manager@company.com',
                'ManagerPass2024',
                'manager',
                'WORKSHOP',
                'GARAGE-03',
                '["dashboard","spk_management","pr"]',
            ],
        ];
    }
}
