<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MBLGTACC2018VolunteerForm extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Form::create([
            'name'      => 'MBLGTACC 2018 Volunteer Interest Form',
            'slug'      => Str::slug('MBLGTACC 2018 Volunteer Interest Form'),
            'event_id'  => 2,
            'start'     => '2017-09-08 00:00:00',
            'end'       => '2017-11-25 00:00:00',
            'is_public' => true,
            'form'      => json_decode(file_get_contents(base_path('database/seeds/data/mblgtacc2018VolunteerForm.json'))),
        ]);
    }
}
