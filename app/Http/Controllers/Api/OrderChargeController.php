<?php

namespace App\Http\Controllers\Api;

use App\Billing\PaymentGateway;
use App\Exceptions\PaymentFailedException;
use App\Mail\ReceiptEmail;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class OrderChargeController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store(Order $order)
    {
        try {
            $this->paymentGateway->setApiKey($order->event->getSecretKey());
            $order->markAsPaid($this->paymentGateway->charge($order->amount, request('stripeToken')));

            flash('You successfully paid for this order, you will receive a confirmation email with a receipt shortly.')->success();

            Mail::to($order->user->email)->send(new ReceiptEmail($order->fresh()));

            return response()->json([
                'created' => true,
                'order' => $order
            ], 201);
        } catch (PaymentFailedException $e) {
            return response()->json([
                'created' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}