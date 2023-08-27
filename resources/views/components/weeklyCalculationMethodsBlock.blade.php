<div id="weeklyCalculationMethods">
    <label>
        Выбор способа расчета <b>среднего за неделю</b>:
    </label>
</div>

<script>
    // Возможные варианты
    let weeklyCalculationMethods = {
        'weeklyCalculationMethod1' : "(сумма всех значений за период) / (количество значений)",
        'weeklyCalculationMethod2' : "(Минимальное + максимальное значение за период) / 2",
    };

    class weeklyCalculationMethodSelector
    {
        wrapperSelector = '#weeklyCalculationMethods';

        // Родительский блок
        wrapper;
        // Выбранный метод
        selectedMethod;

        constructor(weeklyCalculationMethods) {
            this.weeklyCalculationMethods = weeklyCalculationMethods;
            this.fillWrapper();
        }

        getWrapper()
        {
            if (!this.wrapper) {
                this.wrapper = document.querySelector(this.wrapperSelector);
            }

            return this.wrapper;
        }

        renderBlock(method, formula)
        {
            return `
                <input class="form-check-input" type="radio" name="weeklyCalculationMethod" id="${method}" value="${method}">
                <label class="form-check-label" for="${method}">
                    Недельный средний считается по формуле:<br>
                    <span class="formula">
                        ${formula}
                    </span>
                </label>
            `;
        }

        fillWrapper()
        {
            let self = this;
            if (!this.getWrapper()) {
                return self.error(
                    `Класс weeklyCalculationMethodSelector не нашел wrapper с селектором ${wrapperSelector}`
                );
            }

            for (let method in (this.weeklyCalculationMethods)) {
                let formCheck = document.createElement('div');
                this.getWrapper().appendChild(formCheck);
                formCheck.innerHTML = this.renderBlock(method, this.weeklyCalculationMethods[method]);
                formCheck.classList.add('weeklyCalculationMethodsFormCheck');

                formCheck.querySelectorAll('input').forEach(function(input){
                    input.addEventListener('change', function(e){
                        self.setMethod(input.value);
                        console.log(self.getMethod());
                    });
                });
            }

            document.querySelector('.weeklyCalculationMethodsFormCheck input').click();
        }

        getMethod()
        {
            return this.selectedMethod;
        }

        setMethod(method)
        {
            if (!this.weeklyCalculationMethods[method]) {
                return this.error(`Нет метода расчета "${method}"`);
            }

            this.selectedMethod = method;
        }

        error(text)
        {
            alert(text);
        }

        weeklyCalculationMethod1(pricesArr)
        {
            let sum = 0;
            let count = 0;

            let keys = {
                'timestamp' : 0,
                'price' : 1,
            }

            for (let i in (pricesArr)) {
                if (parseInt(pricesArr[keys['timestamp']]) > 0 && parseFloat(pricesArr[keys['price']]) > 0) {
                    sum += parseFloat(pricesArr[keys['price']]);
                    count += 1;
                }
            }

            return sum / count;
        }

        weeklyCalculationMethod2(pricesArr)
        {
            let keys = {
                'timestamp' : 0,
                'price' : 1,
            }

            let min = 0;
            let max = 0;

            for (let i in (pricesArr)) {
                let price = parseFloat(pricesArr[keys['price']]);
                if (parseInt(pricesArr[keys['timestamp']]) > 0 && price > 0) {
                    if (min === 0 || price < min) {
                        min = price;
                    }

                    if (max === 0 || price > max) {
                        max = price;
                    }
                }
            }

            return parseFloat(min + max) / 2;
        }

        calculation(pricesArr)
        {
            return this[this.getMethod()](pricesArr);
        }
    }

    let calculator = new weeklyCalculationMethodSelector(weeklyCalculationMethods);
</script>
