<?php

namespace Tests\Feature\Http\Controllers;

use App\Event;
use App\TicketType;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\OrderReceiptController
 */
class OrderReceiptControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_view_receipt()
    {
        $event = factory(Event::class)->states('published')->create();
        $ticketType = $event->ticket_types()->save(factory(TicketType::class)->make());
        $user = factory(User::class)->create();
        $order = $event->orderTickets($user, [
            ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
        ]);

        $order->markAsPaid($this->charge());

        $response = $this->withoutExceptionHandling()
            ->actingAs($user)
            ->get("/orders/{$order->id}/receipt");

        $response->assertStatus(200)
            ->assertSee('receipt');
    }
}