<?php

namespace App\Http\Controllers;

use App\Models\Bitcoin;
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
        return view('index');
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

        return response('ok', 200);
    }

    public static function addPointToDB($timestamp, $price)
    {
//        $query = 'insert into bitcoins (timestamp, price) values (%s, %)';

        DB::table('bitcoins')->insertOrIgnore([
            'timestamp' => $timestamp,
            'price' => $price
        ]);
//        DB::insert($query, [$timestamp, $price]);
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

    }
}
