<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agent_id' => \App\Models\User::factory()->create(['role' => 'AGENT'])->id,
            'service_id' => \App\Models\Service::factory(),
            'status' => \App\Enums\ApplicationStatus::DRAFT,
            'payment_status' => \App\Enums\PaymentStatus::PENDING,
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'form_data' => ['field' => 'value'],
        ];
    }
}
