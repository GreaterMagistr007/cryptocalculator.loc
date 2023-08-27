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
            console.log(calculationMethods);
            console.log(this.calculationMethods);
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
        }

        getMethod()
        {
            return this.selectedMethod;
        }
    }
</script>
