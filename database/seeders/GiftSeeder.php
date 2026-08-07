<?php

namespace Database\Seeders;

use App\Models\Gift;
use App\Models\Service;
use Illuminate\Database\Seeder;

class GiftSeeder extends Seeder
{
    public function run(): void
    {
        $services = Service::where('active', true)->orderBy('id')->pluck('id');

        if ($services->count() < 2) {
            $this->command->warn('Need at least 2 active services. Skipping.');
            return;
        }

        $s0 = $services[0]; // Primary service — ITR or equivalent
        $s1 = $services[1]; // Secondary service for multi-service gift
        $s2 = $services[2] ?? $services[1]; // Third service fallback

        $gifts = [

            // ================================================================
            // YEARLY — single service (s0), milestone progression
            // ================================================================
            [
                'gift'   => [
                    'name'        => 'Royal Enfield Bullet',
                    'description' => 'Hit 1000 applications in a year and ride home a Bullet.',
                    'period_type' => 'yearly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 1000]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'Tata Punch',
                    'description' => 'Submit 5000 applications in a year and win a Tata Punch.',
                    'period_type' => 'yearly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 5000]],
                ],
            ],

            // ================================================================
            // QUARTERLY — single service (s0), milestone progression
            // ================================================================
            [
                'gift'   => [
                    'name'        => 'Godrej 1.5 Ton AC + Gold Kit',
                    'description' => '350 applications per quarter wins a Godrej AC and gold kit.',
                    'period_type' => 'quarterly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 350]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'Goa Trip',
                    'description' => '500 applications in a quarter earns you a Goa getaway.',
                    'period_type' => 'quarterly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 500]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'Thailand or Dubai Trip',
                    'description' => 'Reach 750 applications quarterly to win an international trip.',
                    'period_type' => 'quarterly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 750]],
                ],
            ],

            // ================================================================
            // MONTHLY — single service (s0), milestone progression low → high
            // ================================================================
            [
                'gift'   => [
                    'name'        => 'Rs 100 Wallet',
                    'description' => 'Submit just 3 applications monthly and earn a wallet bonus.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 3]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'Rs 200 Wallet + Silver Kit',
                    'description' => 'Submit 5 applications in a month to earn a wallet reward.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 5]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'Tax2Win Bag + Silver Kit',
                    'description' => '10 applications monthly wins a Tax2Win branded bag.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 10]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'Boat Airdopes 131 + Silver Kit',
                    'description' => '15 monthly applications earns wireless earbuds.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 15]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'Xiaomi Powerbank + Silver Kit',
                    'description' => 'Submit 25 applications in a month to win a Xiaomi Powerbank.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 25]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'Boat Smart Watch + Silver Kit',
                    'description' => '50 applications monthly earns a Boat Smart Watch.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 50]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'boAt Aavante Bar + Silver Kit',
                    'description' => '75 monthly applications wins a boAt soundbar.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 75]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'Panasonic Microwave + Gold Kit',
                    'description' => '100 applications monthly wins a Panasonic Microwave.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 100]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'HiFresh Smart Cooler + Gold Kit',
                    'description' => '150 applications monthly earns a HiFresh Smart Cooler.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 150]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'OnePlus Nord CE4 Lite 5G + Gold Kit',
                    'description' => '200 monthly applications wins a OnePlus smartphone.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 200]],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'Samsung Smart TV 43 Inch + Gold Kit',
                    'description' => 'Earn 250 applications monthly to win a Samsung Smart TV.',
                    'period_type' => 'monthly',
                    'is_active'   => true,
                ],
                'groups' => [
                    [['service_id' => $s0, 'min_count' => 250]],
                ],
            ],

            // ================================================================
            // MULTI-SERVICE gifts — shown on dashboard + admin agent page only
            // NOT on service page progress bar
            // ================================================================
            [
                'gift'   => [
                    'name'        => 'Super Performer Bundle',
                    'description' => 'Top performers across multiple services win this exclusive bundle.',
                    'period_type' => 'quarterly',
                    'is_active'   => true,
                ],
                'groups' => [
                    // Group A (AND): s0 >= 300 AND s1 >= 100
                    [
                        ['service_id' => $s0, 'min_count' => 300],
                        ['service_id' => $s1, 'min_count' => 100],
                    ],
                    // Group B (OR): s2 >= 500 alone qualifies too
                    [
                        ['service_id' => $s2, 'min_count' => 500],
                    ],
                ],
            ],
            [
                'gift'   => [
                    'name'        => 'All-Rounder Award',
                    'description' => 'Consistent performance across all services earns this yearly award.',
                    'period_type' => 'yearly',
                    'is_active'   => true,
                ],
                'groups' => [
                    // Must hit targets on both s0 AND s1
                    [
                        ['service_id' => $s0, 'min_count' => 2000],
                        ['service_id' => $s1, 'min_count' => 500],
                    ],
                ],
            ],

        ];

        // Wipe existing before re-seeding so it's idempotent
        Gift::query()->delete();

        foreach ($gifts as $entry) {
            $gift = Gift::create($entry['gift']);

            foreach ($entry['groups'] as $i => $conditions) {
                $group = $gift->conditionGroups()->create(['sort_order' => $i]);
                foreach ($conditions as $condData) {
                    $group->conditions()->create($condData);
                }
            }

            $this->command->info("  ✓ {$gift->name}");
        }

        $this->command->info('');
        $this->command->info('All gifts seeded successfully.');
    }
}