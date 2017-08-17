<?php

namespace Tests\Feature;

use App\Event;
use App\TicketType;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_view_order()
    {
        $event = factory(Event::class)->states('published')->create([
            'title' => 'Leadership Conference',
            'slug' => 'leadership-conference',
            'start' => '2018-02-16 19:00:00',
            'end' => '2018-02-18 19:30:00',
            'timezone' => 'America/Chicago',
            'place' => 'University of Nebraska',
            'location' => 'Omaha, Nebraska',
        ]);
        $ticketType = $event->ticket_types()->save(factory(TicketType::class)->make([
            'cost' => 5000,
            'name' => 'Regular Ticket',
        ]));
        $order = $event->orderTickets(factory(User::class)->create(), [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2]
        ]);

        $response = $this->withoutExceptionHandling()->get("/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertSee('Leadership Conference');
        $response->assertSee('University of Nebraska');
        $response->assertSee('Omaha, Nebraska');
        $response->assertSee('Friday February 16, 2018 1:00 PM to Sunday February 18, 2018 1:30 PM CST');
        $response->assertSee('$100.00');
        $response->assertSee('0 of 2');
    }
}
