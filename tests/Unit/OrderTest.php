<?php

namespace Tests\Unit;

use App\Event;
use App\Order;
use App\TicketType;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_get_amount()
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

        $this->assertEquals(10000, $order->amount);
    }

    /** @test */
    public function can_mark_as_paid()
    {
        $order = factory(Order::class)->create();

        $order->markAsPaid('charge_id');

        $order->refresh();
        $this->assertEquals('charge_id', $order->transaction_id);
        $this->assertNotNull($order->transaction_date);
    }
}
