<?php

namespace App\Traits;

use Symfony\Component\DomCrawler\Crawler;
use App\Models\Week;
use Carbon\Carbon;

trait FileUploadTrait
{
    protected function uploadFile($file)
    {
        $filename = $file->getClientOriginalName();
        $content = file_get_contents($file->getRealPath());

        return [
            'filename' => $filename,
            'content' => $content
        ];
    }

    protected function parseFile($content)
    {
        $crawler = new Crawler($content);
        $year = null;
        $month = null;
        $activeWeekId = null;
        $crawler->filter('select#ctl00_Main_periodSelect option')->each(function (Crawler $option) use (&$year, &$month, &$activeWeekId) {
            $value = $option->attr('value');
            $label = $option->text();
            $isActive = $option->attr('selected') === '' || $option->attr('selected') === 'selected' ? 1 : 0;

            list($startDate, $endDate) = explode('|', $value);

            $weekData = Week::updateOrCreate(
                [
                    'name' => $label,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                [
                    'active' => $isActive
                ]
            );

            if ($isActive) {
                $year = date('Y', strtotime($startDate));
                $month = date('m', strtotime($startDate));
                $activeWeekId = $weekData->id;
            }
        });

        $rows = $crawler->filter('table.activityTableStyle tbody tr')->each(function ($row) {
            return $row->filter('td')->each(function ($cell) {
                return trim($cell->text());
            });
        });

        $newrows = array_slice($rows, 1);

        $data = [];
        $previousDate = null;
        foreach ($newrows as $row) {

            $date = $this->parseDate($year, $month, $row[1], $previousDate);
            $checkIn = $this->parseTime($row[5]);
            $checkOut = $this->parseTime($row[7]);
            $activityType = $this->activityType($row[8]);
            $std = $this->parseTime($row[13]);
            $sta = $this->parseTime($row[17]);

            $data[] = [
                'date' =>  $date,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'activity' => $row[8],
                'activity_type' => $activityType,
                'from' => $row[11],
                'std' => $std,
                'to' => $row[15],
                'sta' => $sta,
                'week_id' => $activeWeekId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            $previousDate = $date;
        }

        return $data;
    }

    protected function parseDate($year, $month, $date, $previousDate)
    {
        $pattern = '/^[A-Z][a-z]{2} \d{2}$/';
        if (!preg_match($pattern, $date)) {
            return $previousDate;
        }

        $explode = explode(' ', $date);
        return $year . '-' . $month . '-' . $explode[1];
    }

    protected function parseTime($time)
    {
        $time = preg_replace('/[^A-Za-z0-9]/', '', $time);
        if (empty($time)) {
            return '';
        }
        $hours = substr($time, 0, 2);
        $minutes = substr($time, 2, 2);
        return $hours . ':' . $minutes . ':00';
    }

    protected function activityType($activity)
    {
        if (preg_match('/^[A-Za-z]{2}\d+$/', $activity)) {
            return 'FLT';
        } else if ($activity == 'OFF') {
            return 'DO';
        } else if ($activity == 'SBY') {
            return 'SBY';
        } else {
            return 'UNK';
        }
    }
}
