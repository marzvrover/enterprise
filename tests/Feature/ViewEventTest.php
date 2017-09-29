<?php

namespace Tests\Feature;

use App\Event;
use App\TicketType;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_view_published_event()
    {
        $event = factory(Event::class)->states('published')->create([
            'title' => 'MBLGTACC 2018',
            'slug' => 'mblgtacc-2018',
            'subtitle' => 'All Roads Lead to Intersectionality',
            'start' => '2018-02-16 19:00:00',
            'end' => '2018-02-18 19:30:00',
            'timezone' => 'America/Chicago',
            'place' => 'University of Nebraska',
            'location' => 'Omaha, Nebraska',
            'description' => 'Bacon ipsum dolor amet rump andouille landjaeger ham shoulder.',
            'links' => [
                ['icon' => 'twitter', 'link' => 'https://twitter.com/mblgtacc', 'order' => 1],
                ['icon' => 'facebook', 'link' => 'https://facebook.com/mblgtacc/', 'order' => 2],
                ['icon' => 'instagram', 'link' => 'https://instagram.com/mblgtacc', 'order' => 3],
                ['icon' => 'snapchat-ghost', 'link' => 'https://snapchat.com/add/mblgtacc', 'order' => 4],
                ['icon' => 'website', 'link' => 'https://mblgtacc.org', 'order' => 5],
            ],
            'image' => 'https://mblgtacc.org/themes/mblgtacc2018/assets/images/arts-and-sciences-fall.jpg',
            'logo_light' => 'https://mblgtacc.org/themes/mblgtacc2018/assets/images/mblgtacc-2018-horiz_White.png',
            'logo_dark' => 'https://mblgtacc.org/themes/mblgtacc2018/assets/images/mblgtacc-2018-horiz_Gray.png',
        ]);
        $regular = factory(TicketType::class)->make([
            'cost' => 6500,
            'name' => 'Regular Ticket',
        ]);
        $late = factory(TicketType::class)->make([
            'cost' => 8500,
            'name' => 'Late Ticket',
            'description' => 'You are not guaranteed to receive a conference T-shirt, program, or other memorabilia.',
        ]);

        $event->ticket_types()->save($regular);
        $event->ticket_types()->save($late);

        $response = $this->withoutExceptionHandling()->get("/events/{$event->slug}");

        $response->assertStatus(200);
        $response->assertSee('MBLGTACC 2018');
        $response->assertSee('All Roads Lead to Intersectionality');
        $response->assertSee('Fri, Feb 16');
        $response->assertSee('Sun, Feb 18');
        $response->assertSee('University of Nebraska');
        $response->assertSee('Omaha, Nebraska');
        $response->assertSee('Friday February 16, 2018 1:00 PM to Sunday February 18, 2018 1:30 PM CST');
        $response->assertSee('https://facebook.com/mblgtacc');
        $response->assertSee('https://twitter.com/mblgtacc');
        $response->assertSee('https://instagram.com/mblgtacc');
        $response->assertSee('https://mblgtacc.org');
        $response->assertSee('$65.00');
        $response->assertSee('Regular Ticket');
        $response->assertSee('$85.00');
        $response->assertSee('Late Ticket');
        $response->assertSee('You are not guaranteed to receive a conference T-shirt, program, or other memorabilia.');
        $response->assertSee('https://mblgtacc.org/themes/mblgtacc2018/assets/images/arts-and-sciences-fall.jpg');
        $response->assertSee('https://mblgtacc.org/themes/mblgtacc2018/assets/images/mblgtacc-2018-horiz_White.png');
        $response->assertSee('https://mblgtacc.org/themes/mblgtacc2018/assets/images/mblgtacc-2018-horiz_Gray.png');
    }

    /** @test */
    function cannot_view_unpublished_event()
    {
        $event = factory(Event::class)->create([
            'published_at' => NULL,
        ]);
        $event->ticket_types()->save(factory(TicketType::class)->make());

        $response = $this->get("/events/{$event->slug}");

        $response->assertStatus(404);
    }

    /** @test */
    function cannot_view_event_with_published_at_date_in_future()
    {
        $event = factory(Event::class)->create([
            'published_at' => Carbon::parse('+1 week'),
        ]);
        $event->ticket_types()->save(factory(TicketType::class)->make());

        $response = $this->get("/events/{$event->slug}");

        $response->assertStatus(404);
    }
}