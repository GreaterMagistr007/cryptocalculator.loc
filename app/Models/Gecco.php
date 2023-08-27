<?php

namespace App\Models;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gecco extends Model
{
    use HasFactory;

    /** @var $client CoinGeckoClient  */
    private $client;
    /** @var $currencyId string */
    private $currencyId = 'bitcoin';
    /** @var $currencyVs string */
    private $currencyVs = 'usd';
    /** @var $precision int  */
    private $precision = 6;


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->client  = new CoinGeckoClient();

        if (!$this->client->ping()) {
            throw new \DomainException('lost connection to api.coingecko.com');
        }
    }

    public function getPrices($timestampStart, $timestampEnd)
    {
        try {
            $result = $this->client->coins()->getMarketChartRange(
                $this->currencyId,
                $this->currencyVs,
                $timestampStart,
                $timestampEnd
            );

            return $result['prices'];
        } catch (\Exception $e) {
            dd($e, $e->getMessage());
        }
    }
}
