<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>Our Privacy Policy covers how we collect, use, and protect your personal information.</p>',
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-and-conditions',
                'content' => '<h1>Terms of Service</h1><p>These terms and conditions outline the rules and regulations for the use of our website and services.</p>',
            ],
            [
                'title' => 'Refund Policy',
                'slug' => 'refund-policy',
                'content' => '<h1>Refund Policy</h1><p>Our refund policy guarantees your satisfaction. If you are not satisfied, please contact us.</p>',
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h1>About Us</h1><p>We are a leading provider of tax services, aiming to simplify your financial life.</p>',
            ],
            [
                'title' => 'Contact Support',
                'slug' => 'contact-us',
                'content' => '<h1>Contact Us</h1><p>If you have any questions or queries, please feel free to reach out to our support team.</p>',
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(
                ['slug' => $page['slug']],
                ['title' => $page['title'], 'content' => $page['content'], 'is_active' => true]
            );
        }
    }
}
