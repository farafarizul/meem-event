<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::firstOrCreate(
            ['branch_code' => 'HQ'],
            [
                'branch_name'            => 'MEEM Gold HQ',
                'branch_phone'           => '0137974467',
                'branch_address'         => 'Shah Alam',
                'postcode'               => '41200',
                'state'                  => 'Selangor',
                'area'                   => 'Shah Alam',
                'person_in_charge_name'  => null,
                'person_in_charge_phone' => null,
                'branch_type'            => 'HQ',
            ]
        );
    }
}
