<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $defaults = [

            'app_name'                => 'EasyTax',
            'currency_symbol'         => '₹',
            'currency_code'           => 'INR',
            'currency_position'       => 'before',

            'support_email'           => 'support@easytax.live',
            'support_phone'           => '+91XXXXXXXXXX',

            'default_service_price'   => 999,

            'platform_fee_percentage' => 0,
            'tax_percentage'          => 0,

        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
