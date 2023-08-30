<div id="monthCalculationMethods">
    <label>
        <b>Количество месяцев для отображения</b>:
        <select class="form-control" id="monthCount">
            <option>1</option>
            <option selected>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
            <option>6</option>
        </select>
    </label>
</div>

<script>

    document.querySelectorAll('select#monthCount').forEach(function(el){
        el.addEventListener('change', function(e) {
            if (fetcher) {
                fetcher.getDataFromInternalServer();
            }
        });
    });

    function getMonthCount()
    {
        return document.querySelector('select#monthCount').value;
    }

</script>
