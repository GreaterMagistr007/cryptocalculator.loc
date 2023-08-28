<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class Bitcoin extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'timestamp',  'price'
    ];

    /** @var string  */
    const DATE_START = '2023-01-01';
//    const DATE_START = '2023-06-01';
//    const DATE_START = '2023-07-01';
    /** @var string  */
    const PERIOD = 'month';

    /** @var Gecco  */
    private $apiModel;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->apiModel = new Gecco();
    }

    /**
     * Заполняем таблицу в БД значениями начиная с DATE_START до текущего момента
     * @return void
     */
    public function fillTable()
    {
//        $minDbTimestamp = self::min('timestamp');
        $maxDbTimestamp = self::max('timestamp');

        $timeStart = $maxDbTimestamp ? Carbon::create($maxDbTimestamp) : Carbon::create(self::DATE_START);
        $timeEnd = Carbon::now();

        $time = new Carbon($timeStart);
        $time->add(1, self::PERIOD);
        while ($time < $timeEnd) {
            $this->saveValuesFromServer($timeStart->getTimestampMs(), $time->getTimestampMs());

            $timeStart->add(1, self::PERIOD);
            $time->add(1, self::PERIOD);
        }

        $this->saveValuesFromServer($timeStart->timestamp, $timeEnd->timestamp);
    }

    private function saveValuesFromServer($timestampStart, $timestampEnd)
    {
        $result = $this->apiModel->getPrices($timestampStart, $timestampEnd);

        foreach ($result as $key => $arr) {
            $item = new self([
                'timestamp' => Carbon::createFromTimestamp(intval((int)$arr[0] / 1000)),
                'price' => $arr[1],
            ]);
            $item->save();
        }
    }


    public function getPrices($dateStart = '', $dateEnd = '')
    {
        if (!$dateStart) {
            $dateStart = self::DATE_START;
        }

        if (!$dateEnd) {
            $dateEnd = Carbon::now();
        }

        $dateStart = Carbon::create($dateStart);
        $dateEnd = Carbon::create($dateEnd);

        // Сначала получим значения из БД:
//        if (!$this->getPricesFromDB($dateStart, $dateEnd)) {
//            // Таблица пока пустая.
//        }

        $result = $this->apiModel->getPrices($dateStart->timestamp, $dateEnd->timestamp);

        dd(
            $dateStart->timestamp,
            $dateEnd->timestamp,
            $result
        );
    }

    private function getPricesFromDB($timestampStart, $timestampEnd)
    {
        $minDbTimestamp = self::min('timestamp');
        if (!$minDbTimestamp) {
            return false;
        }
        $maxDbTimestamp = self::max('timestamp');

//        $timestampStart = $minDbTimestamp > $timestampStart ? $minDbTimestamp : $timestampStart;
//        $timestampEnd = $maxDbTimestamp < $timestampEnd ? $maxDbTimestamp : $timestampEnd;
//
//        $result = [
//            'timestampStart' => $minDbTimestamp,
//            'timestampEnd' => $maxDbTimestamp
//        ];
//
//
//        dd(
//            $minDbTimestamp,
//            $maxDbTimestamp
//        );
    }

    public static function getCource($dateStart, $dateEnd)
    {
        $dateStart = Carbon::create($dateStart);
        $dateEnd = Carbon::create($dateEnd);


    }

    public static function uploadNewPricesFromServer()
    {
        $apiModel = new Gecco();
        $maxDbTimestamp = self::getMaxTimestamp();
        $timeStart = $maxDbTimestamp ? Carbon::create($maxDbTimestamp) : Carbon::create(self::DATE_START);
        $timeEnd = Carbon::now();

        $result = $apiModel->getPrices($timeStart->getTimestamp(), $timeEnd->getTimestamp());

        foreach ($result as $point) {
            self::addPointToDB($point);
        }
    }

    public static function addPointToDB(array $point)
    {
        try {
            $timestamp = Carbon::createFromTimestampMs($point[0])->format('Y-m-d H:i:s');
            $price = $point[1];

            DB::table('bitcoins')->insertOrIgnore([
                'timestamp' => $timestamp,
                'price' => $price
            ]);
        } catch (Exception $e) {
            return;
        }
    }

    public static function getMaxTimestamp()
    {
        return self::max('timestamp');
    }
}
