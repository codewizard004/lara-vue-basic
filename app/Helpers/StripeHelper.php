<?php

namespace App\Helpers;

use App\Models\PaymentMethod;
use App\Models\User;
use Laravel\Cashier\Cashier;

class StripeHelper
{
    public $user;

    const CURRENCY_MULTIPLIER = 100;

    public function __construct($id = null)
    {
        $this->user = User::find($id);
    }

    public function createCustomer()
    {
        try {
            return $this->user->createOrGetStripeCustomer();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function addPaymentMethod($paymentMethod)
    {
        $paymentMethod = $this->user->addPaymentMethod($paymentMethod);
    }

    public function payment($amount, $paymentmethodid, $user = null)
    {
        try {
            if (!$user) {
                $user = User::find($this->user->id);
            }
            $stripeUser = $this->createCustomer();
            $this->addPaymentMethod($paymentmethodid);
            $payment = $user->charge($amount * self::CURRENCY_MULTIPLIER, $paymentmethodid,[
                'customer' => $stripeUser->id
            ]);
            return $payment->toArray();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}