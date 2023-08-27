<div id="weeklyCalculationMethods">
    <label>
        Выбор способа расчета:
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
    }

    let calculator = new weeklyCalculationMethodSelector(weeklyCalculationMethods);
</script>
