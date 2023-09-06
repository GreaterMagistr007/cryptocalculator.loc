@foreach($months as $title => $month)
<section class="mt-2"><h2>{!! $title !!}:</h2><tr2>
    </tr2><table class="table table-striped table-bordered"><thead>
        <tr>
            <th scope="col">Период</th>
            <th scope="col">
                Среднее значение за период
                <br>
                <small>по формуле {!! $month->weeks[0]->calculationMethod !!}</small>
            </th>
        </tr>
        </thead><tbody>
        @foreach ($month->weeks as $weekNum => $week)
        <tr>
            <td scope="row">
                Неделя {!! $weekNum + 1 !!} ({!! $week->startDateTime !!} - {!! $week->endDateTime !!}):
                @if ($week->calculateMethod === 'weeklyCalculationMethod2')
                    <br>
                    <small>
                    Минимальное значение {!! $week->dateOfMinimum->format('d.m.Y H:i:s') !!} - {!! $week->minValueOfPeriod !!}
                    <br>
                    Максимальное значение {!! $week->dateOfMaximum->format('d.m.Y H:i:s') !!} - {!! $week->maxValueOfPeriod !!}

                        <br>
                        ({!! $week->minValueOfPeriod !!} + {!! $week->maxValueOfPeriod !!}) = {!! $week->minValueOfPeriod + $week->maxValueOfPeriod !!}
                        <br>
                        {!! $week->minValueOfPeriod + $week->maxValueOfPeriod !!} / 2 = {!! ($week->minValueOfPeriod + $week->maxValueOfPeriod) / 2 !!}
                    </small>
                @endif
            </td>
            <td>

                <span>
                    {!! number_format($week->average, 2, '.', '') !!} $ / btc
                </span>

            </td>
        </tr>
        @endforeach

        </tbody>

        <thead>
        <tr><td scope="row">
                <b>Среднее за период:</b>
                <br>
                <small>по формуле {!! $month->calculationMethod !!}</small>
            </td>
            <td>
                        <span>
                            <b>{!! $month->average !!} </b> $
                        </span>
            </td>
        </tr>
        <tr><td colspan="2">
                <small>
                    Отношение к базовому курсу, прописанному в договоре (28000 $ / btc): {!! $month->relation !!} %
                </small>
            </td>
        </tr>
        </thead>
    </table>
</section>
@endforeach
