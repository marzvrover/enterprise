<?php

namespace App\Billing;


use App\Exceptions\PaymentFailedException;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken()
    {
        return 'valid-token';
    }

    public function charge($amount, $token)
    {
        if($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException();
        }

        $this->charges[] = $amount;

        return 'charge_id';
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }
}