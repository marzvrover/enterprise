<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function show($id)
    {
        $order = Order::with(['event', 'tickets.user.profile', 'user', 'invoice'])->findOrFail($id);

        $this->authorize('view', $order);

        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        if(! $order->isPaid()) {
            $order->delete();

            return redirect('/home');
        }

        flash()->error('Cannot delete an order that has been paid.');
        return response([], 412);
    }
}