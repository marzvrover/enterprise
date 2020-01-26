<?php

namespace Tests\Feature\Http\Controllers\Api\Gemini;

use App\Event;
use App\Imports\ActivitiesImport;
use App\Schedule;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @see \App\Http\Controllers\Api\Gemini\UsersActivitiesController
 */
class UsersActivitiesControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();
        $event = factory(Event::class)->create(['title' => 'MBLGTACC', 'slug' => 'mblgtacc']);
        $mainTrack = factory(Schedule::class)->create(['event_id' => $event->id, 'title' => 'Main Track']);
        $advisorTrack = factory(Schedule::class)->create(['event_id' => $event->id, 'title' => 'Advisor Track']);

        Excel::import(new ActivitiesImport, public_path('documents/schedule.xlsx'));

        $randomActivities = $mainTrack->activities->random(4)->pluck('id');
        $user->schedule()->attach($randomActivities);

        Passport::actingAs($user);

        DB::enableQueryLog();
        $response = $this->withoutExceptionHandling()->getJson("api/gemini/me/activities?event={$event->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'schedule',
                    'title',
                    'speakers' => [
                        '*' => [
                            'name', 'email', 'pronouns'
                        ]
                    ],
                    'description',
                    'type',
                    'location',
                    'start',
                    'end',
                ]
            ]
        ]);

        $this->assertCount(4, $response->decodeResponseJson()['data']);
        $this->assertLessThan(10, count(DB::getQueryLog()));
    }

    /** @test */
    public function storing_new_activity_returns_an_ok_response()
    {
        $user = factory(User::class)->create();
        $event = factory(Event::class)->create(['title' => 'MBLGTACC', 'slug' => 'mblgtacc']);
        $mainTrack = factory(Schedule::class)->create(['event_id' => $event->id, 'title' => 'Main Track']);
        $advisorTrack = factory(Schedule::class)->create(['event_id' => $event->id, 'title' => 'Advisor Track']);

        Excel::import(new ActivitiesImport, public_path('documents/schedule.xlsx'));

        $randomActivity = $mainTrack->activities->random(1)->first();

        Passport::actingAs($user);

        DB::enableQueryLog();
        $response = $this->withoutExceptionHandling()->postJson("api/gemini/me/activities/{$randomActivity->id}?event={$event->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'schedule',
                    'title',
                    'speakers' => [
                        '*' => [
                            'name', 'email', 'pronouns'
                        ]
                    ],
                    'description',
                    'type',
                    'location',
                    'start',
                    'end',
                ]
            ]
        ]);

        $this->assertCount(1, $response->decodeResponseJson()['data']);
        $this->assertEquals($randomActivity->id, $user->schedule->first()->id);
        $this->assertLessThan(10, count(DB::getQueryLog()));
    }

    /** @test */
    public function storing_existing_activity_returns_an_ok_response()
    {
        $user = factory(User::class)->create();
        $event = factory(Event::class)->create(['title' => 'MBLGTACC', 'slug' => 'mblgtacc']);
        $mainTrack = factory(Schedule::class)->create(['event_id' => $event->id, 'title' => 'Main Track']);
        $advisorTrack = factory(Schedule::class)->create(['event_id' => $event->id, 'title' => 'Advisor Track']);

        Excel::import(new ActivitiesImport, public_path('documents/schedule.xlsx'));

        $randomActivity = $mainTrack->activities->random(1)->first();
        $user->schedule()->attach($randomActivity->id);

        Passport::actingAs($user);

        DB::enableQueryLog();
        $response = $this->withoutExceptionHandling()->postJson("api/gemini/me/activities/{$randomActivity->id}?event={$event->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'schedule',
                    'title',
                    'speakers' => [
                        '*' => [
                            'name', 'email', 'pronouns'
                        ]
                    ],
                    'description',
                    'type',
                    'location',
                    'start',
                    'end',
                ]
            ]
        ]);

        $this->assertCount(0, $response->decodeResponseJson()['data']);
        $this->assertCount(0, $user->schedule);
        $this->assertLessThan(10, count(DB::getQueryLog()));
    }
}