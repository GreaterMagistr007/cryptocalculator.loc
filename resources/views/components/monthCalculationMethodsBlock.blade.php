<div id="monthCalculationMethods">
    <label>
        Выбор способа расчета <b>среднего за месяц</b>:
    </label>
</div>

<script>
    // Возможные варианты
    let monthlyCalculationMethods = {
        'monthCalculationMethod1' : "(недельное среднее 1 (НС1) + (НС2) + (НС3) + (НС4) + (НС5)) / (количество недель)",
        'monthCalculationMethod2' : "(сумма всех значений за период) / (количество значений)",
        'monthCalculationMethod3' : "(Минимальное + максимальное значение за период) / 2",
    };

    class monthCalculationMethodSelector extends CalculationMethods
    {
        wrapperSelector = '#monthCalculationMethods';

        constructor(calculationMethods) {
            super(calculationMethods);
            this.fillWrapper();
        }

        renderBlock(method, formula)
        {
            return `
                <input class="form-check-input" type="radio" name="monthCalculationMethod" id="${method}" value="${method}">
                <label class="form-check-label" for="${method}">
                    Месячный средний считается по формуле:<br>
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
                    `Класс monthCalculationMethodSelector не нашел wrapper с селектором ${self.wrapperSelector}`
                );
            }

            for (let method in (this.calculationMethods)) {
                let formCheck = document.createElement('div');
                this.getWrapper().appendChild(formCheck);
                formCheck.innerHTML = this.renderBlock(method, this.calculationMethods[method]);
                formCheck.classList.add('monthCalculationMethodsFormCheck');

                formCheck.querySelectorAll('input').forEach(function(input){
                    input.addEventListener('change', function(e){
                        self.setMethod(input.value);
                    });
                });
            }

            document.querySelector('.monthCalculationMethodsFormCheck input').click();
        }
    }

    let monthCalculator = new monthCalculationMethodSelector(monthlyCalculationMethods);
</script>
