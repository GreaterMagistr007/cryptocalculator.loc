<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    public static function getDatesBySubmonth($subMonths = 2)
    {
        $result = [];

        $dateEnd = Carbon::now()->endOfMonth()->subMonth();
        $dateStart = Carbon::now()->startOfMonth();

        while ($subMonths > 0) {
            $dateStart->subMonth();
            $subMonths -= 1;
        }

        $result = [
            'dateStart' => $dateStart->format('Y-m-d H:i:s'),
            'dateEnd' => $dateEnd->format('Y-m-d H:i:s'),
        ];

        dd($result);

        return $result;

    }

    public static function divideMonthByWeeks(Carbon $monthDate)
    {
        $firstMomentOfMonth = Carbon::createFromTimestamp($monthDate->startOfMonth()->getTimestamp());
        $lastMomentOfMonth = Carbon::createFromTimestamp($monthDate->endOfMonth()->getTimestamp());

        $result = [];

//        while ($firstMomentOfMonth <= $lastMomentOfMonth) {
////            $result
//        }
    }
}
