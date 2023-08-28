<script>
    class Fetcher
    {
        dataObj;
        precision = 6;
        vs_currency = 'usd';

        constructor(fromTimestamp, toTimestamp) {
            this.fetchDataFromExternalServer(fromTimestamp, toTimestamp);
        }

        fetchDataFromExternalServer(fromTimestamp, toTimestamp)
        {
            let self = this;
            let uri = [
                `https://api.coingecko.com/api/v3/coins/bitcoin/market_chart/range?vs_currency=${this.vs_currency}`,
                `from=${fromTimestamp}`,
                `to=${toTimestamp}`,
                `precision=${this.precision}`
            ];
            uri = uri.join('&');
            fetch(uri)
                .then(response => response.json())
                .then(function(response){
                    self.dataObj = response;
                    return self.uploadPricesToServer();
                    // return self.getAveragePrice();
                });
        }

        uploadPricesToServer()
        {
            let self = this;
            let uri = '/add-points';

            let data = this.dataObj;

            fetch(uri, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(function(response){
                    return self.getDataFromInternalServer();
                })
        }

        getSettings()
        {
            console.log('Собираем настройки');
            let obj = {
                'monthCount' : getMonthCount(),
                'monthCalculationMethod' : monthCalculator.getMethod(),
                'weekCalculationMethod' : weekCalculator.getMethod(),
            };

            console.log(obj);

            return obj;
        }

        getDataFromInternalServer()
        {
            let self = this;
            let uri = '/get-data';

            let data = self.getSettings();

            for (let i in data) {
                if (!data[i]) {
                    console.error('Не получен ' + i + ' - отменяем запрос');
                    return;
                }
            }

            fetch (uri, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(function(response){
                    console.log(response)
                })
        }
    }

    let fetcher = new Fetcher({!! $fromTimestamp !!}, {!! $toTimestamp !!});
</script>
