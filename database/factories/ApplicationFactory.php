<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
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
            'agent_id' => User::factory()->create(['role' => 'AGENT'])->id,
            'service_id' => Service::factory(),
            'status' => ApplicationStatus::DRAFT,
            'payment_status' => PaymentStatus::PENDING,
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'form_data' => ['field' => 'value'],
        ];
    }
}
