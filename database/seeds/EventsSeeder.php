<?php

use App\Event;
use Illuminate\Database\Seeder;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createMBLGTACC();
        $this->createMusicFest();
    }

    private function createMBLGTACC()
    {
        $event = Event::create([
            'title' => 'MBLGTACC 2018',
            'subtitle' => 'All Roads Lead to Intersectionality',
            'timezone' => 'America/Chicago',
            'place' => 'University of Nebraska',
            'location' => 'Omaha, Nebraska',
            'slug' => 'mblgtacc-2018',
            'stripe' => 'mblgtacc',
            'start' => '2018-02-16 19:00:00',
            'end' => '2018-02-18 19:30:00',
            'ticket_string' => 'attendee',
            'description' => 'The Midwest Bisexual Lesbian Gay Transgender Ally College Conference (MBLGTACC) is an annual conference held to connect, educate, and empower LGBTQIA+ college students, faculty, and staff around the Midwest and beyond. It has attracted advocates and thought leaders including Angela Davis, Robyn Ochs, Janet Mock, Laverne Cox, Kate Bornstein, Faisal Alam, and LZ Granderson; and entertainers and artists including RuPaul, Margaret Cho, J Mase III, Chely Wright, and Loren Cameron.',
            'published_at' => \Carbon\Carbon::now()->subMonth(),
            'links' => [
                ['icon' => 'twitter', 'link' => 'https://twitter.com/mblgtacc', 'order' => 1],
                ['icon' => 'facebook', 'link' => 'https://www.facebook.com/mblgtacc/', 'order' => 2],
                ['icon' => 'instagram', 'link' => 'https://www.instagram.com/mblgtacc', 'order' => 3],
                ['icon' => 'snapchat-ghost', 'link' => 'https://www.snapchat.com/add/mblgtacc', 'order' => 4],
                ['icon' => 'website', 'link' => 'https://mblgtacc.org', 'order' => 5],
            ],
            'image' => 'https://mblgtacc.org/themes/mblgtacc2018/assets/images/arts-and-sciences-fall.jpg',
            'logo_light' => 'https://mblgtacc.org/themes/mblgtacc2018/assets/images/mblgtacc-2018-horiz_White.png',
            'logo_dark' => 'https://mblgtacc.org/themes/mblgtacc2018/assets/images/mblgtacc-2018-horiz_Gray.png',
        ]);

        $event->ticket_types()->createMany([
            [
                'name' => 'Regular Registration',
                'cost' => 7500,
                'availability_start' => \Carbon\Carbon::now()->subMonth(),
                'availability_end' => \Carbon\Carbon::parse('2018-02-16 19:00:00')->subMonth(),
            ],
            [
                'name' => 'Late Registration',
                'description' => 'You are not guarenteed special items such as T-Shirts, Program Books, etc. Extras will be available on Sunday after the closing ceremony.',
                'cost' => 7500,
                'availability_start' => \Carbon\Carbon::parse('2018-02-17 19:00:00')->subMonth(),
                'availability_end' => '2018-02-18 19:30:00',
            ],
        ]);
    }

    private function createMusicFest()
    {
        $nextYear = date('Y') + 1;
        $start = \Carbon\Carbon::parse("last saturday of june {$nextYear}");
        $end = $start->addDay();

        $event = Event::create([
            'title' => 'Music Fest',
            'timezone' => 'America/New_York',
            'place' => 'Dome',
            'location' => 'Indianapolis, Indiana',
            'slug' => 'music-fest',
            'stripe' => 'institute',
            'ticket_string' => 'ticket',
            'start' => \Carbon\Carbon::parse("last saturday of june {$nextYear}"),
            'end' => $end,
            'published_at' => \Carbon\Carbon::now()->subMonth(),
            'links' => [
                ['icon' => 'twitter', 'link' => 'https://twitter.com/musicfest', 'order' => 1],
                ['icon' => 'facebook', 'link' => 'https://www.facebook.com/musicfest/', 'order' => 2],
                ['icon' => 'instagram', 'link' => 'https://www.instagram.com/musicfest', 'order' => 3],
                ['icon' => 'snapchat-ghost', 'link' => 'https://www.snapchat.com/add/musicfest', 'order' => 4],
                ['icon' => 'website', 'link' => 'https://musicfest.org', 'order' => 5],
            ],
        ]);

        $event->ticket_types()->createMany([
            [
                'name' => 'Lawn Ticket',
                'cost' => 2500,
            ],
            [
                'name' => '200 Section',
                'cost' => 5000,
            ],
            [
                'name' => '100 Section',
                'cost' => 10000,
            ]
        ]);
    }
}