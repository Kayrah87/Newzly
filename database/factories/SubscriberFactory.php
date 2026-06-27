<?php

namespace Database\Factories;

use App\Models\Publication;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'publication_id' => Publication::factory(),
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
            'status' => Subscriber::STATUS_PENDING,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status' => Subscriber::STATUS_CONFIRMED,
            'confirmed_at' => now(),
            'consent_at' => now(),
            'consent_source' => 'public_form',
            'confirmation_token' => null,
        ]);
    }

    public function unsubscribed(): static
    {
        return $this->state(fn () => [
            'status' => Subscriber::STATUS_UNSUBSCRIBED,
            'unsubscribed_at' => now(),
        ]);
    }
}
