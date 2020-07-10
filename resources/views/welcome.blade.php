<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>WSE</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

  <link rel="stylesheet" href="/css/app.css">
</head>

<body>
  <div id="app">
    <app></app>
  </div>
  <script>
    window.__user__ = @json($user);
    window.Laravel = {!! json_encode([
        "apiToken" => auth()->user()->api_token ?? null
      ]) !!};
  </script>
  <script src="/js/app.js"></script>
</body>

</html>