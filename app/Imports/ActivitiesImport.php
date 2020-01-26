<?php

namespace App\Imports;

use App\Activity;
use App\ActivityType;
use App\Nova\Activity as AppActivity;
use App\Schedule;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ActivitiesImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        if ($row['type'] !== null) {
            $schedule = Schedule::where('title', $row['schedule'])->first();
            $type = ActivityType::firstOrCreate(['title' => $row['type']]);

            $start = new Carbon(Date::excelToDateTimeObject($row['start'])->format('Y-m-d H:i:s'), $schedule->event->timezone);
            $start->tz('UTC');
            $end = new Carbon(Date::excelToDateTimeObject($row['end'])->format('Y-m-d H:i:s'), $schedule->event->timezone);
            $end->tz('UTC');

            return new Activity([
                'schedule_id' => $schedule->id,
                'activity_type_id' => $type->id,
                'title' => $row['title'],
                'description' => $row['description'] ?? null,
                'location' => $row['location'] ?? null,
                'room' => $row['room'] ?? null,
                'start' => $start,
                'end' => $end,
                'spots' => $row['spots'] ?? null,
            ]);
        }
    }
}