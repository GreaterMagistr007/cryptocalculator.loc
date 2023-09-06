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
    <section id="settings" class="mp-3">
        <nav id="settings-nav" class="navbar navbar-expand-lg navbar-light bg-light">
            <a id="settings-nav-selector" class="navbar-brand" href="#">Настройки</a>
        </nav>
        <div id="settings-block">
            <div class="hidden">
                @include('components.calculationMethodsClass')
                @include('components.weeklyCalculationMethodsBlock')
                <hr>
                @include('components.monthCalculationMethodsBlock')
                <hr>
            </div>

            @include('components.monthCountSettingsSelector')

        </div>

        <script>
            function toggleSettings() {
                let href =  document.querySelector('#settings-nav a#settings-nav-selector');
                if (!href) {
                    return;
                }

                let settingsBlock = document.querySelector('#settings-block');
                if (!settingsBlock) {
                    return;
                }

                if (settingsBlock.classList.contains('hidden')) {
                    settingsBlock.classList.remove('hidden');
                } else {
                    settingsBlock.classList.add('hidden');
                }

                href.classList.toggle('active');
            }

            document.querySelectorAll('#settings-nav a#settings-nav-selector').forEach(function(href){
                href.addEventListener('click', function(e){
                    e.preventDefault();
                    toggleSettings();
                });
            });
        </script>

    </section>
    <section class="mp-3" id="result"></section>
    @include('components.fetcherClass')
{{--    @include('components.calculatorBlock')--}}
</div>


<style>
    .formula {
        font-weight: bold;
    }
    .hidden {
        display: none!important;
    }
</style>
