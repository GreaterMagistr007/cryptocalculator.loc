<script>
    class CalculationMethods
    {
        // Родительский блок
        wrapper;
        // Выбранный метод
        selectedMethod;

        calculationMethods;

        constructor(calculationMethods) {
            this.calculationMethods = calculationMethods;
        }

        getWrapper()
        {
            if (!this.wrapper) {
                this.wrapper = document.querySelector(this.wrapperSelector);
            }

            console.log(this.wrapper);

            return this.wrapper;
        }

        setMethod(method)
        {
            if (!this.calculationMethods[method]) {
                return this.error(`Нет метода расчета "${method}"`);
            }

            this.selectedMethod = method;

            console.log(this.selectedMethod)
        }

        getMethod()
        {
            return this.selectedMethod;
        }

        error(text)
        {
            alert(text);
        }
    }
</script>
