<script>
    class Calculator
    {
        datesClass;
        dates;
        minDate;
        maxDate;
        /* Округление (знаков после запятой) при получении значений */
        precision = 6;
        /* Округление при выводе на результата */
        result_precision = 2;
        /* валюта, в которой считается стоимость битка */
        vs_currency = 'usd';
        /* json объект, в котором хранятся значения */
        dataObj;
        /* курс рубля к доллару */
        cource;

        constructor() {
            this.fetchDataFromExternalServer();
            this.datesClass = new Dates();
            this.dates = this.datesClass.resultDatesArr;

            this.setTimes();
            this.getDataFromServer();
        }

        setTimes()
        {
            let minDate = this.dates[0].firstDay;
            let maxDate = this.dates[0].lastDay;
            for (let i in this.dates) {
                if (this.dates[i].firstDay < minDate) {
                    minDate = this.dates[i].firstDay;
                }
                if (this.dates[i].lastDay > maxDate) {
                    minDate = this.dates[i].lastDay;
                }
            }

            this.minDate = minDate;
            this.maxDate = maxDate;
        }

        toTimeStamp(date)
        {
            console.log(date);
            return date.getTime() / 1000;
        }

        getDataFromServer()
        {
            let self = this;

            let body = {
                "from" : this.toTimeStamp(this.minDate),
                "to" : this.toTimeStamp(this.maxDate),
            };

            // Получим курс доллара на сегодня:
            fetch('https://min-api.cryptocompare.com/data/price?fsym=USD&tsyms=RUB')
                .then(response => response.json())
                .then(function(response){
                    self.cource = response.RUB;
                })
                .then(function(){
                    // Получив курс валют, получим данные с биржи
                    fetch('get-data', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json;charset=utf-8',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    })
                        .then(response => response.json())
                        .then(function(response){
                            self.dataObj = response;
                            self.calculateData();
                            console.log(self.dataObj);

                            self.render();
                            // return self.getAveragePrice();
                        })
                });
        }

        calculateData()
        {
            for (let i in this.dates) {
                for (let week in this.dates[i].weeks) {
                    this.dates[i].weeks[week]['average'] = this.getAveragePrice(this.dates[i].weeks[week].startDate, this.dates[i].weeks[week].endDate)
                }
            }
        }

        // fetchInternalData()
        // {
        //     let uri = '/get-data';
        //     fetch(uri, {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json;charset=utf-8',
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //     });
        // }

        fetchDataFromExternalServer()
        {
            let self = this;
            let uri = [
                `https://api.coingecko.com/api/v3/coins/bitcoin/market_chart/range?vs_currency=${this.vs_currency}`,
                `from=${this.toTimeStamp(this.dateStart)}`,
                `to=${this.toTimeStamp(this.dateEnd)}`,
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
                    console.log(response);
                })
        }

    }


    let monthNames = {
        1 : 'Январь',
        2 : 'Февраль',
        3 : 'Март',
        4 : 'Апрель',
        5 : 'Май',
        6 : 'Июнь',
        7 : 'Июль',
        8 : 'Август',
        9 : 'Сентябрь',
        10 : 'Октябрь',
        11 : 'Ноябрь',
        12 : 'Декабрь',
    };

    class Dates
    {
        // Количество месяцев для расчетов:
        monthCount = 2;
        // массив с датами для расчетов:
        resultDatesArr = [];
        // Количество миллисекунд в сутках
        dayMilliseconds = 24*60*60*1000;

        constructor() {
            this.fillDatesArr();
        }

        fillDatesArr()
        {
            let date = new Date();

            // Идём в обратном порядке на нужное количество месяцев
            for (let i = this.monthCount; i > 0; i--) {
                date = this.previousDay( this.getFirstMonthDay(date) );
                this.resultDatesArr.push({
                    'firstDay' : this.getFirstMonthDay(date),
                    'lastDay' : this.getLastDayMoment( this.getLastMonthDay(date) )
                });
            }

            // Теперь разобьем месячные периоды на недельные:
            for (let monthPeriod in this.resultDatesArr) {
                // Создадим ключ, в котором будут храниться недельные периоды:
                if (!this.resultDatesArr[monthPeriod]['weeks']) {
                    this.resultDatesArr[monthPeriod]['weeks'] = [];
                }

                let startDate = this.getStartDayTime( this.resultDatesArr[monthPeriod].firstDay );
                let endDate = this.getLastDayMoment( this.addWeek(startDate) );
                let weekNumber = 1;

                let maxIteration = 10

                while (endDate <= this.resultDatesArr[monthPeriod].lastDay && weekNumber < maxIteration) {
                    this.resultDatesArr[monthPeriod].weeks.push({
                        'weekNumber' : weekNumber,
                        'startDate' : startDate,
                        'endDate' : endDate
                    });
                    weekNumber += 1;

                    startDate = this.getStartDayTime( this.getNextDay(endDate) );
                    endDate = this.addWeek(startDate);
                }
            }
        }

        getFirstMonthDay(date)
        {
            return new Date(date.getFullYear(), date.getMonth(), 1);
        }

        getLastMonthDay(date)
        {
            return new Date(date.getFullYear(), date.getMonth() + 1, 0);
        }

        previousDay(date)
        {
            let newDate = new Date(date);
            newDate.setTime(date.getTime() - this.dayMilliseconds);

            return newDate;
        }

        toTimestamp(date)
        {
            return date.getTime();
        }

        getLastDayMoment(date)
        {
            let result = this.getStartDayTime(date);
            result.setSeconds(date.getSeconds() + (60 * 60 * 24) -1);
            return result;
        }

        getNextDay(date)
        {
            // let result = this.getLastDayMoment(date);
            // result.setSeconds( result.getSeconds() + 1 );

            let result = this.getStartDayTime(date);
            result.setTime( result.getTime() + this.dayMilliseconds );

            return result;
        }

        getStartDayTime(date)
        {
            return new Date(date.getFullYear(), date.getMonth(), date.getDate());
        }

        addWeek(date)
        {
            let lastMonthMoment = this.getLastDayMoment( this.getLastMonthDay(date) );
            let result = new Date();
            result.setTime( this.getStartDayTime(date).getTime() + (6 * this.dayMilliseconds) );

            return result.getTime() > lastMonthMoment.getTime() ? lastMonthMoment : result;
        }
    }





    let dateCalculator = new Calculator();
</script>
