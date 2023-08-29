<?php

namespace App\Http\Controllers;

use App\Models\Bitcoin;
use App\Models\Date;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class BitcoinController extends Controller
{
    public function index()
    {
        try {
            Bitcoin::uploadNewPricesFromServer();
        } catch (Exception $e){}

        $variables = [
            'fromTimestamp' => Carbon::create(Bitcoin::getMaxTimestamp())->getTimestamp(),
            'toTimestamp' => Carbon::now()->getTimestamp(),
        ];

        return view('index', $variables);
    }

    public function addPrices(Request $request)
    {
        if (!$request->prices) {
            return response('Not Found', 404);
        }

        // Массив с ценами пришел. Теперь достанем все наши сохраненные точки:
        $savedPoints = self::getAllSavedPricesAsArray();

        // Побежали по пришедшим ценам. Если такой метки времени у нас еще нет - сохраняем
        foreach ($request->prices as $price) {
            if (!isset($savedPoints[$price[0]])) {
                $dateTime = Carbon::createFromTimestampMs($price[0])->format('Y-m-d H:i:s');
                self::addPointToDB($dateTime, $price[1]);
                unset($dateTime);
            }
        }

        return response([
            'result' => 'success'
        ], 200);
    }

    public static function addPointToDB($timestamp, $price)
    {
        DB::table('bitcoins')->insertOrIgnore([
            'timestamp' => $timestamp,
            'price' => $price
        ]);
    }

    public static function getAllSavedPricesAsArray()
    {
        $query = 'select * from bitcoins order by "timestamp"';
        $queryResult = DB::select($query);

        $result = [];
        $carbon = Carbon::now();
        foreach ($queryResult as $item) {
            $result[$carbon::createFromFormat('Y-m-d H:i:s', $item->timestamp)->getTimestampMs()] = $item->price;
        }

        unset($queryResult, $carbon);
        return $result;
    }

    public function getData(Request $request)
    {
//        dd(
//            $request->monthCalculationMethod,
//            $request->monthCount,
//            $request->weekCalculationMethod
//        );

        $result = Date::getDatesBySubmonth($request->monthCount);

//        foreach ($result['months'] as $monthNumber => $month) {
//            $result['months']['average'] =
//        }

        dd($result);
    }
}
