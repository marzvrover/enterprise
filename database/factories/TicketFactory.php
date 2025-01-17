<?php

namespace Database\Factories;

use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition()
    {
        return [
            'ticket_type_id' => TicketType::factory(),
        ];
    }
}
