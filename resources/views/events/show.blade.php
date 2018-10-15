@extends('layouts.app')

@section('title', $event->title)

@section('hero')
    <section class="hero bg-center" style="background-image: linear-gradient(rgba(0, 0, 0, 0.55),rgba(0, 0, 0, 0.55)), url({{ Storage::url($event->image) }})">
        <div class="container">
            <div class="hero-titles">
                @if(isset($event->logo_light))
                    <img src="{{ Storage::url($event->logo_light) }}" alt="{{ $event->title }} Logo" style="min-width: 250px; width: 50%;">
                @else
                    <h1 class="display-3">{{ $event->title }}</h1>
                @endif
                <h2 class="mt-4">{{ $event->subtitle }}</h2>
            </div>
        </div>
        <div class="hero-bar clearfix">
            <div class="container">
                <div class="pull-left">
                    {{ $event->formatted_start }}
                    <br/>
                    {{ $event->formatted_end }}
                </div>
                <div class="pull-right text-right">
                    {{ $event->place }}<br/>{{ $event->location }}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h3 class="description-title">Event Description</h3>
                <ul class="fa-ul list-reset ml-6">
                    <li><span class="fa-li"><i class="fal fa-clock"></i></span>{{ $event->duration }}</li>
                    <li><span class="fa-li"><i class="fal fa-map-marker-alt"></i></span>{{ $event->place }} {{ $event->location }}</li>
                </ul>
                @if($event->description)
                    <div class="description-content">
                        {!! $event->description !!}
                    </div>
                @endif

                @component('components.app.links', ['class' => 'h3 mt-4', 'links' => collect($event->links)->sortBy('order')])
                @endcomponent

                @if($event->refund_policy)
                <p>
                    <a data-toggle="collapse" href="#refund_policy" role="button" aria-expanded="false" aria-controls="refund_policy">
                        Refund Policy <i class="fa fa-info-circle" aria-hidden="true"></i>
                    </a>
                </p>
                <div class="collapse" id="refund_policy">
                    {!! $event->refund_policy !!}
                </div>
                @endif

                @if($event->photo_policy)
                    <p>
                        <a data-toggle="collapse" href="#photo_policy" role="button" aria-expanded="false" aria-controls="photo_policy">
                            Photo Policy <i class="fa fa-info-circle" aria-hidden="true"></i>
                        </a>
                    </p>
                    <div class="collapse" id="photo_policy">
                        {!! $event->photo_policy !!}
                    </div>
                @endif
            </div>
            <div class="col-lg-4">
                <div class="tickets card-list rounded mt-sm-4 mt-xs-4">
                    <start-order :ticket_types="{{ $event->ticket_types }}" :event="{{ $event }}" :user="{{ json_encode(Auth::user()) }}"></start-order>
                </div>
            </div>
        </div>
    </div>
@endsection
