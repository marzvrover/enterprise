<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $url;

    /**
     * Create a new message instance.
     *
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
        $this->url = url("/orders/{$order->id}");
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = PDF::loadView('pdf.invoice', ['order' => $this->order]);

        return $this->view('emails.invoice')
                ->attachData($pdf->output(), 'invoice.pdf');
    }
}