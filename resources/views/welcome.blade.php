<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Криптокалькулятор</title>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<div class="container">
    <section class="content">
        Средняя цена биткоина за период:
        <div class="container-fluid">
            <input type="date" name="calendar" id="date_start">
            <input type="date" name="calendar" id="date_end">
        </div>
        <br></br>
        <span style="display: none!important" id="uploadPricesButton" class="btn btn-success">upload prices to server</span>

    </section>
    <section id="result"></section>
    <hr>
    <div class="wrapper mt-3">


        <!--        <section class="mt-2">-->
        <!--            <h2>Июль:</h2>-->
        <!--            <table class="table table-striped table-bordered">-->
        <!--                <thead>-->
        <!--                <tr>-->
        <!--                    <th scope="col">Период</th>-->
        <!--                    <th scope="col">Среднее значение за период</th>-->
        <!--                </tr>-->
        <!--                </thead>-->
        <!--                <tr>-->
        <!--                    <td scope="row">Неделья 1 (1 - 7 июля): </td>-->
        <!--                    <td>-->
        <!--                    <span>-->
        <!--                        100500 $/btc-->
        <!--                    </span>-->
        <!--                    </td>-->
        <!--                </tr>-->
        <!--                <tr>-->
        <!--                    <td scope="row">Неделья 2 (8 - 14 июля): </td>-->
        <!--                    <td>-->
        <!--                    <span>-->
        <!--                        100500 $/btc-->
        <!--                    </span>-->
        <!--                    </td>-->
        <!--                </tr>-->
        <!--                <tr>-->
        <!--                    <td scope="row">Неделья 3 (15 -21 июля): </td>-->
        <!--                    <td>-->
        <!--                    <span>-->
        <!--                        100500 $/btc-->
        <!--                    </span>-->
        <!--                    </td>-->
        <!--                </tr>-->
        <!--                <tr>-->
        <!--                    <td scope="row">Неделья 4 (22 -28 июля): </td>-->
        <!--                    <td>-->
        <!--                    <span>-->
        <!--                        100500 $/btc-->
        <!--                    </span>-->
        <!--                    </td>-->
        <!--                </tr>-->
        <!--                <tr>-->
        <!--                    <td scope="row">Неделья 5 (29 -31 июля): </td>-->
        <!--                    <td>-->
        <!--                    <span>-->
        <!--                        100500 $/btc-->
        <!--                    </span>-->
        <!--                    </td>-->
        <!--                </tr>-->

        <!--                <tr>-->
        <!--                    <td scope="row">-->
        <!--                        <b>Среднее значение за месяц:</b>-->
        <!--                    </td>-->
        <!--                    <td>-->
        <!--                    <span>-->
        <!--                        <b>100500</b> $/btc-->
        <!--                    </span>-->
        <!--                    </td>-->
        <!--                </tr>-->

        <!--                <thead>-->
        <!--                <td scope="row">-->
        <!--                    <b>Сумма к оплате:</b>-->
        <!--                </td>-->
        <!--                <td>-->
        <!--                    <span>-->
        <!--                        <b>(нс1+нс2+нс3+нс4+нс5) / 5 </b> $-->
        <!--                    </span>-->
        <!--                </td>-->
        <!--                </thead>-->
        <!--            </table>-->
        <!--        </section>-->
    </div>
</div>


