<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Mailaway</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <header>
            <h1>Mailaway</h1>
        </header>
        <section>
            <div id="app">
                <example-component></example-component>
            </div>
        </section>
        <footer></footer>
        <script src="/js/app.js"></script>
    </body>
</html>
