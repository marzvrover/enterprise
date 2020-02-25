<?php

namespace Tests\Unit\Mail;

use App\Invoice;
use App\Mail\InvoiceEmail;
use App\Order;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_contains_link_to_order_page()
    {
        $order = factory(Order::class)->create();
        $invoice = $order->invoice()->save(factory(Invoice::class)->make());

        $email = (new InvoiceEmail($order))->render();

        $this->assertStringContainsString(url('/orders/'.$order->id), $email);
    }

    /** @test */
    public function email_contains_invoice_attachment()
    {
        $order = factory(Order::class)->create();
        $invoice = $order->invoice()->save(factory(Invoice::class)->make());

        $email = (new InvoiceEmail($order))->build();

        $this->assertNotNull($email->rawAttachments);
    }
}
