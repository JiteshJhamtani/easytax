<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [

            [
                'name' => 'Section 8 Company Registration',
                'slug' => 'section-8-company',
                'description' => 'Incorporation of Section 8 Company under Companies Act for non-profit objectives including charitable, social, educational or cultural purposes.',
                'price' => 9999,
                'active' => true,
            ],

            [
                'name' => 'FPO Registration',
                'slug' => 'fpo-registration',
                'description' => 'Farmer Producer Organization registration under Producer Company or Cooperative structure for agricultural and allied activities.',
                'price' => 14999,
                'active' => true,
            ],

            [
                'name' => 'NGO / Trust / Section 8 Registration',
                'slug' => 'ngo-trust-registration',
                'description' => 'Registration of Trust, Society or Section 8 Company for charitable and non-profit activities.',
                'price' => 8999,
                'active' => true,
            ],

            [
                'name' => 'ITR Filing (Individual / Business)',
                'slug' => 'itr-filing',
                'description' => 'Income Tax Return filing service for salaried individuals, professionals and business entities.',
                'price' => 999,
                'active' => true,
            ],

            [
                'name' => 'GST Registration',
                'slug' => 'gst-registration',
                'description' => 'New GST registration for businesses including proprietorship, partnership, private limited and other entities.',
                'price' => 1999,
                'active' => true,
            ],

            [
                'name' => 'GST Return Filing (GSTR-1 / GSTR-3B)',
                'slug' => 'gst-return-filing',
                'description' => 'Monthly or quarterly GST return filing including GSTR-1 and GSTR-3B compliance.',
                'price' => 1499,
                'active' => true,
            ],

            [
                'name' => 'Private Limited Company Registration',
                'slug' => 'private-limited-company-registration',
                'description' => 'Incorporation of Private Limited Company under Companies Act including name approval, MOA and AOA filing.',
                'price' => 14999,
                'active' => true,
            ],

            [
                'name' => 'Partnership Firm Registration',
                'slug' => 'partnership-firm-registration',
                'description' => 'Registration of Partnership Firm under Indian Partnership Act including drafting of partnership deed.',
                'price' => 4999,
                'active' => true,
            ],

            [
                'name' => 'MSME / Udyam Registration',
                'slug' => 'msme-udyam-registration',
                'description' => 'Udyam registration under MSME ministry for micro, small and medium enterprises.',
                'price' => 999,
                'active' => true,
            ],

        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                $service
            );
        }
    }
}
