<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Stripe\Charge;

class Receipt extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = ['transaction_id', 'amount', 'card_last_four'];

    protected $dates = ['deleted_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function charge()
    {
        if (starts_with($this->transaction_id, 'ch')) {
            $stripe = $this->order->event->stripe ?? $this->donation->group;
            return Charge::retrieve($this->transaction_id, [
                'api_key' => config($stripe . '.stripe.secret'),
            ]);
        }
    }

    public function subscription()
    {
        if (starts_with($this->transaction_id, 'sub')) {
            $stripe = $this->order->event->stripe ?? $this->donation->group;
            return \Stripe\Subscription::retrieve($this->transaction_id, [
                'api_key' => config($stripe . '.stripe.secret'),
            ]);
        }
    }
}
