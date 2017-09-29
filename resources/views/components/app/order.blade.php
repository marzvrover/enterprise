<div class="col-lg-6 mb-4">
    <a href="/orders/{{ $order->id }}" class="card card-background-image"
       style="background-image: linear-gradient(rgba(0, 0, 0, 0.55),rgba(0, 0, 0, 0.55)), url({{ $order->event->image }})">
        <div class="card-body text-white">
            @if(isset($order->event->logo_light))
                <img src="{{ $order->event->logo_light }}" alt="{{ $order->event->title }} Logo"
                     style="width: 75%; margin-bottom: .5em;">
            @else
                <h4 class="card-title">{{ $order->event->title }}</h4>
            @endif
            <ul class="fa-ul">
                <li><i class="fa-li fa fa-clock-o"></i>
                    {{ $order->event->formatted_start }} -
                    {{ $order->event->formatted_end }}, {{ $order->event->end->format('Y') }}
                </li>
                <li>
                    <i class="fa-li fa fa-map-marker"></i> {{ $order->event->place }} {{ $order->event->location }}
                </li>
                <li><i class="fa-li fa fa-ticket"></i> {{ $order->tickets->count() }} Tickets</li>
            </ul>
            <div class="row">
                <div class="col">
                    @if($order->isPaid())
                        <h4 class="card-title">PAID</h4>
                    @endif
                </div>
                <div class="col">
                    <h4 class="card-title text-right">{{ '$' . number_format($order->amount/100, 2) }}</h4>
                </div>
            </div>
        </div>
    </a>
</div>