<script>
    class CryptoCalculator
    {
        /* Дата начала отсчета */
        dateStart;
        /* Дата окончания отсчета */
        dateEnd;
        /* Округление (знаков после запятой) при получении значений */
        precision = 6;
        /* Округление при выводе на результата */
        result_precision = 2;
        /* валюта, в которой считается стоимость битка */
        vs_currency = 'usd';
        /*json объект, в котором хранятся значения*/
        dataObj;

        constructor() {
            let self = this;
            document.querySelectorAll('#uploadPricesButton').forEach(function(button){
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    return self.uploadPricesToServer();
                })
            });
        }
        setDateStart(dateStart)
        {
            this.dateStart = new Date(dateStart);
            this.calculate();
        }
        setDateEnd(dateEnd)
        {
            this.dateEnd = new Date(dateEnd);
            this.calculate();
        }
        toTimeStamp(date)
        {
            return date.getTime() / 1000;
        }
        dateNormalize(date)
        {
            return `${date.getDate()}.${date.getMonth()}.${date.getFullYear()}`;
        }
        getDataFromServer()
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
                    return self.renderResult();
                    // return self.getAveragePrice();
                });
        }
        getAveragePrice()
        {
            let count = 0;
            let summ = 0;
            this.dataObj.prices.forEach(function(item){
                count += 1;
                summ += item[1]
            });

            return parseFloat(summ / count).toFixed(this.result_precision);
        }
        calculate()
        {
            if (this.dateStart && this.dateEnd) {
                this.getDataFromServer();
            }
        }
        renderResult()
        {
            let html = `` +

                // <p> ${this.dateStart.toLocaleDateString()} - ${this.dateEnd.toLocaleDateString()}:</p>
                `<h1>$${this.getAveragePrice()}</h1>
            `;

            if (dateCalculator && dateCalculator.cource) {
                html += ' (' + parseFloat(this.getAveragePrice() * dateCalculator.cource).toFixed(2) + ') руб. по текущему курсу';
            }

            document.querySelectorAll('#result').forEach(function(el){
                el.innerHTML = html;
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

    // let CryptoCalculator = new CryptoCalculator();

    let calculator = new CryptoCalculator();
    document.querySelectorAll('#date_start').forEach(function(el){
        el.addEventListener('change', function(e){
            e.preventDefault();
            calculator.setDateStart(el.value)
        });
    });
    document.querySelectorAll('#date_end').forEach(function(el){
        el.addEventListener('change', function(e){
            e.preventDefault();
            calculator.setDateEnd(el.value)
        });
    });


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
            this.datesClass = new Dates();
            this.dates = this.datesClass.resultDatesArr;

            this.setTimes();
            this.getDataFromServer();


            console.log(this);
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
            return date.getTime() / 1000;
        }

        getDataFromServer()
        {
            let self = this;

            // Получаем курсы битка за выбранные периоды:
            let uri = [
                `https://api.coingecko.com/api/v3/coins/bitcoin/market_chart/range?vs_currency=${this.vs_currency}`,
                `from=${this.toTimeStamp(this.minDate)}`,
                `to=${this.toTimeStamp(this.maxDate)}`,
                `precision=${this.precision}`
            ];
            uri = uri.join('&');

            // Получим курс доллара на сегодня:
            fetch('https://min-api.cryptocompare.com/data/price?fsym=USD&tsyms=RUB')
                .then(response => response.json())
                .then(function(response){
                    self.cource = response.RUB;
                })
                .then(function(){
                    // Получив курс валют, получим данные с биржи
                    fetch(uri)
                        .then(response => response.json())
                        .then(function(response){
                            self.dataObj = response;
                            self.calculateData();
                            console.log(self.dataObj);

                            self.render();
                            // return self.getAveragePrice();
                        })
                })
            ;
        }

        calculateData()
        {
            for (let i in this.dates) {
                for (let week in this.dates[i].weeks) {
                    this.dates[i].weeks[week]['average'] = this.getAveragePrice(this.dates[i].weeks[week].startDate, this.dates[i].weeks[week].endDate)
                }
            }
        }

        _renderMonthBlock(startDate, endDate, weeks)
        {
            let resultHtml = `<section class="mt-2">`;
            resultHtml += `<h2>${monthNames[startDate.getMonth() +1]}:</h2>`;
            resultHtml += `<table class="table table-striped table-bordered">`;
            resultHtml += `<thead>
                                <tr>
                                    <th scope="col">Период</th>
                                    <th scope="col">Среднее значение за период</th>
                                </tr>
                           </thead><tbody>`;

            let weekCount = 0;
            let summ = 0;
            let formula = '';
            for (let i in weeks) {
                weekCount += 1;
                summ += parseFloat(weeks[i].average);
                resultHtml += this._renderWeekBlock(weeks[i]);

                if (weekCount > 1) {
                    formula += '+';
                }
                formula += 'нс' + weekCount;
            }

            formula += ' / ' + weekCount;

            let dollarAverage = (summ / weekCount).toFixed(2);
            let rubAverage = (dollarAverage * this.cource).toFixed(2);

            let relation = parseFloat(100 - parseFloat(parseFloat( 28000 / dollarAverage).toFixed(4) * 100).toFixed(2)).toFixed(2);

            if (relation > 0) {
                relation = '+' + relation;
            }

            resultHtml += `
                </tbody>

                <thead>
                    <tr><td scope="row">
                        <b>Среднее за период:</b>
                        <br>
                        <small>по формуле ${formula}</small>
                    </td>
                    <td>
                        <span>
                            <b>${ dollarAverage } </b> $`
                // (${rubAverage} руб по текущему курсу)
                +`
                        </span>
                    </td>
                    </tr>
                    <tr2>
                        <td colspan="2">
                            <small>
                                Отношение к базовому курсу, прописанному в договоре (28000 $ / btc): ${ relation } %
                            </small>
                        </td>
                    </tr>
                </thead>
                </table>
                </section>
            `;

            return resultHtml;
        }

        _normalizeDayToString(day)
        {
            let result = '';
            if (day < 10) {
                result += '0';
            }

            return result + String(day);
        }

        _normalizeWeekString(week)
        {
            let monthNumber = this._normalizeDayToString(week.startDate.getMonth() + 1);

            let startDateDay = this._normalizeDayToString(week.startDate.getDate());

            let endDateDay = this._normalizeDayToString(week.endDate.getDate());

            return `
                Неделя ${week.weekNumber} (${ startDateDay }.${ monthNumber } - ${ endDateDay }.${ monthNumber })
            `;
        }

        _renderWeekBlock(week)
        {
            return `
            <tr>
                <td scope="row">${this._normalizeWeekString(week)}: </td>
                <td>

                <span>
                    ${this.renderPrice( week.average )}
                </span>

                </td>
            </tr>
            `;
        }

        renderPrice(price)
        {
            // return `${ parseFloat(price).toFixed(2) } $ (${ parseFloat(this.cource * price).toFixed(2) } руб) за 1 btc`;
            return `${ parseFloat(price).toFixed(2) } $ / btc`;
        }

        getAveragePrice(timeStart, timeEnd)
        {
            let count = 0;
            let summ = 0;
            for (let i in this.dataObj.prices) {
                if (this.dataObj.prices[i][0] > timeStart && this.dataObj.prices[i][0] < timeEnd) {
                    count += 1;
                    summ += this.dataObj.prices[i][1]
                }
            }

            return parseFloat(summ / count).toFixed(this.result_precision);
        }

        render()
        {
            let html = ``;
            for (let i in this.dates) {
                html += this._renderMonthBlock(this.dates[i].firstDay, this.dates[i].lastDay, this.dates[i].weeks);
            }

            document.querySelectorAll('.wrapper').forEach(function(el){
                el.innerHTML += html;
            })
        }
    }


    /**
     * 1- получим текущий номер месяца
     * 2- найдем два предыдущих
     * 3- разбиваем нужный месяц на периоды по 7 дней (неделя)
     * 4- запрос на получение данных за период
     * 5- расчет средней цены запериод
     * 6- поучить курсы валют
     * 7- вычислить сумму за месяц в долларах и рублях
     * 8- рендер месяца
     */

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
</body>
</html>
