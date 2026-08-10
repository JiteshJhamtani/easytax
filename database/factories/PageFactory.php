<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => '<p>'.$this->faker->paragraphs(3, true).'</p>',
            'is_active' => true,
        ];
    }
}
