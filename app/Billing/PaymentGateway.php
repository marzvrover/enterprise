<?php

namespace App\Billing;


interface PaymentGateway
{
    public function charge($amount, $token);

    public function getValidTestToken();

    public function newChargesDuring($callback);

    public function setApiKey($apiKey);

    public function getApiKey();
}