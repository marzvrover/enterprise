<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Event;
use App\Order;
use App\TicketType;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_get_amount_for_unpaid_order()
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
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
        ]);

        $this->assertEquals(10000, $order->amount);
    }

    /** @test */
    function can_get_amount_for_paid_order()
    {
        $event = factory(Event::class)->states('published')->create();
        $ticketType = $event->ticket_types()->save(factory(TicketType::class)->make([
            'cost' => 5000,
            'name' => 'Regular Ticket',
        ]));
        $order = $event->orderTickets(factory(User::class)->create(), [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 1],
        ]);

        $order->markAsPaid($this->charge());
        $ticketType->cost = 7500;
        $ticketType->save();

        $this->assertEquals(5000, $order->amount);
    }

    /** @test */
    function can_mark_as_paid()
    {
        $order = factory(Order::class)->create();

        $order->markAsPaid($this->charge());

        $order->refresh();
        $this->assertEquals('charge_id', $order->receipt->transaction_id);
        $this->assertNotNull($order->receipt->created_at);
        $this->assertEquals(5000, $order->receipt->amount);
        $this->assertNotNull($order->confirmation_number);
    }

    /** @test */
    function is_order_paid()
    {
        $order = factory(Order::class)->create();
        $order->markAsPaid($this->charge());

        $this->assertTrue($order->isPaid());
        $this->assertNotNull($order->receipt);
    }

    /** @test */
    function can_mark_as_unpiad()
    {
        $order = factory(Order::class)->create();
        $order->markAsPaid($this->charge());

        $order->markAsUnpaid();

        $order->refresh();

        $this->assertNull($order->receipt);
        $this->assertNull($order->confirmation_number);
    }

    /** @test */
    function can_get_tickets_with_name_count_and_amount()
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
        $ticketType1 = $event->ticket_types()->save(factory(TicketType::class)->make([
            'cost' => 5000,
            'name' => 'Regular Ticket',
        ]));
        $ticketType2 = $event->ticket_types()->save(factory(TicketType::class)->make([
            'cost' => 6000,
            'name' => 'Pro Ticket',
        ]));
        $user = factory(User::class)->create();
        $order = $event->orderTickets($user, [
            ['ticket_type_id' => $ticketType1->id, 'quantity' => 2],
            ['ticket_type_id' => $ticketType2->id, 'quantity' => 3],
        ]);

        $tickets = $order->getTicketsWithNameAndAmount();

        $this->assertEquals([
            ["name" => "Regular Ticket", "count" => 2, "cost" => 5000, "amount" => 10000],
            ["name" => "Pro Ticket", "count" => 3, "cost" => 6000, "amount" => 18000],
        ], $tickets->values()->all());
    }

    /** @test */
    function can_get_orders_with_upcoming_events()
    {
        $user = factory(User::class)->create();
        $upcomingEvent = factory(Event::class)->states('published')->create([
            'start' => Carbon::now()->addMonth(2),
        ]);
        $ticketType = $upcomingEvent->ticket_types()->save(factory(TicketType::class)->make());
        $order1 = $upcomingEvent->orderTickets($user, [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
        ]);
        $order2 = $upcomingEvent->orderTickets($user, [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
        ]);
        $order3 = $upcomingEvent->orderTickets($user, [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
        ]);

        $pastEvent = factory(Event::class)->states('published')->create([
            'start' => Carbon::now()->subMonth(2),
        ]);
        $ticketType = $pastEvent->ticket_types()->save(factory(TicketType::class)->make());
        $order = $pastEvent->orderTickets($user, [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
        ]);

        $upcoming = $user->orders()->upcoming()->get()->pluck('id');

        $this->assertCount(3, $upcoming);
        $this->assertContains($order1->id, $upcoming);
        $this->assertContains($order2->id, $upcoming);
        $this->assertContains($order3->id, $upcoming);
    }

    /** @test */
    function can_get_orders_with_past_events()
    {
        $user = factory(User::class)->create();
        $upcomingEvent = factory(Event::class)->states('published')->create([
            'start' => Carbon::now()->addMonth(2),
        ]);
        $ticketType = $upcomingEvent->ticket_types()->save(factory(TicketType::class)->make());
        $order = $upcomingEvent->orderTickets($user, [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
        ]);

        $pastEvent = factory(Event::class)->states('published')->create([
            'start' => Carbon::now()->subMonth(2),
        ]);
        $ticketType = $pastEvent->ticket_types()->save(factory(TicketType::class)->make());
        $order1 = $pastEvent->orderTickets($user, [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
        ]);
        $order2 = $pastEvent->orderTickets($user, [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
        ]);
        $order3 = $pastEvent->orderTickets($user, [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
        ]);

        $past = $user->orders()->past()->get()->pluck('id');

        $this->assertCount(3, $past);
        $this->assertContains($order1->id, $past);
        $this->assertContains($order2->id, $past);
        $this->assertContains($order3->id, $past);
    }

    /** @test */
    function can_see_if_order_was_paid_with_check()
    {
        $order = factory(Order::class)->create();
        $order->markAsPaid(collect(['id' => '#1234', 'amount' => $order->amount]));

        $this->assertTrue($order->refresh()->isCheck());
    }

    /** @test */
    function can_see_if_order_was_paid_with_card()
    {
        $order = factory(Order::class)->create();
        $order->markAsPaid($this->charge());

        $this->assertTrue($order->refresh()->isCard());
    }
}